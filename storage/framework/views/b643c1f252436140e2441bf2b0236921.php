<?php $__env->startSection('title', 'Transaksi'); ?>
<?php $__env->startSection('page-title', 'Transaksi'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4 mt-1">

    
    <div class="col-12">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm" style="border-radius:.75rem;border-left:4px solid #10b981!important;">
                    <div class="card-body py-3 px-4">
                        <div class="small text-success fw-medium mb-1">Total Pemasukan</div>
                        <h5 class="fw-bold text-success mb-0">Rp <?php echo e(number_format($summary['total_pemasukan'] ?? 0, 0, ',', '.')); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm" style="border-radius:.75rem;border-left:4px solid #ef4444!important;">
                    <div class="card-body py-3 px-4">
                        <div class="small text-danger fw-medium mb-1">Total Pengeluaran</div>
                        <h5 class="fw-bold text-danger mb-0">Rp <?php echo e(number_format($summary['total_pengeluaran'] ?? 0, 0, ',', '.')); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm" style="border-radius:.75rem;border-left:4px solid #3b82f6!important;">
                    <div class="card-body py-3 px-4">
                        <div class="small text-primary fw-medium mb-1">Saldo Bersih</div>
                        <h5 class="fw-bold text-primary mb-0">Rp <?php echo e(number_format(($summary['total_pemasukan'] ?? 0) - ($summary['total_pengeluaran'] ?? 0), 0, ',', '.')); ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-12">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="<?php echo e(route('transaksi.create')); ?>" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Transaksi
            </a>
            <button class="btn btn-outline-secondary btn-sm" type="button"
                    data-bs-toggle="collapse" data-bs-target="#filterPanel">
                <i class="bi bi-funnel me-1"></i> Filter
                <?php if(request()->hasAny(['jenis', 'kategori_id', 'tanggal_dari', 'tanggal_sampai', 'search'])): ?>
                    <span class="badge bg-primary ms-1">Aktif</span>
                <?php endif; ?>
            </button>
            <?php if(request()->hasAny(['jenis', 'kategori_id', 'tanggal_dari', 'tanggal_sampai', 'search'])): ?>
                <a href="<?php echo e(route('transaksi.index')); ?>" class="btn btn-link btn-sm text-danger p-0">
                    <i class="bi bi-x-circle me-1"></i>Reset filter
                </a>
            <?php endif; ?>
        </div>

        
        <div class="collapse <?php echo e(request()->hasAny(['jenis','kategori_id','tanggal_dari','tanggal_sampai','search']) ? 'show' : ''); ?> mt-3"
             id="filterPanel">
            <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3">
                        <div class="col-12 col-md-4 col-lg-2">
                            <label class="form-label small fw-medium">Jenis</label>
                            <select name="jenis" class="form-select form-select-sm">
                                <option value="">Semua</option>
                                <option value="pemasukan"  <?php echo e(request('jenis') === 'pemasukan'  ? 'selected' : ''); ?>>Pemasukan</option>
                                <option value="pengeluaran"<?php echo e(request('jenis') === 'pengeluaran'? 'selected' : ''); ?>>Pengeluaran</option>
                                <option value="transfer"   <?php echo e(request('jenis') === 'transfer'   ? 'selected' : ''); ?>>Transfer</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-2">
                            <label class="form-label small fw-medium">Kategori</label>
                            <select name="kategori_id" class="form-select form-select-sm">
                                <option value="">Semua</option>
                                <?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($kat->id); ?>" <?php echo e(request('kategori_id') == $kat->id ? 'selected' : ''); ?>><?php echo e($kat->nama); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3">
                            <label class="form-label small fw-medium">Cari</label>
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                                   class="form-control form-control-sm" placeholder="Keterangan...">
                        </div>
                        <div class="col-6 col-lg-2">
                            <label class="form-label small fw-medium">Dari Tanggal</label>
                            <input type="date" name="tanggal_dari" value="<?php echo e(request('tanggal_dari')); ?>"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-6 col-lg-2">
                            <label class="form-label small fw-medium">Sampai Tanggal</label>
                            <input type="date" name="tanggal_sampai" value="<?php echo e(request('tanggal_sampai')); ?>"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-12 col-lg-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <?php if(session('warning_duplicate')): ?>
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Peringatan:</strong> <?php echo e(session('warning_duplicate.message')); ?>

            </div>
        </div>
    <?php endif; ?>

    
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:.75rem;">
            <div class="card-body p-0">
                <?php $__empty_1 = true; $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('transaksi.show', $t)); ?>"
                       class="d-flex align-items-center gap-3 px-4 py-3 border-bottom text-decoration-none"
                       style="transition:.15s;">
                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                             style="width:42px;height:42px;
                             background:<?php echo e($t->jenis === 'pemasukan' ? 'rgba(16,185,129,.12)' : ($t->jenis === 'pengeluaran' ? 'rgba(239,68,68,.12)' : 'rgba(59,130,246,.12)')); ?>">
                            <?php if($t->jenis === 'pemasukan'): ?>
                                <i class="bi bi-arrow-up-circle text-success fs-5"></i>
                            <?php elseif($t->jenis === 'pengeluaran'): ?>
                                <i class="bi bi-arrow-down-circle text-danger fs-5"></i>
                            <?php else: ?>
                                <i class="bi bi-arrow-left-right text-primary fs-5"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="small fw-medium text-dark text-truncate">
                                <?php echo e($t->keterangan ?: 'Tanpa keterangan'); ?>

                            </div>
                            <div class="text-muted d-flex align-items-center gap-1" style="font-size:.72rem;">
                                <span><?php echo e($t->tanggal->translatedFormat('d M Y')); ?></span>
                                <?php if($t->kategori): ?> <span>&bull;</span><span><?php echo e($t->kategori->nama); ?></span> <?php endif; ?>
                                <?php if($t->sumberTransaksi): ?> <span>&bull;</span><span><?php echo e($t->sumberTransaksi->nama); ?></span> <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="small fw-bold <?php echo e($t->jenis === 'pemasukan' ? 'text-success' : ($t->jenis === 'pengeluaran' ? 'text-danger' : 'text-primary')); ?>">
                                <?php echo e($t->jenis === 'pemasukan' ? '+' : ($t->jenis === 'pengeluaran' ? '-' : '')); ?>Rp <?php echo e(number_format($t->jumlah, 0, ',', '.')); ?>

                            </div>
                            <div class="text-muted" style="font-size:.7rem;"><?php echo e($t->user?->name); ?></div>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="py-5 text-center">
                        <i class="bi bi-receipt fs-1 d-block mb-2 text-muted opacity-25"></i>
                        <p class="text-muted small mb-2">Belum ada transaksi.</p>
                        <a href="<?php echo e(route('transaksi.create')); ?>" class="small text-primary fw-medium text-decoration-none">
                            + Tambah transaksi pertama
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <?php if($transaksi->hasPages()): ?>
        <div class="col-12">
            <?php echo e($transaksi->withQueryString()->links('pagination::bootstrap-5')); ?>

        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Finanku\resources\views/transaksi/index.blade.php ENDPATH**/ ?>