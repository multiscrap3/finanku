<?php $__env->startSection('title', __('recurring.title')); ?>
<?php $__env->startSection('page-title', __('recurring.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4">

    <div class="col-12 d-flex justify-content-end">
        <a href="<?php echo e(route('recurring.create')); ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i><?php echo e(__('recurring.add')); ?>

        </a>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $recurring ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                             style="width:40px;height:40px;
                             background:<?php echo e($item->jenis === 'pemasukan' ? 'rgba(16,185,129,.12)' : 'rgba(239,68,68,.12)'); ?>;">
                            <i class="bi bi-arrow-repeat <?php echo e($item->jenis === 'pemasukan' ? 'text-success' : 'text-danger'); ?> fs-5"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="small fw-medium text-dark text-truncate"><?php echo e($item->keterangan); ?></div>
                            <div class="text-muted d-flex align-items-center gap-1 flex-wrap" style="font-size:.72rem;">
                                <span class="text-capitalize"><?php echo e($item->frekuensi); ?></span>
                                <span>&bull;</span>
                                <span><?php echo e(__('recurring.start_date')); ?> <?php echo e($item->tanggal_mulai->translatedFormat('d M Y')); ?></span>
                                <?php if($item->sumberTransaksi): ?>
                                    <span>&bull;</span>
                                    <span><?php echo e($item->sumberTransaksi->nama); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="small fw-bold <?php echo e($item->jenis === 'pemasukan' ? 'text-success' : 'text-danger'); ?>">
                                Rp <?php echo e(number_format($item->jumlah, 0, ',', '.')); ?>

                            </div>
                            <div class="d-flex align-items-center gap-2 mt-1 justify-content-end" style="font-size:.72rem;">
                                <span class="badge rounded-pill <?php echo e($item->is_active ? 'bg-success' : 'bg-secondary'); ?>"
                                      style="font-size:.6rem;"><?php echo e($item->is_active ? __('recurring.active') : __('recurring.inactive')); ?></span>
                                <form method="POST" action="<?php echo e(route('recurring.toggle', $item)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-link btn-sm p-0 text-primary" style="font-size:.72rem;">
                                        <?php echo e($item->is_active ? __('recurring.inactive') : __('recurring.active')); ?>

                                    </button>
                                </form>
                                <a href="<?php echo e(route('recurring.edit', $item)); ?>" class="text-muted text-decoration-none"><?php echo e(__('messages.edit')); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="py-5 text-center">
                        <i class="bi bi-arrow-repeat fs-1 d-block mb-2 text-muted opacity-25"></i>
                        <p class="text-muted small mb-2"><?php echo e(__('recurring.no_recurring')); ?></p>
                        <a href="<?php echo e(route('recurring.create')); ?>" class="small text-primary fw-medium text-decoration-none">
                            + <?php echo e(__('messages.add')); ?>

                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Finanku\resources\views/recurring/index.blade.php ENDPATH**/ ?>