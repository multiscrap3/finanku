<div class="card border-0 shadow-sm h-100" style="border-radius:.75rem;">
    <div class="card-body p-4">
        <h6 class="fw-semibold text-dark mb-3">Tren 6 Bulan Terakhir</h6>
        @if(!empty($summary['chart_data']['labels']))
            <canvas id="chartTrend" height="180"></canvas>
        @else
            <div class="d-flex align-items-center justify-content-center text-muted"
                 style="height:180px;font-size:.875rem;">
                <div class="text-center">
                    <i class="bi bi-bar-chart fs-2 d-block mb-2 opacity-25"></i>
                    Belum ada data.
                </div>
            </div>
        @endif
    </div>
</div>
