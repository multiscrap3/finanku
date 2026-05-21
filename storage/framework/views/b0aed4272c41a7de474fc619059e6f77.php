<div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3 px-4">
        <h6 class="fw-semibold mb-0">Transaksi Terbaru</h6>
        <a href="<?php echo e(route('transaksi.index')); ?>" class="small text-primary text-decoration-none">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <?php $__empty_1 = true; $__currentLoopData = $summary['transaksi_terbaru'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('transaksi.show', $t)); ?>"
               class="d-flex align-items-center gap-3 px-4 py-3 border-bottom text-decoration-none"
               style="transition:.15s;">
                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                     style="width:36px;height:36px;
                            background:<?php echo e($t->jenis === 'pemasukan' ? 'rgba(16,185,129,.12)' : 'rgba(239,68,68,.12)'); ?>">
                    <span class="fw-bold small <?php echo e($t->jenis === 'pemasukan' ? 'text-success' : 'text-danger'); ?>">
                        <?php echo e($t->jenis === 'pemasukan' ? '+' : '-'); ?>

                    </span>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <div class="small fw-medium text-dark text-truncate">
                        <?php echo e($t->keterangan ?: ($t->kategori?->nama ?? '-')); ?>

                    </div>
                    <div class="text-muted" style="font-size:.72rem;"><?php echo e($t->tanggal->translatedFormat('d M Y')); ?></div>
                </div>
                <div class="small fw-semibold <?php echo e($t->jenis === 'pemasukan' ? 'text-success' : 'text-danger'); ?> flex-shrink-0">
                    Rp <?php echo e(number_format($t->jumlah, 0, ',', '.')); ?>

                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-2 d-block mb-2 text-muted opacity-25"></i>
                <p class="text-muted small mb-2">Belum ada transaksi.</p>
                <a href="<?php echo e(route('transaksi.create')); ?>" class="small text-primary fw-medium text-decoration-none">+ Catat pertama</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\Finanku\resources\views/dashboard/widgets/transaksi-terbaru.blade.php ENDPATH**/ ?>