<div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;">
    <div class="card-body p-4">
        <h6 class="fw-semibold text-dark mb-3">Pengeluaran Bulan Ini per Kategori</h6>
        <?php if(!empty($pengeluaranPerKategori['labels'])): ?>
            <canvas id="chartKategori" height="180"></canvas>
        <?php else: ?>
            <div class="d-flex align-items-center justify-content-center text-muted"
                 style="height:180px;font-size:.875rem;">
                <div class="text-center">
                    <i class="bi bi-pie-chart fs-2 d-block mb-2 opacity-25"></i>
                    Belum ada data pengeluaran.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\Finanku\resources\views/dashboard/widgets/chart-kategori.blade.php ENDPATH**/ ?>