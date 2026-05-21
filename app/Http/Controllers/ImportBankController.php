<?php

namespace App\Http\Controllers;

use App\Exceptions\ExcelPasswordRequiredException;
use App\Http\Requests\ImportBankRequest;
use App\Models\ImportBank;
use App\Services\BankImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class ImportBankController extends Controller
{
    public function __construct(
        private readonly BankImportService $bankImportService
    ) {
    }

    public function index(Request $request)
    {
        $imports = ImportBank::query()
            ->with(['user', 'sumberTransaksi'])
            ->where('household_id', $request->user()->household_id)
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $imports,
        ]);
    }

    public function store(ImportBankRequest $request)
    {
        $import = $this->bankImportService->import(
            user: $request->user(),
            file: $request->file('file'),
            sumberTransaksiId: (int) $request->validated('sumber_transaksi_id'),
            bankCode: $request->validated('bank_code') ?? 'generic',
            kategoriId: $request->validated('kategori_id') ? (int) $request->validated('kategori_id') : null,
            password: $request->validated('password')
        );

        return response()->json([
            'success' => $import->status !== 'failed',
            'message' => $import->status === 'completed'
                ? 'Import mutasi bank berhasil.'
                : 'Import mutasi bank selesai dengan error.',
            'data' => $import,
        ], $import->status === 'failed' ? 422 : 201);
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'file' => [
                'required', 'file', 'max:10240',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (! in_array($ext, ['csv', 'txt', 'xlsx'], true)) {
                        $fail('File harus berformat CSV, TXT, atau XLSX.');
                    }
                },
            ],
            'bank_code' => ['nullable', 'string', 'in:generic,bca,mandiri,bni,bsi'],
            'password' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $preview = $this->bankImportService->preview(
                file: $request->file('file'),
                bankCode: $validated['bank_code'] ?? 'generic',
                password: $validated['password'] ?? null
            );
        } catch (ExcelPasswordRequiredException $e) {
            return response()->json([
                'success' => false,
                'password_required' => true,
                'message' => $e->getMessage(),
            ], 422);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $preview,
        ]);
    }

    public function webIndex(Request $request)
    {
        $imports = ImportBank::query()
            ->with(['user', 'sumberTransaksi'])
            ->where('household_id', $request->user()->household_id)
            ->latest()
            ->paginate(20);

        return view('import-bank.index', compact('imports'));
    }

    public function webForm(Request $request)
    {
        $sumberTransaksi = \App\Models\SumberTransaksi::orderBy('nama')
            ->where('household_id', $request->user()->household_id)
            ->get();
        $kategori = \App\Models\Kategori::orderBy('jenis')->orderBy('nama')->get();

        return view('import-bank.form', compact('sumberTransaksi', 'kategori'));
    }

    public function downloadTemplate()
    {
        $path = public_path('templates/template_mutasi_bank.csv');

        return response()->download($path, 'template_mutasi_bank.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function show(Request $request, ImportBank $importBank)
    {
        abort_unless($importBank->household_id === $request->user()->household_id, 403);

        $importBank->load(['user', 'sumberTransaksi']);

        return response()->json([
            'success' => true,
            'data' => $importBank,
        ]);
    }

    /**
     * PDP: Hapus hanya file fisik dari storage, record import & transaksi tetap ada.
     */
    public function deleteFile(Request $request, ImportBank $importBank)
    {
        abort_unless($importBank->household_id === $request->user()->household_id, 403);

        if ($importBank->file_path) {
            Storage::delete($importBank->file_path);
            $importBank->update(['file_path' => null]);
        }

        return back()->with('success', 'File mutasi bank berhasil dihapus. Data transaksi tetap tersimpan.');
    }
}