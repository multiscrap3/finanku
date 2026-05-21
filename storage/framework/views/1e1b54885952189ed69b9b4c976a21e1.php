<?php $hp = $summary['hutang_piutang_summary'] ?? []; ?>
<a href="<?php echo e(route('hutang-piutang.index', ['tab' => 'hutang'])); ?>" class="text-decoration-none">
    <div class="card h-100 border-0 shadow-sm" style="border-radius:.75rem;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="d-flex align-items-center justify-content-center rounded-2"
                     style="width:40px;height:40px;background:rgba(239,68,68,.12);flex-shrink:0;">
                    <i class="bi bi-arrow-down-circle text-danger fs-5"></i>
                </div>
                <span class="small text-muted fw-medium">Hutang</span>
            </div>
            <h5 class="fw-bold text-danger mb-1">Rp <?php echo e(number_format($hp['hutang_sisa'] ?? 0, 0, ',', '.')); ?></h5>
            <div class="text-muted" style="font-size:.72rem;">sisa yang belum lunas</div>
        </div>
    </div>
</a>
<?php /**PATH C:\laragon\www\Finanku\resources\views/dashboard/widgets/card-hutang.blade.php ENDPATH**/ ?>