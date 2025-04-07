@extends('layouts.master')

@section('title')
    {{ $symbol }} - NSE Chart
@endsection

@section('css')
    <style>
        .chart-container {
            height: 500px;
            width: 100%;
            margin-bottom: 20px;
        }

        .chart-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .chart-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .chart-info-label {
            font-weight: 600;
            color: #495057;
        }

        .chart-info-value {
            font-weight: 500;
        }

        .positive {
            color: #28a745;
        }

        .negative {
            color: #dc3545;
        }

        .chart-controls {
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $symbol }} Chart</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Market</a></li>
                        <li class="breadcrumb-item active">NSE Chart</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Basic Candlestick Chart</h4>
                        <div class="chart-controls">
                            <button id="refresh-chart" class="btn btn-sm btn-primary">
                                <i class="ri-refresh-line me-1"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="chart-container">
                                <div id="candlestick-chart" data-colors='["--vz-success", "--vz-danger"]'></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="chart-info">
                                <h5 class="mb-3">Market Information</h5>
                                <div class="chart-info-item">
                                    <span class="chart-info-label">Last Price:</span>
                                    <span class="chart-info-value" id="last-price">-</span>
                                </div>
                                <div class="chart-info-item">
                                    <span class="chart-info-label">Change:</span>
                                    <span class="chart-info-value" id="price-change">-</span>
                                </div>
                                <div class="chart-info-item">
                                    <span class="chart-info-label">Change %:</span>
                                    <span class="chart-info-value" id="price-change-percent">-</span>
                                </div>
                                <div class="chart-info-item">
                                    <span class="chart-info-label">Open:</span>
                                    <span class="chart-info-value" id="open-price">-</span>
                                </div>
                                <div class="chart-info-item">
                                    <span class="chart-info-label">High:</span>
                                    <span class="chart-info-value" id="high-price">-</span>
                                </div>
                                <div class="chart-info-item">
                                    <span class="chart-info-label">Low:</span>
                                    <span class="chart-info-value" id="low-price">-</span>
                                </div>
                                <div class="chart-info-item">
                                    <span class="chart-info-label">Prev. Close:</span>
                                    <span class="chart-info-value" id="prev-close">-</span>
                                </div>
                                <div class="chart-info-item">
                                    <span class="chart-info-label">Volume:</span>
                                    <span class="chart-info-value" id="volume">-</span>
                                </div>
                                <div class="chart-info-item">
                                    <span class="chart-info-label">Last Updated:</span>
                                    <span class="chart-info-value" id="last-updated">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{-- <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="https://apexcharts.com/samples/assets/ohlc.js"></script>
    <!-- for Category x-axis chart -->
    <script src="https://img.themesbrand.com/velzon/apexchart-js/dayjs.min.js"></script>
    <script src="{{ URL::asset('build/js/pages/apexcharts-candlestick.init.js') }}"></script> --}}
    <script>
        $(document).ready(function() {
            let candlestickOptions = {
                series: [{
                    data: []
                }],
                chart: {
                    type: 'candlestick',
                    height: 450,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            selection: true,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: true,
                            reset: true
                        }
                    }
                },
                title: {
                    text: '{{ $symbol }} Stock Price',
                    align: 'left'
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        datetimeUTC: false
                    }
                },
                yaxis: {
                    tooltip: {
                        enabled: true
                    },
                    labels: {
                        formatter: function(val) {
                            return '₹' + val.toFixed(2);
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    custom: function({
                        seriesIndex,
                        dataPointIndex,
                        w
                    }) {
                        const o = w.globals.seriesCandleO[seriesIndex][dataPointIndex];
                        const h = w.globals.seriesCandleH[seriesIndex][dataPointIndex];
                        const l = w.globals.seriesCandleL[seriesIndex][dataPointIndex];
                        const c = w.globals.seriesCandleC[seriesIndex][dataPointIndex];
                        const date = new Date(w.globals.seriesX[seriesIndex][dataPointIndex])
                            .toLocaleDateString();

                        return (
                            '<div class="apexcharts-tooltip-candlestick">' +
                            '<div>Date: <b>' + date + '</b></div>' +
                            '<div>Open: <b>₹' + o.toFixed(2) + '</b></div>' +
                            '<div>High: <b>₹' + h.toFixed(2) + '</b></div>' +
                            '<div>Low: <b>₹' + l.toFixed(2) + '</b></div>' +
                            '<div>Close: <b>₹' + c.toFixed(2) + '</b></div>' +
                            '</div>'
                        );
                    }
                },
                plotOptions: {
                    candlestick: {
                        colors: {
                            upward: '#28a745',
                            downward: '#dc3545'
                        },
                        wick: {
                            useFillColor: true,
                        }
                    }
                }
            };

            let candlestickChart = new ApexCharts(document.querySelector("#candlestick-chart"), candlestickOptions);
            candlestickChart.render();

            function fetchChartData() {
                $.ajax({
                    url: `/api/nse-chart/${encodeURIComponent('{{ $symbol }}')}`,
                    method: 'GET',
                    success: function(response) {
                        if (response && response.grapthData) {
                            // Transform data for candlestick chart
                            const candlestickData = transformToCandlestickData(response.grapthData);

                            candlestickChart.updateSeries([{
                                data: candlestickData
                            }]);

                            // Update market information
                            if (response.metadata) {
                                const metadata = response.metadata;

                                $('#last-price').text('₹' + metadata.lastPrice);

                                const change = parseFloat(metadata.change || 0);
                                const changePercent = parseFloat(metadata.percentChange || 0);

                                $('#price-change').text('₹' + change.toFixed(2))
                                    .removeClass('positive negative')
                                    .addClass(change >= 0 ? 'positive' : 'negative');

                                $('#price-change-percent').text(changePercent.toFixed(2) + '%')
                                    .removeClass('positive negative')
                                    .addClass(changePercent >= 0 ? 'positive' : 'negative');

                                $('#open-price').text('₹' + metadata.open);
                                $('#high-price').text('₹' + metadata.high);
                                $('#low-price').text('₹' + metadata.low);
                                $('#prev-close').text('₹' + metadata.previousClose);
                                $('#volume').text(formatNumber(metadata.totalTradedVolume));
                                $('#last-updated').text(new Date().toLocaleTimeString());
                            }
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching chart data:', error);
                        toastr.error('Failed to fetch chart data. Please try again later.');
                    }
                });
            }

            // Transform time series data to OHLC format for candlestick chart
            function transformToCandlestickData(timeSeriesData) {
                // Group data by day
                const groupedByDay = {};

                timeSeriesData.forEach(item => {
                    const timestamp = new Date(item[0]);
                    const price = item[1];

                    // Create a date string without time component
                    const dateKey = timestamp.toISOString().split('T')[0];

                    if (!groupedByDay[dateKey]) {
                        groupedByDay[dateKey] = {
                            timestamp: timestamp.getTime(),
                            open: price,
                            high: price,
                            low: price,
                            close: price,
                            prices: [price]
                        };
                    } else {
                        groupedByDay[dateKey].prices.push(price);
                        groupedByDay[dateKey].high = Math.max(groupedByDay[dateKey].high, price);
                        groupedByDay[dateKey].low = Math.min(groupedByDay[dateKey].low, price);
                        groupedByDay[dateKey].close = price; // Last price of the day
                    }
                });

                // Convert to candlestick format
                return Object.values(groupedByDay).map(day => ({
                    x: day.timestamp,
                    y: [day.open, day.high, day.low, day.close]
                }));
            }

            // Format number with commas
            function formatNumber(num) {
                return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
            }

            // Initial data fetch
            fetchChartData();

            // Refresh button click handler
            $('#refresh-chart').on('click', function() {
                fetchChartData();
            });

            // Auto refresh every 5 minutes
            setInterval(fetchChartData, 1 * 60 * 1000);
        });
    </script>
@endpush
