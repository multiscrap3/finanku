<div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3 px-4">
        <h6 class="fw-semibold mb-0">Saldo Rekening</h6>
        <a href="{{ route('sumber-transaksi.index') }}" class="small text-primary text-decoration-none">Kelola</a>
    </div>
    <div class="card-body p-4">
        @if(!empty($saldoPerSumber['labels']))
            <div class="d-flex flex-column gap-3">
                @foreach($saldoPerSumber['labels'] as $i => $nama)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-dark text-truncate" style="max-width:150px;">{{ $nama }}</span>
                        <span class="small fw-semibold">Rp {{ number_format($saldoPerSumber['values'][$i] ?? 0, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-credit-card fs-2 d-block mb-2 text-muted opacity-25"></i>
                <p class="text-muted small mb-2">Belum ada rekening.</p>
                <a href="{{ route('sumber-transaksi.index') }}" class="small text-primary text-decoration-none">+ Tambah rekening</a>
            </div>
        @endif
    </div>
</div>
