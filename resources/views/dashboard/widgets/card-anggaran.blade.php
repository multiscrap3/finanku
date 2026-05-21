@php
    $anggaran       = $summary['anggaran_summary'] ?? [];
    $persenAnggaran = ($anggaran['total_anggaran'] ?? 0) > 0
        ? min(100, (($anggaran['total_terpakai'] ?? 0) / $anggaran['total_anggaran']) * 100)
        : 0;
@endphp
<a href="{{ route('anggaran.index') }}" class="text-decoration-none">
    <div class="card h-100 border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="d-flex align-items-center justify-content-center rounded-2"
                     style="width:40px;height:40px;background:rgba(249,115,22,.12);flex-shrink:0;">
                    <i class="bi bi-calculator text-warning fs-5"></i>
                </div>
                <span class="small text-muted fw-medium">Anggaran</span>
            </div>
            <h5 class="fw-bold text-dark mb-1">{{ number_format($persenAnggaran, 0) }}%</h5>
            <div class="progress mb-1" style="height:5px;">
                <div class="progress-bar {{ $persenAnggaran >= 90 ? 'bg-danger' : ($persenAnggaran >= 70 ? 'bg-warning' : 'bg-success') }}"
                     style="width:{{ $persenAnggaran }}%"></div>
            </div>
            <div class="text-muted" style="font-size:.72rem;">
                dari Rp {{ number_format($anggaran['total_anggaran'] ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </div>
</a>
