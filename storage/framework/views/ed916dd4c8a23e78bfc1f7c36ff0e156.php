<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Masuk'); ?> - Finanku</title>
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('favicon.ico')); ?>">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('dompet/icons/bootstrap-icons/font/bootstrap-icons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('dompet/css/style.css')); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<div class="authincation" style="min-height:100vh;">
    <div class="container-fluid p-0" style="min-height:100vh;">
        <div class="row g-0" style="min-height:100vh;">

            
            <div class="col-xl-6 col-lg-6 d-none d-lg-flex flex-column justify-content-between"
                 style="background:linear-gradient(150deg,#1a73e8 0%,#0d47a1 100%);min-height:100vh;">
                <div class="px-5 pt-5">
                    <a href="/" class="text-decoration-none d-flex align-items-center gap-3 mb-5">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:48px;height:48px;background:rgba(255,255,255,0.2);">
                            <i class="bi bi-wallet2 text-white" style="font-size:1.4rem;"></i>
                        </div>
                        <span class="text-white fw-bold fs-4">Finanku</span>
                    </a>

                    <h2 class="text-white fw-bold mb-3" style="font-size:2rem;line-height:1.3;">
                        Kelola keuangan<br>rumah tangga<br>dengan mudah
                    </h2>
                    <p style="color:rgba(255,255,255,.75);font-size:1rem;">
                        Satu platform untuk seluruh kebutuhan<br>keuangan keluarga Anda.
                    </p>

                    <div class="mt-5 d-flex flex-column gap-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                                 style="width:44px;height:44px;background:rgba(255,255,255,0.15);">
                                <i class="bi bi-bar-chart-fill text-white fs-5"></i>
                            </div>
                            <div>
                                <div class="text-white fw-semibold">Laporan Lengkap</div>
                                <div style="color:rgba(255,255,255,.65);font-size:.875rem;">Harian, mingguan, bulanan, tahunan</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                                 style="width:44px;height:44px;background:rgba(255,255,255,0.15);">
                                <i class="bi bi-people-fill text-white fs-5"></i>
                            </div>
                            <div>
                                <div class="text-white fw-semibold">Kelola Bersama</div>
                                <div style="color:rgba(255,255,255,.65);font-size:.875rem;">Satu akun untuk seluruh keluarga</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                                 style="width:44px;height:44px;background:rgba(255,255,255,0.15);">
                                <i class="bi bi-piggy-bank-fill text-white fs-5"></i>
                            </div>
                            <div>
                                <div class="text-white fw-semibold">Tabungan & Anggaran</div>
                                <div style="color:rgba(255,255,255,.65);font-size:.875rem;">Pantau target keuangan keluarga</div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="px-5 pb-4 mb-0" style="color:rgba(255,255,255,.45);font-size:.8rem;">
                    &copy; <?php echo e(date('Y')); ?> Finanku. Semua hak dilindungi.
                    &nbsp;&middot;&nbsp; v<?php echo e(config('app.version')); ?>

                    &nbsp;&middot;&nbsp;
                    <a href="<?php echo e(route('privacy.policy')); ?>" style="color:rgba(255,255,255,.45);">Kebijakan Privasi</a>
                    &nbsp;&middot;&nbsp;
                    <a href="<?php echo e(route('privacy.terms')); ?>" style="color:rgba(255,255,255,.45);">Syarat &amp; Ketentuan</a>
                </p>
            </div>

            
            <div class="col-xl-6 col-lg-6 col-12 d-flex align-items-center justify-content-center"
                 style="background:#f8f9fa;min-height:100vh;">
                <div class="w-100" style="max-width:480px;padding:40px 24px;">

                    
                    <div class="d-flex d-lg-none align-items-center gap-2 mb-4">
                        <div class="d-flex align-items-center justify-content-center rounded-3"
                             style="width:36px;height:36px;background:var(--primary);">
                            <i class="bi bi-wallet2 text-white"></i>
                        </div>
                        <span class="fw-bold fs-5" style="color:var(--primary);">Finanku</span>
                    </div>

                    
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <ul class="mb-0 ps-3">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i> <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php echo $__env->yieldContent('content'); ?>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\Finanku\resources\views/layouts/auth.blade.php ENDPATH**/ ?>