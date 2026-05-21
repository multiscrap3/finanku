<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransaksiRequest;
use App\Http\Requests\UpdateTransaksiRequest;
use App\Models\Transaksi;
use App\Models\Kategori;
use App\Models\SumberTransaksi;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;
use App\Services\AnomalyDetectionService;
use App\Services\DedupService;
use App\Services\TokoPolaService;
use App\Services\TransaksiService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    protected $transaksiService;

    public function __construct(
        TransaksiService $transaksiService,
        private readonly DedupService $dedupService,
        private readonly AnomalyDetectionService $anomalyDetectionService,
        private readonly TokoPolaService $tokoPolaService,
    ) {
        $this->transaksiService = $transaksiService;
    }

    /**
     * Display a listing of transaksi
     */
    public function index(Request $request)
    {
        $filters = [
            'jenis' => $request->jenis,
            'kategori_id' => $request->kategori_id,
            'sumber_transaksi_id' => $request->sumber_transaksi_id,
            'tanggal_dari' => $request->tanggal_dari,
            'tanggal_sampai' => $request->tanggal_sampai,
            'tags' => $request->tags,
            'search' => $request->search,
            'per_page' => $request->per_page ?? 20,
        ];

        $transaksi = $this->transaksiService->getTransaksi($filters);
        $summary = $this->transaksiService->getSummary($filters);

        // Data untuk filter
        $kategori = Kategori::orderBy('nama')->get();
        $sumberTransaksi = SumberTransaksi::orderBy('nama')->get();
        $tags = Tag::orderBy('nama')->get();

        return view('transaksi.index', compact(
            'transaksi',
            'summary',
            'kategori',
            'sumberTransaksi',
            'tags',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new transaksi
     */
    public function create()
    {
        $kategori = Kategori::with('children')->whereNull('parent_id')->orderBy('nama')->get();
        $sumberTransaksi = SumberTransaksi::orderBy('nama')->get();
        $tags = Tag::orderBy('nama')->get();

        return view('transaksi.create', compact('kategori', 'sumberTransaksi', 'tags'));
    }

    /**
     * Store a newly created transaksi
     */
    public function store(StoreTransaksiRequest $request)
    {
        try {
            $data = $request->validated();

            // Validasi saldo cukup untuk pengeluaran/transfer
            if (in_array($data['jenis'], ['pengeluaran', 'transfer'])) {
                $sumber = SumberTransaksi::find($data['sumber_transaksi_id']);
                if ($sumber && $sumber->saldo_saat_ini < $data['jumlah']) {
                    return back()->withInput()->with('error',
                        'Saldo tidak cukup! Saldo tersedia: Rp ' . number_format($sumber->saldo_saat_ini, 0, ',', '.') .
                        ', dibutuhkan: Rp ' . number_format($data['jumlah'], 0, ',', '.')
                    );
                }
            }

            // Upload bukti jika ada
            if ($request->hasFile('bukti_transaksi')) {
                $data['bukti_transaksi'] = $this->transaksiService->uploadBukti($request->file('bukti_transaksi'));
            } elseif (!empty($data['ocr_image_path']) && Storage::disk('public')->exists($data['ocr_image_path'])) {
                // Gunakan gambar OCR sebagai bukti transaksi
                $newPath = 'transaksi/bukti/' . basename($data['ocr_image_path']);
                Storage::disk('public')->copy($data['ocr_image_path'], $newPath);
                $data['bukti_transaksi'] = $newPath;
            }
            unset($data['ocr_image_path']);

            $data['household_id'] = $data['household_id'] ?? $request->user()->household_id;
            $duplicates = $this->dedupService->findDuplicates($data);
            $anomaly = $this->anomalyDetectionService->detect($data, false);

            $transaksi = $this->transaksiService->create($data);

            $redirect = redirect()
                ->route('transaksi.show', $transaksi)
                ->with('success', 'Transaksi berhasil ditambahkan!');

            if ($duplicates->isNotEmpty()) {
                $redirect->with('warning_duplicate', [
                    'message' => 'Transaksi mirip ditemukan. Periksa kembali untuk menghindari duplikasi.',
                    'candidates' => $duplicates->take(5)->values()->all(),
                ]);
            }

            if ($anomaly['is_anomaly']) {
                $redirect->with('warning_anomaly', $anomaly);
            }

            return $redirect;
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified transaksi
     */
    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['kategori', 'sumberTransaksi', 'user', 'tags', 'transferKe']);

        // Get audit logs
        $auditLogs = $transaksi->auditLogs()->with('user')->latest()->get();

        return view('transaksi.show', compact('transaksi', 'auditLogs'));
    }

    /**
     * Show the form for editing the specified transaksi
     */
    public function edit(Transaksi $transaksi)
    {
        $kategori = Kategori::with('children')->whereNull('parent_id')->orderBy('nama')->get();
        $sumberTransaksi = SumberTransaksi::orderBy('nama')->get();
        $tags = Tag::orderBy('nama')->get();

        return view('transaksi.edit', compact('transaksi', 'kategori', 'sumberTransaksi', 'tags'));
    }

    /**
     * Update the specified transaksi
     */
    public function update(UpdateTransaksiRequest $request, Transaksi $transaksi)
    {
        try {
            $data = $request->validated();

            // Upload bukti baru jika ada
            if ($request->hasFile('bukti_transaksi')) {
                $data['bukti_transaksi'] = $this->transaksiService->uploadBukti($request->file('bukti_transaksi'));
            }

            $data['household_id'] = $data['household_id'] ?? $request->user()->household_id;
            $data['exclude_id'] = $transaksi->id;
            $duplicates = $this->dedupService->findDuplicates($data);
            $anomaly = $this->anomalyDetectionService->detect($data, false);

            unset($data['exclude_id']);

            $transaksi = $this->transaksiService->update($transaksi, $data);

            $redirect = redirect()
                ->route('transaksi.show', $transaksi)
                ->with('success', 'Transaksi berhasil diupdate!');

            if ($duplicates->isNotEmpty()) {
                $redirect->with('warning_duplicate', [
                    'message' => 'Transaksi mirip ditemukan. Periksa kembali untuk menghindari duplikasi.',
                    'candidates' => $duplicates->take(5)->values()->all(),
                ]);
            }

            if ($anomaly['is_anomaly']) {
                $redirect->with('warning_anomaly', $anomaly);
            }

            return $redirect;
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified transaksi
     */
    public function destroy(Transaksi $transaksi)
    {
        try {
            $this->transaksiService->delete($transaksi);

            return redirect()
                ->route('transaksi.index')
                ->with('success', 'Transaksi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Restore soft deleted transaksi
     */
    public function restore($id)
    {
        try {
            $transaksi = Transaksi::withTrashed()->findOrFail($id);
            $transaksi->restore();

            return redirect()
                ->route('transaksi.show', $transaksi)
                ->with('success', 'Transaksi berhasil dipulihkan!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Gagal memulihkan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Get transaksi summary (AJAX)
     */
    public function summary(Request $request)
    {
        $filters = [
            'tanggal_dari' => $request->tanggal_dari,
            'tanggal_sampai' => $request->tanggal_sampai,
        ];

        $summary = $this->transaksiService->getSummary($filters);

        return response()->json($summary);
    }

    /**
     * Export transaksi to Excel/PDF
     */
    public function export(Request $request)
    {
        // TODO: Implement export functionality
        // Will be implemented with ExportService
    }

    /**
     * Suggest keterangan / data transaksi dari histori (AJAX)
     */
    public function suggest(Request $request)
    {
        $query       = $request->input('q', '');
        $householdId = $request->user()->household_id;

        if (strlen($query) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $suggestions = $this->tokoPolaService->getSuggestions($query, $householdId);

        return response()->json(['success' => true, 'data' => $suggestions]);
    }
}
