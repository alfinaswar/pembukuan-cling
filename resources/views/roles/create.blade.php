@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-plus me-2 text-primary"></i>Tambah Role
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('roles.index') }}" class="text-decoration-none text-reset">Role</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Role</li>
                </ol>
            </nav>
        </div>

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-start gap-2">
                    <i class="ti ti-alert-circle fs-5 mt-1"></i>
                    <div>
                        <strong>Error!</strong> Ada data yang belum valid.
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Form Card -->
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-file-text me-2"></i>Form Data Role
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        {!! Form::open(['route' => 'roles.store', 'method' => 'POST', 'id' => 'formRole']) !!}
                        <!-- Info Box -->
                        <div class="alert alert-light border mb-4 d-flex align-items-center gap-2">
                            <i class="ti ti-info-circle text-primary"></i>
                            <small class="mb-0 text-dark">Kolom dengan tanda <span class="text-danger">*</span> wajib
                                diisi.</small>
                        </div>
                        <!-- Nama Role -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold mb-2">
                                Nama Role <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="ti ti-shield text-muted"></i>
                                </span>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    required placeholder="Contoh: Dokter, Admin, Kasir, Perawat" autocomplete="off">
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="ti ti-help me-1"></i>Gunakan nama yang jelas dan deskriptif
                            </small>
                            @error('name')
                                <div class="invalid-feedback d-block mt-1">
                                    <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Permission -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-2">
                                Permission / Hak Akses
                            </label>

                            <!-- Search Filter for Permissions -->
                            <div class="mb-3">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-search text-muted"></i>
                                    </span>
                                    <input type="text" id="permissionSearch" class="form-control form-control-sm"
                                        placeholder="Cari permission...">
                                    <button type="button" class="btn btn-light btn-sm" id="selectAll">
                                        <i class="ti ti-checkbox me-1"></i>Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm" id="deselectAll">
                                        <i class="ti ti-square me-1"></i>Batal Semua
                                    </button>
                                </div>
                            </div>

                            <!-- Permission Checkboxes Grid -->
                            <div class="border rounded p-3 bg-light-subtle" style="max-height: 300px; overflow-y: auto;">
                                <div class="row g-2" id="permissionList">
                                    @foreach ($permission as $value)
                                        <div class="col-md-4 col-sm-6 permission-item">
                                            <label
                                                class="d-flex align-items-center gap-3 p-2 rounded hover-bg-light cursor-pointer">
                                                <input type="checkbox" name="permission[]" value="{{ $value->id }}"
                                                    class="form-check-input" id="perm-{{ $value->id }}"
                                                    {{ collect(old('permission'))->contains($value->id) ? 'checked' : '' }}>
                                                <span class=" text-break">{{ $value->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="ti ti-help me-1"></i>Pilih permission yang ingin diberikan ke role ini
                            </small>
                            @error('permission')
                                <div class="invalid-feedback d-block mt-1">
                                    <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Divider -->
                        <hr class="my-4 text-muted opacity-25">

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <a href="{{ route('roles.index') }}" class="btn btn-light px-4">
                                <i class="ti ti-arrow-left me-1"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ti ti-device-floppy me-1"></i>Simpan Role
                            </button>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>

                <!-- Back Link (Mobile Friendly) -->
                <div class="text-center mt-3 d-lg-none">
                    <a href="{{ route('roles.index') }}" class="text-muted small text-decoration-none">
                        <i class="ti ti-arrow-back-up me-1"></i>Kembali ke daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Fokus input yang lebih halus */
        .form-control:focus,
        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.15);
            border-color: #2196f3;
        }

        /* Input group icon styling */
        .input-group-text {
            border-right: none;
            min-width: 45px;
            justify-content: center;
        }

        .input-group .form-control {
            border-left: none;
        }

        /* Smooth transition untuk feedback */
        .invalid-feedback,
        .alert {
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Permission checkbox styling */
        .permission-item label {
            transition: background-color 0.2s ease;
            cursor: pointer;
        }

        .permission-item label:hover {
            background-color: rgba(33, 150, 243, 0.08) !important;
        }

        .permission-item input[type="checkbox"]:checked+span {
            font-weight: 500;
            color: #1976d2;
        }

        /* Scrollbar styling for permission list */
        #permissionList {
            scrollbar-width: thin;
            scrollbar-color: #adb5bd #f8f9fa;
        }

        #permissionList::-webkit-scrollbar {
            width: 6px;
        }

        #permissionList::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 3px;
        }

        #permissionList::-webkit-scrollbar-thumb {
            background: #adb5bd;
            border-radius: 3px;
        }

        #permissionList::-webkit-scrollbar-thumb:hover {
            background: #6c757d;
        }

        /* Cursor pointer for labels */
        .cursor-pointer {
            cursor: pointer;
        }

        /* Hover background utility */
        .hover-bg-light:hover {
            background-color: rgba(33, 150, 243, 0.08) !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===== Auto-focus =====
            document.getElementById('name')?.focus();

            // ===== Permission Search Filter =====
            const searchInput = document.getElementById('permissionSearch');
            const permissionItems = document.querySelectorAll('.permission-item');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const keyword = this.value.toLowerCase().trim();

                    permissionItems.forEach(item => {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(keyword)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // ===== Select All / Deselect All =====
            const selectAllBtn = document.getElementById('selectAll');
            const deselectAllBtn = document.getElementById('deselectAll');
            const permissionCheckboxes = document.querySelectorAll('input[name="permission[]"]');

            if (selectAllBtn) {
                selectAllBtn.addEventListener('click', function() {
                    permissionCheckboxes.forEach(checkbox => {
                        // Only check visible checkboxes (respecting search filter)
                        if (checkbox.closest('.permission-item').style.display !== 'none') {
                            checkbox.checked = true;
                        }
                    });
                });
            }

            if (deselectAllBtn) {
                deselectAllBtn.addEventListener('click', function() {
                    permissionCheckboxes.forEach(checkbox => {
                        if (checkbox.closest('.permission-item').style.display !== 'none') {
                            checkbox.checked = false;
                        }
                    });
                });
            }

            // ===== Label click to toggle checkbox =====
            document.querySelectorAll('.permission-item label').forEach(label => {
                label.addEventListener('click', function(e) {
                    // Prevent double-toggle when clicking the checkbox itself
                    if (e.target.type !== 'checkbox') {
                        const checkbox = this.querySelector('input[type="checkbox"]');
                        if (checkbox) {
                            checkbox.checked = !checkbox.checked;
                        }
                    }
                });
            });
        });
    </script>
@endpush
