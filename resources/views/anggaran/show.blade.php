@extends('layouts.app')

@section('title', __('anggaran.detail'))
@section('page-title', __('anggaran.detail'))

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-lg-8">

    <div class="card border-0 shadow-sm mb-4" style="border-radius:.75rem;">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex align-items-start justify-content-between mb-4">
                <div>
                    <h5 class="fw-bold mb-1">{{ $anggaran->kategori->nama }}</h5>
                    <p class="text-muted small mb-0">
                        {{ \Carbon\Carbon::createFromDate($anggaran->tahun, $anggaran->bulan, 1)->translatedFormat('F Y') }}
                    </p>
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('anggaran.edit', $anggaran) }}" class="small text-primary text-decoration-none">{{ __('messages.edit') }}</a>
                    <form method="POST" action="{{ route('anggaran.destroy', $anggaran) }}"
                          onsubmit="return confirm('{{ __('anggaran.delete_confirm') }}')" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link btn-sm text-danger p-0" style="font-size:.78rem;">{{ __('messages.delete') }}</button>
                    </form>
                </div>
            </div>

            @php $persen = $realisasi['persentase']; @endphp

            <div class="row g-3 mb-4 text-center">
                <div class="col-4">
                    <div class="bg-light rounded p-3">
                        <div class="text-muted mb-1" style="font-size:.72rem;">{{ __('anggaran.limit') }}</div>
                        <div class="fw-bold">Rp {{ number_format($realisasi['jumlah'], 0, ',', '.') }}</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-light rounded p-3">
                        <div class="text-muted mb-1" style="font-size:.72rem;">{{ __('anggaran.used') }}</div>
                        <div class="fw-bold {{ $realisasi['over_budget'] ? 'text-danger' : 'text-primary' }}">
                            Rp {{ number_format($realisasi['terpakai'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="bg-light rounded p-3">
                        <div class="text-muted mb-1" style="font-size:.72rem;">{{ __('anggaran.remaining') }}</div>
                        <div class="fw-bold {{ $realisasi['sisa'] < 0 ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format(abs($realisasi['sisa']), 0, ',', '.') }}
                            @if($realisasi['sisa'] < 0)<small>(lebih)</small>@endif
                        </div>
                    </div>
                </div>
            </div>

            @php
                $barClass = $persen >= 100 ? 'bg-danger' : ($persen >= 80 ? 'bg-warning' : 'bg-primary');
                $statusLabel = match($realisasi['status']) {
                    'aman'   => ['text' => 'Aman',           'badge' => 'bg-success'],
                    'hampir' => ['text' => 'Mendekati Limit','badge' => 'bg-warning text-dark'],
                    'lebih'  => ['text' => 'Over Budget',    'badge' => 'bg-danger'],
                    default  => ['text' => ucfirst($realisasi['status']), 'badge' => 'bg-secondary'],
                };
            @endphp

            <div class="progress mb-2" style="height:10px;">
                <div class="progress-bar {{ $barClass }}" role="progressbar"
                     style="width:{{ min(100, $persen) }}%"></div>
            </div>
            <div class="d-flex align-items-center justify-content-between small">
                <span class="{{ $realisasi['over_budget'] ? 'text-danger fw-medium' : 'text-muted' }}">
                    {{ number_format($persen, 1) }}% terpakai
                </span>
                <span class="badge {{ $statusLabel['badge'] }}">{{ $statusLabel['text'] }}</span>
            </div>
        </div>
    </div>

    <a href="{{ route('anggaran.index') }}" class="btn btn-link btn-sm text-muted text-decoration-none p-0">
        &larr; {{ __('messages.back') }}
    </a>

</div>
</div>
@endsection
