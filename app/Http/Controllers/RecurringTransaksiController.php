<?php

namespace App\Http\Controllers;

use App\Models\RecurringTransaksi;
use App\Models\Kategori;
use App\Models\SumberTransaksi;
use Illuminate\Http\Request;

class RecurringTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = RecurringTransaksi::with(['kategori', 'sumberTransaksi'])
            ->where('household_id', auth()->user()->household_id);

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->is_active);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $recurring = $query->orderBy('tanggal_mulai', 'desc')->get();

        return view('recurring.index', compact('recurring'));
    }

    public function create()
    {
        $kategori = Kategori::orderBy('jenis')->orderBy('nama')->get();
        $sumberTransaksi = SumberTransaksi::orderBy('nama')
            ->where('household_id', auth()->user()->household_id)
            ->get();

        return view('recurring.create', compact('kategori', 'sumberTransaksi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis'               => 'required|in:pemasukan,pengeluaran',
            'keterangan'          => 'required|string|max:255',
            'kategori_id'         => 'required|exists:kategori,id',
            'sumber_transaksi_id' => 'required|exists:sumber_transaksi,id',
            'jumlah'              => 'required|numeric|min:1',
            'frekuensi'           => 'required|in:harian,mingguan,bulanan,tahunan',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'nullable|date|after:tanggal_mulai',
        ]);

        try {
            RecurringTransaksi::create([
                'household_id'        => auth()->user()->household_id,
                'user_id'             => auth()->id(),
                'jenis'               => $request->jenis,
                'keterangan'          => $request->keterangan,
                'kategori_id'         => $request->kategori_id,
                'sumber_transaksi_id' => $request->sumber_transaksi_id,
                'jumlah'              => $request->jumlah,
                'frekuensi'           => $request->frekuensi,
                'tanggal_mulai'       => $request->tanggal_mulai,
                'tanggal_selesai'     => $request->tanggal_selesai,
                'next_run'            => $request->tanggal_mulai,
                'is_active'           => true,
            ]);

            return redirect()
                ->route('recurring.index')
                ->with('success', 'Transaksi rutin berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan: ' . $e->getMessage());
        }
    }

    public function show(RecurringTransaksi $recurring)
    {
        $recurring->load(['kategori', 'sumberTransaksi', 'transaksi']);

        return view('recurring.show', compact('recurring'));
    }

    public function edit(RecurringTransaksi $recurring)
    {
        $kategori = Kategori::orderBy('jenis')->orderBy('nama')->get();
        $sumberTransaksi = SumberTransaksi::orderBy('nama')
            ->where('household_id', auth()->user()->household_id)
            ->get();

        return view('recurring.edit', compact('recurring', 'kategori', 'sumberTransaksi'));
    }

    public function update(Request $request, RecurringTransaksi $recurring)
    {
        $request->validate([
            'keterangan'          => 'sometimes|string|max:255',
            'kategori_id'         => 'sometimes|exists:kategori,id',
            'sumber_transaksi_id' => 'sometimes|exists:sumber_transaksi,id',
            'jumlah'              => 'sometimes|numeric|min:1',
            'frekuensi'           => 'sometimes|in:harian,mingguan,bulanan,tahunan',
            'tanggal_selesai'     => 'nullable|date',
        ]);

        try {
            $recurring->update($request->only([
                'keterangan',
                'kategori_id',
                'sumber_transaksi_id',
                'jumlah',
                'frekuensi',
                'tanggal_selesai',
            ]));

            return redirect()
                ->route('recurring.index')
                ->with('success', 'Transaksi rutin berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    public function destroy(RecurringTransaksi $recurring)
    {
        try {
            $recurring->delete();

            return redirect()
                ->route('recurring.index')
                ->with('success', 'Transaksi rutin berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function toggle(RecurringTransaksi $recurring)
    {
        try {
            $recurring->update(['is_active' => !$recurring->is_active]);

            $status = $recurring->is_active ? 'diaktifkan' : 'dijeda';
            return back()->with('success', 'Transaksi rutin berhasil ' . $status);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}
