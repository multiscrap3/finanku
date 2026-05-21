<?php

namespace App\Http\Controllers;

use App\Models\Tabungan;
use App\Models\SumberTransaksi;
use App\Services\TabunganService;
use Illuminate\Http\Request;

class TabunganController extends Controller
{
    protected $tabunganService;

    public function __construct(TabunganService $tabunganService)
    {
        $this->tabunganService = $tabunganService;
    }

    /**
     * Display a listing of tabungan
     */
    public function index()
    {
        $tabungan = Tabungan::orderBy('created_at', 'desc')->get();
        $sumberTransaksi = SumberTransaksi::orderBy('nama')->get();

        return view('tabungan.index', compact('tabungan', 'sumberTransaksi'));
    }

    /**
     * Show the form for creating a new tabungan
     */
    public function create()
    {
        return view('tabungan.create');
    }

    /**
     * Store a newly created tabungan
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'target' => 'required|numeric|min:0',
            'tanggal_target' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $tabungan = $this->tabunganService->create($request->all());

            return redirect()
                ->route('tabungan.show', $tabungan)
                ->with('success', 'Tabungan berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified tabungan
     */
    public function show(Tabungan $tabungan)
    {
        $progress = $this->tabunganService->getProgress($tabungan);
        $riwayat = $this->tabunganService->getRiwayat($tabungan);
        $sumberTransaksi = SumberTransaksi::orderBy('nama')->get();

        return view('tabungan.show', compact('tabungan', 'progress', 'riwayat', 'sumberTransaksi'));
    }

    /**
     * Show the form for editing the specified tabungan
     */
    public function edit(Tabungan $tabungan)
    {
        return view('tabungan.edit', compact('tabungan'));
    }

    /**
     * Update the specified tabungan
     */
    public function update(Request $request, Tabungan $tabungan)
    {
        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'target' => 'sometimes|numeric|min:0',
            'tanggal_target' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $this->tabunganService->update($tabungan, $request->all());

            return redirect()
                ->route('tabungan.show', $tabungan)
                ->with('success', 'Tabungan berhasil diperbarui');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified tabungan
     */
    public function destroy(Tabungan $tabungan)
    {
        try {
            $this->tabunganService->delete($tabungan);

            return redirect()
                ->route('tabungan.index')
                ->with('success', 'Tabungan berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus tabungan: ' . $e->getMessage());
        }
    }

    /**
     * Setor ke tabungan
     */
    public function setor(Request $request, Tabungan $tabungan)
    {
        $request->validate([
            'sumber_transaksi_id' => 'required|exists:sumber_transaksi,id',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $this->tabunganService->setor($tabungan, $request->all());

            return back()->with('success', 'Berhasil setor ke tabungan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal setor: ' . $e->getMessage());
        }
    }

    /**
     * Tarik dari tabungan
     */
    public function tarik(Request $request, Tabungan $tabungan)
    {
        $request->validate([
            'sumber_transaksi_id' => 'required|exists:sumber_transaksi,id',
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $this->tabunganService->tarik($tabungan, $request->all());

            return back()->with('success', 'Berhasil tarik dari tabungan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal tarik: ' . $e->getMessage());
        }
    }
}
