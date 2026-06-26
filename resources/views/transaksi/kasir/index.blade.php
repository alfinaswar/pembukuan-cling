@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="datatables">
            <div class="card">
                <div class="card-header bg-teal-primary text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-currency-dollar me-2"></i>
                        Kasir - Daftar Transaksi
                    </h5>
                    @if (auth()->user() && (auth()->user()->hasRole('Superadmin') || auth()->user()->hasRole('Kasir / Resepsionis')))
                        <a href="{{ route('Transaksi.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Transaksi Baru
                        </a>
                    @endif

                </div>
                <div class="card-body">

                    {{-- 🔹 FILTER TANGGAL + KLINIK --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Tanggal Mulai</label>
                            <input type="date" id="filter_tanggal_mulai" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Tanggal Akhir</label>
                            <input type="date" id="filter_tanggal_akhir" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Shift</label>
                            <select id="filter_shift" class="form-control form-control-sm">
                                <option value="">Semua Shift</option>
                                @foreach ($shift as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->Nama }}
                                        ({{ \Carbon\Carbon::createFromFormat('H:i:s', $item->JamMulai)->format('H:i') }} -
                                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $item->JamSelesai)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- @php
                            dd(auth()->user()->getRoleNames()->toArray());
                        @endphp --}}
                        @if (auth()->user() && in_array('Superadmin', auth()->user()->getRoleNames()->toArray()))
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Klinik</label>
                                <select id="filter_klinik" class="form-control form-control-sm">
                                    <option value="">Semua Klinik</option>
                                    @foreach ($klinik as $item)
                                        <option value="{{ $item->Kode }}">{{ $item->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                            <button id="btnFilter" class="btn btn-primary btn-sm">
                                <i class="ti ti-filter me-1"></i> Filter
                            </button>
                            <button id="btnReset" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-refresh me-1"></i> Reset
                            </button>
                        </div>
                    </div>


                    {{-- END FILTER --}}
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="avatar bg-success text-white rounded-circle">
                                                <i class="ti ti-wallet"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="text-muted small mb-1">Total Omset</div>
                                            <div id="sum_omset" class="fw-bold fs-5">Rp 0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="avatar bg-primary text-white rounded-circle">
                                                <i class="ti ti-user-plus"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="text-muted small mb-1">Total Pasien Baru</div>
                                            <div id="sum_pasien_baru" class="fw-bold fs-5">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="avatar bg-info text-white rounded-circle">
                                                <i class="ti ti-user-check"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="text-muted small mb-1">Total Pasien Lama</div>
                                            <div id="sum_pasien_lama" class="fw-bold fs-5">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="avatar bg-secondary text-white rounded-circle">
                                                <i class="ti ti-users"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="text-muted small mb-1">Total Pasien</div>
                                            <div id="sum_pasien_total" class="fw-bold fs-5">0</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="table-responsive">
                        <table id="transaksiKasirTable" class="table table-striped table-bordered align-middle"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Pasien</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Layanan</th>
                                    <th>Total Bayar</th>
                                    <th>Petugas</th>
                                    <th>Shift</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if (Session::get('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ Session::get('success') }}',
                iconColor: '#4BCC1F',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#4BCC1F',
            });
        </script>
    @endif

    <script>
        $(function() {
            // Set default value input date = hari ini
            const today = new Date().toISOString().split('T')[0];
            $('#filter_tanggal_mulai, #filter_tanggal_akhir').val(today);

            // Fungsi reload DataTable dengan parameter filter
            function reloadTable() {
                const mulai = $('#filter_tanggal_mulai').val();
                const akhir = $('#filter_tanggal_akhir').val();
                const shift = $('#filter_shift').val();
                const klinik = $('#filter_klinik').val();
                $('#transaksiKasirTable').DataTable().ajax.reload();
            }

            // Event klik tombol Filter
            $('#btnFilter').on('click', function() {
                reloadTable();
            });

            // Event klik tombol Reset → kembali ke hari ini
            $('#btnReset').on('click', function() {
                const today = new Date().toISOString().split('T')[0];
                $('#filter_tanggal_mulai, #filter_tanggal_akhir').val(today);
                $('#filter_shift').val(''); // Reset filter shift ke default (kosong)
                $('#filter_klinik').val(''); // Reset filter klinik ke default (kosong)
                reloadTable();
            });

            // TOMBOL DELETE
            $('body').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Apakah Anda yakin ingin menghapus transaksi ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('Transaksi.destroy', ':id') }}'.replace(':id',
                                id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire('Dihapus!', response.message, 'success');
                                    $('#transaksiKasirTable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON?.message ??
                                    'Terjadi kesalahan saat menghapus.', 'error');
                            }
                        });
                    }
                });
            });

            // DATATABLES
            const table = $('#transaksiKasirTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                bDestroy: true,
                ajax: {
                    url: "{{ route('Transaksi.index') }}",
                    data: function(d) {
                        // Kirim parameter filter ke server
                        d.tanggal_mulai = $('#filter_tanggal_mulai').val();
                        d.tanggal_akhir = $('#filter_tanggal_akhir').val();
                        d.shift = $('#filter_shift').val();
                        d.klinik = $('#filter_klinik').val();
                    }
                },
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Memuat...</span>',
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'Kode',
                        name: 'Kode'
                    },
                    {
                        data: 'Tanggal',
                        name: 'Tanggal'
                    },
                    {
                        data: 'NamaPasien',
                        name: 'NamaPasien'
                    },
                    {
                        data: 'JenisPasien',
                        name: 'JenisPasien'
                    },
                    {
                        data: 'MetodePembayaran',
                        name: 'MetodePembayaran'
                    },
                    {
                        data: 'Layanan',
                        name: 'Layanan'
                    },
                    {
                        data: 'TotalBayar',
                        name: 'TotalBayar'
                    },
                    {
                        data: 'Petugas',
                        name: 'Petugas'
                    },
                    {
                        data: 'Shift',
                        name: 'Shift'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                // ✅ Pastikan default load = hari ini
                initComplete: function() {
                    reloadTable();
                },
                drawCallback: function(settings) {
                    const json = settings.json;
                    if (json && json.summary) {
                        const s = json.summary;
                        // Format rupiah
                        const formatRp = (val) =>
                            'Rp ' + parseInt(val).toLocaleString('id-ID');
                        $('#sum_omset').text(formatRp(s.total_omset));
                        $('#sum_pasien_baru').text(s.pasien_baru);
                        $('#sum_pasien_lama').text(s.pasien_lama);
                        $('#sum_pasien_total').text(s.pasien_total);
                    }
                }
            });
        });
    </script>
@endpush
