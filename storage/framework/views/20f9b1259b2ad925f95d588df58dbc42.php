<div class="row g-3">
    <div class="col-6 col-md-3">
        <a href="<?php echo e(route('transaksi.create')); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                         style="width:40px;height:40px;background:rgba(91,207,197,.15);">
                        <i class="bi bi-plus-circle text-primary fs-5"></i>
                    </div>
                    <span class="small fw-medium text-dark">Catat Transaksi</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?php echo e(route('laporan.bulanan')); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                         style="width:40px;height:40px;background:rgba(16,185,129,.12);">
                        <i class="bi bi-file-bar-graph text-success fs-5"></i>
                    </div>
                    <span class="small fw-medium text-dark">Laporan Bulanan</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?php echo e(route('import-bank.web.index')); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                         style="width:40px;height:40px;background:rgba(139,92,246,.12);">
                        <i class="bi bi-upload fs-5" style="color:#8b5cf6;"></i>
                    </div>
                    <span class="small fw-medium text-dark">Import Bank</span>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="<?php echo e(route('anggaran.create')); ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0"
                         style="width:40px;height:40px;background:rgba(249,115,22,.12);">
                        <i class="bi bi-calculator text-warning fs-5"></i>
                    </div>
                    <span class="small fw-medium text-dark">Atur Anggaran</span>
                </div>
            </div>
        </a>
    </div>
</div>
<?php /**PATH C:\laragon\www\Finanku\resources\views/dashboard/widgets/quick-actions.blade.php ENDPATH**/ ?>