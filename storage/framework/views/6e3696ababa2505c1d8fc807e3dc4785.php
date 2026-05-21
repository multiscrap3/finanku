<div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center py-3 px-4">
        <h6 class="fw-semibold mb-0">Saldo Rekening</h6>
        <a href="<?php echo e(route('sumber-transaksi.index')); ?>" class="small text-primary text-decoration-none">Kelola</a>
    </div>
    <div class="card-body p-4">
        <?php if(!empty($saldoPerSumber['labels'])): ?>
            <div class="d-flex flex-column gap-3">
                <?php $__currentLoopData = $saldoPerSumber['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $nama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-dark text-truncate" style="max-width:150px;"><?php echo e($nama); ?></span>
                        <span class="small fw-semibold">Rp <?php echo e(number_format($saldoPerSumber['values'][$i] ?? 0, 0, ',', '.')); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="bi bi-credit-card fs-2 d-block mb-2 text-muted opacity-25"></i>
                <p class="text-muted small mb-2">Belum ada rekening.</p>
                <a href="<?php echo e(route('sumber-transaksi.index')); ?>" class="small text-primary text-decoration-none">+ Tambah rekening</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\Finanku\resources\views/dashboard/widgets/saldo-rekening.blade.php ENDPATH**/ ?>