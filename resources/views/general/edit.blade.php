@extends('layouts.master')
@section('title', $module_name)
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            {{ $module_name }}
        @endslot
        @slot('title')
            Edit {{ $module_name }}
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Edit {{ $module_name }}</h4>
                    <div class="flex-shrink-0">
                        <a href="{{ $module_route }}" class="btn btn-primary"><i
                                class="ri-arrow-left-line align-bottom me-1"></i> Back</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="live-preview">
                        <form method="post" class="needs-validation" novalidate
                            action="{{ $module_route . '/' . $result['id'] }}" enctype="multipart/form-data">
                            @csrf
                            <input name="_method" type="hidden" value="PUT">
                            @include($module_view . '._form')
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@endpush
