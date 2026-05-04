@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Role Management</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active">Role</li>
                </ol>
            </div>
        </div>

        <!-- Alert -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success mt-2">
                {{ $message }}
            </div>
        @endif

        <!-- Card -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">

                    <!-- Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Data Role</h4>

                        @can('role-create')
                            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                                + Tambah Role
                            </a>
                        @endcan
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table data-tables="basic" class="table table-striped dt-responsive align-middle mb-0"
                                id="usersTable">
                                <thead class="text-uppercase fs-xxs">
                                    <tr>
                                        <th width="5%" class="text-center">#</th>
                                        <th>Nama Role</th>
                                        <th width="25%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($roles as $key => $role)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>
                                                @can('role-edit')
                                                    <a href="{{ route('roles.edit', $role->id) }}"
                                                        class="btn btn-warning btn-sm">
                                                        Edit
                                                    </a>
                                                @endcan

                                                @can('role-delete')
                                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Yakin hapus role ini?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endcan

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                Data role belum tersedia
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {!! $roles->links() !!}
                        </div>

                    </div>
                    <!-- end card-body -->

                </div>
            </div>
        </div>

    </div>
@endsection
