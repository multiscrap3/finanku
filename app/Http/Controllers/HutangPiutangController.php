<?php

namespace App\Http\Controllers;

use App\Models\HutangPiutang;
use App\Models\SumberTransaksi;
use App\Services\HutangPiutangService;
use Illuminate\Http\Request;

class HutangPiutangController extends Controller
{
    protected $hutangPiutangService;

    public function __construct(HutangPiutangService $hutangPiutangService)
    {
        $this->hutangPiutangService = $hutangPiutangService;
    }

    /**
     * Display a listing of hutang/piutang
     */
    public function index(Request $request)
    {
        $hutangPiutang = $this->hutangPiutangService->getHutangPiutang([
            'jenis' => $request->jenis,
            'status' => $request->status,
            'search' => $request->search,
        ]);

        $summary = $this->hutangPiutangService->getSummary();

        return view('hutang-piutang.index', compact('hutangPiutang', 'summary'));
    }

    /**
     * Show the form for creating a new hutang/piutang
     */
    public function create()
    {
        return view('hutang-piutang.create');
    }

    /**
     * Store a newly created hutang/piutang
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis'             => 'required|in:hutang,piutang',
            'nama_pihak'        => 'required|string|max:255',
            'jumlah'            => 'required|numeric|min:1',
            'tanggal'           => 'nullable|date',
            'jatuh_tempo'       => 'nullable|date|after:tanggal',
            'keterangan'        => 'nullable|string|max:500',
            'tipe_pembayaran'   => 'required|in:sekali,cicilan',
            'jumlah_cicilan'    => 'required_if:tipe_pembayaran,cicilan|nullable|numeric|min:1',
            'frekuensi_cicilan' => 'required_if:tipe_pembayaran,cicilan|nullable|in:mingguan,bulanan,tahunan',
        ]);

        try {
            $hutangPiutang = $this->hutangPiutangService->create($request->all());

            return redirect()
                ->route('hutang-piutang.show', $hutangPiutang)
                ->with('success', ucfirst($request->jenis) . ' berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified hutang/piutang
     */
    public function show(HutangPiutang $hutangPiutang)
    {
        $riwayat = $this->hutangPiutangService->getRiwayat($hutangPiutang);
        $sumberTransaksi = SumberTransaksi::orderBy('nama')->get();

        return view('hutang-piutang.show', compact('hutangPiutang', 'riwayat', 'sumberTransaksi'));
    }

    /**
     * Show the form for editing the specified hutang/piutang
     */
    public function edit(HutangPiutang $hutangPiutang)
    {
        return view('hutang-piutang.edit', compact('hutangPiutang'));
    }

    /**
     * Update the specified hutang/piutang
     */
    public function update(Request $request, HutangPiutang $hutangPiutang)
    {
        $request->validate([
            'nama_pihak' => 'sometimes|string|max:255',
            'jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $this->hutangPiutangService->update($hutangPiutang, $request->all());

            return redirect()
                ->route('hutang-piutang.show', $hutangPiutang)
                ->with('success', ucfirst($hutangPiutang->jenis) . ' berhasil diperbarui');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified hutang/piutang
     */
    public function destroy(HutangPiutang $hutangPiutang)
    {
        try {
            $jenis = $hutangPiutang->jenis;
            $this->hutangPiutangService->delete($hutangPiutang);

            return redirect()
                ->route('hutang-piutang.index')
                ->with('success', ucfirst($jenis) . ' berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * Bayar hutang/piutang
     */
    public function bayar(Request $request, HutangPiutang $hutangPiutang)
    {
        $request->validate([
            'sumber_transaksi_id' => 'required|exists:sumber_transaksi,id',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $this->hutangPiutangService->bayar($hutangPiutang, $request->all());

            $message = $hutangPiutang->jenis === 'hutang' 
                ? 'Pembayaran hutang berhasil dicatat' 
                : 'Penerimaan piutang berhasil dicatat';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Get summary (AJAX)
     */
    public function summary()
    {
        $summary = $this->hutangPiutangService->getSummary();
        return response()->json($summary);
    }
}
