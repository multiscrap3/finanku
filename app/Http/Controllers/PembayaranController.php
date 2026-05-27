<?php

namespace App\Http\Controllers;

use App\Models\HutangPiutangPembayaran;
use App\Models\SumberTransaksi;
use App\Services\HutangPiutangService;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function __construct(private HutangPiutangService $service) {}

    public function edit(HutangPiutangPembayaran $pembayaran)
    {
        $sumberTransaksi = SumberTransaksi::orderBy('nama')->get();

        return view('hutang-piutang.pembayaran.edit', compact('pembayaran', 'sumberTransaksi'));
    }

    public function update(Request $request, HutangPiutangPembayaran $pembayaran)
    {
        $request->validate([
            'sumber_transaksi_id' => 'required|exists:sumber_transaksi,id',
            'jumlah'              => 'required|numeric|min:1',
            'tanggal'             => 'required|date',
            'keterangan'          => 'nullable|string|max:500',
        ]);

        try {
            $this->service->editPembayaran($pembayaran, $request->all());

            return redirect()
                ->route('hutang-piutang.show', $pembayaran->hutang_piutang_id)
                ->with('success', __('hutang.payment_updated'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(HutangPiutangPembayaran $pembayaran)
    {
        $hutangPiutangId = $pembayaran->hutang_piutang_id;

        try {
            $this->service->hapusPembayaran($pembayaran);

            return redirect()
                ->route('hutang-piutang.show', $hutangPiutangId)
                ->with('success', __('hutang.payment_deleted'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
