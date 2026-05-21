@extends('layouts.app')

@section('title', 'Laporan Bulanan')
@section('page-title', 'Laporan Bulanan')

@section('content')
<div class="row g-4">

    {{-- Filter --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="card-body p-4">
                <form method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-6 col-sm-auto">
                            <label class="form-label small fw-medium text-muted mb-1">Bulan</label>
                            <select name="bulan" class="form-select form-select-sm">
                                @foreach(range(1, 12) as $b)
                                    <option value="{{ $b }}" {{ (request('bulan', now()->month) == $b) ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $b, 1)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-sm-auto">
                            <label class="form-label small fw-medium text-muted mb-1">Tahun</label>
                            <select name="tahun" class="form-select form-select-sm">
                                @foreach(range(now()->year, now()->year - 3) as $y)
                                    <option value="{{ $y }}" {{ request('tahun', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-sm-auto">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Tampilkan</button>
                        </div>
                        <div class="col-6 col-sm-auto">
                            <a href="{{ route('laporan.export') }}?{{ request()->getQueryString() }}&format=excel"
                               class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-download me-1"></i>Export
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(isset($data))
        {{-- Summary cards --}}
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;border-top:3px solid #10b981;">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1">Total Pemasukan</div>
                    <div class="fw-bold fs-6 text-success">Rp {{ number_format($data['total_pemasukan'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;border-top:3px solid #ef4444;">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1">Total Pengeluaran</div>
                    <div class="fw-bold fs-6 text-danger">Rp {{ number_format($data['total_pengeluaran'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;border-top:3px solid #3b82f6;">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1">Cashflow</div>
                    <div class="fw-bold fs-6 text-primary">Rp {{ number_format(($data['cashflow'] ?? 0), 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;border-top:3px solid #8b5cf6;">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1">Saving Rate</div>
                    <div class="fw-bold fs-6" style="color:#7c3aed;">{{ number_format($data['saving_rate'] ?? 0, 1) }}%</div>
                </div>
            </div>
        </div>

        {{-- Chart & kategori --}}
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Pengeluaran per Kategori</h6>
                    @if(!empty($data['pengeluaran_per_kategori']))
                        <canvas id="chartKategori" height="200"></canvas>
                    @else
                        <p class="text-muted small text-center py-4">Belum ada data.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Rincian per Kategori</h6>
                    @forelse($data['pengeluaran_per_kategori'] ?? [] as $kat)
                        <div class="d-flex justify-content-between align-items-center small mb-2">
                            <span class="text-dark">{{ $kat['nama'] ?? '-' }}</span>
                            <span class="fw-medium text-danger">Rp {{ number_format($kat['total'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <p class="text-muted small">Belum ada pengeluaran.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Transaction list --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius:.75rem .75rem 0 0;">
                    <h6 class="fw-semibold mb-0">Semua Transaksi</h6>
                </div>
                <div class="card-body p-0">
                    @forelse($data['transaksi'] ?? [] as $t)
                        <a href="{{ route('transaksi.show', $t) }}"
                           class="d-flex align-items-center gap-3 px-4 py-3 border-bottom text-decoration-none">
                            <div class="flex-grow-1 small">
                                <div class="fw-medium text-dark">{{ $t->keterangan ?: '-' }}</div>
                                <div class="text-muted" style="font-size:.72rem;">
                                    {{ $t->tanggal->translatedFormat('d M Y') }} &bull; {{ $t->kategori?->nama ?? 'Tanpa kategori' }}
                                </div>
                            </div>
                            <div class="small fw-semibold {{ $t->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                {{ $t->jenis === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($t->jumlah, 0, ',', '.') }}
                            </div>
                        </a>
                    @empty
                        <div class="py-5 text-center text-muted small">Tidak ada transaksi pada periode ini.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body py-5 text-center text-muted small">
                    Pilih bulan dan tahun lalu klik Tampilkan.
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
@if(isset($data) && !empty($data['pengeluaran_per_kategori']))
<script>
    const ctx = document.getElementById('chartKategori');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(collect($data['pengeluaran_per_kategori'])->pluck('nama')) !!},
                datasets: [{
                    data: {!! json_encode(collect($data['pengeluaran_per_kategori'])->pluck('total')) !!},
                    backgroundColor: ['#3B82F6','#EF4444','#10B981','#F59E0B','#8B5CF6','#EC4899','#14B8A6','#F97316'],
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }
</script>
@endif
@endpush
