@extends('layouts.app')

@section('title', __('laporan.yearly'))
@section('page-title', __('laporan.yearly'))

@section('content')
<div class="row g-4">

    {{-- Filter --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="card-body p-4">
                <form method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-6 col-sm-auto">
                            <label class="form-label small fw-medium text-muted mb-1">{{ __('laporan.year') }}</label>
                            <select name="tahun" class="form-select form-select-sm">
                                @foreach(range(now()->year, now()->year - 5) as $y)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-sm-auto">
                            <button type="submit" class="btn btn-primary btn-sm w-100">{{ __('messages.apply') }}</button>
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
                    <div class="small text-muted mb-1">{{ __('laporan.total') }} {{ __('laporan.income') }}</div>
                    <div class="fw-bold fs-6 text-success">Rp {{ number_format($data['total_pemasukan'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;border-top:3px solid #ef4444;">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1">{{ __('laporan.total') }} {{ __('laporan.expense') }}</div>
                    <div class="fw-bold fs-6 text-danger">Rp {{ number_format($data['total_pengeluaran'] ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;border-top:3px solid #3b82f6;">
                <div class="card-body p-3">
                    <div class="small text-muted mb-1">{{ __('laporan.balance') }}</div>
                    <div class="fw-bold fs-6 text-primary">Rp {{ number_format($data['cashflow'] ?? 0, 0, ',', '.') }}</div>
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

        {{-- Chart tren bulanan --}}
        @if(!empty($data['per_bulan']))
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-4">{{ __('laporan.trend') }} {{ __('laporan.by_category') }}</h6>
                        <canvas id="chartBulanan" height="100"></canvas>
                    </div>
                </div>
            </div>

            {{-- Ringkasan per bulan --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                    <div class="card-header bg-white border-bottom py-3 px-4" style="border-radius:.75rem .75rem 0 0;">
                        <h6 class="fw-semibold mb-0">{{ __('laporan.summary') }}</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach($data['per_bulan'] as $b => $row)
                            <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                                <div class="small fw-medium text-dark" style="min-width:3rem;">
                                    {{ \Carbon\Carbon::create(null, $b, 1)->translatedFormat('MMM') }}
                                </div>
                                <div class="flex-grow-1 small">
                                    <span class="text-success me-3">+Rp {{ number_format($row['pemasukan'] ?? 0, 0, ',', '.') }}</span>
                                    <span class="text-danger">-Rp {{ number_format($row['pengeluaran'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="small fw-semibold {{ ($row['cashflow'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ ($row['cashflow'] ?? 0) >= 0 ? '+' : '' }}Rp {{ number_format($row['cashflow'] ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>
@endsection

@push('scripts')
@if(isset($data) && !empty($data['per_bulan']))
<script>
    new Chart(document.getElementById('chartBulanan'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($data['per_bulan'])->keys()->map(fn($b) => \Carbon\Carbon::create(null, (int)$b, 1)->translatedFormat('MMM'))->values()) !!},
            datasets: [
                {
                    label: '{{ __('laporan.income') }}',
                    data: {!! json_encode(collect($data['per_bulan'])->pluck('pemasukan')->values()) !!},
                    backgroundColor: '#10B981',
                    borderRadius: 4,
                },
                {
                    label: '{{ __('laporan.expense') }}',
                    data: {!! json_encode(collect($data['per_bulan'])->pluck('pengeluaran')->values()) !!},
                    backgroundColor: '#EF4444',
                    borderRadius: 4,
                }
            ]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
    });
</script>
@endif
@endpush
