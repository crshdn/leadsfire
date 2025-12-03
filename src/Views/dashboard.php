<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

// Start output buffering for content
ob_start();
?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-card-value">0</div>
        <div class="stat-card-label">Total Clicks Today</div>
        <div class="stat-card-change positive">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <span>0% from yesterday</span>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-card-value">0</div>
        <div class="stat-card-label">Conversions Today</div>
        <div class="stat-card-change positive">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <span>0% from yesterday</span>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-card-value">$0.00</div>
        <div class="stat-card-label">Revenue Today</div>
        <div class="stat-card-change positive">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <span>$0.00 from yesterday</span>
        </div>
    </div>
    
    <div class="stat-card danger">
        <div class="stat-card-value">$0.00</div>
        <div class="stat-card-label">Profit Today</div>
        <div class="stat-card-change positive">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <span>$0.00 from yesterday</span>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Performance Chart -->
    <div class="col-span-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="m-0" style="font-size: 1rem;">Performance Overview</h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-ghost btn-sm">7 Days</button>
                    <button class="btn btn-ghost btn-sm">30 Days</button>
                    <button class="btn btn-ghost btn-sm">90 Days</button>
                </div>
            </div>
            <div class="card-body">
                <div id="performanceChart" style="height: 300px; display: flex; align-items: center; justify-content: center;">
                    <p class="text-muted">No data available yet. Create a campaign to start tracking.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="col-span-4">
        <div class="card">
            <div class="card-header">
                <h3 class="m-0" style="font-size: 1rem;">Quick Stats</h3>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Active Campaigns</span>
                    <span class="fw-semibold">0</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Total Campaigns</span>
                    <span class="fw-semibold">0</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Traffic Sources</span>
                    <span class="fw-semibold">0</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Affiliate Networks</span>
                    <span class="fw-semibold">0</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Landing Pages</span>
                    <span class="fw-semibold">0</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Campaigns -->
    <div class="col-span-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="m-0" style="font-size: 1rem;">Recent Campaigns</h3>
                <a href="/campaigns/create" class="btn btn-primary btn-sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    New Campaign
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-wrapper" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Traffic Source</th>
                                <th>Views</th>
                                <th>Clicks</th>
                                <th>Conv.</th>
                                <th>Revenue</th>
                                <th>Cost</th>
                                <th>Profit</th>
                                <th>ROI</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="10" class="text-center text-muted" style="padding: 3rem;">
                                    No campaigns yet. <a href="/campaigns/create">Create your first campaign</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include layout
require __DIR__ . '/layouts/app.php';

