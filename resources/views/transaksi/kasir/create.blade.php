@extends('layouts.app')

@section('content')
    <style>
        /* ===== TEAL THEME VARIABLES ===== */
        :root {
            --teal-primary: #0d9488;
            --teal-dark: #0f766e;
            --teal-darker: #115e59;
            --teal-light: #ccfbf1;
            --teal-lighter: #f0fdfa;
            --teal-accent: #14b8a6;
            --teal-text: #134e4a;
            --border-color: #d1fae5;
            --text-muted: #6b7280;
            --card-shadow: 0 1px 3px rgba(0, 0, 0, .08), 0 1px 2px rgba(0, 0, 0, .04);
        }

        /* ===== CARD ===== */
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #f3f4f6;
            border-radius: 10px 10px 0 0 !important;
            padding: 14px 20px;
        }

        .card-header h5.card-title {
            font-size: .95rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        /* ===== RINGKASAN PER SHIFT header (teal) ===== */
        .card-header-teal {
            background: var(--teal-primary) !important;
            border-radius: 10px 10px 0 0 !important;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header-teal h5 {
            color: #fff;
            font-size: .85rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            margin: 0;
        }

        .card-header-teal .icon-group {
            color: rgba(255, 255, 255, .8);
        }

        /* ===== FORM LABELS ===== */
        .form-label.fw-semibold,
        .form-label.fw-bold {
            color: #374151;
            font-size: .875rem;
        }

        label.form-label.fw-bold.text-uppercase {
            color: var(--teal-primary);
            font-size: .8rem;
            letter-spacing: .06em;
        }

        /* ===== INPUTS ===== */
        .form-control,
        .form-select {
            border-color: #d1d5db;
            border-radius: 7px;
            font-size: .875rem;
            color: #111827;
            transition: border-color .15s, box-shadow .15s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--teal-primary);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, .15);
            outline: none;
        }

        .form-control.bg-light {
            background-color: #f9fafb !important;
        }

        /* ===== RADIO BUTTONS (teal) ===== */
        .form-check-input:checked {
            background-color: var(--teal-primary);
            border-color: var(--teal-primary);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(13, 148, 136, .2);
        }

        /* ===== TABLE ===== */
        #table-perawatan {
            border-radius: 8px;
            overflow: hidden;
        }

        #table-perawatan thead th {
            background: #f9fafb;
            color: #6b7280;
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 12px;
        }

        #table-perawatan tbody td {
            padding: 8px 12px;
            vertical-align: middle;
            border-color: #f3f4f6;
            font-size: .875rem;
        }

        .btn-remove-perawatan {
            color: #ef4444 !important;
            opacity: .7;
            transition: opacity .15s;
        }

        .btn-remove-perawatan:hover {
            opacity: 1;
        }

        /* ===== JENIS PERAWATAN label ===== */
        label.fw-semibold.mb-2 {
            color: var(--teal-primary);
            font-size: .8rem;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        /* ===== TAMBAH PERAWATAN button ===== */
        #btn-tambah-perawatan {
            border-color: var(--teal-primary);
            color: var(--teal-primary);
            font-size: .82rem;
            border-radius: 7px;
            padding: 6px 14px;
            transition: background .15s, color .15s;
        }

        #btn-tambah-perawatan:hover {
            background: var(--teal-primary);
            color: #fff;
        }

        /* ===== TOTAL BIAYA CARD ===== */
        .total-biaya-card {
            background: var(--teal-lighter) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 8px;
        }

        .total-biaya-card .label-total {
            font-size: .8rem;
            color: var(--teal-primary);
            font-weight: 500;
        }

        .total-biaya-card h3 {
            color: var(--teal-primary) !important;
            font-weight: 700;
            font-size: 1.5rem;
        }

        /* ===== BIAYA ADMIN ===== */
        .biaya-admin-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-top: 1px solid #f3f4f6;
        }

        .biaya-admin-label {
            font-size: .875rem;
            color: #374151;
        }

        .biaya-admin-value {
            font-size: .875rem;
            font-weight: 600;
            color: #111827;
        }

        /* ===== CARA BAYAR section ===== */
        .cara-bayar-title {
            font-size: .8rem;
            font-weight: 700;
            color: var(--teal-primary);
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .cara-bayar-divider {
            border-color: #e5e7eb;
        }

        /* ===== TOTAL BAYAR row ===== */
        .total-bayar-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: var(--teal-lighter);
            border-radius: 7px;
            margin-top: 8px;
        }

        .total-bayar-label {
            font-size: .875rem;
            font-weight: 700;
            color: #374151;
        }

        .total-bayar-value {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--teal-primary);
        }

        /* ===== RINGKASAN values ===== */
        .ringkasan-total-label {
            font-size: .85rem;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .ringkasan-total-value {
            font-size: 1.6rem;
            font-weight: 800;
            color: #111827;
        }

        .ringkasan-stat-label {
            font-size: .82rem;
            color: #6b7280;
        }

        .ringkasan-stat-value {
            font-size: .875rem;
            font-weight: 600;
            color: #111827;
        }

        /* ===== STAFF CARD row ===== */
        .staff-icon {
            width: 32px;
            height: 32px;
            background: var(--teal-lighter);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--teal-primary);
            flex-shrink: 0;
        }

        /* ===== ACTION BUTTONS ===== */
        .btn-cancel {
            border: 1px solid #d1d5db;
            background: #fff;
            color: #374151;
            border-radius: 8px;
            padding: 10px 28px;
            font-size: .875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background .15s;
        }

        .btn-cancel:hover {
            background: #f9fafb;
        }

        .btn-save {
            background: var(--teal-primary);
            border: none;
            color: #fff;
            border-radius: 8px;
            padding: 10px 28px;
            font-size: .875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background .15s;
        }

        .btn-save:hover {
            background: var(--teal-dark);
            color: #fff;
        }

        /* ===== PRIMARY BUTTON generic ===== */
        .btn-primary {
            background-color: var(--teal-primary) !important;
            border-color: var(--teal-primary) !important;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--teal-dark) !important;
            border-color: var(--teal-dark) !important;
        }

        /* ===== PATIENT SEARCH icon ===== */
        .input-search-wrap {
            position: relative;
        }

        .input-search-wrap .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .input-search-wrap .form-control {
            padding-left: 32px;
        }
    </style>

    <div class="container-fluid">
        <!-- ROW 1 -->
        <div class="row mt-3">
            <!-- LEFT: Form Transaksi Kasir -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Transaksi Kasir</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('Transaksi.store') }}" method="POST" id="formTransaksiKasir">
                            @csrf
                            {{-- <div class="col-12">
                                <label class="form-label mt-3">Default Material Date Picker</label>
                                <input type="text" class="form-control" placeholder="2024-06-04" id="mdate" />
                            </div> --}}
                            <!-- Hari & Tanggal -->
                            <div class="col-12 mb-3">
                                <label class="form-label mt-3" for="Tanggal">Hari &amp; Tanggal</label>
                                <input type="text"
                                    class="form-control bootstrapMaterialDatePicker @error('Tanggal') is-invalid @enderror"
                                    placeholder="2024-06-04" name="Tanggal" value="{{ old('Tanggal', date('Y-m-d')) }}"
                                    id="mdate" />
                                @error('Tanggal')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>


                            <!-- Nama Pasien -->
                            <div class="mb-2">
                                <label for="nama_pasien" class="form-label fw-semibold">Nama Pasien</label>
                                <div class="input-search-wrap">
                                    <span class="search-icon">
                                        <i data-lucide="search" style="width:15px;height:15px;"></i>
                                    </span>
                                    <input type="text" id="nama_pasien" name="NamaPasien"
                                        class="form-control @error('NamaPasien') is-invalid @enderror"
                                        placeholder="Masukkan nama pasien" autocomplete="off">
                                </div>
                                @error('NamaPasien')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Jenis Pasien radio -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold mb-1">Jenis Pasien</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('JenisPasien') is-invalid @enderror"
                                        type="radio" name="JenisPasien" id="pasien_baru" value="Baru"
                                        {{ old('JenisPasien') == 'Baru' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pasien_baru">Pasien Baru</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('JenisPasien') is-invalid @enderror"
                                        type="radio" name="JenisPasien" id="pasien_lama" value="Lama"
                                        {{ old('JenisPasien') == 'Lama' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pasien_lama">Pasien Lama</label>
                                </div>
                                @error('JenisPasien')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>


                            <!-- Jenis Perawatan -->
                            <div class="mb-3">
                                <label class="fw-semibold mb-2">Jenis Perawatan</label>
                                <div class="table-responsive">
                                    <table class="table align-middle table-bordered mb-0" id="table-perawatan">
                                        <thead>
                                            <tr>
                                                <th style="width:4%;">No.</th>
                                                <th>Jenis Perawatan</th>
                                                <th style="width:26%;">Biaya Perawatan</th>
                                                <th style="width:8%;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="body-perawatan">
                                            @php
                                                $oldPerawatan = old('JenisPerawatan');
                                            @endphp
                                            @if (is_array($oldPerawatan) && count($oldPerawatan) > 0)
                                                @foreach ($oldPerawatan as $idx => $perawatan)
                                                    <tr>
                                                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <select class="form-control perawatan-select"
                                                                name="JenisPerawatan[{{ $idx }}][id]">
                                                                <option value="">Pilih Jenis Perawatan</option>
                                                                @foreach ($Perawatan as $row)
                                                                    <option value="{{ $row->id }}"
                                                                        data-harga="{{ $row->Tarif }}"
                                                                        {{ isset($perawatan['id']) && $perawatan['id'] == $row->id ? 'selected' : '' }}>
                                                                        {{ $row->Nama }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text"
                                                                class="form-control biaya-perawatan bg-light"
                                                                name="JenisPerawatan[{{ $idx }}][Biaya]"
                                                                placeholder="Rp 0"
                                                                value="{{ isset($perawatan['Biaya']) ? $perawatan['Biaya'] : '' }}">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-link btn-remove-perawatan p-1"
                                                                title="Hapus">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                                    height="18" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M3 6h18" />
                                                                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                                    <path d="M10 11v6" />
                                                                    <path d="M14 11v6" />
                                                                </svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center align-middle">1</td>
                                                    <td>
                                                        <select class="form-control perawatan-select"
                                                            name="JenisPerawatan[0][id]">
                                                            <option value="">Pilih Jenis Perawatan</option>
                                                            @foreach ($Perawatan as $row)
                                                                <option value="{{ $row->id }}"
                                                                    data-harga="{{ $row->Tarif }}">{{ $row->Nama }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control biaya-perawatan bg-light"
                                                            name="JenisPerawatan[0][Biaya]" placeholder="Rp 0">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-link btn-remove-perawatan p-1"
                                                            title="Hapus">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="18"
                                                                height="18" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M3 6h18" />
                                                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                                <path d="M10 11v6" />
                                                                <path d="M14 11v6" />
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-primary mt-3" id="btn-tambah-perawatan">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Perawatan
                                </button>
                            </div>


                            <!-- Biaya Admin -->
                            <div class="biaya-admin-row mb-3">
                                <span class="biaya-admin-label">
                                    Biaya Admin
                                    <span class="text-muted" style="font-size:.78rem;">(hanya untuk pasien baru)</span>
                                </span>
                                <div>
                                    <input type="text"
                                        class="form-control text-end @error('BiayaAdmin') is-invalid @enderror"
                                        id="biaya_admin" name="BiayaAdmin"
                                        style="width:130px;display:inline-block;background:#f9fafb;border:none;font-weight:600;color:#111827;"
                                        placeholder="Rp 0" value="{{ old('BiayaAdmin') }}" readonly>
                                </div>
                                @error('BiayaAdmin')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Total Biaya -->
                            <input type="hidden" id="total-biaya-input" name="TotalBiaya" value="0">
                            <div class="total-biaya-card card border-0 mb-3">
                                <div class="card-body py-3 px-4">
                                    <div class="label-total mb-1">
                                        Total Biaya (Perawatan + Biaya Admin<span id="info-pasien-baru"></span>)
                                    </div>
                                    <h3 class="mb-0">Rp <span id="total-biaya">0</span></h3>
                                </div>
                            </div>


                            <!-- Action buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('MasterShift.index') }}" class="btn-cancel">
                                    <i data-lucide="x" style="width:15px;height:15px;"></i> Batal
                                </a>
                                <button type="submit" class="btn-save">
                                    <i data-lucide="save" style="width:15px;height:15px;"></i> Simpan Transaksi
                                </button>
                            </div>

                    </div>
                </div>
            </div>

            <!-- RIGHT: Ringkasan + Metode Bayar + Staff -->
            <div class="col-xl-4">
                <div class="row g-3">

                    <!-- Ringkasan Per Shift -->
                    <div class="col-12">
                        <div class="card mb-0">
                            <div class="card-header-teal">
                                <h5>Ringkasan Per Shift</h5>
                                <span class="icon-group">
                                    <i data-lucide="users" style="width:20px;height:20px;"></i>
                                </span>
                            </div>
                            <div class="card-body py-3 px-4">
                                <div class="ringkasan-total-label">Total Biaya Per Shift</div>
                                <div class="ringkasan-total-value mb-3">Rp 0</div>
                                <div class="d-flex justify-content-between">
                                    <span class="ringkasan-stat-label">Total Pasien Baru Per Shift</span>
                                    <span class="ringkasan-stat-value">{{ $totalPasienBaru }} Pasien</span>
                                </div>
                                <hr class="my-2" style="border-color:#f3f4f6;">
                                <div class="d-flex justify-content-between">
                                    <span class="ringkasan-stat-label">Total Pasien Lama Per Shift</span>
                                    <span class="ringkasan-stat-value">{{ $totalPasienLama }} Pasien</span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="col-12">
                        <div class="card mb-0">
                            <div class="card-body py-3 px-4">
                                <div class="cara-bayar-title">Cara Bayar</div>
                                @foreach ($MetodePembayaran as $mp)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input @error('MetodePembayaran') is-invalid @enderror"
                                            type="radio" name="MetodePembayaran"
                                            id="metode_pembayaran_{{ $mp->id }}" value="{{ $mp->id }}"
                                            {{ old('MetodePembayaran') == $mp->id ? 'checked' : '' }}>
                                        <label class="form-check-label" for="metode_pembayaran_{{ $mp->id }}">
                                            {{ $mp->Nama }}
                                        </label>
                                    </div>
                                @endforeach
                                @error('MetodePembayaran')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror

                                <hr class="cara-bayar-divider mt-2 mb-3">

                                <div class="total-bayar-row">
                                    <span class="total-bayar-label">Total Bayar</span>
                                    <span class="total-bayar-value">Rp <span id="total-bayar">0</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Staff: Dokter / Perawat / Kasir -->
                    <div class="col-12">
                        <div class="card mb-0">
                            <div class="card-body py-3 px-4">

                                <!-- Dokter -->
                                <div class="mb-2">
                                    <label for="Dokter" class="form-label fw-bold text-uppercase">
                                        <i data-lucide="stethoscope" style="width:14px;height:14px;" class="me-1"></i>
                                        Nama Dokter
                                    </label>
                                    <select name="Dokter" id="Dokter"
                                        class="form-select staff-select @error('Dokter') is-invalid @enderror">
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach ($dokter as $d)
                                            <option value="{{ $d->id }}"
                                                {{ old('Dokter') == $d->id ? 'selected' : '' }}>{{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('Dokter')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Perawat -->
                                <div class="mb-2">
                                    <label for="Perawat" class="form-label fw-bold text-uppercase">
                                        <i data-lucide="syringe" style="width:14px;height:14px;" class="me-1"></i>
                                        Nama Perawat
                                    </label>
                                    <select name="Perawat" id="Perawat"
                                        class="form-select staff-select @error('Perawat') is-invalid @enderror">
                                        <option value="">-- Pilih Perawat --</option>
                                        @foreach ($perawat as $p)
                                            <option value="{{ $p->id }}"
                                                {{ old('Perawat') == $p->id ? 'selected' : '' }}>{{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('Perawat')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Kasir / Resepsionis -->
                                <div class="mb-2">
                                    <label for="Kasir" class="form-label fw-bold text-uppercase">
                                        <i data-lucide="user-check" style="width:14px;height:14px;" class="me-1"></i>
                                        Nama Resepsionis
                                    </label>
                                    <select name="Kasir" id="Kasir"
                                        class="form-select staff-select @error('Kasir') is-invalid @enderror">
                                        <option value="">-- Pilih Resepsionis --</option>
                                        @foreach ($kasir as $r)
                                            <option value="{{ $r->id }}"
                                                {{ old('Kasir') == $r->id ? 'selected' : '' }}>{{ $r->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('Kasir')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </form>
        </div>
    </div>


@endsection
@push('scripts')
    <script>
        let perawatanCount = 1;

        function rupiahFormat(num) {
            if (!num) return 'Rp 0';
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        function isPasienBaru() {
            return $('input[name="JenisPasien"]:checked').val() === "Baru";
        }

        function recalculateTotal() {
            let total = 0;
            $('.biaya-perawatan').each(function() {
                let val = ($(this).val() + '').replace(/[^0-9]/g, '');
                total += +val;
            });
            let admin = +($('#biaya_admin').val() + '').replace(/[^0-9]/g, '');
            if (isNaN(admin)) admin = 0;
            total += admin;

            $('#info-pasien-baru').html(isPasienBaru() ? " (Rp 50.000 untuk pasien baru)" : "");
            $('#total-biaya').text(total.toLocaleString('id-ID'));
            $('#total-bayar').text(total.toLocaleString('id-ID'));
            $('#total-biaya-input').val(total); // ← Set value ke input hidden
        }

        $('#biaya_admin').on('input', function() {
            let value = $(this).val().replace(/[^0-9]/g, '');
            if (!value) value = '0';
            $(this).val(rupiahFormat(value));
            recalculateTotal();
        });

        $('#formTransaksiKasir').on('submit', function() {
            $('#biaya_admin').val(($('#biaya_admin').val() + '').replace(/[^0-9]/g, ''));
            $('.biaya-perawatan').each(function() {
                $(this).val(($(this).val() + '').replace(/[^0-9]/g, ''));
            });
        });

        $(document).ready(function() {
            // Init select2 for Perawatan ONLY on dynamic rows
            $('.perawatan-select').select2({
                dropdownParent: $('#table-perawatan').parent()
            });
            // Init select2 for Staff (Dokter, Perawat, Kasir)
            $('.staff-select').select2({
                dropdownParent: $('.card-body:has(#Kasir)')
            });

            $('input[name="JenisPasien"]').on('change', function() {
                if (isPasienBaru()) {
                    $('#biaya_admin').prop('readonly', true).val(rupiahFormat(50000));
                } else {
                    $('#biaya_admin').val(rupiahFormat(0)).prop('readonly', true);
                }
                recalculateTotal();
            });

            let jenisPasienChecked = $('input[name="JenisPasien"]:checked').val();
            if (jenisPasienChecked === "Baru") {
                $('#biaya_admin').val(rupiahFormat(50000)).prop('readonly', true);
            } else {
                $('#biaya_admin').val(rupiahFormat(0)).prop('readonly', true);
            }
            recalculateTotal();
        });

        $('#btn-tambah-perawatan').on('click', function() {
            let idx = perawatanCount;
            let options = @json($Perawatan);
            let selectOpt = `<option value="">Pilih Jenis Perawatan</option>`;
            options.forEach(row => {
                selectOpt += `<option value="${row.id}" data-harga="${row.Tarif}">${row.Nama}</option>`;
            });

            let html = `
            <tr>
                <td class="text-center align-middle"></td>
                <td>
                    <select class="form-control perawatan-select" name="JenisPerawatan[${idx}][id]" >
                        ${selectOpt}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control biaya-perawatan bg-light" name="JenisPerawatan[${idx}][Biaya]" placeholder="Rp 0">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-link btn-remove-perawatan p-1" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                        </svg>
                    </button>
                </td>
            </tr>`;

            $('#body-perawatan').append(html);
            perawatanCount++;
            // Initialize select2 for new perawatan-select
            $('#body-perawatan tr:last .perawatan-select').select2({
                dropdownParent: $('#table-perawatan').parent()
            });
            $('#body-perawatan tr').each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
            recalculateTotal();
        });

        $('#body-perawatan').on('click', '.btn-remove-perawatan', function() {
            $(this).closest('tr').remove();
            $('#body-perawatan tr').each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
            recalculateTotal();
        });

        // When Jenis Perawatan select changes, update the biaya accordingly
        $(document).on('change', '.perawatan-select', function() {
            let harga = $(this).find('option:selected').data('harga') ?? 0;
            $(this).closest('tr').find('.biaya-perawatan').val(rupiahFormat(harga));
            recalculateTotal();
        });

        $('#body-perawatan').on('input', '.biaya-perawatan', function() {
            let val = ($(this).val() + '').replace(/[^0-9]/g, '');
            if (!val) val = '0';
            $(this).val(rupiahFormat(val));
            recalculateTotal();
        });

        // Inital setup for the first row & staff select2
        $(function() {
            $('.perawatan-select').each(function() {
                let harga = $(this).find('option:selected').data('harga') ?? 0;
                $(this).closest('tr').find('.biaya-perawatan').val(rupiahFormat(harga));
            });
            $('#body-perawatan tr').each(function(i) {
                $(this).find('td:first').text(i + 1);
            });
            let adminCurrent = $('#biaya_admin').val();
            if (adminCurrent && !adminCurrent.match(/Rp/)) {
                $('#biaya_admin').val(rupiahFormat(adminCurrent.replace(/[^0-9]/g, '')));
            }
            recalculateTotal();
        });
    </script>
@endpush
