<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    /**
     * Display a listing of kategori
     */
    public function index(Request $request)
    {
        $kategori = Kategori::orderBy('jenis')->orderBy('nama')->get();

        return view('kategori.index', compact('kategori'));
    }

    /**
     * Show the form for creating a new kategori
     */
    public function create()
    {
        return view('kategori.create');
    }

    /**
     * Store a newly created kategori
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'jenis'     => 'required|in:pemasukan,pengeluaran',
            'parent_id' => 'nullable|exists:kategori,id',
            'icon'      => 'nullable|string|max:50',
            'warna'     => 'nullable|string|max:7',
        ]);

        try {
            Kategori::create([
                'household_id' => auth()->user()->household_id,
                'nama'         => $request->nama,
                'jenis'        => $request->jenis,
                'parent_id'    => $request->parent_id ?: null,
                'icon'         => $request->icon,
                'warna'        => $request->warna ?? '#6c757d',
            ]);

            return redirect()
                ->route('kategori.index')
                ->with('success', 'Kategori berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified kategori
     */
    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    /**
     * Update the specified kategori
     */
    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'jenis' => 'sometimes|in:pemasukan,pengeluaran',
            'icon' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:7',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $kategori->update($request->only(['nama', 'jenis', 'icon', 'warna', 'keterangan']));

            return redirect()
                ->route('kategori.index')
                ->with('success', 'Kategori berhasil diperbarui');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    /**
     * Check if kategori has dependencies (AJAX)
     */
    public function checkDelete(Kategori $kategori)
    {
        $transaksiCount = $kategori->transaksi()->count();
        $anggaranCount = $kategori->anggaran()->count();

        $penggantiOptions = [];
        if ($transaksiCount > 0 || $anggaranCount > 0) {
            $penggantiOptions = Kategori::where('id', '!=', $kategori->id)
                ->where('jenis', $kategori->jenis)
                ->whereNull('parent_id')
                ->orderBy('nama')
                ->get(['id', 'nama'])
                ->toArray();
        }

        return response()->json([
            'has_dependencies' => $transaksiCount > 0 || $anggaranCount > 0,
            'transaksi_count'  => $transaksiCount,
            'anggaran_count'   => $anggaranCount,
            'pengganti'        => $penggantiOptions,
        ]);
    }

    /**
     * Remove the specified kategori
     */
    public function destroy(Request $request, Kategori $kategori)
    {
        $action    = $request->input('action', '');
        $replaceId = $request->input('replace_kategori_id');

        $transaksiCount = $kategori->transaksi()->count();
        $anggaranCount  = $kategori->anggaran()->count();
        $hasDep         = $transaksiCount > 0 || $anggaranCount > 0;

        // Belum memilih tindakan tapi ada dependensi → kembalikan dengan data konflik
        if ($hasDep && !in_array($action, ['hapus_transaksi', 'ganti_kategori'])) {
            $pengganti = Kategori::where('id', '!=', $kategori->id)
                ->where('jenis', $kategori->jenis)
                ->whereNull('parent_id')
                ->orderBy('nama')
                ->get(['id', 'nama'])
                ->map(fn($k) => ['id' => $k->id, 'nama' => $k->nama])
                ->values()
                ->toArray();

            return back()
                ->with('konflik_id',        $kategori->id)
                ->with('konflik_nama',      $kategori->nama)
                ->with('konflik_trx',       $transaksiCount)
                ->with('konflik_ang',       $anggaranCount)
                ->with('konflik_jenis',     $kategori->jenis)
                ->with('konflik_url',       route('kategori.destroy', $kategori))
                ->with('konflik_pengganti', $pengganti);
        }

        DB::beginTransaction();
        try {
            if ($hasDep) {
                if ($action === 'hapus_transaksi') {
                    $kategori->transaksi()->delete();
                    $kategori->anggaran()->delete();
                } elseif ($action === 'ganti_kategori') {
                    if (!$replaceId) {
                        return back()->with('error', 'Pilih kategori pengganti terlebih dahulu.');
                    }
                    $kategori->transaksi()->update(['kategori_id' => $replaceId]);
                    $kategori->anggaran()->update(['kategori_id' => $replaceId]);
                }
            }

            $kategori->delete();
            DB::commit();

            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    /**
     * Search kategori (AJAX)
     */
    public function search(Request $request)
    {
        $query = Kategori::query();

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }

        $kategori = $query->orderBy('nama')->limit(10)->get();

        return response()->json($kategori);
    }
}
