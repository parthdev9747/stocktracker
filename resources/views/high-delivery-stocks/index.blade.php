@extends('layouts.master')

@section('title')
    High Delivery Stocks
@endsection

@section('css')
    <style>
        .filter-bar {
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">High Delivery Stocks</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Market</a></li>
                        <li class="breadcrumb-item active">High Delivery Stocks</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-bar">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="days-filter" class="form-label">Days to Look Back</label>
                            <select id="days-filter" class="form-select">
                                <option value="7">Last 7 Days</option>
                                <option value="14">Last 14 Days</option>
                                <option value="30">Last 30 Days</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button id="refresh-data" class="btn btn-primary">
                            <i class="ri-refresh-line me-1"></i> Refresh Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Stocks with High Delivery Volume</h5>
                    <p class="text-muted mb-0">
                        Showing stocks where:
                    </p>
                    <ul class="mb-0 text-muted">
                        <li>Delivery quantity is 3x more than previous day</li>
                        <li>Delivery percentage > 70%</li>
                        <li>Closing price > Opening price</li>
                    </ul>
                </div>
                <div class="card-body">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts() }}

    <script>
        $(document).ready(function() {
            // Refresh when days filter changes
            $('#days-filter').on('change', function() {
                window.LaravelDataTables['high-delivery-stocks-table'].draw();
            });
            
            // Manual refresh button
            $('#refresh-data').on('click', function() {
                window.LaravelDataTables['high-delivery-stocks-table'].draw();
            });
        });
    </script>
@endpush