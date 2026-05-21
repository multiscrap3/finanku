@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan Keuangan')

@section('content')
<div class="row g-4">

    {{-- Quick links --}}
    <div class="col-12">
        <div class="row g-3">
            @foreach([
                ['route' => 'laporan.harian',   'label' => 'Harian',   'icon' => 'bi-calendar-day',   'color' => '#3b82f6'],
                ['route' => 'laporan.mingguan',  'label' => 'Mingguan', 'icon' => 'bi-calendar-week',  'color' => '#6366f1'],
                ['route' => 'laporan.bulanan',   'label' => 'Bulanan',  'icon' => 'bi-bar-chart-line',  'color' => '#8b5cf6'],
                ['route' => 'laporan.tahunan',   'label' => 'Tahunan',  'icon' => 'bi-pie-chart',       'color' => '#ec4899'],
            ] as $item)
                <div class="col-6 col-md-3">
                    <a href="{{ route($item['route']) }}"
                       class="card border-0 shadow-sm text-decoration-none h-100"
                       style="border-radius:.75rem;transition:.15s;">
                        <div class="card-body p-4 d-flex flex-column align-items-center gap-3 text-center">
                            <div class="d-flex align-items-center justify-content-center rounded-circle"
                                 style="width:52px;height:52px;background:{{ $item['color'] }}20;">
                                <i class="bi {{ $item['icon'] }} fs-4" style="color:{{ $item['color'] }};"></i>
                            </div>
                            <span class="fw-semibold small text-dark">{{ $item['label'] }}</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Info + Export --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0">Laporan Cepat — Bulan Ini</h6>
                    <a href="{{ route('laporan.bulanan') }}" class="small text-primary text-decoration-none">Lihat detail</a>
                </div>
                <p class="text-muted small mb-3">Pilih periode laporan di atas untuk melihat detail transaksi, grafik, dan perbandingan keuangan.</p>
                <button type="button" class="btn btn-outline-secondary btn-sm disabled" tabindex="-1" title="Tersedia di laporan periode">
                    <i class="bi bi-download me-1"></i>Export Excel
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
