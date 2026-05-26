@extends('layouts.app')

@section('title', __('laporan.comparison'))
@section('page-title', __('laporan.comparison'))

@section('content')
<div class="row g-4">

    {{-- Filter --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="card-body p-4">
                <form method="GET" class="d-flex flex-wrap align-items-end gap-3">
                    <div>
                        <label class="form-label small fw-medium text-muted mb-1">{{ __('laporan.month') }} 1</label>
                        <div class="d-flex gap-2">
                            <select name="bulan1" class="form-select form-select-sm" style="min-width:120px;">
                                @foreach(range(1, 12) as $b)
                                    <option value="{{ $b }}" {{ $bulan1 == $b ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $b, 1)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="tahun1" class="form-select form-select-sm" style="min-width:80px;">
                                @foreach(range(now()->year, now()->year - 3) as $y)
                                    <option value="{{ $y }}" {{ $tahun1 == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-muted small pb-1">vs</div>
                    <div>
                        <label class="form-label small fw-medium text-muted mb-1">{{ __('laporan.month') }} 2</label>
                        <div class="d-flex gap-2">
                            <select name="bulan2" class="form-select form-select-sm" style="min-width:120px;">
                                @foreach(range(1, 12) as $b)
                                    <option value="{{ $b }}" {{ $bulan2 == $b ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $b, 1)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="tahun2" class="form-select form-select-sm" style="min-width:80px;">
                                @foreach(range(now()->year, now()->year - 3) as $y)
                                    <option value="{{ $y }}" {{ $tahun2 == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('laporan.comparison') }}</button>
                </form>
            </div>
        </div>
    </div>

    @if(isset($data) && !empty($data))
        @php
            $label1 = \Carbon\Carbon::create($tahun1, $bulan1, 1)->translatedFormat('F Y');
            $label2 = \Carbon\Carbon::create($tahun2, $bulan2, 1)->translatedFormat('F Y');
        @endphp

        {{-- Side-by-side comparison --}}
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">{{ $label1 }}</h6>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">{{ __('laporan.income') }}</span>
                        <span class="fw-medium text-success">Rp {{ number_format($data['bulan1']['total_pemasukan'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-3">
                        <span class="text-muted">{{ __('laporan.expense') }}</span>
                        <span class="fw-medium text-danger">Rp {{ number_format($data['bulan1']['total_pengeluaran'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small border-top pt-3">
                        <span class="fw-medium">{{ __('laporan.balance') }}</span>
                        <span class="fw-bold {{ ($data['bulan1']['cashflow'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($data['bulan1']['cashflow'] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">{{ $label2 }}</h6>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">{{ __('laporan.income') }}</span>
                        <span class="fw-medium text-success">Rp {{ number_format($data['bulan2']['total_pemasukan'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-3">
                        <span class="text-muted">{{ __('laporan.expense') }}</span>
                        <span class="fw-medium text-danger">Rp {{ number_format($data['bulan2']['total_pengeluaran'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small border-top pt-3">
                        <span class="fw-medium">{{ __('laporan.balance') }}</span>
                        <span class="fw-bold {{ ($data['bulan2']['cashflow'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                            Rp {{ number_format($data['bulan2']['cashflow'] ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Selisih summary --}}
        @php
            $selisihPengeluaran = ($data['bulan2']['total_pengeluaran'] ?? 0) - ($data['bulan1']['total_pengeluaran'] ?? 0);
            $selisihPemasukan   = ($data['bulan2']['total_pemasukan'] ?? 0)   - ($data['bulan1']['total_pemasukan'] ?? 0);
        @endphp
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-4">Perubahan ({{ $label2 }} vs {{ $label1 }})</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="small text-muted mb-1">{{ __('laporan.net') }} {{ __('laporan.expense') }}</div>
                            <div class="fs-5 fw-bold {{ $selisihPengeluaran <= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $selisihPengeluaran > 0 ? '+' : '' }}Rp {{ number_format($selisihPengeluaran, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="small text-muted mb-1">{{ __('laporan.net') }} {{ __('laporan.income') }}</div>
                            <div class="fs-5 fw-bold {{ $selisihPemasukan >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $selisihPemasukan > 0 ? '+' : '' }}Rp {{ number_format($selisihPemasukan, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body py-5 text-center text-muted small">
                    {{ __('laporan.no_data') }}
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
