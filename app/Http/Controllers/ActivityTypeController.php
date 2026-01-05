<?php

namespace App\Http\Controllers;

use App\Models\ActivityType;
use Illuminate\Http\Request;

class ActivityTypeController extends Controller
{
    public function index(Request $request)
    {
        // withCount supaya bisa tahu berapa activity yang memakai tipe ini
        $query = ActivityType::withCount('activities');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $activityTypes = $query->orderBy('name')->paginate(15);

        return view('activity_types.index', compact('activityTypes'));
    }

    public function create()
    {
        // partial view untuk modal create
        return view('activity_types.partials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:activity_types,name',
        ]);

        ActivityType::create($validated);

        return redirect()->route('activitytypes.index')
            ->with('success', 'Activity Type berhasil ditambahkan.');
    }

    public function edit(ActivityType $activityType)
    {
        // partial view untuk modal edit
        return view('activity_types.partials.edit', compact('activityType'));
    }

    public function update(Request $request, ActivityType $activityType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:activity_types,name,' . $activityType->id,
        ]);

        $activityType->update($validated);

        return redirect()->route('activitytypes.index')
            ->with('success', 'Activity Type berhasil diupdate.');
    }

    public function destroy(ActivityType $activityType)
    {
        // opsional: cegah delete kalau sudah dipakai activity
        if ($activityType->activities()->exists()) {
            return redirect()->route('activitytypes.index')
                ->with('error', 'Activity Type tidak bisa dihapus karena sudah dipakai di aktivitas.');
        }

        $activityType->delete();

        return redirect()->route('activitytypes.index')
            ->with('success', 'Activity Type berhasil dihapus.');
    }
}
