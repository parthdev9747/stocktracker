@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>{{ $data->preOpenMarketData->symbol ?? 'Unknown' }} - {{ Carbon\Carbon::parse($data->trade_date)->format('d M Y') }}</h5>
                        <a href="{{ route('stock-historical-data.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Stock Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Symbol:</th>
                                    <td>{{ $data->preOpenMarketData->symbol ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Series:</th>
                                    <td>{{ $data->series }}</td>
                                </tr>
                                <tr>
                                    <th>Market Type:</th>
                                    <td>{{ $data->market_type }}</td>
                                </tr>
                                <tr>
                                    <th>ISIN:</th>
                                    <td>{{ $data->isin }}</td>
                                </tr>
                                <tr>
                                    <th>Trade Date:</th>
                                    <td>{{ Carbon\Carbon::parse($data->trade_date)->format('d M Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Price Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Opening Price:</th>
                                    <td>{{ number_format($data->opening_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>High Price:</th>
                                    <td>{{ number_format($data->high_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Low Price:</th>
                                    <td>{{ number_format($data->low_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Closing Price:</th>
                                    <td>{{ number_format($data->closing_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Last Traded Price:</th>
                                    <td>{{ number_format($data->last_traded_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Previous Close:</th>
                                    <td>{{ number_format($data->previous_close_price, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">Volume Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Traded Quantity:</th>
                                    <td>{{ number_format($data->traded_quantity) }}</td>
                                </tr>
                                <tr>
                                    <th>Traded Value:</th>
                                    <td>{{ number_format($data->traded_value, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Trades:</th>
                                    <td>{{ number_format($data->total_trades) }}</td>
                                </tr>
                                <tr>
                                    <th>VWAP:</th>
                                    <td>{{ number_format($data->vwap, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3">52 Week Range</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>52 Week High:</th>
                                    <td>{{ number_format($data->week_high_52, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>52 Week Low:</th>
                                    <td>{{ number_format($data->week_low_52, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Current vs 52W High:</th>
                                    <td>
                                        @php
                                            $highDiff = $data->week_high_52 ? (($data->closing_price - $data->week_high_52) / $data->week_high_52) * 100 : 0;
                                            $highClass = $highDiff >= 0 ? 'text-success' : 'text-danger';
                                        @endphp
                                        <span class="{{ $highClass }}">{{ number_format($highDiff, 2) }}%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Current vs 52W Low:</th>
                                    <td>
                                        @php
                                            $lowDiff = $data->week_low_52 ? (($data->closing_price - $data->week_low_52) / $data->week_low_52) * 100 : 0;
                                            $lowClass = $lowDiff >= 0 ? 'text-success' : 'text-danger';
                                        @endphp
                                        <span class="{{ $lowClass }}">{{ number_format($lowDiff, 2) }}%</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection