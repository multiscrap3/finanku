<?php

namespace App\Http\Controllers;

use App\Models\SumberTransaksi;
use Illuminate\Http\Request;

class SumberTransaksiController extends Controller
{
    /**
     * Display a listing of sumber transaksi
     */
    public function index()
    {
        $sumberTransaksi = SumberTransaksi::orderBy('nama')->get();

        // Calculate total saldo
        $totalSaldo = $sumberTransaksi->sum('saldo_saat_ini');

        return view('sumber-transaksi.index', compact('sumberTransaksi', 'totalSaldo'));
    }

    /**
     * Show the form for creating a new sumber transaksi
     */
    public function create()
    {
        return view('sumber-transaksi.create');
    }

    /**
     * Store a newly created sumber transaksi
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|in:bank,e-wallet,cash,kartu_kredit,investasi,lainnya',
            'saldo' => 'required|numeric|min:0',
            'nomor_rekening' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:7',
        ]);

        try {
            SumberTransaksi::create([
                'household_id'  => auth()->user()->household_id,
                'nama'          => $request->nama,
                'jenis'         => $request->jenis,
                'saldo_awal'    => $request->saldo,
                'saldo_saat_ini'=> $request->saldo,
                'nomor_rekening'=> $request->nomor_rekening,
                'icon'          => $request->icon,
                'warna'         => $request->warna ?? '#6c757d',
            ]);

            return redirect()
                ->route('sumber-transaksi.index')
                ->with('success', 'Sumber transaksi berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan sumber transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified sumber transaksi
     */
    public function edit(SumberTransaksi $sumberTransaksi)
    {
        return view('sumber-transaksi.edit', compact('sumberTransaksi'));
    }

    /**
     * Update the specified sumber transaksi
     */
    public function update(Request $request, SumberTransaksi $sumberTransaksi)
    {
        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'jenis' => 'sometimes|in:bank,e-wallet,cash,kartu_kredit,investasi,lainnya',
            'nomor_rekening' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:7',
        ]);

        try {
            // Tidak boleh update saldo langsung, harus melalui transaksi
            $sumberTransaksi->update($request->except(['saldo']));

            return redirect()
                ->route('sumber-transaksi.index')
                ->with('success', 'Sumber transaksi berhasil diperbarui');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui sumber transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified sumber transaksi
     */
    public function destroy(SumberTransaksi $sumberTransaksi)
    {
        try {
            // Cek apakah masih ada saldo
            if ($sumberTransaksi->saldo_saat_ini != 0) {
                return back()->with('error', 'Sumber transaksi tidak dapat dihapus karena masih memiliki saldo');
            }

            // Cek apakah masih digunakan
            if ($sumberTransaksi->transaksi()->exists()) {
                return back()->with('error', 'Sumber transaksi tidak dapat dihapus karena masih digunakan dalam transaksi');
            }

            $sumberTransaksi->delete();

            return redirect()
                ->route('sumber-transaksi.index')
                ->with('success', 'Sumber transaksi berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus sumber transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Get saldo sumber transaksi (AJAX)
     */
    public function getSaldo(Request $request)
    {
        $sumberId = $request->sumber_id;
        
        if (!$sumberId) {
            return response()->json(['error' => 'Sumber ID required'], 400);
        }

        $sumber = SumberTransaksi::find($sumberId);

        if (!$sumber) {
            return response()->json(['error' => 'Sumber not found'], 404);
        }

        return response()->json([
            'id'             => $sumber->id,
            'nama'           => $sumber->nama,
            'saldo'          => $sumber->saldo_saat_ini,
            'saldo_formatted'=> 'Rp ' . number_format($sumber->saldo_saat_ini, 0, ',', '.'),
        ]);
    }

    /**
     * Adjust saldo (untuk koreksi manual)
     */
    public function adjustSaldo(Request $request, SumberTransaksi $sumberTransaksi)
    {
        $request->validate([
            'saldo_baru' => 'required|numeric',
            'keterangan' => 'required|string|max:500',
        ]);

        try {
            $saldoLama = $sumberTransaksi->saldo_saat_ini;
            $saldoBaru = $request->saldo_baru;
            $selisih = $saldoBaru - $saldoLama;

            // Update saldo
            $sumberTransaksi->update(['saldo_saat_ini' => $saldoBaru]);

            // Log adjustment (bisa ditambahkan ke audit log)
            \App\Models\AuditLog::create([
                'household_id' => auth()->user()->household_id,
                'user_id' => auth()->id(),
                'action' => 'adjust_saldo',
                'model' => 'SumberTransaksi',
                'model_id' => $sumberTransaksi->id,
                'data' => json_encode([
                    'saldo_lama' => $saldoLama,
                    'saldo_baru' => $saldoBaru,
                    'selisih' => $selisih,
                    'keterangan' => $request->keterangan,
                ]),
            ]);

            return back()->with('success', 'Saldo berhasil disesuaikan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyesuaikan saldo: ' . $e->getMessage());
        }
    }
}
