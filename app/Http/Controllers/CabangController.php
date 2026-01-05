<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Regions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class CabangController extends Controller
{
    public function index(Request $request)
    {

        // $query = Cabang::query();
        $query = Cabang::with('region');

        if (!empty($request->nama_cabang)) {
            $query->where('nama_cabang', 'like', '%' . $request->nama_cabang . '%');
        }
        $query->orderBy('kode_cabang');
        $cabang = $query->paginate(10);
        $cabang->appends(request()->all());
        return view('datamaster.cabang.index', compact('cabang'));
    }

    public function create()
    {
        $regions = Regions::orderBy('nama_region')->get();
        return view('datamaster.cabang.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_cabang' => 'required|max:5|unique:cabang,kode_cabang',
            'nama_cabang' => 'required',
            'alamat_cabang' => 'required',
            'telepon_cabang' => 'required|numeric',
            'lokasi_cabang' => 'required',
            'radius_cabang' => 'required',
            'kode_region'    => 'required|exists:regions,kode_region',
        ]);


        try {
            Cabang::create([
                'kode_cabang' => $request->kode_cabang,
                'nama_cabang' => $request->nama_cabang,
                'alamat_cabang' => $request->alamat_cabang,
                'telepon_cabang' => $request->telepon_cabang,
                'lokasi_cabang' => $request->lokasi_cabang,
                'radius_cabang' => $request->radius_cabang,
                'kode_region'    => $request->kode_region,
            ]);
            return Redirect::back()->with(messageSuccess('Data Berhasil Disimpan'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }


    public function edit($kode_cabang)
    {
        $kode_cabang = Crypt::decrypt($kode_cabang);
        $cabang = Cabang::where('kode_cabang', $kode_cabang)->first();
        $regions = Regions::orderBy('nama_region')->get();

        return view('datamaster.cabang.edit', compact('cabang', 'regions'));
    }


    public function update(Request $request, $kode_cabang)
    {
        $kode_cabang = Crypt::decrypt($kode_cabang);
        $request->validate([
            'nama_cabang' => 'required',
            'alamat_cabang' => 'required',
            'telepon_cabang' => 'required|numeric',
            'lokasi_cabang' => 'required',
            'radius_cabang' => 'required',
            'kode_region'    => 'required|exists:regions,kode_region',
        ]);


        try {
            Cabang::where('kode_cabang', $kode_cabang)->update([
                'nama_cabang' => $request->nama_cabang,
                'alamat_cabang' => $request->alamat_cabang,
                'telepon_cabang' => $request->telepon_cabang,
                'lokasi_cabang' => $request->lokasi_cabang,
                'radius_cabang' => $request->radius_cabang,
                'kode_region'    => $request->kode_region,
            ]);
            return Redirect::back()->with(messageSuccess('Data Berhasil Di Update'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }

    public function destroy($kode_cabang)
    {
        $kode_cabang = Crypt::decrypt($kode_cabang);
        try {
            Cabang::where('kode_cabang', $kode_cabang)->delete();
            return Redirect::back()->with(messageSuccess('Data Berhasil Dihapus'));
        } catch (\Exception $e) {
            return Redirect::back()->with(messageError($e->getMessage()));
        }
    }
}
