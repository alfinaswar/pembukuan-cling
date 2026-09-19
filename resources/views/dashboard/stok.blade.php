@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #6d28d9;
        --primary-light: #ddd6fe;
        --primary-bg: #f5f3ff;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
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

    /* ===== TOP STATS ===== */
    .top-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 20px;
        border: 1px solid #f0e9ff;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(109,40,217,.1);
    }
    .stat-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        flex-shrink: 0;
    }
    .stat-icon-box.purple { background: var(--primary-bg); color: var(--primary); }
    .stat-icon-box.green { background: #d1fae5; color: var(--success); }
    .stat-icon-box.orange { background: #ffedd5; color: var(--warning); }
    .stat-icon-box.blue { background: #dbeafe; color: #3b82f6; }

    .stat-content { flex-grow: 1; }
    .stat-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 6px;
    }
    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #1e1b4b;
        letter-spacing: -.5px;
        margin-bottom: 4px;
    }
    .stat-desc {
        font-size: 12px;
        color: #6b7280;
    }

    /* ===== FILTER SECTION ===== */
    .filter-section {
        background: white;
        border-radius: 16px;
        border: 1px solid #f0e9ff;
        padding: 24px;
        margin-bottom: 24px;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }
    .filter-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 8px;
        display: block;
    }
    .filter-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .filter-chip {
        padding: 8px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        color: #4b5563;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-chip:hover {
        background: var(--primary-bg);
        border-color: var(--primary);
    }
    .filter-chip.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* ===== BEHEL STOCK GRID ===== */
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .behel-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .behel-card {
        background: white;
        border-radius: 14px;
        border: 1px solid #f0e9ff;
        padding: 16px;
        text-align: center;
        transition: all 0.2s;
    }
    .behel-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(109,40,217,.12);
    }
    .behel-name {
        font-size: 12px;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: 12px;
        line-height: 1.3;
        min-height: 32px;
    }
    .behel-image {
        width: 80px;
        height: 80px;
        background: var(--primary-bg);
        border-radius: 10px;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
    }
    .behel-stock-label {
        font-size: 11px;
        color: #6b7280;
        margin-bottom: 4px;
    }
    .behel-stock-value {
        font-size: 24px;
        font-weight: 800;
        color: #1e1b4b;
        margin-bottom: 8px;
    }
    .behel-stock-value.negative {
        color: var(--danger);
    }
    .progress-bar-bg {
        background: #ede9fe;
        height: 6px;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 6px;
    }
    .progress-bar-fill {
        height: 100%;
        background: var(--primary);
        border-radius: 3px;
        transition: width 0.3s;
    }
    .progress-bar-fill.low { background: var(--warning); }
    .progress-bar-fill.critical { background: var(--danger); }
    .behel-percentage {
        font-size: 12px;
        font-weight: 700;
        color: var(--primary);
    }

    /* ===== TABLE SECTION ===== */
    .table-section {
        background: white;
        border-radius: 16px;
        border: 1px solid #f0e9ff;
        overflow: hidden;
    }
    .table-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f0e9ff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .table-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e1b4b;
    }
    .table-search {
        position: relative;
        flex-grow: 1;
        max-width: 400px;
    }
    .table-search input {
        width: 100%;
        padding: 10px 16px 10px 40px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
    }
    .table-search input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(109,40,217,.1);
    }
    .table-search i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table thead {
        background: var(--primary-bg);
    }
    .data-table th {
        padding: 14px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .data-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f3f4f6;
        font-size: 13px;
        color: #4b5563;
    }
    .data-table tbody tr:hover {
        background: #fafafa;
    }
    .badge-shift {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .badge-pagi { background: #d1fae5; color: #059669; }
    .badge-siang { background: #ffedd5; color: #ea580c; }
    .badge-malam { background: #dbeafe; color: #2563eb; }

    .table-footer {
        padding: 16px 24px;
        border-top: 1px solid #f0e9ff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        color: #6b7280;
    }

    /* ===== INFO BANNER ===== */
    .info-banner {
        background: var(--primary-bg);
        border-radius: 12px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }
    .info-banner-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .info-banner-text {
        flex-grow: 1;
        font-size: 14px;
        color: #374151;
    }
    .info-banner-text strong {
        color: var(--primary);
        font-weight: 700;
    }

    @media (max-width: 1200px) {
        .behel-grid { grid-template-columns: repeat(4, 1fr); }
        .top-stats-grid { grid-template-columns: repeat(2, 1fr); }
        .filter-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .behel-grid { grid-template-columns: repeat(2, 1fr); }
        .top-stats-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="container-fluid py-4">
    {{-- HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">STOCK OPNAME BEHEL</h1>
            <p class="page-subtitle">Monitor ketersediaan stock behel dan transaksi inersi secara real-time</p>
        </div>
        <div class="d-flex gap-2">
            <select class="filter-select" id="filterTanggal">
                <option value="today">Hari Ini</option>
                <option value="week" selected>Minggu Ini</option>
                <option value="month">Bulan Ini</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
    </div>

    {{-- INFO BANNER --}}
    <div class="info-banner">
        <div class="info-banner-icon">
            <i class="ti ti-info-circle"></i>
        </div>
        <div class="info-banner-text">
            Stock opname diperbarui secara otomatis setiap ada transaksi inersi.
            Total transaksi bulan ini: <strong>{{ number_format($totalTransaksiBulanIni) }}</strong>
        </div>
    </div>

    {{-- TOP STATS --}}
    <div class="top-stats-grid">
        <div class="stat-card">
            <div class="stat-icon-box purple">
                <i class="ti ti-box"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Stock Behel</div>
                <div class="stat-value">{{ number_format($totalStock) }}</div>
                <div class="stat-desc">Total semua jenis behel</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-box green">
                <i class="ti ti-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Terpakai (Bulan Ini)</div>
                <div class="stat-value">{{ number_format($totalTerpakai) }}</div>
                <div class="stat-desc">Transaksi inersi behel</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-box orange">
                <i class="ti ti-package"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Sisa Stock Behel</div>
                <div class="stat-value">{{ number_format($sisaStock) }}</div>
                <div class="stat-desc">Sisa kuota stock behel</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-box blue">
                <i class="ti ti-receipt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Transaksi Hari Ini</div>
                <div class="stat-value">{{ $transaksiHariIni }}</div>
                <div class="stat-desc">Terakhir: {{ $lastTransaksiTime ? \Carbon\Carbon::parse($lastTransaksiTime)->format('H:i') : '-' }} WIB</div>
            </div>
        </div>
    </div>

   {{-- FILTER SECTION --}}
    <div class="filter-section">
        {{-- action="{{ url()->current() }}" memastikan tetap di halaman dashboard ini --}}
        <form id="filterForm" method="GET" action="{{ url()->current() }}">
            <div class="filter-grid">
                <div>
                    <label class="filter-label">Filter Tanggal</label>
                    <div class="d-flex gap-2">
                        <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="filter-select" style="flex: 1;">
                        <input type="date" name="tanggal_akhir" value="{{ $tanggalAkhir }}" class="filter-select" style="flex: 1;">
                    </div>
                </div>

                <div>
                    <label class="filter-label">Filter Cabang</label>
                    <select name="klinik" class="filter-select" style="width: 100%;">
                        {{-- Tambahkan opsi "Semua Cabang" khusus Superadmin --}}
                        @if(auth()->user()->hasRole('Superadmin'))
                            <option value="" {{ empty($kodeKlinik) ? 'selected' : '' }}>Semua Cabang</option>
                        @endif

                        @foreach($kliniks as $klinik)
                            <option value="{{ $klinik->Kode }}" {{ $kodeKlinik == $klinik->Kode ? 'selected' : '' }}>
                                {{ $klinik->Nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="filter-label">Filter Jenis Behel</label>
                    <div class="filter-chips">
                        <button type="button" class="filter-chip active" data-filter="all">
                            <i class="ti ti-layout-grid me-1"></i> Semua Behel
                        </button>
                        @foreach($behelTypes->take(5) as $type)
                            <button type="button" class="filter-chip" data-filter="{{ $type->id }}">
                                {{ Str::limit($type->NamaBarang, 15) }}
                            </button>
                        @endforeach
                    </div>
                    {{-- Hidden input untuk menyimpan filter behel yang dipilih agar ikut terkirim --}}
                    <input type="hidden" name="filter_behel" id="hiddenFilterBehel" value="all">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary" style="background: var(--primary); border: none; padding: 10px 24px; border-radius: 10px; font-weight: 600;">
                    <i class="ti ti-filter me-2"></i>Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- BEHEL STOCK GRID --}}
    <div class="section-title">
        <span>Ringkasan Stock Behel</span>
        <a href="#" class="btn btn-sm" style="color: var(--primary); text-decoration: none; font-weight: 600;">
            <i class="ti ti-list-details me-1"></i> Lihat Semua Detail Stock
        </a>
    </div>

    <div class="behel-grid">
        @forelse($stockPerType as $item)
            <div class="behel-card" data-behel-id="{{ $item['barang']->id }}">
                <div class="behel-name">{{ $item['barang']->NamaBarang }}</div>
                <div class="behel-image">
                    <i class="ti ti-braces" style="color: var(--primary);"></i>
                </div>
                <div class="behel-stock-label">Sisa Stock</div>
                <div class="behel-stock-value {{ $item['isNegative'] ? 'negative' : '' }}">
                    {{ $item['stok'] }}
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill {{ $item['percentage'] < 20 ? 'critical' : ($item['percentage'] < 50 ? 'low' : '') }}"
                         style="width: {{ $item['percentage'] }}%"></div>
                </div>
                <div class="behel-percentage">{{ $item['percentage'] }}%</div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="ti ti-database-off" style="font-size: 48px; opacity: 0.3;"></i>
                <p class="mt-3">Tidak ada data jenis behel ditemukan</p>
            </div>
        @endforelse
    </div>

    {{-- TABLE SECTION --}}
    <div class="table-section">
        <div class="table-header">
            <div class="table-title">Detail Transaksi Inersi Behel (Stock Berkurang)</div>
            <div class="table-search">
                <i class="ti ti-search"></i>
                <input type="text" id="searchTable" placeholder="Cari nama pasien / jenis behel / dokter...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal Transaksi</th>
                        <th>Jenis Perawatan</th>
                        <th>Nama Pasien</th>
                        <th>Behel Dipakai</th>
                        <th>Dokter</th>
                        <th>Perawat</th>
                        <th>Resepsionis</th>
                        <th>Shift</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        @php
                            $detail = $trx->TransaksiDetail->first();
                            $jenisPerawatan = $detail->masterJenisPerawatan->Nama ?? '-';
                            $shiftClass = match(strtolower($trx->Shift ?? '')) {
                                'pagi' => 'badge-pagi',
                                'siang' => 'badge-siang',
                                'malam' => 'badge-malam',
                                default => ''
                            };
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($trx->Tanggal)->format('d M Y H:i') }}</td>
                            <td>{{ $jenisPerawatan }}</td>
                            <td><strong>{{ $trx->NamaPasien }}</strong></td>
                            <td>Behel</td>
                            <td>{{ $trx->getDokter->name ?? '-' }}</td>
                            <td>{{ $trx->getPerawat->name ?? '-' }}</td>
                            <td>{{ $trx->getResepsionis->name ?? '-' }}</td>
                            <td><span class="badge-shift {{ $shiftClass }}">{{ ucfirst($trx->Shift ?? '-') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="ti ti-inbox" style="font-size: 32px; opacity: 0.3;"></i>
                                <p class="mt-3 mb-0">Tidak ada transaksi pada periode ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Filter chip interaction
    $('.filter-chip').on('click', function() {
        $('.filter-chip').removeClass('active');
        $(this).addClass('active');

        const filter = $(this).data('filter');
        if (filter === 'all') {
            $('.behel-card').show();
        } else {
            $('.behel-card').each(function() {
                if ($(this).data('behel-id') == filter) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });

    // Table search
    $('#searchTable').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.data-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Date filter change
    $('#filterTanggal').on('change', function() {
        const val = $(this).val();
        if (val === 'today') {
            const today = new Date().toISOString().split('T')[0];
            $('[name="tanggal_mulai"]').val(today);
            $('[name="tanggal_akhir"]').val(today);
        } else if (val === 'week') {
            // Simple week logic
            const today = new Date();
            const weekStart = new Date(today.setDate(today.getDate() - today.getDay()));
            $('[name="tanggal_mulai"]').val(weekStart.toISOString().split('T')[0]);
            $('[name="tanggal_akhir"]').val(new Date().toISOString().split('T')[0]);
        } else if (val === 'month') {
            const today = new Date();
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            $('[name="tanggal_mulai"]').val(monthStart.toISOString().split('T')[0]);
            $('[name="tanggal_akhir"]').val(new Date().toISOString().split('T')[0]);
        }
    });
});
</script>
@endpush
