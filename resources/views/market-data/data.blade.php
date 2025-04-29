<div id="data-container" style="display: none;">
    <!-- Stock Header Information -->
    <div class="stock-header">
        <div class="stock-content">
            <div class="row">
                <div class="col-md-6">
                    <div class="stock-symbol" id="stock-symbol">-</div>
                    <div class="stock-name" id="stock-name">-</div>
                    <div class="d-flex align-items-center">
                        <div class="stock-price" id="stock-price">₹0.00</div>
                        <div class="price-change ms-3" id="price-change">0.00 (0.00%)</div>
                    </div>
                    <div class="last-updated" id="last-updated">Last updated: --</div>
                </div>
                <div class="col-md-6">
                    <div class="price-details">
                        <div class="price-item">
                            <div class="price-label">Open</div>
                            <div class="price-value" id="price-open">₹0.00</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">High</div>
                            <div class="price-value" id="price-high">₹0.00</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">Low</div>
                            <div class="price-value" id="price-low">₹0.00</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">Close</div>
                            <div class="price-value" id="price-close">₹0.00</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">52W High</div>
                            <div class="price-value" id="price-year-high">₹0.00</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">52W Low</div>
                            <div class="price-value" id="price-year-low">₹0.00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Delivery Percentage Card -->
        <div class="col-md-4">
            <div class="card data-card">
                <div class="card-header bg-soft-primary">
                    <h5 class="card-title mb-0"><i class="bx bx-package me-2"></i>Delivery Percentage</h5>
                </div>
                <div class="card-body position-relative">
                    <div class="loading-overlay" id="delivery-loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="gauge-container">
                        <div id="delivery-gauge-chart"></div>
                        <div class="gauge-value" id="delivery-percentage-value">0%</div>
                        <div class="gauge-label">Delivery to Traded Quantity</div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Quantity Traded</div>
                                <div class="metric-value" id="quantity-traded">0</div>
                            </div>
                            <div>
                                <div class="metric-label">Delivery Quantity</div>
                                <div class="metric-value" id="delivery-quantity">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demand-Supply Pressure Card -->
        <div class="col-md-4">
            <div class="card data-card">
                <div class="card-header bg-soft-success">
                    <h5 class="card-title mb-0">Demand-Supply Pressure</h5>
                </div>
                <div class="card-body position-relative">
                    <div class="loading-overlay" id="demand-supply-loading">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="gauge-container">
                        <div id="demand-supply-gauge-chart"></div>
                        <div class="gauge-value" id="demand-supply-ratio-value">0%</div>
                        <div class="gauge-label">Buy Qty / Sell Qty</div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="metric-label">Total Buy Qty</div>
                                <div class="metric-value" id="total-buy-qty">0</div>
                            </div>
                            <div>
                                <div class="metric-label">Total Sell Qty</div>
                                <div class="metric-value" id="total-sell-qty">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trade Analysis Card -->
        <div class="col-md-4">
            <div class="card data-card">
                <div class="card-header bg-soft-info">
                    <h5 class="card-title mb-0">Trade Analysis</h5>
                </div>
                <div class="card-body position-relative">
                    <div class="loading-overlay" id="trade-analysis-loading">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="text-center mb-4">
                        <div class="metric-label">Average Trade Price</div>
                        <div class="metric-value" id="avg-trade-price">₹0.00</div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="metric-label">Total Traded Volume</div>
                            <div class="metric-value" id="total-traded-volume">0</div>
                        </div>
                        <div class="col-6">
                            <div class="metric-label">Total Traded Value</div>
                            <div class="metric-value" id="total-traded-value">₹0.00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Weighted Bid-Ask Prices Card -->
        <div class="col-md-6">
            <div class="card data-card">
                <div class="card-header bg-soft-warning">
                    <h5 class="card-title mb-0">Weighted Bid-Ask Prices</h5>
                </div>
                <div class="card-body position-relative">
                    <div class="loading-overlay" id="bid-ask-loading">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-4 text-center">
                            <div class="metric-label">Weighted Bid</div>
                            <div class="metric-value bid-price" id="weighted-bid">₹0.00</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="metric-label">Spread</div>
                            <div class="metric-value" id="bid-ask-spread">₹0.00</div>
                            <div id="bid-ask-spread-percentage">(0.00%)</div>
                        </div>
                        <div class="col-4 text-center">
                            <div class="metric-label">Weighted Ask</div>
                            <div class="metric-value ask-price" id="weighted-ask">₹0.00</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <h6 class="text-center bid-price">Bid (Buy)</h6>
                            <div class="table-responsive">
                                <table class="table table-sm bid-ask-table">
                                    <thead>
                                        <tr>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bid-table-body">
                                        <!-- Bid data will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="text-center ask-price">Ask (Sell)</h6>
                            <div class="table-responsive">
                                <table class="table table-sm bid-ask-table">
                                    <thead>
                                        <tr>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ask-table-body">
                                        <!-- Ask data will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liquidity Analysis Card -->
        <div class="col-md-6">
            <div class="card data-card">
                <div class="card-header bg-soft-danger">
                    <h5 class="card-title mb-0">Liquidity Analysis</h5>
                </div>
                <div class="card-body position-relative">
                    <div class="loading-overlay" id="liquidity-loading">
                        <div class="spinner-border text-danger" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="metric-label">Impact Cost</div>
                            <div class="metric-value" id="impact-cost">0.00%</div>
                            <div class="mt-2">
                                <div class="metric-label">Liquidity Rating</div>
                                <div class="liquidity-indicator" id="liquidity-indicator"></div>
                                <div id="liquidity-rating">N/A</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metric-label">Market Depth Ratio</div>
                            <div class="metric-value" id="market-depth-ratio">0.00%</div>
                            <div class="mt-2">
                                <div class="metric-label">Volatility (Daily)</div>
                                <div class="metric-value" id="daily-volatility">0.00%</div>
                            </div>
                        </div>
                    </div>
                    <div class="market-depth-container">
                        <div id="market-depth-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
