{{-- File: resources/views/users/index.blade.php --}}

@extends('layouts.master')

@section('title', $module_name)

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ $module_name }} List</h4>
                <div class="flex-shrink-0">
                    @canany('add-user')
                        <a href="{{ route('user.create') }}" class="btn btn-primary">
                            <i class="ri-add-line align-bottom me-1"></i>
                            Add New
                        </a>
                    @endcan
                </div>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="mb-4">
                    <div class="table-responsive-sm table-responsive-md table-responsive-lg">
                        {{ $dataTable->table() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script>
        function deleteRecord(id) {
            let url = '{{ $module_route }}/' + id;
            deleteRecordByAjax(url, "{{ $module_name }}", 'users-table');
        }
    </script>
@endpush
