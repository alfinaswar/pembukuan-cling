@extends('layouts.app')

@section('content')
    <style>
        .dashboard-card-bg-primary {
            background: rgba(13, 110, 253, 0.18) !important;
        }

        .dashboard-card-bg-info {
            background: rgba(13, 202, 240, 0.18) !important;
        }

        .dashboard-card-bg-success {
            background: rgba(25, 135, 84, 0.18) !important;
        }

        .dashboard-card-bg-warning {
            background: rgba(255, 193, 7, 0.18) !important;
        }

        .dashboard-card-bg-danger {
            background: rgba(220, 53, 69, 0.18) !important;
        }

        .dashboard-card-bg-secondary {
            background: rgba(108, 117, 125, 0.18) !important;
        }
    </style>
    <style>
        /* Custom for 5 even cols on large and above */
        @media (min-width: 992px) {
            .col-lg-2-4 {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }
    </style>
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <h3 class="mb-0 fw-semibold">Dashboard Laporan Dokter</h3>
            <small class="text-muted">Ringkasan pencapaian dan aktivitas dokter dalam periode terpilih</small>
        </div>



    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body pb-2">
                    <form id="perawatFilterForm" class="row align-items-end g-2" method="POST"
                        action="{{ route('laporan-dokter.store') }}" style="font-size: 0.925rem;">
                        @csrf
                        @php
                            $user = Auth::user();
                            $dokter_selected = old(
                                'dokter',
                                request(
                                    'dokter',
                                    $user && !$user->hasRole(['Superadmin', 'Management']) ? $user->id : '',
                                ),
                            );
                            $isSuperadminOrManagement =
                                $user && ($user->hasRole('Superadmin') || $user->hasRole('Management'));
                        @endphp
                        <div class="col-md-3">
                            <label for="perawatSelect" class="form-label mb-1" style="font-size: 0.95em;">
                                Pilih Dokter
                            </label>
                            <div class="input-group mb-2">
                                <select id="perawatSelect" name="dokter" class="select2 form-control"
                                    style="width:100%; font-size: 0.96em; min-height:36px; {{ !$isSuperadminOrManagement ? 'pointer-events: none; background: #f3f3f3;' : '' }}"
                                    {{ !$isSuperadminOrManagement ? 'tabindex=-1 aria-disabled=true' : '' }}>
                                    <option value="">Pilih Dokter</option>
                                    @foreach ($dokter as $d)
                                        <option value="{{ $d->id }}"
                                            {{ $dokter_selected == $d->id ? 'selected' : '' }}>
                                            {{ $d->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Tambahan Pilih Klinik (Khusus Superadmin/Management) -->
                        @php
                            // Ambil nilai yang terpilih dari old() -> request() -> kosong
                            $klinik_selected = old('KodeCabang', request('KodeCabang', ''));
                        @endphp
                        @if ($isSuperadminOrManagement)
                            <div class="col-md-3">
                                <label for="klinikSelect" class="form-label mb-1" style="font-size: 0.95em;">Pilih
                                    Klinik</label>
                                <div class="input-group mb-2">
                                    <select id="klinikSelect" name="KodeCabang" class="select2 form-control"
                                        style="width:100%; font-size:0.96em; min-height:36px;">
                                        <option value="">Semua Klinik</option>
                                        @if (isset($klinik) && count($klinik) > 0)
                                            @foreach ($klinik as $item)
                                                <option value="{{ $item->Kode }}"
                                                    {{ $klinik_selected == $item->Kode ? 'selected' : '' }}>
                                                    {{ $item->Nama }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        @endif


                        <!-- 2. Pilih Periode -->
                        <div class="col-md-3">
                            <label for="periodeInput" class="form-label mb-1" style="font-size: 0.95em;">Pilih
                                Periode</label>
                            <div class="input-group mb-2">
                                <input type="text" id="periodeInput" name="FilterTanggal" class="form-control daterange"
                                    style="font-size:0.96em; min-height:36px;" value="{{ request('FilterTanggal') }}"
                                    autocomplete="off" />
                                <span class="input-group-text" style="font-size: 1em;">
                                    <i class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>

                        <!-- 3. Pilih Shift (TAMBAHAN BARU) -->
                        <div class="col-md-3">
                            <label for="shiftSelect" class="form-label mb-1" style="font-size: 0.95em;">Pilih Shift</label>
                            <div class="input-group mb-2">
                                <select id="shiftSelect" name="shift" class="form-select"
                                    style="font-size:0.96em; min-height:36px;">
                                    <option value="">Semua Shift</option>
                                    @if (isset($shift) && count($shift) > 0)
                                        @foreach ($shift as $s)
                                            <option value="{{ $s->id }}"
                                                {{ request('shift') == $s->id ? 'selected' : '' }}>
                                                {{ $s->Nama }}
                                                @if ($s->JamMulai && $s->JamSelesai)
                                                    ({{ $s->JamMulai }} - {{ $s->JamSelesai }})
                                                @endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-end mt-1 gap-2">
                            <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.96em;">
                                <i class="fa fa-filter"></i> Tampilkan
                            </button>
                            <a href="{{ route('laporan-dokter.download-excel', array_merge(request()->all(), ['export' => 'excel'])) }}"
                                class="btn btn-sm" style="font-size: 0.96em; background-color: #27ae60; color: #fff;">
                                <i class="fa fa-file-excel"></i> Export
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mb-3">
        <!-- 1. Total Pasien Baru -->
        <div class="col-6 col-sm-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px; background: #e7e1ff; border-radius: 50%; min-width: 48px;">
                        <i class="ti ti-user-plus" style="color: #7367f0; font-size: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #868686; margin-bottom: 2px;">Total Pasien Baru</div>
                        <div style="font-size: 22px; font-weight: 700; color: #333;">{{ $TotalPasienBaru ?? '0' }}</div>
                        <div style="font-size: 12px; color: #484848;">Pasien</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Total Pasien Lama -->
        <div class="col-6 col-sm-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px; background: #e4f4e8; border-radius: 50%; min-width: 48px;">
                        <i class="ti ti-users" style="color: #28c76f; font-size: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #868686; margin-bottom: 2px;">Total Pasien Lama</div>
                        <div style="font-size: 22px; font-weight: 700; color: #333;">{{ $TotalPasienLama ?? '0' }}</div>
                        <div style="font-size: 12px; color: #484848;">Pasien</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Total Pasien (1 Shift) -->
        <div class="col-6 col-sm-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px; background: #e1f3ff; border-radius: 50%; min-width: 48px;">
                        <i class="ti ti-calendar" style="color: #00cfe8; font-size: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #868686; margin-bottom: 2px;">Total Pasien (1 Shift)</div>
                        <div style="font-size: 22px; font-weight: 700; color: #333;">{{ $TotalPasien ?? '0' }}</div>
                        <div style="font-size: 12px; color: #484848;">Pasien</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Total Perawatan -->
        <div class="col-6 col-sm-4 col-lg-2">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px; background: #fff0e0; border-radius: 50%; min-width: 48px;">
                        <i class="ti ti-tooth" style="color: #ff9f43; font-size: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #868686; margin-bottom: 2px;">Total Perawatan</div>
                        <div style="font-size: 22px; font-weight: 700; color: #333;">{{ $TotalPerawatan ?? '0' }}</div>
                        <div style="font-size: 12px; color: #484848;">Perawatan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Total Biaya (Perawatan + Admin) -->
        <div class="col-6 col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 text-white"
                style="border-radius: 12px; background: linear-gradient(135deg, #3958fd 0%, #4f46e5 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 10px; min-width: 48px;">
                        <i class="ti ti-wallet" style="color: #fff; font-size: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 12px; opacity: 0.9; margin-bottom: 2px;">Total Biaya (Perawatan + Admin)
                        </div>
                        <div style="font-size: 18px; font-weight: 700;">
                            {{ 'Rp ' . number_format($TotalBiayaPerawatan ?? 0, 0, ',', '.') }}</div>

                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Total Biaya (1 Shift) -->
        <div class="col-6 col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 text-white"
                style="border-radius: 12px; background: linear-gradient(135deg, #2dce89 0%, #00b894 100%);">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3 d-flex align-items-center justify-content-center"
                        style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 10px; min-width: 48px;">
                        <i class="ti ti-moneybag" style="color: #fff; font-size: 24px;"></i>
                    </div>
                    <div>
                        <div style="font-size: 12px; opacity: 0.9; margin-bottom: 2px;">Total Biaya (1 Shift)</div>
                        <div style="font-size: 18px; font-weight: 700;">
                            {{ 'Rp ' . number_format($TotalBiayaPerawatan ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Detail Perawatan Pasien -->
        <div class="col-8">
            <div class="datatables">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0 fw-semibold">Detail Perawatan Pasien</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="zero_config" class="table table-striped table-bordered  align-middle"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="fw-semibold text-center">No.</th>
                                        <th class="fw-semibold text-center">Waktu<br><span
                                                style="font-weight:400;">Tanggal</span></th>
                                        <th class="fw-semibold text-center">Nama Pasien<br><span
                                                style="font-weight:400;">Jenis Pasien<br>No RM</span></th>
                                        <th class="fw-semibold text-center">Jenis Perawatan</th>
                                        <th class="fw-semibold text-center">Biaya Per Jenis
                                            Perawatan
                                        </th>
                                        <th class="fw-semibold text-center">Biaya Admin<br><span
                                                style="font-weight:400;">Per Pasien</span></th>
                                        <th class="fw-semibold text-center">Total Per
                                            Pasien<br><span style="font-weight:400;">(Perawatan + Admin)</span></th>
                                        <th class="fw-semibold text-center">Perawat</th>
                                        <th class="fw-semibold text-center">Resepsionis</th>

                                    </tr>

                                </thead>
                                <tbody>
                                    {{-- Example static data, replace with @foreach for dynamic content --}}
                                    @if (isset($dataTransaksi) && count($dataTransaksi))
                                        @foreach ($dataTransaksi as $i => $transaksi)
                                            <tr>
                                                <td class="text-center">{{ $i + 1 }}</td>
                                                <td class="text-center" style="white-space:nowrap;">
                                                    {{ optional($transaksi->created_at)->format('H:i') ?? '-' }}<br>
                                                    <span style="font-size:12px; color:#868686;">
                                                        {{ optional($transaksi->created_at)->format('d M Y') ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <span style="font-weight:600; color:#222;">
                                                            {{ $transaksi->NamaPasien ?? '-' }}
                                                        </span><br>
                                                        <span style="color:#27ae60; font-size:13px; font-weight:600;">
                                                            {{ strtoupper($transaksi->JenisPasien ?? '-') }}
                                                        </span><br>
                                                        <span style="font-size:12px; color:#484848;">
                                                            {{ $transaksi->NoRM ?? '-' }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <ol class="mb-0 ps-3" style="font-size:13px;">
                                                        @if ($transaksi->TransaksiDetail && count($transaksi->TransaksiDetail))
                                                            @foreach ($transaksi->TransaksiDetail as $detail)
                                                                <li>{{ $detail->MasterJenisPerawatan->Nama ?? '-' }}</li>
                                                            @endforeach
                                                        @else
                                                            <li>-</li>
                                                        @endif
                                                    </ol>
                                                </td>
                                                <td>
                                                    <div style="font-size:13px;">
                                                        @if ($transaksi->TransaksiDetail && count($transaksi->TransaksiDetail))
                                                            @foreach ($transaksi->TransaksiDetail as $detail)
                                                                <div>
                                                                    Rp
                                                                    {{ number_format($detail->Biaya ?? 0, 0, ',', '.') }}
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div>-</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center" style="font-size:13px;">
                                                    Rp {{ number_format($transaksi->BiayaAdmin ?? 0, 0, ',', '.') }}
                                                </td>
                                                <td class="fw-bold text-primary text-center" style="font-size:15px;">
                                                    Rp {{ number_format($transaksi->TotalBayar ?? 0, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $transaksi->getPerawat->name ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $transaksi->getResepsionis->name ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif



                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Right Sidebar -->
        <div class="col-4">
            <!-- Filter Cepat -->
            <div class="card shadow-sm mb-4" style="border-radius: 14px;">
                <div class="card-body p-4" style="border-radius: 14px;">
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <i class="ti ti-filter fs-4" style="color:#5641f5"></i>
                        <span class="fw-semibold" style="font-size:17px; color:#5641f5;">Filter Cepat</span>
                    </div>
                    <form>
                        <div class="mb-3">
                            <label for="jenisPerawatan" class="form-label"
                                style="color:#868686;font-weight:500;font-size:14px;">Pilih Jenis Perawatan</label>
                            <select id="jenisPerawatan" class="form-select"
                                style="border-radius:8px; font-size:14px; color:#4f4f4f;">
                                <option selected>Semua Jenis</option>
                                {{-- Add option list dynamically --}}
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block mb-2"
                                style="color:#868686;font-weight:500;font-size:14px;">Pilih
                                Jenis Pasien</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn"
                                    style="background:#5641f5; color:white; font-weight:600; border-radius:8px; min-width:70px; font-size:14px;">Semua</button>
                                <button type="button" class="btn"
                                    style="background:#e8f7f1; color:#26c17e; font-weight:600; border-radius:8px; min-width:70px;font-size:14px;">Baru</button>
                                <button type="button" class="btn"
                                    style="background:#eaefff; color:#4665d6; font-weight:600; border-radius:8px; min-width:70px; font-size:14px;">Lama</button>
                            </div>
                        </div>

                        <div class="mb-3 mt-4">
                            <button type="submit" class="btn w-100"
                                style="background: #5641f5; color: white; font-weight:600; border-radius:8px; font-size:15px; padding:10px 0;">Terapkan
                                Filter</button>
                        </div>
                        <div>
                            <button type="reset" class="btn w-100"
                                style="background: #fff; color: #5641f5; border:1px solid #ececec; font-weight:600; border-radius:8px; font-size:15px; padding:10px 0;">Reset
                                Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Rincian Biaya per Jenis Perawatan -->
            <div class="card shadow-sm mb-4" style="border-radius: 14px;">
                <div class="card-body p-4" style="border-radius: 14px;">
                    <div class="mb-3">
                        <span class="fw-semibold" style="font-size:17px; color:#333;">Rincian Biaya per Jenis
                            Perawatan</span>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <!-- Scaling Gigi -->
                        @if (isset($RincianJenisPerawatan) && count($RincianJenisPerawatan) > 0)
                            @php
                                $colorVariants = [
                                    ['bg' => '#e8f7f1', 'icon' => '#26c17e'],
                                    ['bg' => '#eaefff', 'icon' => '#4665d6'],
                                    ['bg' => '#fdf6e5', 'icon' => '#faa43a'],
                                    ['bg' => '#ffeaea', 'icon' => '#eb5757'],
                                    ['bg' => '#eaf2ff', 'icon' => '#5688fd'],
                                    ['bg' => '#f5e6ff', 'icon' => '#a259fd'],
                                ];
                                $i = 0;
                            @endphp
                            @foreach ($RincianJenisPerawatan as $item)
                                @php
                                    $color = $colorVariants[$i % count($colorVariants)];
                                    $i++;
                                @endphp
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div
                                            style="width:32px; height:32px; background:{{ $color['bg'] }}; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                                            <i class="ti ti-tooth"
                                                style="color:{{ $color['icon'] }}; font-size:18px;"></i>
                                        </div>
                                        <span
                                            style="font-size:14px; color:#4f4f4f;">{{ $item->MasterJenisPerawatan->Nama ?? '-' }}</span>
                                    </div>
                                    <div class="text-end">
                                        <div style="font-size:13px; color:#333; font-weight:600;">
                                            Rp {{ number_format($item->rata_rata_biaya ?? 0, 0, ',', '.') }}
                                        </div>
                                        <div style="font-size:12px; color:#868686;">
                                            {{ $item->jumlah ?? 0 }}x
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4" style="color:#868686; font-size:14px;">
                                Data tidak ada
                            </div>
                        @endif





                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="d-flex flex-wrap align-items-stretch">

                    <!-- Ringkasan 1 Shift (Left Block) -->
                    <div class="d-flex align-items-center px-4 py-3"
                        style="background: linear-gradient(90deg, #3958fd 0%, #6d7ff8 100%); color: #fff; min-width: 260px;">
                        <div class="me-3">
                            <div
                                style="background: rgba(255,255,255,0.2); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-calendar-event" style="font-size: 24px; color: #fff;"></i>
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 500; opacity: 0.9;">Ringkasan 1 Shift</div>
                            <div style="font-size: 16px; font-weight: 700;">{{ $Hari ?? '-' }}</div>
                            <div style="font-size: 13px; opacity: 0.8;">{{ $NamaDokter->name ?? '-' }}</div>
                        </div>

                    </div>

                    <!-- Data Counters (Middle Section) -->
                    <div class="flex-grow-1 d-flex align-items-center justify-content-around px-3 py-2"
                        style="background: #fff;">

                        <!-- Item 1: Total Pasien Baru -->
                        <div class="text-center px-2" style="border-right: 1px solid #f0f0f0; min-width: 110px;">
                            <div style="font-size: 12px; color: #868686; margin-bottom: 4px;">Total Pasien Baru</div>
                            <div style="font-size: 22px; font-weight: 700; color: #27ae60;">
                                {{ $TotalPasienBaru ?? 0 }}
                            </div>
                            <div style="font-size: 12px; color: #484848;">Pasien</div>
                        </div>

                        <!-- Item 2: Total Pasien Lama -->
                        <div class="text-center px-2" style="border-right: 1px solid #f0f0f0; min-width: 110px;">
                            <div style="font-size: 12px; color: #868686; margin-bottom: 4px;">Total Pasien Lama</div>
                            <div style="font-size: 22px; font-weight: 700; color: #27ae60;">
                                {{ $TotalPasienLama ?? 0 }}
                            </div>
                            <div style="font-size: 12px; color: #484848;">Pasien</div>
                        </div>

                        <!-- Item 3: Total Pasien (1 Shift) -->
                        <div class="text-center px-2" style="border-right: 1px solid #f0f0f0; min-width: 110px;">
                            <div style="font-size: 12px; color: #868686; margin-bottom: 4px;">Total Pasien (1 Shift)
                            </div>
                            <div style="font-size: 22px; font-weight: 700; color: #333;">
                                {{ $TotalPasien ?? 0 }}
                            </div>
                            <div style="font-size: 12px; color: #484848;">Pasien</div>
                        </div>

                        <!-- Item 4: Total Perawatan -->
                        <div class="text-center px-2" style="border-right: 1px solid #f0f0f0; min-width: 110px;">
                            <div style="font-size: 12px; color: #868686; margin-bottom: 4px;">Total Perawatan</div>
                            <div style="font-size: 22px; font-weight: 700; color: #333;">
                                {{ $TotalPerawatan ?? 0 }}
                            </div>
                            <div style="font-size: 12px; color: #484848;">Perawatan</div>
                        </div>

                        <!-- Item 5: Total Biaya Perawatan -->
                        <div class="text-center px-2" style="border-right: 1px solid #f0f0f0; min-width: 140px;">
                            <div style="font-size: 12px; color: #868686; margin-bottom: 4px;">Total Biaya
                                Perawatan<br>(Semua Pasien)</div>
                            <div style="font-size: 15px; font-weight: 700; color: #333;">
                                Rp {{ number_format($TotalBiayaPerawatan ?? 0, 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Item 6: Total Biaya Admin -->
                        <div class="text-center px-2" style="min-width: 120px;">
                            <div style="font-size: 12px; color: #868686; margin-bottom: 4px;">Total Biaya
                                Admin<br>(Semua
                                Pasien)</div>
                            <div style="font-size: 15px; font-weight: 700; color: #6d7ff8;">
                                Rp {{ number_format($TotalBiayaAdmin ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>


                    <!-- Grand Total (Right Block) -->
                    <div class="d-flex align-items-center px-4 py-3 text-white"
                        style="background: linear-gradient(135deg, #3958fd 0%, #00b894 100%); min-width: 220px;">
                        <div class="w-100 text-center">
                            <div style="font-size: 13px; font-weight: 500; opacity: 0.9; margin-bottom: 5px;">TOTAL
                                BIAYA
                                (1 SHIFT)</div>
                            <div style="font-size: 24px; font-weight: 700;">Rp
                                {{ number_format($TotalBiayaPerawatan ?? 0, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Perawat yang Bertugas -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; background: #f3f0ff; border-radius: 10px;">
                            <i class="ti ti-user-shield" style="color: #6d7ff8; font-size: 22px;"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold" style="color: #27ae60; font-size: 16px;">Perawat yang Bertugas
                        </h6>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        @if (isset($PerawatBertugas) && count($PerawatBertugas) > 0)
                            @foreach ($PerawatBertugas as $perawat)
                                <div class="d-flex align-items-center px-3 py-2"
                                    style="background: #f8f9fa; border-radius: 20px; border: 1px solid #e9ecef;">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(isset($perawat->name) ? $perawat->name : 'Perawat') }}&background=random"
                                        alt="{{ isset($perawat->name) ? $perawat->name : 'Perawat' }}"
                                        class="rounded-circle me-2" style="width: 28px; height: 28px; object-fit: cover;">
                                    <span
                                        style="font-size: 13px; color: #333; font-weight: 500;">{{ isset($perawat->name) ? $perawat->name : '-' }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-muted" style="font-size: 13px;">Tidak ada perawat yang bertugas pada shift
                                dan tanggal ini.</div>
                        @endif


                    </div>
                </div>
            </div>
        </div>


        <!-- Resepsionis yang Bertugas -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3 d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; background: #e8f7f1; border-radius: 10px;">
                            <i class="ti ti-user-circle" style="color: #26c17e; font-size: 22px;"></i>
                        </div>
                        <h6 class="mb-0 fw-semibold" style="color: #27ae60; font-size: 16px;">
                            Resepsionis yang Bertugas
                        </h6>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        @if (isset($ResepsionisBertugas) && count($ResepsionisBertugas) > 0)
                            @foreach ($ResepsionisBertugas as $resepsionis)
                                <div class="d-flex align-items-center px-3 py-2"
                                    style="background: #f8f9fa; border-radius: 20px; border: 1px solid #e9ecef;">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(isset($resepsionis->name) ? $resepsionis->name : 'Resepsionis') }}&background=random"
                                        alt="{{ isset($resepsionis->name) ? $resepsionis->name : 'Resepsionis' }}"
                                        class="rounded-circle me-2" style="width: 28px; height: 28px; object-fit: cover;">
                                    <span style="font-size: 13px; color: #333; font-weight: 500;">
                                        {{ isset($resepsionis->name) ? $resepsionis->name : '-' }}
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-muted" style="font-size: 13px;">
                                Tidak ada resepsionis yang bertugas pada shift dan tanggal ini.
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    @if (session('fail_message'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: `{!! session('fail_message') !!}`,
                confirmButtonColor: '#665be7'
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: `{!! session('error') !!}`,
                confirmButtonColor: '#665be7'
            });
        </script>
    @endif
@endpush
