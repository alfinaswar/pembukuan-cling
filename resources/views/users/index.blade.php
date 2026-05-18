@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-users me-2 text-primary"></i>Master Users
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0)" class="text-decoration-none text-reset">Master</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Data Users</li>
                </ol>
            </nav>
        </div>

        <!-- Content Card -->
        <div class="row">
            <div class="col-12">
                <div class="datatables">
                    <div class="card shadow-sm border-0">
                        <!-- Card Header -->
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0 fw-semibold">
                                <i class="ti ti-list me-2"></i>Data Users
                            </h4>
                            <a href="{{ route('users.create') }}"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                <i class="ti ti-plus"></i> Tambah User
                            </a>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- DataTable -->
                            <div class="table-responsive">
                                <table data-tables="basic" class="table table-striped dt-responsive align-middle mb-0"
                                    id="usersTable">
                                    <thead class="thead-sm text-uppercase fs-xxs">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">#</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th style="width: 90px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Data dimuat via DataTables --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Toast notification config
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            // Show success toast from session
            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });
            @endif

            // Delete Handler - Vanilla JS inside jQuery ready (for DataTables compatibility)
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-delete');
                if (!btn) return;

                e.preventDefault();

                const id = btn.dataset.id;
                const name = btn.dataset.name;

                Swal.fire({
                    title: 'Hapus User?',
                    html: `Anda akan menghapus user:<br><strong class="text-primary">${name}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Menghapus...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        try {
                            const response = await fetch(`/users/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]')?.content ||
                                        '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const data = await response.json();

                            if (response.ok && data.status === 200) {
                                await Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                // Reload DataTable
                                $('#usersTable').DataTable().ajax.reload(null, false);
                            } else {
                                throw new Error(data.message || 'Gagal menghapus data');
                            }
                        } catch (error) {
                            Swal.fire('Gagal!', error.message ||
                                'Terjadi kesalahan saat menghapus data', 'error');
                        }
                    }
                });
            });

            // DataTables Init - ✅ Style asli dipertahankan, ✅ NO ORDERING
            $('#usersTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true, // ✅ Ganti dari bDestroy (deprecated)
                ordering: false, // ✅ DISABLE ORDERING sesuai request
                ajax: {
                    url: "{{ route('users.index') }}",
                    type: 'GET'
                },
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Memuat...</span>',
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    },
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" // ✅ Bahasa Indonesia
                },
                columnDefs: [{
                        className: 'text-center',
                        targets: [0, 4]
                    },

                    {
                        targets: [2], // Email column
                        render: function(data) {
                            return data ?
                                `<a href="mailto:${data}" class="text-decoration-none">${data}</a>` :
                                '-';
                        }
                    }
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        render: function(data) {
                            return `<span class="fw-semibold text-primary">${data}</span>`;
                        }
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'roles',
                        name: 'roles'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,

                    }
                ]
            });
        });
    </script>
@endpush
