@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #6d28d9;
        --primary-light: #ddd6fe;
        --primary-bg: #f5f3ff;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .page-title { font-size: 26px; font-weight: 800; color: #1e1b4b; margin: 0; }
    .page-subtitle { color: #6b7280; font-size: 14px; margin: 4px 0 0 0; }

    .filter-select {
        border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 16px;
        font-size: 14px; background: white; color: #374151; font-weight: 500;
        min-width: 170px; cursor: pointer;
    }
    .filter-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(109,40,217,.1); }

    .card-custom {
        background: white; border-radius: 16px; border: 1px solid #f0e9ff;
        box-shadow: 0 1px 3px rgba(0,0,0,.04); padding: 24px;
    }

    /* ===== TOP CARD ===== */
    .top-stats {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 24px;
        align-items: center;
        margin-bottom: 20px;
    }
    .stat-box { display: flex; align-items: center; gap: 18px; }
    .stat-icon {
        width: 56px; height: 56px; border-radius: 14px;
        background: var(--primary-bg); color: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-size: 28px; flex-shrink: 0;
    }
    .stat-label {
        font-size: 11px; font-weight: 700; color: var(--primary);
        text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;
    }
    .stat-value { font-size: 30px; font-weight: 800; color: var(--primary); letter-spacing: -.5px; }
    .stat-divider { border-bottom: 1px solid #f0e9ff; margin: 10px 0; }
    .stat-desc { font-size: 13px; color: #6b7280; }

    .donut-wrapper { position: relative; width: 200px; height: 200px; }
    .donut-center-text {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        text-align: center;
    }
    .donut-percent { font-size: 36px; font-weight: 800; color: var(--primary); line-height: 1; }
    .donut-label { font-size: 13px; color: #6b7280; margin-top: 4px; }

    .info-banner {
        background: var(--primary-bg); border-radius: 12px; padding: 14px 20px;
        display: flex; justify-content: space-between; align-items: center;
        flex-wrap: wrap; gap: 12px;
    }
    .info-banner-left { display: flex; align-items: center; gap: 12px; }
    .info-banner-icon {
        width: 36px; height: 36px; border-radius: 50%; background: var(--primary);
        color: white; display: flex; align-items: center; justify-content: center; font-size: 18px;
    }
    .info-banner-text { font-size: 14px; color: #374151; }
    .info-banner-text strong { color: var(--primary); }
    .info-banner-right { font-size: 13px; color: #6b7280; }
    .info-banner-right strong { color: var(--primary); font-size: 16px; }

    /* ===== MIDDLE 3 KOLOM ===== */
    .middle-row {
        display: grid;
        grid-template-columns: 5fr 4fr 4fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    .card-title-sm { font-size: 16px; font-weight: 700; color: #1e1b4b; margin-bottom: 18px; }

    .ringkasan-item { display: flex; align-items: center; padding: 13px 0; border-bottom: 1px solid #f3f4f6; }
    .ringkasan-item:last-child { border-bottom: none; }
    .ringkasan-icon {
        width: 38px; height: 38px; border-radius: 10px; background: var(--primary-bg);
        color: var(--primary); display: flex; align-items: center; justify-content: center;
        font-size: 19px; margin-right: 12px; flex-shrink: 0;
    }
    .ringkasan-label { flex-grow: 1; font-size: 13px; color: #4b5563; }
    .ringkasan-value { font-size: 14px; font-weight: 700; color: #1e1b4b; text-align: right; }

    /* ===== GAUGE ===== */
    .gauge-wrapper { position: relative; height: 170px; margin-bottom: 8px; }
    .gauge-value {
        position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);
        font-size: 34px; font-weight: 800; color: var(--primary); letter-spacing: -1px;
    }
    .gauge-min, .gauge-max { position: absolute; bottom: 0; font-size: 12px; color: #6b7280; }
    .gauge-min { left: 4px; }
    .gauge-max { right: 4px; }

    .status-box {
        border-radius: 12px; padding: 14px 16px;
        display: flex; align-items: center; gap: 12px; margin-top: 14px;
    }
    .status-good { background: var(--primary-bg); }
    .status-warn { background: #fff7ed; }
    .status-icon {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 18px; flex-shrink: 0;
    }
    .status-good .status-icon { background: var(--primary); }
    .status-warn .status-icon { background: #f59e0b; }
    .status-title { font-size: 14px; font-weight: 700; color: #1e1b4b; }
    .status-desc { font-size: 12px; color: #6b7280; }

    /* ===== FOOTER ===== */
    .dashboard-footer {
        background: var(--primary-bg); border-radius: 12px; padding: 14px 20px;
        display: flex; align-items: center; gap: 10px;
        font-size: 13px; color: #4b5563;
    }
    .footer-icon {
        width: 32px; height: 32px; border-radius: 50%; background: var(--primary);
        color: white; display: flex; align-items: center; justify-content: center; font-size: 16px;
    }

    @media (max-width: 992px) {
        .top-stats { grid-template-columns: 1fr; }
        .donut-wrapper { margin: 0 auto; }
        .middle-row { grid-template-columns: 1fr; }
        .stat-value { font-size: 24px; }
    }
</style>

<div class="container-fluid py-4">
    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard Monitor</h1>
            <p class="page-subtitle">Ringkasan Pencapaian Target Bulanan — Cabang {{ $namaCabang }}</p>
        </div>
        <div class="d-flex gap-2">
            <select class="filter-select" id="filterBulan">
                @foreach($daftarBulan as $num => $nama)
                    <option value="{{ $num }}" {{ $bulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
            <select class="filter-select" id="filterTahun">
                @foreach($daftarTahun as $y)
                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- TOP CARD --}}
    <div class="card-custom mb-4">
        <div class="top-stats">
            {{-- Target --}}
            <div class="stat-box">
                <div class="stat-icon"><i class="ti ti-target"></i></div>
                <div class="flex-grow-1">
                    <div class="stat-label">Target Bulanan</div>
                    <div class="stat-value">Rp {{ number_format($target, 0, ',', '.') }}</div>
                    <div class="stat-divider"></div>
                    <div class="stat-desc">Target Pendapatan Bulan Ini</div>
                </div>
            </div>

            {{-- Donut --}}
            <div class="donut-wrapper">
                <canvas id="donutChart"></canvas>
                <div class="donut-center-text">
                    <div class="donut-percent">{{ $persentase }}%</div>
                    <div class="donut-label">Pencapaian</div>
                </div>
            </div>

            {{-- Pencapaian --}}
            <div class="stat-box" style="margin-left:auto;">
                <div class="stat-icon"><i class="ti ti-chart-bar"></i></div>
                <div class="flex-grow-1">
                    <div class="stat-label">Pencapaian</div>
                    <div class="stat-value">Rp {{ number_format($pencapaian, 0, ',', '.') }}</div>
                    <div class="stat-divider"></div>
                    <div class="stat-desc">Total Pendapatan Saat Ini</div>
                </div>
            </div>

        </div>

        {{-- Info Banner --}}
        <div class="info-banner">
            <div class="info-banner-left">
                <div class="info-banner-icon"><i class="ti ti-arrow-up"></i></div>
                <div class="info-banner-text">
                    Anda telah mencapai <strong>{{ $persentase }}%</strong> dari target bulanan.
                </div>
            </div>
            <div class="info-banner-right">
                Sisa untuk mencapai target: <strong>Rp {{ number_format($sisa, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    {{-- MIDDLE 3 KOLOM --}}
    <div class="middle-row">
        {{-- Bar Chart --}}
        <div class="card-custom">
            <div class="card-title-sm">Perbandingan Target vs Pencapaian</div>
            <div style="height: 280px;">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="card-custom">
            <div class="card-title-sm">Ringkasan</div>

            <div class="ringkasan-item">
                <div class="ringkasan-icon"><i class="ti ti-target"></i></div>
                <div class="ringkasan-label">Target Bulanan</div>
                <div class="ringkasan-value">Rp {{ number_format($target, 0, ',', '.') }}</div>
            </div>
            <div class="ringkasan-item">
                <div class="ringkasan-icon"><i class="ti ti-chart-bar"></i></div>
                <div class="ringkasan-label">Pencapaian Saat Ini</div>
                <div class="ringkasan-value">Rp {{ number_format($pencapaian, 0, ',', '.') }}</div>
            </div>
            <div class="ringkasan-item">
                <div class="ringkasan-icon"><i class="ti ti-percentage"></i></div>
                <div class="ringkasan-label">Persentase Pencapaian</div>
                <div class="ringkasan-value">{{ $persentase }}%</div>
            </div>
            <div class="ringkasan-item">
                <div class="ringkasan-icon"><i class="ti ti-flag"></i></div>
                <div class="ringkasan-label">Sisa Target</div>
                <div class="ringkasan-value">Rp {{ number_format($sisa, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Gauge + Status --}}
        <div class="card-custom">
            <div class="card-title-sm">Progress Pencapaian</div>

            <div class="gauge-wrapper">
                <canvas id="gaugeChart"></canvas>
                <div class="gauge-value">{{ $persentase }}%</div>
                <div class="gauge-min">0%</div>
                <div class="gauge-max">100%</div>
            </div>

            <div class="status-box {{ $onTrack ? 'status-good' : 'status-warn' }}">
                <div class="status-icon">
                    <i class="ti ti-{{ $onTrack ? 'trending-up' : 'alert-triangle' }}"></i>
                </div>
                <div>
                    <div class="status-title">{{ $statusTitle }}</div>
                    <div class="status-desc">{{ $statusDesc }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="dashboard-footer">
        <div class="footer-icon"><i class="ti ti-star-filled"></i></div>
        Data diperbarui per {{ $lastUpdate }} WIB
    </div>
</div>
@endsection
  @php
        $user = auth()->user();
        $today = \Carbon\Carbon::now()->toDateString();
        $forceShiftModal = false;
        if($user) {
            $forceShiftModal = empty($user->shift) || $user->last_login !== $today;
        }
    @endphp

    @if ($forceShiftModal)
        <!-- Modal for Set Shift -->
        <div class="modal fade" id="setShiftModal" tabindex="-1" aria-labelledby="setShiftModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('home.update-shift') }}" id="setShiftForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="setShiftModalLabel">Pilih Shift Kerja Anda</h5>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                @if(empty($user->shift))
                                    Shift Anda belum diatur. Silakan pilih shift aktif anda sebelum melanjutkan.
                                @else
                                    Shift Anda kadaluarsa atau belum diatur hari ini. Silakan pilih shift aktif anda untuk hari ini.
                                @endif
                            </div>
                            <div class="mb-3">
                                <label for="shift" class="form-label">Shift</label>
                                <select name="shift" id="shift" class="form-select" required>
                                    <option value="">-- Pilih Shift --</option>
                                    @foreach ($listShift as $shift)
                                        <option value="{{ $shift->id }}">
                                            {{ $shift->Nama }}
                                            ({{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->JamMulai)->format('H:i') }}
                                            -
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $shift->JamSelesai)->format('H:i') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan Shift</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var setShiftModal = new bootstrap.Modal(document.getElementById('setShiftModal'));
                    setShiftModal.show();
                });
            </script>
            @if (session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        setTimeout(function() {
                            var alert = document.createElement('div');
                            alert.className = 'alert alert-success alert-dismissible fade show';
                            alert.role = 'alert';
                            alert.innerHTML = '{{ session('success') }}' +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                            document.body.appendChild(alert);
                            setTimeout(function() {
                                var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                                bsAlert.close();
                            }, 3500);
                        }, 500);
                    });
                </script>
            @endif
        @endpush
    @endif
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
$(document).ready(function() {
    Chart.register(ChartDataLabels);

    // Filter
    $('#filterBulan, #filterTahun').on('change', function() {
        const bulan = $('#filterBulan').val();
        const tahun = $('#filterTahun').val();
        window.location.href = `{{ route('dashboard.monitor') }}?bulan=${bulan}&tahun=${tahun}`;
    });

    const persentase = {{ $persentase }};

    // ===== 1. DONUT =====
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [persentase, Math.max(0, 100 - persentase)],
                backgroundColor: ['#6d28d9', '#ede9fe'],
                borderWidth: 0,
                cutout: '78%'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false }, datalabels: { display: false } }
        }
    });

    // ===== 2. BAR CHART (dengan label nilai di atas bar) =====
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: ['Target', 'Pencapaian'],
            datasets: [{
                data: [{{ $target }}, {{ $pencapaian }}],
                backgroundColor: ['#ddd6fe', '#6d28d9'],
                borderRadius: 6,
                barPercentage: 0.45
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                    }
                },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    color: '#1e1b4b',
                    font: { weight: 700, size: 12 },
                    formatter: v => 'Rp ' + v.toLocaleString('id-ID')
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6' },
                    ticks: {
                        callback: v => (v / 1000000).toFixed(0) + ' JT',
                        color: '#6b7280'
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#4b5563', font: { weight: 600 } }
                }
            }
        }
    });

    // ===== 3. GAUGE (setengah lingkaran + jarum) =====
    const needlePlugin = {
        id: 'needle',
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            const meta = chart.getDatasetMeta(0);
            if (!meta.data.length) return;

            const arc = meta.data[0];
            const cx = arc.x, cy = arc.y;
            const r = arc.outerRadius * 0.92;

            const data = chart.data.datasets[0].data;
            const total = data[0] + data[1];
            const ratio = total > 0 ? data[0] / total : 0;
            const angle = -Math.PI + Math.PI * ratio; // kiri → kanan

            ctx.save();
            ctx.beginPath();
            ctx.moveTo(cx, cy);
            ctx.lineTo(cx + r * Math.cos(angle), cy + r * Math.sin(angle));
            ctx.strokeStyle = '#6d28d9';
            ctx.lineWidth = 3;
            ctx.lineCap = 'round';
            ctx.stroke();

            ctx.beginPath();
            ctx.arc(cx, cy, 6, 0, Math.PI * 2);
            ctx.fillStyle = '#6d28d9';
            ctx.fill();
            ctx.restore();
        }
    };

    new Chart(document.getElementById('gaugeChart'), {
        type: 'doughnut',
        plugins: [needlePlugin],
        data: {
            datasets: [{
                data: [persentase, Math.max(0, 100 - persentase)],
                backgroundColor: ['#6d28d9', '#ede9fe'],
                borderWidth: 0,
                circumference: 180,
                rotation: -90,
                cutout: '72%'
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false }, datalabels: { display: false } }
        }
    });
});
</script>
@endpush
