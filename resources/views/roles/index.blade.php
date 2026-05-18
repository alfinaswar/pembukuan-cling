@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-shield me-2 text-primary"></i>Role Management
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
                    <li class="breadcrumb-item active" aria-current="page">Role</li>
                </ol>
            </nav>
        </div>

        <!-- Alert Success -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="ti ti-circle-check me-2"></i>{{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Content Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-list me-2"></i>Data Role
                        </h4>

                        <a href="{{ route('roles.create') }}"
                            class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                            <i class="ti ti-plus"></i> Tambah Role
                        </a>

                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Table -->
                        <div class="table-responsive">
                            <table data-tables="basic" class="table table-striped dt-responsive align-middle mb-0"
                                id="shiftTable">
                                <thead class="thead-sm text-uppercase fs-xxs">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">#</th>
                                        <th>Nama Role</th>
                                        <th style="width: 200px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($roles as $key => $role)
                                        <tr>
                                            <td class="text-center fw-semibold">
                                                {{ ($roles->currentPage() - 1) * $roles->perPage() + $loop->iteration }}
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-primary">{{ $role->name }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">

                                                    <a href="{{ route('roles.edit', $role->id) }}"
                                                        class="btn btn-outline-primary" title="Edit">
                                                        <i class="ti ti-edit"></i>
                                                    </a>

                                                    <button type="button" class="btn btn-outline-danger btn-delete"
                                                        data-id="{{ $role->id }}" data-name="{{ $role->name }}"
                                                        title="Hapus">
                                                        <i class="ti ti-trash"></i>
                                                    </button>

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-5 text-muted">
                                                <i class="ti ti-shield-x fs-1 d-block mb-2"></i>
                                                Data role belum tersedia
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($roles->hasPages())
                            <div class="d-flex justify-content-between align-items-center mt-4 pt-2">
                                <small class="text-muted">
                                    Menampilkan {{ $roles->firstItem() }} - {{ $roles->lastItem() }} dari
                                    {{ $roles->total() }} data
                                </small>
                                <nav>
                                    {{ $roles->links() }}
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Table hover effect halus */
        .table-hover tbody tr:hover {
            background-color: rgba(33, 150, 243, 0.04) !important;
            transition: background-color 0.2s ease;
        }

        /* Pagination styling match theme */
        .pagination .page-item .page-link {
            border-radius: 6px !important;
            margin: 0 2px;
            color: #495057;
            border: 1px solid #dee2e6;
        }

        .pagination .page-item.active .page-link {
            background-color: #2196f3 !important;
            border-color: #2196f3 !important;
            color: white !important;
        }

        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            pointer-events: none;
        }

        /* Smooth transition untuk feedback */
        .alert {
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===== Delete Handler (Vanilla JS + SweetAlert2) =====
            document.body.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-delete');
                if (!btn) return;

                e.preventDefault();

                const id = btn.dataset.id;
                const name = btn.dataset.name;

                Swal.fire({
                    title: 'Hapus Role?',
                    html: `Anda akan menghapus role: <strong class="text-primary">${name}</strong><br><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="ti ti-trash me-1"></i>Ya, Hapus!',
                    cancelButtonText: '<i class="ti ti-x me-1"></i>Batal',
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
                            const response = await fetch(`/roles/${id}`, {
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

                            if (response.ok) {
                                await Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message || 'Role berhasil dihapus',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                // Reload page to reflect changes (plain table, no AJAX reload)
                                window.location.reload();
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

            // ===== Auto-hide alert after 5 seconds =====
            const alert = document.querySelector('.alert-success');
            if (alert) {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            }
        });
    </script>
@endpush
