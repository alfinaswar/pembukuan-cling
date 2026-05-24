    @extends('layouts.app')

    @section('content')
        <div class="container-fluid">
            <!-- Page Title Header -->
            <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
                <div class="flex-grow-1">
                    <h4 class="page-main-title m-0 fw-semibold">
                        <i class="ti ti-gift me-2 text-primary"></i>Rule Insentif
                    </h4>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}" class="text-decoration-none text-reset">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0)" class="text-decoration-none text-reset">Insentif</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Data Rule Insentif</li>
                    </ol>
                </nav>
            </div>

            <!-- Content Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <!-- Card Header -->
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title mb-0 fw-semibold">
                                <i class="ti ti-list me-2"></i>Data Rule Insentif per Role
                            </h5>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4">
                            <!-- Info Box -->
                            <div class="alert alert-light border mb-4 d-flex align-items-center gap-2">
                                <i class="ti ti-info-circle text-primary"></i>
                                <small class="mb-0">Pilih role di sebelah kiri untuk melihat rule insentif yang
                                    berlaku.</small>
                            </div>

                            <div class="row">
                                <!-- Nav Tabs (Sidebar) -->
                                <div class="col-lg-3 col-md-4">
                                    <div class="nav flex-column nav-pills gap-1" id="v-pills-tab" role="tablist"
                                        aria-orientation="vertical">
                                        @php $first = true; @endphp
                                        @foreach ($role as $r)
                                            <a class="nav-link d-flex align-items-center gap-2 py-2 px-3 @if ($first) active @endif"
                                                id="v-pills-{{ $r->id }}-tab" data-bs-toggle="pill"
                                                href="#v-pills-{{ $r->id }}" role="tab"
                                                aria-controls="v-pills-{{ $r->id }}"
                                                aria-selected="{{ $first ? 'true' : 'false' }}">
                                                <i class="ti ti-user-circle text-muted"></i>
                                                <span class="fw-medium">{{ $r->name }}</span>
                                                <span
                                                    class="badge bg-primary-subtle text-primary-emphasis ms-auto rounded-pill">
                                                    {{ $r->getRuleInsentif->count() }}
                                                </span>
                                            </a>
                                            @php $first = false; @endphp
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Tab Content -->
                                <div class="col-lg-9 col-md-8">
                                    <div class="tab-content" id="v-pills-tabContent">
                                        @php $first = true; @endphp
                                        @foreach ($role as $r)
                                            <div class="tab-pane fade @if ($first) show active @endif"
                                                id="v-pills-{{ $r->id }}" role="tabpanel"
                                                aria-labelledby="v-pills-{{ $r->id }}-tab">

                                                <!-- Tab Header -->
                                                <div
                                                    class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                                    <h5 class="mb-0 fw-semibold">
                                                        <i class="ti ti-shield-check me-2 text-primary"></i>
                                                        Rule untuk Role: <span
                                                            class="text-primary">{{ $r->name }}</span>
                                                    </h5>
                                                    <a href="{{ route('Insentif.create', $r->id) }}"
                                                        class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                                        <i class="ti ti-plus"></i> Tambah Rule
                                                    </a>
                                                </div>

                                                @if ($r->getRuleInsentif->isEmpty())
                                                    <!-- Empty State -->
                                                    <div class="text-center py-5">
                                                        <i class="ti ti-file-x fs-1 text-muted d-block mb-3"></i>
                                                        <p class="text-muted mb-0">Belum ada rule insentif untuk role ini.
                                                        </p>
                                                        <a href="{{ route('Insentif.create', $r->id) }}"
                                                            class="btn btn-outline-primary btn-sm mt-3">
                                                            <i class="ti ti-plus me-1"></i>Buat Rule Pertama
                                                        </a>
                                                    </div>
                                                @else
                                                    <!-- Table -->
                                                    <div class="table-responsive">
                                                        <table class="table table-hover align-middle mb-0">
                                                            <thead class="table-light text-uppercase">
                                                                <tr>
                                                                    <th style="width: 40px;" class="text-center">#</th>
                                                                    <th>Jenis Rule</th>
                                                                    <th>Kondisi</th>
                                                                    <th class="text-end">Nominal</th>
                                                                    <th>Berlaku Per</th>
                                                                    <th class="text-center">Status</th>
                                                                    <th>Keterangan</th>
                                                                    <th style="width: 90px;" class="text-center">Aksi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($r->getRuleInsentif as $idx => $rule)
                                                                    <tr>
                                                                        <td class="text-center fw-semibold">
                                                                            {{ $idx + 1 }}
                                                                        </td>

                                                                        <!-- Jenis Rule Badge -->
                                                                        <td>
                                                                            @php
                                                                                $jenisBadge = match ($rule->JenisRule) {
                                                                                    'omzet_shift'
                                                                                        => 'bg-info-subtle text-info-emphasis',
                                                                                    'omzet_harian'
                                                                                        => 'bg-success-subtle text-success-emphasis',
                                                                                    'jumlah_pasien'
                                                                                        => 'bg-warning-subtle text-warning-emphasis',
                                                                                    'prosedur_tertentu'
                                                                                        => 'bg-purple-subtle text-purple-emphasis',
                                                                                    default
                                                                                        => 'bg-secondary text-white',
                                                                                };
                                                                            @endphp
                                                                            <span class="badge {{ $jenisBadge }}">
                                                                                {{ $rule->JenisRule }}
                                                                            </span>
                                                                        </td>

                                                                        <!-- Kondisi -->
                                                                        <td>
                                                                            <code class="bg-light px-2 py-1 rounded">
                                                                                {{ $rule->Operator }}
                                                                                {{ is_numeric($rule->Nilai) ? number_format($rule->Nilai, 0, ',', '.') : $rule->Nilai }}
                                                                            </code>
                                                                        </td>

                                                                        <!-- Nominal -->
                                                                        <td class="text-end fw-semibold">
                                                                            @php
                                                                                $nominalFormatted = is_numeric(
                                                                                    $rule->Nominal,
                                                                                )
                                                                                    ? 'Rp ' .
                                                                                        number_format(
                                                                                            $rule->Nominal,
                                                                                            0,
                                                                                            ',',
                                                                                            '.',
                                                                                        )
                                                                                    : $rule->Nominal;
                                                                            @endphp
                                                                            {{ $nominalFormatted }}
                                                                            @if ($rule->TipeNominal === 'percent')
                                                                                <small class="text-muted d-block fs-7">(%
                                                                                    dari
                                                                                    omzet)</small>
                                                                            @endif
                                                                        </td>

                                                                        <!-- Berlaku Per -->
                                                                        <td>
                                                                            @php
                                                                                $berlakuLabel = match (
                                                                                    $rule->BerlakuPer
                                                                                ) {
                                                                                    'shift' => 'Per Shift',
                                                                                    'harian' => 'Harian',
                                                                                    'mingguan' => 'Mingguan',
                                                                                    'bulanan' => 'Bulanan',
                                                                                    default => $rule->BerlakuPer,
                                                                                };
                                                                            @endphp
                                                                            <span
                                                                                class="badge bg-info-subtle text-info-emphasis">
                                                                                {{ $berlakuLabel }}
                                                                            </span>
                                                                        </td>

                                                                        <!-- Status -->
                                                                        <td class="text-center">
                                                                            @php
                                                                                $isActive =
                                                                                    $rule->Status == 1 ||
                                                                                    strtolower($rule->Status) ===
                                                                                        'aktif';
                                                                                $statusBadge = $isActive
                                                                                    ? 'bg-success'
                                                                                    : 'bg-secondary';
                                                                                $statusIcon = $isActive
                                                                                    ? 'ti ti-circle-check'
                                                                                    : 'ti ti-circle-x';
                                                                                $statusLabel = $isActive
                                                                                    ? 'Aktif'
                                                                                    : 'Tidak Aktif';
                                                                            @endphp
                                                                            <span class="badge {{ $statusBadge }}">
                                                                                <i
                                                                                    class="{{ $statusIcon }} me-1"></i>{{ $statusLabel }}
                                                                            </span>
                                                                        </td>

                                                                        <!-- Keterangan (truncate) -->
                                                                        <td>
                                                                            <small class="text-muted d-block text-truncate"
                                                                                style="max-width: 200px;"
                                                                                title="{{ $rule->Keterangan }}">
                                                                                {{ $rule->Keterangan }}
                                                                            </small>
                                                                        </td>

                                                                        <!-- Actions -->
                                                                        <td class="text-center">
                                                                            <div class="btn-group btn-group-sm">
                                                                                <a href="{{ route('Insentif.edit', encrypt($rule->id)) }}"
                                                                                    class="btn btn-outline-primary"
                                                                                    title="Edit">
                                                                                    <i class="ti ti-edit"></i>
                                                                                </a>

                                                                                <button type="button"
                                                                                    class="btn btn-outline-danger btn-delete"
                                                                                    data-id="{{ $rule->id }}"
                                                                                    data-role="{{ $r->name }}"
                                                                                    data-keterangan="{{ $rule->Keterangan }}"
                                                                                    title="Hapus">
                                                                                    <i class="ti ti-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                            @php $first = false; @endphp
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('styles')
        <style>
            /* Nav pills styling */
            .nav-pills .nav-link {
                border-radius: 8px !important;
                transition: all 0.2s ease;
                border: 1px solid transparent;
            }

            .nav-pills .nav-link:hover {
                background-color: rgba(33, 150, 243, 0.08) !important;
                border-color: rgba(33, 150, 243, 0.2);
            }

            .nav-pills .nav-link.active {
                background-color: #2196f3 !important;
                color: white !important;
                box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
            }

            .nav-pills .nav-link.active .text-muted {
                color: rgba(255, 255, 255, 0.9) !important;
            }

            .nav-pills .nav-link.active .badge {
                background-color: rgba(255, 255, 255, 0.2) !important;
                color: white !important;
            }

            /* Table hover effect */
            .table-hover tbody tr:hover {
                background-color: rgba(33, 150, 243, 0.04) !important;
            }

            /* Badge custom colors */
            .bg-purple-subtle {
                background-color: #f3e5f5 !important;
            }

            .text-purple-emphasis {
                color: #7b1fa2 !important;
            }

            /* Code styling */
            code {
                font-size: 0.85em;
                font-weight: 500;
            }

            /* Truncate text with ellipsis */
            .text-truncate {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Delete handler (event delegation)
                document.body.addEventListener('click', function(e) {
                    const btn = e.target.closest('.btn-delete');
                    if (!btn) return;

                    e.preventDefault();

                    const id = btn.dataset.id;
                    const role = btn.dataset.role;
                    const keterangan = btn.dataset.keterangan;

                    Swal.fire({
                        title: 'Hapus Rule?',
                        html: `Anda akan menghapus rule untuk role <strong>${role}</strong>:<br><span class="text-primary">${keterangan}</span><br><br>Tindakan ini tidak dapat dibatalkan!`,
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
                                const response = await fetch(`/rule-insentif/${id}`, {
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
                                    // Reload page to reflect changes
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

                // Show toast notification from session
                @if (session('success'))
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    Toast.fire({
                        icon: 'success',
                        title: '{{ session('success') }}'
                    });
                @endif
            });
        </script>
    @endpush
