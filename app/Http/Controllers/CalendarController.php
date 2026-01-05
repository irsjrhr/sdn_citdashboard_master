<?php

namespace App\Http\Controllers;
use App\Models\ActivityType;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // $activityTypes = ActivityType::orderBy('name')->get();

        // $jabatanList = DB::table('jabatan')
        //     ->select('kode_jabatan', 'nama_jabatan')
        //     ->orderBy('nama_jabatan')
        //     ->get();

        // // return view('calendar.index', compact('activityTypes'));
        // return view('calendar.index', compact('activityTypes', 'jabatanList'));
        $user = $request->user();

        // 1️⃣ Ambil NIK user login
        $myNik = DB::table('users_karyawan')
            ->where('id_user', $user->id)
            ->value('nik');

        if (!$myNik) {
            abort(403);
        }

        // 2️⃣ Ambil diri sendiri + bawahan (recursive)
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
            SELECT DISTINCT kode_jabatan
            FROM karyawan
            WHERE nik IN (SELECT nik FROM bawahan)
        ";

        $jabatanCodes = collect(DB::select($sql, [$myNik]))
            ->pluck('kode_jabatan');

        // 3️⃣ Ambil nama jabatan (master)
        $jabatanList = DB::table('jabatan')
            ->whereIn('kode_jabatan', $jabatanCodes)
            ->orderBy('nama_jabatan')
            ->get();

        $activityTypes = ActivityType::orderBy('name')->get();

        return view('calendar.index', compact('activityTypes', 'jabatanList'));
    }

    public function karyawanByJabatan(Request $request)
    {
        return DB::table('users_karyawan')
            ->join('karyawan', 'karyawan.nik', '=', 'users_karyawan.nik')
            ->select(
                'karyawan.nik',
                DB::raw('MAX(karyawan.nama_karyawan) as nama')
            )
            ->where('karyawan.kode_jabatan', $request->jabatan)
            ->groupBy('karyawan.nik')
            ->orderBy('nama')
            ->get();
    }
}
