<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ActivityController extends Controller
{
    public function index(Request $request)
    {
        // $user = $request->user();

        // $query = Activity::with('activityType')
        //     ->where('user_id', $user->id);
        $user = $request->user();

        /**
         * 1. Ambil NIK user login
         */
        $myNik = DB::table('users_karyawan')
            ->where('id_user', $user->id)
            ->value('nik');

        if (!$myNik) {
            return response()->json([]);
        }

        /**
         * 2. CTE RECURSIVE → diri sendiri + semua bawahan
         */
        $sql = "
        WITH RECURSIVE bawahan AS (
            SELECT nik, nik_atasan
            FROM karyawan
            WHERE nik = ?

            UNION ALL

            SELECT k.nik, k.nik_atasan
            FROM karyawan k
            INNER JOIN bawahan b ON k.nik_atasan = b.nik
        )
        SELECT nik FROM bawahan
        ";

        $nikList = collect(DB::select($sql, [$myNik]))
            ->pluck('nik');

        /**
         * 3. Mapping ke user_id + filter jabatan
         */
        $userIdsQuery = DB::table('users_karyawan')
            ->join('karyawan', 'users_karyawan.nik', '=', 'karyawan.nik')
            ->whereIn('users_karyawan.nik', $nikList);

        if ($request->filled('jabatan')) {
            $userIdsQuery->where('karyawan.kode_jabatan', $request->jabatan);
        }

        if ($request->filled('karyawan')) {
            $userIdsQuery->where('users_karyawan.nik', $request->karyawan);
        }

        // $userIds = $userIdsQuery->pluck('users_karyawan.id_user');
        $userIds = $userIdsQuery
            ->select('users_karyawan.id_user')
            ->distinct()
            ->pluck('id_user');

        /**
         * 4. Query activity
         */
        $start = Carbon::parse($request->start)->startOfDay();
        $end   = Carbon::parse($request->end)->endOfDay();

        // $query = Activity::with('activityType')
        //     ->whereIn('user_id', $userIds)
        //     ->whereBetween('start', [$start, $end]);

        $query = Activity::with('activityType')
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('start', [$start, $end]);
            })
            ->when($request->my_only == 1, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            }, function ($q) use ($userIds) {
                $q->whereIn('user_id', $userIds);
            });
        // if ($request->filled('start')) {
        //     $query->whereDate('start', '>=', $request->start);
        // }
        // if ($request->filled('end')) {
        //     $query->whereDate('start', '<=', $request->end);
        // }
        

        $activities = $query->get();

        $events = $activities->map(function ($a) {
            $color = $a->color ?: ($a->is_focus ? '#d13438' : '#3c88faff');

            return [
                'id'    => $a->id,
                'title' => $a->title,
                'start' => $a->start->toIso8601String(),
                'end'   => optional($a->end)->toIso8601String(),
                'color' => $color,
                'extendedProps' => [
                    'description'        => $a->description,
                    'location'           => $a->location,
                    'activityTypeId'     => $a->activity_type_id,
                    'activityTypeName'   => optional($a->activityType)->name,
                    'isFocus'            => $a->is_focus,
                    'realizationStatus'  => $a->realization_status,
                    'realizationNote'    => $a->realization_note,
                    'realizationAt'      => optional($a->realization_at)->toIso8601String(),
                    'isOwner' => $a->user_id === auth()->id(),
                ],
            ];
        });

        return response()->json($events);
    }

    // CREATE (planning)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'start'            => 'required|date',
            'end'              => 'nullable|date|after_or_equal:start',
            'location'         => 'nullable|string|max:255',
            'activity_type_id' => 'required|exists:activity_types,id',
            'is_focus'         => 'nullable|boolean',
            'repeat_type'      => 'nullable|in:daily,weekly',
        ]);

        $userId   = $request->user()->id;
        $isFocus  = $request->has('is_focus');
        $repeat   = $validated['repeat_type'] ?? null;

        $start = \Carbon\Carbon::parse($validated['start']);
        $end   = isset($validated['end']) ? \Carbon\Carbon::parse($validated['end']) : null;

        // base payload
        $baseData = [
            'user_id'          => $userId,
            'title'            => $validated['title'],
            'description'      => $validated['description'] ?? null,
            'location'         => $validated['location'] ?? null,
            'activity_type_id' => $validated['activity_type_id'],
            'is_focus'         => $isFocus,
        ];

        // PATCH: no repetition
        if (!$repeat) {
            Activity::create(array_merge($baseData, [
                'start' => $start,
                'end'   => $end,
            ]));

            return redirect()->route('calendar.index')
                ->with('success', 'Aktivitas berhasil ditambahkan.');
        }

        // PATCH: DAILY → sampai Sabtu minggu yang sama
        if ($repeat === 'daily') {
            $lastDay = $start->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);

            for ($date = $start->copy(); $date <= $lastDay; $date->addDay()) {
                Activity::create(array_merge($baseData, [
                    'start' => $date->copy(),
                    'end'   => $end ? $date->copy()->setTimeFrom($end) : null,
                ]));
            }
        }

        // PATCH: WEEKLY → hari & jam sama sampai akhir bulan
        if ($repeat === 'weekly') {
            $lastWeek = $start->copy()->endOfMonth();

            for ($date = $start->copy(); $date <= $lastWeek; $date->addWeek()) {
                Activity::create(array_merge($baseData, [
                    'start' => $date->copy(),
                    'end'   => $end ? $date->copy()->setTimeFrom($end) : null,
                ]));
            }
        }

        return redirect()->route('calendar.index')
            ->with('success', 'Aktivitas berulang berhasil ditambahkan.');
    }

    // UPDATE (planning + realisasi)
    public function update(Request $request, Activity $activity)
    {
        if ($activity->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'start'             => 'required|date',
            'end'               => 'nullable|date|after_or_equal:start',
            'location'          => 'nullable|string|max:255',
            'activity_type_id'  => 'required|exists:activity_types,id',
            'is_focus'          => 'nullable|boolean',
            'realization_status'=> 'nullable|in:realized,not_realized',
            'realization_note'  => 'required_with:realization_status|string|nullable',
        ]);

        $data = [
            'title'            => $validated['title'],
            'description'      => $validated['description'] ?? null,
            'start'            => $validated['start'],
            'end'              => $validated['end'] ?? null,
            'location'         => $validated['location'] ?? null,
            'activity_type_id' => $validated['activity_type_id'],
            'is_focus'         => $request->has('is_focus'),
        ];

        $data['color'] = $data['is_focus'] ? '#d13438' : '#3c88faff';

        if (!empty($validated['realization_status'])) {
            $data['realization_status'] = $validated['realization_status'];
            $data['realization_note']   = $validated['realization_note'];

            if (
                $activity->realization_status !== $validated['realization_status'] ||
                $activity->realization_note !== $validated['realization_note'] ||
                $activity->realization_at === null
            ) {
                $data['realization_at'] = now();
            }
        }

        $activity->update($data);

        return redirect()->route('calendar.index')
            ->with('success', 'Aktivitas berhasil diupdate.');
    }

    public function destroy(Request $request, Activity $activity)
    {
        if ($activity->user_id !== $request->user()->id) {
            abort(403);
        }

        $activity->delete();

        return redirect()->route('calendar.index')
            ->with('success', 'Aktivitas berhasil dihapus.');
    }
}
