@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-list me-2 text-primary"></i>Detail Billing Minimal Perawat
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0)" class="text-decoration-none text-reset">Laporan</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Billing Minimal Perawat</li>
                </ol>
            </nav>
        </div>

        <!-- Filter Info Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-secondary"
                    style="background:#f4f6fb; color:#162878; border-color:#d6e6fa; font-size:15px;">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <div><strong>Periode:</strong> {{ $FilterTanggal ?? '-' }}</div>
                        @if (!empty($shift))
                            <div>
                                <strong>Shift:</strong>
                                @if ($shift == 1)
                                    1 (Pagi)
                                @elseif ($shift == 2)
                                    2 (Siang)
                                @else
                                    {{ $shift }}
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <!-- End Filter Info Section -->

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-semibold" style="color: #162878;">
                            <i class="ti ti-cash me-2"></i>Daftar Transaksi Billing Minimal (≥ Rp 1.000.000)
                        </h5>
                        <button type="button" onclick="window.close();"
                            class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                            <i class="ti ti-x"></i>
                            Tutup Tab
                        </button>

                    </div>
                    <div class="card-body pb-2">
                        <div class="alert alert-info mb-4"
                            style="background: #f5f8ff; color: #1a438b; border-color: #dbeafc;">
                            Berikut adalah daftar transaksi insentif perawat dengan billing minimal <b>Rp1.000.000</b> per
                            transaksi<br>
                            pada tanggal dan shift terpilih. Data ditampilkan per transaksi (bukan agregat perawat).
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-center" style="color: #162878; width:40px;">#</th>
                                        <th class="fw-semibold" style="color: #162878;">Waktu Transaksi</th>
                                        <th class="fw-semibold" style="color: #162878;">Nama Perawat</th>
                                        <th class="fw-semibold" style="color: #162878;">Nama Pasien</th>
                                        <th class="fw-semibold" style="color: #162878;">Total Billing</th>
                                        <th class="fw-semibold" style="color: #162878;">Insentif</th>
                                        <th class="fw-semibold" style="color: #162878;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($billingByPerawat as $index => $item)
                                        <tr>
                                            <td class="text-center" style="color: #162878;">{{ $index + 1 }}</td>
                                            <td style="color: #162878;">
                                                {{ isset($item->Tanggal) ? \Carbon\Carbon::parse($item->Tanggal)->format('d F Y') : '-' }}

                                            </td>
                                            <td style="color: #162878;">
                                                {{ $item->getUser->name ?? '-' }}
                                            </td>
                                            <td style="color: #162878;">
                                                {{ $item->getTransaksi->NamaPasien ?? '-' }}
                                            </td>
                                            <td style="color: #162878;">
                                                Rp
                                                {{ isset($item->getTransaksi->TotalBayar) ? number_format($item->getTransaksi->TotalBayar, 0, ',', '.') : '0' }}
                                            </td>
                                            <td class="fw-semibold" style="color: #376ede;">
                                                Rp
                                                {{ isset($item->Nominal) ? number_format($item->Nominal, 0, ',', '.') : '0' }}
                                            </td>
                                            <td style="color: #476684;">
                                                {{ $item->Keterangan ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted" style="color: #162878;">Tidak
                                                ada data transaksi billing minimal ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="mt-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="fw-semibold" style="font-size: 15px; color: #376ede;">
                                Total Transaksi: {{ $billingByPerawat->count() }}
                            </div>
                            <div class="fw-semibold" style="font-size: 15px; color: #376ede;">
                                Total Insentif:
                                <span style="font-size:18px;">
                                    Rp {{ number_format($billingByPerawat->sum('Nominal'), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
