<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\Kategori;
use App\Services\AnggaranService;
use App\Http\Requests\StoreAnggaranRequest;
use App\Http\Requests\UpdateAnggaranRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnggaranController extends Controller
{
    protected $anggaranService;

    public function __construct(AnggaranService $anggaranService)
    {
        $this->anggaranService = $anggaranService;
    }

    /**
     * Display a listing of anggaran
     */
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $anggaran = $this->anggaranService->getAnggaran([
            'bulan' => $bulan,
            'tahun' => $tahun,
        ]);

        $summary = $this->anggaranService->getSummaryBulanan($bulan, $tahun);

        return view('anggaran.index', compact('anggaran', 'summary', 'bulan', 'tahun'));
    }

    /**
     * Show the form for creating a new anggaran
     */
    public function create()
    {
        $kategori = Kategori::where('jenis', 'pengeluaran')
            ->orderBy('nama')
            ->get();

        return view('anggaran.create', compact('kategori'));
    }

    /**
     * Store a newly created anggaran
     */
    public function store(StoreAnggaranRequest $request)
    {
        try {
            $anggaran = $this->anggaranService->create($request->validated());

            return redirect()
                ->route('anggaran.index', [
                    'bulan' => $anggaran->bulan,
                    'tahun' => $anggaran->tahun
                ])
                ->with('success', 'Anggaran berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan anggaran: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified anggaran
     */
    public function show(Anggaran $anggaran)
    {
        $anggaran->load('kategori');
        $realisasi = $this->anggaranService->getRealisasi($anggaran);

        return view('anggaran.show', compact('anggaran', 'realisasi'));
    }

    /**
     * Show the form for editing the specified anggaran
     */
    public function edit(Anggaran $anggaran)
    {
        $kategori = Kategori::where('jenis', 'pengeluaran')
            ->orderBy('nama')
            ->get();

        return view('anggaran.edit', compact('anggaran', 'kategori'));
    }

    /**
     * Update the specified anggaran
     */
    public function update(UpdateAnggaranRequest $request, Anggaran $anggaran)
    {
        try {
            $this->anggaranService->update($anggaran, $request->validated());

            return redirect()
                ->route('anggaran.index', [
                    'bulan' => $anggaran->bulan,
                    'tahun' => $anggaran->tahun
                ])
                ->with('success', 'Anggaran berhasil diperbarui');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui anggaran: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified anggaran
     */
    public function destroy(Anggaran $anggaran)
    {
        try {
            $bulan = $anggaran->bulan;
            $tahun = $anggaran->tahun;
            
            $this->anggaranService->delete($anggaran);

            return redirect()
                ->route('anggaran.index', ['bulan' => $bulan, 'tahun' => $tahun])
                ->with('success', 'Anggaran berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus anggaran: ' . $e->getMessage());
        }
    }

    /**
     * Get summary anggaran (AJAX)
     */
    public function summary(Request $request)
    {
        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $summary = $this->anggaranService->getSummaryBulanan($bulan, $tahun);

        return response()->json($summary);
    }

    /**
     * Copy anggaran dari bulan sebelumnya
     */
    public function copyFromPrevious(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
        ]);

        try {
            $copied = $this->anggaranService->copyFromPreviousMonth(
                $request->bulan,
                $request->tahun
            );

            if ($copied > 0) {
                return redirect()
                    ->route('anggaran.index', [
                        'bulan' => $request->bulan,
                        'tahun' => $request->tahun
                    ])
                    ->with('success', "Berhasil menyalin {$copied} anggaran dari bulan sebelumnya");
            } else {
                return back()->with('info', 'Tidak ada anggaran baru yang disalin');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyalin anggaran: ' . $e->getMessage());
        }
    }
}
