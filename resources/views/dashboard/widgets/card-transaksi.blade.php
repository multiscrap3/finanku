<a href="{{ route('transaksi.index') }}" class="text-decoration-none">
    <div class="card h-100 border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="d-flex align-items-center justify-content-center rounded-2"
                     style="width:40px;height:40px;background:rgba(16,185,129,.12);flex-shrink:0;">
                    <i class="bi bi-receipt text-success fs-5"></i>
                </div>
                <span class="small text-muted fw-medium">Transaksi</span>
            </div>
            <h5 class="fw-bold mb-1">
                <a href="{{ route('transaksi.create') }}"
                   class="text-primary small fw-semibold text-decoration-none">
                    <i class="bi bi-plus-circle me-1"></i>Catat Transaksi
                </a>
            </h5>
            <div class="text-muted" style="font-size:.72rem;">{{ now()->translatedFormat('d F Y') }}</div>
        </div>
    </div>
</a>
