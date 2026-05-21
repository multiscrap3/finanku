@extends('layouts.app')

@section('title', 'Laporan Harian')
@section('page-title', 'Laporan Harian')

@section('content')
<div class="row g-4">

    {{-- Filter --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="card-body p-4">
                <form method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-sm-auto">
                            <label class="form-label small fw-medium text-muted mb-1">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 col-sm-auto">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Tampilkan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(isset($data))
        {{-- Summary cards --}}
        <div class="col-4 col-sm-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;border-top:3px solid #10b981;">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1">Pemasukan</div>
                    <div class="fw-bold fs-6 text-success">Rp {{ number_format($data['total_pemasukan'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-4 col-sm-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;border-top:3px solid #ef4444;">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1">Pengeluaran</div>
                    <div class="fw-bold fs-6 text-danger">Rp {{ number_format($data['total_pengeluaran'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-4 col-sm-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;border-top:3px solid #3b82f6;">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1">Cashflow</div>
                    <div class="fw-bold fs-6 text-primary">Rp {{ number_format($data['cashflow'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- Transaction list --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius:.75rem .75rem 0 0;">
                    <h6 class="fw-semibold mb-0">
                        Transaksi {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($data['transaksi'] ?? [] as $t)
                        <a href="{{ route('transaksi.show', $t) }}"
                           class="d-flex align-items-center gap-3 px-4 py-3 border-bottom text-decoration-none">
                            <div class="flex-grow-1 small">
                                <div class="fw-medium text-dark">{{ $t->keterangan ?: '-' }}</div>
                                <div class="text-muted" style="font-size:.72rem;">
                                    {{ $t->kategori?->nama ?? 'Tanpa kategori' }} &bull; {{ $t->sumberTransaksi?->nama ?? '-' }}
                                </div>
                            </div>
                            <div class="small fw-semibold {{ $t->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                {{ $t->jenis === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                            </div>
                        </a>
                    @empty
                        <div class="py-5 text-center text-muted small">Tidak ada transaksi pada tanggal ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
