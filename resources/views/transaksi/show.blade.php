@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-8">

    {{-- Card utama --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">

            {{-- Header --}}
            <div class="d-flex align-items-start justify-content-between mb-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                         style="width:56px;height:56px;
                         background:{{ $transaksi->jenis === 'pemasukan' ? 'rgba(16,185,129,.15)' : ($transaksi->jenis === 'pengeluaran' ? 'rgba(239,68,68,.15)' : 'rgba(59,130,246,.15)') }};">
                        @if($transaksi->jenis === 'pemasukan')
                            <i class="bi bi-arrow-up-circle text-success fs-3"></i>
                        @elseif($transaksi->jenis === 'pengeluaran')
                            <i class="bi bi-arrow-down-circle text-danger fs-3"></i>
                        @else
                            <i class="bi bi-arrow-left-right text-primary fs-3"></i>
                        @endif
                    </div>
                    <div>
                        <div class="fw-semibold text-dark fs-6">
                            {{ $transaksi->keterangan ?: 'Transaksi #' . $transaksi->id }}
                        </div>
                        <div class="text-muted small">{{ $transaksi->tanggal->translatedFormat('l, d F Y') }}</div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold fs-4 {{ $transaksi->jenis === 'pemasukan' ? 'text-success' : ($transaksi->jenis === 'pengeluaran' ? 'text-danger' : 'text-primary') }}">
                        {{ $transaksi->jenis === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                    </div>
                    <span class="badge rounded-pill mt-1
                        {{ $transaksi->jenis === 'pemasukan' ? 'bg-success' : ($transaksi->jenis === 'pengeluaran' ? 'bg-danger' : 'bg-primary') }}">
                        {{ ucfirst($transaksi->jenis) }}
                    </span>
                </div>
            </div>

            {{-- Detail grid --}}
            <div class="row g-3 small">
                @if($transaksi->kategori)
                    <div class="col-6">
                        <div class="text-muted mb-1" style="font-size:.72rem;">Kategori</div>
                        <div class="fw-medium">{{ $transaksi->kategori->nama }}</div>
                    </div>
                @endif
                @if($transaksi->sumberTransaksi)
                    <div class="col-6">
                        <div class="text-muted mb-1" style="font-size:.72rem;">Sumber Dana</div>
                        <div class="fw-medium">{{ $transaksi->sumberTransaksi->nama }}</div>
                    </div>
                @endif
                <div class="col-6">
                    <div class="text-muted mb-1" style="font-size:.72rem;">Dicatat Oleh</div>
                    <div class="fw-medium">{{ $transaksi->user?->name ?? '-' }}</div>
                </div>
                <div class="col-6">
                    <div class="text-muted mb-1" style="font-size:.72rem;">Dicatat Pada</div>
                    <div class="fw-medium">{{ $transaksi->created_at->translatedFormat('d M Y H:i') }}</div>
                </div>
                @if($transaksi->catatan)
                    <div class="col-12">
                        <div class="text-muted mb-1" style="font-size:.72rem;">Catatan</div>
                        <div class="fw-medium">{{ $transaksi->catatan }}</div>
                    </div>
                @endif
                @if($transaksi->tags && count($transaksi->tags))
                    <div class="col-12">
                        <div class="text-muted mb-1" style="font-size:.72rem;">Tags</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($transaksi->tags as $tag)
                                <span class="badge bg-light text-dark border" style="font-size:.7rem;">
                                    {{ is_array($tag) ? $tag['nama'] : $tag }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Detail items dari OCR --}}
            @if(!empty($transaksi->ocr_items) && count($transaksi->ocr_items) > 0)
                <div class="mt-4 pt-4 border-top">
                    <p class="text-muted fw-medium mb-2" style="font-size:.78rem;">Detail Item (dari Struk)</p>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered" style="font-size:.78rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center" style="width:50px;">Qty</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi->ocr_items as $item)
                                    <tr>
                                        <td>{{ $item['nama_item'] ?? '-' }}</td>
                                        <td class="text-center text-muted">{{ $item['qty'] ?? '-' }}</td>
                                        <td class="text-end text-muted">
                                            {{ isset($item['harga_satuan']) ? 'Rp ' . number_format($item['harga_satuan'], 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-end fw-medium">
                                            {{ isset($item['subtotal']) ? 'Rp ' . number_format($item['subtotal'], 0, ',', '.') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-semibold">Total</td>
                                    <td class="text-end fw-bold">Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Bukti foto --}}
            @if($transaksi->bukti_transaksi)
                <div class="mt-4 pt-4 border-top">
                    <p class="text-muted fw-medium mb-2" style="font-size:.78rem;">Bukti Transaksi</p>
                    <a href="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" target="_blank">
                        <img src="{{ asset('storage/' . $transaksi->bukti_transaksi) }}" alt="Bukti Transaksi"
                             class="rounded border" style="max-height:192px;object-fit:cover;">
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Aksi --}}
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('transaksi.edit', $transaksi) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>

        <form method="POST" action="{{ route('transaksi.destroy', $transaksi) }}"
              onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash me-1"></i>Hapus
            </button>
        </form>

        <a href="{{ route('transaksi.index') }}" class="btn btn-link btn-sm text-muted text-decoration-none ms-auto">
            &larr; Kembali
        </a>
    </div>

    {{-- Audit log --}}
    @if($auditLogs->isNotEmpty())
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius:.75rem .75rem 0 0;">
                <h6 class="fw-semibold mb-0">Riwayat Perubahan</h6>
            </div>
            <div class="card-body p-0">
                @foreach($auditLogs as $log)
                    <div class="d-flex align-items-start justify-content-between gap-3 px-4 py-3 border-bottom small">
                        <div>
                            <span class="fw-medium">{{ $log->user?->name }}</span>
                            <span class="text-muted ms-1">{{ $log->action }}</span>
                            @if($log->description)
                                <div class="text-muted mt-1" style="font-size:.7rem;">{{ $log->description }}</div>
                            @endif
                        </div>
                        <span class="text-muted flex-shrink-0" style="font-size:.7rem;">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
</div>
@endsection
