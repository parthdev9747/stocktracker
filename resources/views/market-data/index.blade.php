@extends('layouts.master')

@section('title')
    Market Data Analysis
@endsection

@push('css')
    <link href="{{ URL::asset('build/libs/apexcharts/apexcharts.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/gauge.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <style>
        .gauge-container {
            height: 200px;
            width: 100%;
        }

        /* Base styles */
        .data-card {
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
            overflow: hidden;
        }

        .data-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            border-radius: 15px 15px 0 0;
            font-weight: 600;
            padding: 1.2rem 1.5rem;
            border-bottom: none;
        }

        /* Stock header section */
        .stock-header {
            background: linear-gradient(120deg, #2b4162 0%, #12100e 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .stock-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://cdn.pixabay.com/photo/2021/11/11/16/05/stock-market-6786786_1280.jpg') center/cover no-repeat;
            opacity: 0.15;
            z-index: 0;
        }

        .stock-content {
            position: relative;
            z-index: 1;
        }

        .stock-symbol {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 5px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .stock-name {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .stock-price {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .price-change {
            font-size: 18px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
        }

        .price-change.positive {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .price-change.negative {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .price-details {
            display: flex;
            flex-wrap: wrap;
            margin-top: 20px;
            gap: 15px;
        }

        .price-item {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 10px 15px;
            border-radius: 10px;
            min-width: 120px;
        }

        .price-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .price-value {
            font-size: 16px;
            font-weight: 600;
        }

        /* Metrics styling */
        .metric-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .metric-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        /* Chart containers */
        .gauge-container {
            position: relative;
            height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .gauge-value {
            position: absolute;
            bottom: 40px;
            width: 100%;
            text-align: center;
            font-size: 28px;
            font-weight: 700;
        }

        .gauge-label {
            position: absolute;
            bottom: 15px;
            width: 100%;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }

        /* Tables */
        .bid-ask-table {
            font-size: 14px;
            border-collapse: separate;
            border-spacing: 0 5px;
        }

        .bid-ask-table tr {
            background-color: rgba(0, 0, 0, 0.02);
            border-radius: 8px;
        }

        .bid-ask-table td,
        .bid-ask-table th {
            padding: 10px;
        }

        .bid-price {
            color: #28a745;
            font-weight: 600;
        }

        .ask-price {
            color: #dc3545;
            font-weight: 600;
        }

        /* Liquidity indicators */
        .liquidity-indicator {
            height: 10px;
            border-radius: 5px;
            margin-bottom: 8px;
            position: relative;
            overflow: hidden;
        }

        .liquidity-indicator::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 100%);
        }

        .liquidity-high {
            background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        }

        .liquidity-medium {
            background: linear-gradient(90deg, #ffc107 0%, #fd7e14 100%);
        }

        .liquidity-low {
            background: linear-gradient(90deg, #dc3545 0%, #e83e8c 100%);
        }

        /* Loading overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            border-radius: 15px;
            backdrop-filter: blur(3px);
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        /* Refresh timer */
        .refresh-timer {
            font-size: 13px;
            color: #6c757d;
            background-color: rgba(0, 0, 0, 0.05);
            padding: 5px 12px;
            border-radius: 20px;
        }

        /* Market depth container */
        .market-depth-container {
            height: 300px;
            margin-top: 20px;
        }

        /* Select stock section */
        .select-stock-card {
            background: linear-gradient(120deg, #f6f9fc 0%, #f1f4f9 100%);
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .select-stock-card .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        #choices-filter-symbol {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        #fetch-data-btn {
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }

        #fetch-data-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(13, 110, 253, 0.4);
        }

        #fetch-data-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Last updated timestamp */
        .last-updated {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            text-align: right;
            margin-top: 10px;
        }
    </style>
@endpush

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Analysis
        @endslot
        @slot('title')
            Market Data Analysis
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card select-stock-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Select Stock for Analysis</h4>
                        <div class="refresh-timer" id="refresh-timer">
                            <i class="bx bx-refresh me-1"></i> Next refresh in: <span id="countdown">150</span>s
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Select Stock</label>
                                <select class="form-control select2" class="form-select" data-choices
                                    id="choices-filter-symbol">
                                    <option value="">Select a stock...</option>
                                    @foreach ($stocks as $stock)
                                        <option value="{{ $stock->symbol }}">{{ $stock->symbol }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" id="fetch-data-btn" class="btn btn-primary mb-3" disabled>
                                <i class="bx bx-line-chart me-1"></i> Analyze Stock
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('market-data.data')
@endsection

@push('js')
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        // Define chart variables in global scope
        let deliveryChart, demandChart;

        $(document).ready(function() {
            // Initialize charts
            let marketDepthChart = initMarketDepthChart();

            let refreshInterval;
            let countdownInterval;
            let countdownSeconds = 150; // 2.5 minutes

            // Enable/disable fetch button based on selection
            $('#choices-filter-symbol').on('change', function() {
                $('#fetch-data-btn').prop('disabled', !$(this).val());
            });

            // Fetch data on button click
            $('#fetch-data-btn').on('click', function() {
                fetchData();

                // Clear existing intervals
                if (refreshInterval) clearInterval(refreshInterval);
                if (countdownInterval) clearInterval(countdownInterval);

                // Set up auto-refresh
                refreshInterval = setInterval(fetchData, 150000); // 2.5 minutes

                // Set up countdown timer
                countdownSeconds = 150;
                updateCountdown();
                countdownInterval = setInterval(updateCountdown, 1000);

                // Show data container
                $('#data-container').show();
            });

            function updateCountdown() {
                countdownSeconds--;
                $('#countdown').text(countdownSeconds);

                if (countdownSeconds <= 0) {
                    countdownSeconds = 150;
                }
            }

            function fetchData() {
                const symbol = $('#choices-filter-symbol').val();
                if (!symbol) return;

                // Show loading overlays
                $('.loading-overlay').show();

                $.ajax({
                    url: "{{ route('market-data.fetch') }}",
                    type: 'GET',
                    data: {
                        symbol: symbol
                    },
                    success: function(response) {
                        // Update UI with the data
                        updateUI(response.data, response.metrics);

                        // Update the stock header with market info
                        updateStockHeader(response.marketInfo, response.lastUpdated);
                    },
                    error: function(xhr) {
                        console.error('Error fetching data:', xhr);
                        alert('Failed to fetch data. Please try again.');
                    },
                    complete: function() {
                        // Hide loading overlays
                        $('.loading-overlay').hide();
                    }
                });
            }

            // New function to update the stock header
            function updateStockHeader(marketInfo, lastUpdated) {
                // Update stock symbol and name
                $('#stock-symbol').text(marketInfo.symbol);
                $('#stock-name').text(marketInfo.companyName + ' • ' + marketInfo.industry);

                // Update price and change
                $('#stock-price').text('₹' + formatNumber(marketInfo.lastPrice, 2));

                const priceChange = marketInfo.change;
                const pctChange = marketInfo.pChange;
                const changeText = formatNumber(priceChange, 2) + ' (' + formatNumber(pctChange, 2) + '%)';
                $('#price-change').text(changeText);

                // Set color based on price change
                if (priceChange > 0) {
                    $('#price-change').removeClass('negative').addClass('positive');
                } else if (priceChange < 0) {
                    $('#price-change').removeClass('positive').addClass('negative');
                } else {
                    $('#price-change').removeClass('positive negative');
                }

                // Update price details
                $('#price-open').text('₹' + formatNumber(marketInfo.open, 2));
                $('#price-high').text('₹' + formatNumber(marketInfo.high, 2));
                $('#price-low').text('₹' + formatNumber(marketInfo.low, 2));
                $('#price-close').text('₹' + formatNumber(marketInfo.close, 2));
                $('#price-volume').text(formatNumber(marketInfo.totalTradedVolume));
                $('#price-year-high').text('₹' + formatNumber(marketInfo.yearHigh, 2));
                $('#price-year-low').text('₹' + formatNumber(marketInfo.yearLow, 2));
                $('#price-pe').text(formatNumber(marketInfo.pe, 2));

                // Update last updated timestamp
                $('#last-updated').text('Last updated: ' + lastUpdated);
            }

            function updateUI(data, metrics) {
                // Update Delivery Percentage
                const deliveryPercentage = metrics.deliveryPercentage;
                $('#delivery-percentage-value').text(formatNumber(deliveryPercentage, 2) + '%');
                $('#quantity-traded').text(formatNumber(data.securityWiseDP.quantityTraded));
                $('#delivery-quantity').text(formatNumber(data.securityWiseDP.deliveryQuantity));
                window.updateGaugeChart(deliveryChart, deliveryPercentage);

                // Update Demand-Supply Pressure
                const demandSupplyRatio = metrics.demandSupplyRatio;
                $('#demand-supply-ratio-value').text(formatNumber(demandSupplyRatio, 2) + '%');
                $('#total-buy-qty').text(formatNumber(data.marketDeptOrderBook.totalBuyQuantity));
                $('#total-sell-qty').text(formatNumber(data.marketDeptOrderBook.totalSellQuantity));
                window.updateGaugeChart(demandChart, demandSupplyRatio);

                // Update Trade Analysis
                $('#avg-trade-price').text('₹' + formatNumber(metrics.avgTradePrice, 2));
                $('#total-traded-volume').text(formatNumber(data.marketDeptOrderBook.tradeInfo.totalTradedVolume,
                    2) + 'M');
                $('#total-traded-value').text('₹' + formatNumber(data.marketDeptOrderBook.tradeInfo
                    .totalTradedValue, 2) + 'Cr');

                // Update Weighted Bid-Ask Prices
                $('#weighted-bid').text('₹' + formatNumber(metrics.weightedBid, 2));
                $('#weighted-ask').text('₹' + formatNumber(metrics.weightedAsk, 2));
                $('#bid-ask-spread').text('₹' + formatNumber(metrics.bidAskSpread, 2));
                $('#bid-ask-spread-percentage').text('(' + formatNumber(metrics.bidAskSpreadPercentage, 2) + '%)');

                // Update Bid-Ask tables
                updateBidAskTables(data.marketDeptOrderBook.bid, data.marketDeptOrderBook.ask);

                // Update Liquidity Analysis
                $('#impact-cost').text(formatNumber(metrics.impactCost, 2) + '%');
                $('#market-depth-ratio').text(formatNumber(metrics.marketDepthRatio, 2) + '%');
                $('#daily-volatility').text(data.marketDeptOrderBook.tradeInfo.cmDailyVolatility + '%');

                // Update Liquidity Rating
                updateLiquidityRating(metrics.impactCost, metrics.bidAskSpreadPercentage);

                // Update Market Depth Chart
                updateMarketDepthChart(marketDepthChart, data.marketDeptOrderBook.bid, data.marketDeptOrderBook
                    .ask);
            }

            function updateBidAskTables(bidData, askData) {
                let bidHtml = '';
                let askHtml = '';

                bidData.forEach(function(item) {
                    bidHtml += `<tr>
                        <td class="bid-price">₹${formatNumber(item.price, 2)}</td>
                        <td>${formatNumber(item.quantity)}</td>
                    </tr>`;
                });

                askData.forEach(function(item) {
                    askHtml += `<tr>
                        <td class="ask-price">₹${formatNumber(item.price, 2)}</td>
                        <td>${formatNumber(item.quantity)}</td>
                    </tr>`;
                });

                $('#bid-table-body').html(bidHtml);
                $('#ask-table-body').html(askHtml);
            }

            function updateLiquidityRating(impactCost, spreadPercentage) {
                let liquidityClass = '';
                let liquidityText = '';

                if (impactCost < 0.05 && spreadPercentage < 0.1) {
                    liquidityClass = 'liquidity-high';
                    liquidityText = 'High Liquidity';
                } else if (impactCost < 0.15 && spreadPercentage < 0.3) {
                    liquidityClass = 'liquidity-medium';
                    liquidityText = 'Medium Liquidity';
                } else {
                    liquidityClass = 'liquidity-low';
                    liquidityText = 'Low Liquidity';
                }

                $('#liquidity-indicator').removeClass('liquidity-high liquidity-medium liquidity-low').addClass(
                    liquidityClass);
                $('#liquidity-rating').text(liquidityText);
            }



            function updateGaugeChart(chart, value) {
                chart.updateSeries([Math.min(100, value)]);
            }

            function initMarketDepthChart() {
                const options = {
                    series: [{
                        name: 'Bid',
                        data: []
                    }, {
                        name: 'Ask',
                        data: []
                    }],
                    chart: {
                        type: 'bar',
                        height: 250,
                        stacked: true,
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true,
                            barHeight: '80%',
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 1,
                        colors: ['#fff']
                    },
                    grid: {
                        xaxis: {
                            lines: {
                                show: false
                            }
                        }
                    },
                    yaxis: {
                        min: -5,
                        max: 5,
                        title: {
                            text: 'Price Levels',
                        },
                    },
                    tooltip: {
                        shared: false,
                        y: {
                            formatter: function(val) {
                                return Math.abs(val);
                            }
                        }
                    },
                    colors: ['#28a745', '#dc3545'],
                    title: {
                        text: 'Market Depth',
                        align: 'center'
                    },
                    xaxis: {
                        categories: [],
                        title: {
                            text: 'Quantity'
                        },
                        labels: {
                            formatter: function(val) {
                                return Math.abs(Math.round(val));
                            }
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector('#market-depth-chart'), options);
                chart.render();
                return chart;
            }

            function updateMarketDepthChart(chart, bidData, askData) {
                const bidSeries = [];
                const askSeries = [];
                const categories = [];

                // Process bid data (negative values for left side of chart)
                bidData.forEach(function(item) {
                    bidSeries.push(-item.quantity); // Negative for left side
                    askSeries.push(0);
                    categories.push('₹' + formatNumber(item.price, 2));
                });

                // Process ask data (positive values for right side of chart)
                askData.forEach(function(item, index) {
                    if (index < bidData.length) {
                        // Update existing entry
                        askSeries[index] = item.quantity;
                    } else {
                        // Add new entry
                        bidSeries.push(0);
                        askSeries.push(item.quantity);
                        categories.push('₹' + formatNumber(item.price, 2));
                    }
                });

                chart.updateOptions({
                    xaxis: {
                        categories: categories
                    }
                });

                chart.updateSeries([{
                    name: 'Bid',
                    data: bidSeries
                }, {
                    name: 'Ask',
                    data: askSeries
                }]);
            }

            function formatNumber(value, decimals = 0) {
                if (value === null || value === undefined) return '0';

                // Convert to number if it's a string
                value = Number(value);

                // Format with commas and specified decimals
                return value.toLocaleString('en-IN', {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
            }

            // Initialize charts on page load
            //deliveryGaugeChart.render();
            //demandSupplyGaugeChart.render();
            marketDepthChart.render();
        });

        $(document).ready(function() {
            // Initialize AmCharts root elements
            let deliveryRoot = am5.Root.new("delivery-gauge-chart");
            let demandRoot = am5.Root.new("demand-supply-gauge-chart");

            // Set themes
            deliveryRoot.setThemes([am5themes_Animated.new(deliveryRoot)]);
            demandRoot.setThemes([am5themes_Animated.new(demandRoot)]);

            // Create charts and assign to global variables
            deliveryChart = createGaugeChart(deliveryRoot);
            demandChart = createGaugeChart(demandRoot);

            // Update the gauge update function
            window.updateGaugeChart = function(chart, value) {
                if (chart && typeof chart.animate === 'function') {
                    chart.animate({
                        key: "value",
                        to: Math.min(100, value),
                        duration: 800,
                        easing: am5.ease.out(am5.ease.cubic)
                    });
                }
            }

            // Remove the duplicate updateUI function from here
        });
    </script>
@endpush
