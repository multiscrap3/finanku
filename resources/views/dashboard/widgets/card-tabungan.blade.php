@php $tab = $summary['tabungan_summary'] ?? []; @endphp
<a href="{{ route('tabungan.index') }}" class="text-decoration-none">
    <div class="card h-100 border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="d-flex align-items-center justify-content-center rounded-2"
                     style="width:40px;height:40px;background:rgba(59,130,246,.12);flex-shrink:0;">
                    <i class="bi bi-wallet2 text-primary fs-5"></i>
                </div>
                <span class="small text-muted fw-medium">Tabungan</span>
            </div>
            <h5 class="fw-bold text-dark mb-1">Rp {{ number_format($tab['total_terkumpul'] ?? 0, 0, ',', '.') }}</h5>
            <div class="text-muted" style="font-size:.72rem;">{{ $tab['total_tabungan'] ?? 0 }} tujuan aktif</div>
        </div>
    </div>
</a>
