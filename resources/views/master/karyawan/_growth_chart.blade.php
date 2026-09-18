<!-- STATISTIK PROGRESS PERTUMBUHAN EMPLOYEE 12 JAM TERAKHIR -->
<div class="space-y-4">
    <!-- TOP 4 STAT METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Employee Aktif -->
        <a href="{{ route('master.karyawan.index', ['status' => 'Aktiv']) }}" 
           class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 transition-all hover:border-blue-300 hover:shadow-sm group {{ ($status ?? '') === 'Aktiv' ? 'ring-2 ring-blue-500/20 border-blue-400' : '' }}"
           title="Filter Karyawan Aktif">
            <div class="w-12 h-12 rounded-xl bg-blue-50/70 border border-blue-100 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">TOTAL EMPLOYEE AKTIF</p>
                <p class="text-2xl font-black text-blue-600 leading-tight mt-0.5">
                    {{ number_format($stats['aktif'] ?? 0) }} <span class="text-xs font-semibold text-slate-500 ml-1">Karyawan</span>
                </p>
            </div>
        </a>

        <!-- 2. Resign / Non-Aktif -->
        <a href="{{ route('master.karyawan.index', ['status' => 'Resign']) }}" 
           class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 transition-all hover:border-slate-300 hover:shadow-sm group {{ ($status ?? '') === 'Resign' ? 'ring-2 ring-slate-500/20 border-slate-400' : '' }}"
           title="Filter Karyawan Resign">
            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-center text-slate-500 shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-hotel text-slate-500 text-lg"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">RESIGN / NON-AKTIF</p>
                <p class="text-2xl font-black text-slate-800 leading-tight mt-0.5">
                    {{ number_format($stats['resign'] ?? 0) }} <span class="text-xs font-semibold text-slate-500 ml-1">Orang</span>
                </p>
            </div>
        </a>

        <!-- 3. Karyawan Baru (+) -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 transition-all hover:shadow-sm group">
            <div class="w-12 h-12 rounded-xl bg-emerald-50/70 border border-emerald-100 flex items-center justify-center text-emerald-600 text-lg shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">KARYAWAN BARU (+)</p>
                <p class="text-2xl font-black text-emerald-600 leading-tight mt-0.5">
                    +{{ number_format($chartSummary['new_employees_12h'] ?? 0) }} <span class="text-xs font-semibold text-slate-500 ml-1">Orang</span>
                </p>
            </div>
        </div>

        <!-- 4. Mutasi Resign (-) -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5 transition-all hover:shadow-sm group">
            <div class="w-12 h-12 rounded-xl bg-rose-50/70 border border-rose-100 flex items-center justify-center text-rose-500 text-lg shrink-0 group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-user-minus"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">MUTASI RESIGN (-)</p>
                <p class="text-2xl font-black text-rose-600 leading-tight mt-0.5">
                    -{{ number_format($chartSummary['resigned_employees_12h'] ?? 0) }} <span class="text-xs font-semibold text-slate-500 ml-1">Orang</span>
                </p>
            </div>
        </div>
    </div>

    <!-- DUAL-AXIS LINE CHART CONTAINER -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 relative">
        <!-- Legend Bar (Right Aligned per screenshot) -->
        <div class="flex flex-wrap items-center justify-end gap-5 text-xs font-bold mb-4 pb-2 border-b border-slate-100/80">
            <div class="inline-flex items-center gap-2 text-slate-700">
                <span class="w-3.5 h-3.5 rounded-full border-2 border-blue-600 bg-white inline-block shadow-2xs"></span>
                <span class="tracking-tight font-bold text-slate-800">Total Employee Aktif</span>
            </div>
            <div class="inline-flex items-center gap-2 text-slate-700">
                <span class="w-3.5 h-3.5 rounded-full border-2 border-emerald-500 bg-white inline-block shadow-2xs"></span>
                <span class="tracking-tight font-bold text-slate-800">Karyawan Baru (+) Odoo</span>
            </div>
            <div class="inline-flex items-center gap-2 text-slate-700">
                <span class="w-3.5 h-3.5 rounded-full border-2 border-rose-500 bg-white inline-block shadow-2xs"></span>
                <span class="tracking-tight font-bold text-slate-800">Resign / Non-Aktif (-) Odoo</span>
            </div>
        </div>

        <!-- Canvas Container -->
        <div class="relative w-full h-[260px] sm:h-[280px]">
            <canvas id="employeeGrowthChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('employeeGrowthChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    // Chart Data from Controller
    const labels = @json($chartData['labels'] ?? []);
    const activeSeries = @json($chartData['active_series'] ?? []);
    const newSeries = @json($chartData['new_series'] ?? []);
    const resignSeries = @json($chartData['resign_series'] ?? []);
    const minActive = @json($chartData['min_active'] ?? 0);
    const maxActive = @json($chartData['max_active'] ?? 0);
    const maxChange = @json($chartData['max_change'] ?? 0);

    // Gradient for Total Employee Aktif
    const blueGradient = ctx.createLinearGradient(0, 0, 0, 260);
    blueGradient.addColorStop(0, 'rgba(29, 104, 216, 0.18)');
    blueGradient.addColorStop(1, 'rgba(29, 104, 216, 0.005)');

    // Min & Max range for left axis to keep lines nicely proportioned
    const activeRange = maxActive - minActive;
    const suggestedYMin = activeRange <= 3 ? Math.max(0, minActive - 2) : Math.max(0, minActive - Math.ceil(activeRange * 0.2));
    const suggestedYMax = activeRange <= 3 ? maxActive + 2 : maxActive + Math.ceil(activeRange * 0.2);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Employee Aktif',
                    data: activeSeries,
                    borderColor: '#1d68d8',
                    backgroundColor: blueGradient,
                    borderWidth: 2.8,
                    fill: true,
                    tension: 0.35,
                    yAxisID: 'y',
                    pointRadius: 4.5,
                    pointHoverRadius: 6.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#1d68d8',
                    pointBorderWidth: 2.5
                },
                {
                    label: 'Karyawan Baru (+) Odoo',
                    data: newSeries,
                    borderColor: '#10b981',
                    borderDash: [4, 4],
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3,
                    yAxisID: 'y1',
                    pointRadius: 3.5,
                    pointHoverRadius: 5.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2
                },
                {
                    label: 'Resign / Non-Aktif (-) Odoo',
                    data: resignSeries,
                    borderColor: '#ef4444',
                    borderDash: [2, 3],
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3,
                    yAxisID: 'y1',
                    pointRadius: 3.5,
                    pointHoverRadius: 5.5,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#ef4444',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: false // Using custom header legend matching screenshot
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.92)',
                    titleColor: '#ffffff',
                    bodyColor: '#e2e8f0',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 11 },
                    padding: 11,
                    cornerRadius: 10,
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 10,
                            weight: '600'
                        },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 24
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Total Employee Aktif',
                        color: '#64748b',
                        font: {
                            size: 11,
                            weight: 'bold'
                        }
                    },
                    suggestedMin: suggestedYMin,
                    suggestedMax: suggestedYMax,
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 10
                        },
                        callback: function (value) {
                            return new Intl.NumberFormat('id-ID').format(value);
                        }
                    },
                    grid: {
                        color: '#f1f5f9',
                        borderDash: [3, 3]
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Perubahan Odoo (+ / -)',
                        color: '#64748b',
                        font: {
                            size: 11,
                            weight: 'bold'
                        }
                    },
                    min: 0,
                    suggestedMax: Math.max(10, maxChange + 2),
                    ticks: {
                        color: '#64748b',
                        font: {
                            size: 10
                        },
                        stepSize: 2
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
