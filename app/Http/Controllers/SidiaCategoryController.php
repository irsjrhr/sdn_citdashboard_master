<?php

namespace App\Http\Controllers;

use App\Models\SobatBrand;
use App\Models\SidiaCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SidiaCategoryController extends Controller
{
    // List + filter
    public function index(Request $request)
    {
        $query = SidiaCategory::query();

        if ($request->filled('category_name')) {
            $query->where('category_name', 'like', '%'.$request->category_name.'%');
        }

        $category = $query->orderBy('category_name')->paginate(10);
        $category->appends($request->all());

        return view('sidia.categories.index', compact('category'));
    }

    // ----- CREATE -----
    public function create()
    {
        return view('sidia.categories.create');
    }

    public function store(Request $request)
    {
        // catatan: koneksi model SobatBrand sudah mysqlsobat
        $request->validate([
            'category_code'     => ['required','string','max:5','unique:mysql.sidia_categories,category_code'],
            'category_name'     => ['nullable','string'],
        ]);

        $data = [
            'category_code'     => $request->category_code,
            'category_name'     => $request->category_name,
        ];

        SidiaCategory::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    // ----- EDIT -----
    // Tetap pakai brand_name terenkripsi (kompatibel dengan route kamu saat ini)
    public function edit($category_code)
    {
        $category_code = Crypt::decrypt($category_code);
        $category = SidiaCategory::where('category_code', $category_code)->firstOrFail();
        return view('sidia.categories.edit', compact('category'));
    }

    public function update(Request $request, $encCode)
    {
        $categoryCode = Crypt::decrypt($encCode);
        $category = SidiaCategory::where('category_code', $categoryCode)->firstOrFail();

        $request->validate([
            'category_name' => ['nullable','string'],
        ]);

        $category->update([
            'category_name' => $request->category_name
        ]);

        return back()->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy($category_code)
    {
        $category = SidiaCategory::where('category_code', $category_code)->firstOrFail();
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
