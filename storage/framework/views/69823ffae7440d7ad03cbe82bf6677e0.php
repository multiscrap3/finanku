<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Dashboard'); ?> - Finanku</title>
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('favicon.ico')); ?>">

    
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet">

    
    <link rel="stylesheet" href="<?php echo e(asset('dompet/icons/bootstrap-icons/font/bootstrap-icons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('dompet/icons/avasta/css/style.css')); ?>">

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/line-awesome@1.3.0/dist/line-awesome/css/line-awesome.min.css">

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('dompet/css/perfect-scrollbar.css')); ?>">

    
    <link rel="stylesheet" href="<?php echo e(asset('dompet/css/style.css')); ?>">

    <style>
    /* ============================================================
       MOBILE RESPONSIVE — SIDEBAR OVERLAY & LAYOUT FIXES
       ============================================================ */

    /* Mobile hamburger button (hanya tampil di mobile) */
    .mobile-hamburger {
        display: none;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: .25rem .4rem;
        border-radius: .5rem;
        line-height: 1;
        color: inherit;
        flex-shrink: 0;
        transition: background .15s;
    }
    .mobile-hamburger:hover { background: rgba(0,0,0,.06); }
    [data-theme-version="dark"] .mobile-hamburger:hover { background: rgba(255,255,255,.08); }

    /* Sidebar brand logo — hanya tampil di mobile */
    .dlabnav-mobile-brand {
        display: none;
        align-items: center;
        gap: .65rem;
        padding: 1rem 1.5rem .75rem;
        border-bottom: 1px solid rgba(255,255,255,.1);
        margin-bottom: .25rem;
    }

    /* Mobile backdrop overlay */
    #mobile-nav-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1054;
        background: rgba(0,0,0,.5);
        -webkit-backdrop-filter: blur(2px);
        backdrop-filter: blur(2px);
    }

    @media (max-width: 767px) {
        /* ── Sidebar off-screen by default ── */
        .dlabnav {
            position: fixed !important;
            top: 0 !important;
            left: -21rem !important;
            height: 100dvh !important;
            width: 20.5rem !important;
            z-index: 1056 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            transition: left .28s cubic-bezier(.4,0,.2,1) !important;
            box-shadow: none !important;
        }

        /* ── Sidebar visible ketika mobile-nav-open ── */
        #main-wrapper.mobile-nav-open .dlabnav {
            left: 0 !important;
            box-shadow: 8px 0 40px rgba(0,0,0,.28) !important;
        }

        /* ── Backdrop tampil saat sidebar terbuka ── */
        #main-wrapper.mobile-nav-open #mobile-nav-backdrop {
            display: block;
        }

        /* ── Logo dalam sidebar (mobile) ── */
        .dlabnav-mobile-brand { display: flex; }

        /* ── Sembunyikan nav-header asli (logo+hamburger template) ── */
        .nav-header { display: none !important; }

        /* ── Tampilkan mobile hamburger di topbar ── */
        .mobile-hamburger { display: flex !important; align-items: center; }

        /* ── Header full-width ── */
        .header {
            left: 0 !important;
            width: 100% !important;
            padding-left: 0 !important;
        }

        /* ── Content body full-width & padding atas sesuai header ── */
        .content-body,
        [data-header-position="fixed"] .content-body {
            margin-left: 0 !important;
            padding-top: 4.75rem !important;
        }

        /* ── Footer full-width ── */
        .footer,
        #main-wrapper.menu-toggle .footer {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }

        /* ── FAB posisi lebih rendah di mobile ── */
        #fab-container { bottom: 1.25rem; right: 1.25rem; }
        #fab-main { width: 50px; height: 50px; font-size: 1.2rem; }
        #fab-container .fab-mini { width: 42px; height: 42px; font-size: 1.05rem; }
    }

    /* ============================================================
       EXISTING STYLES
       ============================================================ */
    .brand-title { color: #1e2130; }
    [data-theme-version="dark"] .brand-title { color: #fff !important; }

    .footer .copyright p { text-align: center !important; }
    #main-wrapper.menu-toggle .footer { padding-left: 5.7rem; }

    /* Speed Dial FAB */
    #fab-container { position:fixed; bottom:1.75rem; right:1.75rem; z-index:1040; display:flex; flex-direction:column; align-items:flex-end; }
    #fab-actions { display:flex; flex-direction:column-reverse; gap:.65rem; margin-bottom:.65rem; align-items:flex-end; pointer-events:none; opacity:0; transform:translateY(8px); transition:opacity .2s ease, transform .2s ease; }
    #fab-actions.fab-open { pointer-events:auto; opacity:1; transform:translateY(0); }
    .fab-action { display:flex; align-items:center; gap:.5rem; }
    .fab-label { background:rgba(30,33,48,.82); color:#fff; padding:.2rem .65rem; border-radius:6px; font-size:.75rem; white-space:nowrap; backdrop-filter:blur(4px); box-shadow:0 2px 8px rgba(0,0,0,.2); }
    .fab-mini { width:44px; height:44px; border-radius:50%; color:#fff !important; display:flex; align-items:center; justify-content:center; box-shadow:0 3px 14px rgba(0,0,0,.28); text-decoration:none; font-size:1.15rem; transition:transform .15s; flex-shrink:0; }
    .fab-mini:hover { transform:scale(1.12); }
    #fab-main { width:56px; height:56px; border-radius:50%; background:var(--primary); color:#fff; border:none; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 22px rgba(0,0,0,.3); font-size:1.4rem; cursor:pointer; transition:transform .25s, box-shadow .2s; }
    #fab-main:hover { box-shadow:0 6px 28px rgba(0,0,0,.38); }
    #fab-main.fab-open { transform:rotate(45deg); }
    #fab-overlay { display:none; position:fixed; inset:0; z-index:1039; }
    #fab-overlay.fab-open { display:block; }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body data-sidebar-style="full" data-layout="vertical" data-header-position="fixed" data-container="wide" data-primary="color_1">

<div id="main-wrapper" class="show">

    
    <div class="nav-header">
        <a href="<?php echo e(route('dashboard')); ?>" class="brand-logo">
            <div class="logo-abbr d-flex align-items-center justify-content-center"
                 style="width:47px;height:47px;background:var(--primary);border-radius:12px;">
                <span style="color:#fff;font-weight:700;font-size:1.2rem;">F</span>
            </div>
            <div class="brand-title ms-2" style="font-size:1.3rem;font-weight:700;">Finanku</div>
        </a>
        <div class="nav-control">
            <div class="hamburger">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </div>
        </div>
    </div>

    
    <div class="header">
        <div class="header-content">
            <nav class="navbar navbar-expand">
                <div class="collapse navbar-collapse justify-content-between">

                    
                    <div class="header-left d-flex align-items-center gap-2">
                        
                        <button type="button" id="mobileNavToggle" class="mobile-hamburger" aria-label="<?php echo e(__('messages.open_menu')); ?>">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                        <div class="dashboard_bar"><?php echo $__env->yieldContent('page-title', 'Dashboard'); ?></div>
                    </div>

                    
                    <ul class="navbar-nav header-right">

                        
                        <li class="nav-item dropdown notification_dropdown">
                            <a class="nav-link" href="<?php echo e(route('notifikasi.index')); ?>" title="<?php echo e(__('messages.notifications')); ?>">
                                <i class="bi bi-bell fs-5"></i>
                            </a>
                        </li>

                        
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);" id="darkModeToggle" title="<?php echo e(__('messages.toggle_theme')); ?>">
                                <i class="bi bi-moon-stars-fill fs-5" id="darkModeIcon"></i>
                            </a>
                        </li>

                        
                        <li class="nav-item dropdown header-profile">
                            <a class="nav-link" href="#" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="header-info d-flex align-items-center gap-2">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white"
                                         style="width:38px;height:38px;font-weight:600;font-size:.9rem;">
                                        <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

                                    </div>
                                    <div class="d-none d-md-block">
                                        <span class="fw-semibold text-dark fs-14"><?php echo e(auth()->user()->name); ?></span>
                                    </div>
                                    <i class="bi bi-chevron-down fs-6 text-muted"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="<?php echo e(route('settings.index')); ?>" class="dropdown-item ai-icon">
                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                    <?php echo e(__('messages.profile_settings')); ?>

                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item ai-icon text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        <?php echo e(__('messages.logout')); ?>

                                    </button>
                                </form>
                            </div>
                        </li>

                    </ul>
                </div>
            </nav>
        </div>
    </div>

    
    <div id="mobile-nav-backdrop"></div>

    
    <div class="dlabnav">

        
        <div class="dlabnav-mobile-brand">
            <div style="width:38px;height:38px;background:var(--primary);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="color:#fff;font-weight:700;font-size:1.05rem;">F</span>
            </div>
            <span style="font-weight:700;font-size:1.1rem;color:inherit;">Finanku</span>
        </div>

        <div class="dlabnav-scroll">
            <ul class="metismenu" id="menu">

                
                <li class="<?php echo e(request()->routeIs('dashboard') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('dashboard')); ?>" aria-expanded="false">
                        <i class="bi bi-house-door"></i>
                        <span class="nav-text"><?php echo e(__('navigation.dashboard')); ?></span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('transaksi.*') || request()->routeIs('import-bank.*') ? 'mm-active' : ''); ?>">
                    <a class="<?php echo e(request()->routeIs('transaksi.*') || request()->routeIs('import-bank.*') ? '' : 'has-arrow'); ?>"
                       href="javascript:void(0);" aria-expanded="<?php echo e(request()->routeIs('transaksi.*') || request()->routeIs('import-bank.*') ? 'true' : 'false'); ?>">
                        <i class="bi bi-arrow-left-right"></i>
                        <span class="nav-text"><?php echo e(__('navigation.transactions')); ?></span>
                    </a>
                    <ul aria-expanded="<?php echo e(request()->routeIs('transaksi.*') || request()->routeIs('import-bank.*') ? 'true' : 'false'); ?>">
                        <li><a href="<?php echo e(route('transaksi.index')); ?>" class="<?php echo e(request()->routeIs('transaksi.*') ? 'mm-active' : ''); ?>"><?php echo e(__('navigation.all_transactions')); ?></a></li>
                        <li><a href="<?php echo e(route('import-bank.web.index')); ?>" class="<?php echo e(request()->routeIs('import-bank.*') ? 'mm-active' : ''); ?>"><?php echo e(__('navigation.import_bank')); ?></a></li>
                    </ul>
                </li>

                
                <li class="<?php echo e(request()->routeIs('laporan.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('laporan.index')); ?>" aria-expanded="false">
                        <i class="bi bi-bar-chart-line"></i>
                        <span class="nav-text"><?php echo e(__('navigation.reports')); ?></span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('anggaran.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('anggaran.index')); ?>" aria-expanded="false">
                        <i class="bi bi-calculator"></i>
                        <span class="nav-text"><?php echo e(__('navigation.budget')); ?></span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('tabungan.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('tabungan.index')); ?>" aria-expanded="false">
                        <i class="bi bi-piggy-bank"></i>
                        <span class="nav-text"><?php echo e(__('navigation.savings')); ?></span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('hutang-piutang.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('hutang-piutang.index')); ?>" aria-expanded="false">
                        <i class="bi bi-arrow-left-right"></i>
                        <span class="nav-text"><?php echo e(__('navigation.debt')); ?></span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('recurring.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('recurring.index')); ?>" aria-expanded="false">
                        <i class="bi bi-arrow-repeat"></i>
                        <span class="nav-text"><?php echo e(__('navigation.recurring')); ?></span>
                    </a>
                </li>

                
                <li class="menu-title">
                    <span><?php echo e(__('navigation.management')); ?></span>
                </li>

                
                <li class="<?php echo e(request()->routeIs('household.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('household.index')); ?>" aria-expanded="false">
                        <i class="bi bi-people"></i>
                        <span class="nav-text"><?php echo e(__('navigation.household')); ?></span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('kategori.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('kategori.index')); ?>" aria-expanded="false">
                        <i class="bi bi-tags"></i>
                        <span class="nav-text"><?php echo e(__('navigation.categories')); ?></span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('sumber-transaksi.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('sumber-transaksi.index')); ?>" aria-expanded="false">
                        <i class="bi bi-credit-card"></i>
                        <span class="nav-text"><?php echo e(__('navigation.fund_sources')); ?></span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('tags.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('tags.index')); ?>" aria-expanded="false">
                        <i class="bi bi-bookmark"></i>
                        <span class="nav-text"><?php echo e(__('navigation.tags')); ?></span>
                    </a>
                </li>

                
                <li class="<?php echo e(request()->routeIs('notifikasi.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('notifikasi.index')); ?>" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="nav-text"><?php echo e(__('navigation.notifications')); ?></span>
                    </a>
                </li>

                
                <li class="menu-title">
                    <span><?php echo e(__('navigation.account')); ?></span>
                </li>

                
                <li class="<?php echo e(request()->routeIs('settings.*') ? 'mm-active' : ''); ?>">
                    <a href="<?php echo e(route('settings.index')); ?>" aria-expanded="false">
                        <i class="bi bi-gear"></i>
                        <span class="nav-text"><?php echo e(__('navigation.settings')); ?></span>
                    </a>
                </li>

            </ul>
        </div>
    </div>

    
    <div class="content-body">
        <div class="container-fluid">

            
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <i class="bi bi-x-circle me-2"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i> <?php echo e(session('warning')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(session('info')): ?>
                <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                    <i class="bi bi-info-circle me-2"></i> <?php echo e(session('info')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            
            <?php echo $__env->yieldContent('content'); ?>

        </div>
    </div>

    
    <div class="footer">
        <div class="copyright">
            <p class="mb-0">
                &copy; <?php echo e(date('Y')); ?> <strong>Finanku</strong>. <?php echo e(__('messages.footer_tagline')); ?>

                <span class="ms-2 text-muted" style="font-size:.75rem;">v<?php echo e(config('app.version')); ?></span>
                <span class="ms-3" style="font-size:.75rem;">
                    <a href="<?php echo e(route('privacy.policy')); ?>" target="_blank" class="text-muted text-decoration-none me-2"><?php echo e(__('messages.privacy_policy')); ?></a>
                    <a href="<?php echo e(route('privacy.terms')); ?>" target="_blank" class="text-muted text-decoration-none"><?php echo e(__('messages.terms')); ?></a>
                </span>
            </p>
        </div>
    </div>

</div>


<div id="fab-overlay" onclick="closeFab()"></div>
<div id="fab-container">
    <div id="fab-actions">
        <div class="fab-action">
            <span class="fab-label"><?php echo e(__('navigation.expense')); ?></span>
            <a href="<?php echo e(route('transaksi.create')); ?>?jenis=pengeluaran" class="fab-mini bg-danger">
                <i class="fas fa-arrow-down"></i>
            </a>
        </div>
        <div class="fab-action">
            <span class="fab-label"><?php echo e(__('navigation.income')); ?></span>
            <a href="<?php echo e(route('transaksi.create')); ?>?jenis=pemasukan" class="fab-mini bg-success">
                <i class="fas fa-arrow-up"></i>
            </a>
        </div>
        <div class="fab-action">
            <span class="fab-label"><?php echo e(__('navigation.transfer')); ?></span>
            <a href="<?php echo e(route('transaksi.create')); ?>?jenis=transfer" class="fab-mini" style="background:var(--primary);">
                <i class="fas fa-right-left"></i>
            </a>
        </div>
    </div>
    <button id="fab-main" type="button" onclick="toggleFab()" title="<?php echo e(__('navigation.add_transaction')); ?>">
        <i class="fas fa-plus"></i>
    </button>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.5/perfect-scrollbar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Init MetisMenu sidebar
    $("#menu").metisMenu();

    // ── Desktop hamburger: toggle sidebar collapse ────────────
    $(".nav-header .hamburger").on("click", function () {
        $("#main-wrapper").toggleClass("menu-toggle");
        $(this).toggleClass("is-active");
    });

    // ── Mobile hamburger: slide-in sidebar overlay ────────────
    var mobileNavToggle  = document.getElementById('mobileNavToggle');
    var mobileBackdrop   = document.getElementById('mobile-nav-backdrop');
    var mainWrapper      = document.getElementById('main-wrapper');

    function openMobileNav() {
        mainWrapper.classList.add('mobile-nav-open');
        document.body.style.overflow = 'hidden';
    }
    function closeMobileNav() {
        mainWrapper.classList.remove('mobile-nav-open');
        document.body.style.overflow = '';
    }

    if (mobileNavToggle) {
        mobileNavToggle.addEventListener('click', function () {
            if (mainWrapper.classList.contains('mobile-nav-open')) {
                closeMobileNav();
            } else {
                openMobileNav();
            }
        });
    }
    if (mobileBackdrop) {
        mobileBackdrop.addEventListener('click', closeMobileNav);
    }

    // Close mobile nav when a sidebar link is clicked
    document.querySelectorAll('.dlabnav .metismenu a[href]').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 767) closeMobileNav();
        });
    });

    // Close mobile nav on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 767) closeMobileNav();
    });

    // ── Dark / Light mode toggle ──────────────────────────────
    (function () {
        var saved = localStorage.getItem('finanku-theme') || 'light';
        applyTheme(saved);

        $("#darkModeToggle").on("click", function () {
            var current = $("body").attr("data-theme-version") === "dark" ? "dark" : "light";
            var next = current === "dark" ? "light" : "dark";
            applyTheme(next);
            localStorage.setItem('finanku-theme', next);
        });

        function applyTheme(theme) {
            $("body").attr("data-theme-version", theme);
            if (theme === "dark") {
                $("#darkModeIcon").removeClass("bi-moon-stars-fill").addClass("bi-sun-fill");
            } else {
                $("#darkModeIcon").removeClass("bi-sun-fill").addClass("bi-moon-stars-fill");
            }
        }
    })();

    // Auto dismiss flash alerts setelah 5 detik
    setTimeout(function () {
        $(".alert").fadeOut("slow", function () { $(this).remove(); });
    }, 5000);

    // ── Speed Dial FAB ────────────────────────────────────────
    function toggleFab() {
        var actions = document.getElementById('fab-actions');
        var btn = document.getElementById('fab-main');
        var overlay = document.getElementById('fab-overlay');
        var isOpen = actions.classList.contains('fab-open');
        if (isOpen) {
            actions.classList.remove('fab-open');
            btn.classList.remove('fab-open');
            overlay.classList.remove('fab-open');
        } else {
            actions.classList.add('fab-open');
            btn.classList.add('fab-open');
            overlay.classList.add('fab-open');
        }
    }
    function closeFab() {
        document.getElementById('fab-actions').classList.remove('fab-open');
        document.getElementById('fab-main').classList.remove('fab-open');
        document.getElementById('fab-overlay').classList.remove('fab-open');
    }
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>


<?php echo $__env->yieldPushContent('modals'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\Finanku\resources\views/layouts/app.blade.php ENDPATH**/ ?>