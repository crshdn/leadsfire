<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

use LeadsFire\Models\Stats;
use LeadsFire\Models\Campaign;

// Get stats
$todayStats = ['views' => 0, 'clicks' => 0, 'conversions' => 0, 'revenue' => 0, 'profit' => 0, 
               'views_change' => 0, 'conversions_change' => 0, 'revenue_change' => 0, 'profit_change' => 0];
$quickStats = ['active_campaigns' => 0, 'total_campaigns' => 0, 'traffic_sources' => 0, 
               'affiliate_networks' => 0, 'landing_pages' => 0];
$chartData = [];
$topCampaigns = [];

try {
    if (is_installed()) {
        $statsModel = new Stats();
        $todayStats = $statsModel->getTodayStats();
        $quickStats = $statsModel->getQuickStats();
        
        // Get last 7 days for chart
        $startDate = date('Y-m-d', strtotime('-6 days'));
        $endDate = date('Y-m-d');
        $chartData = $statsModel->getDateRangeStats($startDate, $endDate);
        
        // Get top campaigns
        $topCampaigns = $statsModel->getTopCampaigns(10);
    }
} catch (Exception $e) {
    // Database not ready
}

// Start output buffering for content
ob_start();
?>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-card-value"><?= number_format($todayStats['views']) ?></div>
        <div class="stat-card-label">Total Clicks Today</div>
        <div class="stat-card-change <?= $todayStats['views_change'] >= 0 ? 'positive' : 'negative' ?>">
            <?php if ($todayStats['views_change'] >= 0): ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <?php else: ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline>
                <polyline points="17 18 23 18 23 12"></polyline>
            </svg>
            <?php endif; ?>
            <span><?= abs($todayStats['views_change']) ?>% from yesterday</span>
        </div>
    </div>
    
    <div class="stat-card success">
        <div class="stat-card-value"><?= number_format($todayStats['conversions']) ?></div>
        <div class="stat-card-label">Conversions Today</div>
        <div class="stat-card-change <?= $todayStats['conversions_change'] >= 0 ? 'positive' : 'negative' ?>">
            <?php if ($todayStats['conversions_change'] >= 0): ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <?php else: ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline>
                <polyline points="17 18 23 18 23 12"></polyline>
            </svg>
            <?php endif; ?>
            <span><?= abs($todayStats['conversions_change']) ?>% from yesterday</span>
        </div>
    </div>
    
    <div class="stat-card warning">
        <div class="stat-card-value"><?= format_money($todayStats['revenue']) ?></div>
        <div class="stat-card-label">Revenue Today</div>
        <div class="stat-card-change <?= $todayStats['revenue_change'] >= 0 ? 'positive' : 'negative' ?>">
            <?php if ($todayStats['revenue_change'] >= 0): ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <?php else: ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline>
                <polyline points="17 18 23 18 23 12"></polyline>
            </svg>
            <?php endif; ?>
            <span><?= format_money(abs($todayStats['revenue_change'])) ?> from yesterday</span>
        </div>
    </div>
    
    <div class="stat-card <?= $todayStats['profit'] >= 0 ? 'success' : 'danger' ?>">
        <div class="stat-card-value"><?= format_money($todayStats['profit']) ?></div>
        <div class="stat-card-label">Profit Today</div>
        <div class="stat-card-change <?= $todayStats['profit_change'] >= 0 ? 'positive' : 'negative' ?>">
            <?php if ($todayStats['profit_change'] >= 0): ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                <polyline points="17 6 23 6 23 12"></polyline>
            </svg>
            <?php else: ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline>
                <polyline points="17 18 23 18 23 12"></polyline>
            </svg>
            <?php endif; ?>
            <span><?= format_money(abs($todayStats['profit_change'])) ?> from yesterday</span>
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
                    <button class="btn btn-ghost btn-sm date-range-btn active" data-days="7">7 Days</button>
                    <button class="btn btn-ghost btn-sm date-range-btn" data-days="30">30 Days</button>
                    <button class="btn btn-ghost btn-sm date-range-btn" data-days="90">90 Days</button>
                </div>
            </div>
            <div class="card-body">
                <div id="performanceChart" style="height: 300px;"></div>
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
                    <span class="fw-semibold"><?= number_format($quickStats['active_campaigns']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Total Campaigns</span>
                    <span class="fw-semibold"><?= number_format($quickStats['total_campaigns']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Traffic Sources</span>
                    <span class="fw-semibold"><?= number_format($quickStats['traffic_sources']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Affiliate Networks</span>
                    <span class="fw-semibold"><?= number_format($quickStats['affiliate_networks']) ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Landing Pages</span>
                    <span class="fw-semibold"><?= number_format($quickStats['landing_pages']) ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Campaigns -->
    <div class="col-span-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="m-0" style="font-size: 1rem;">Top Campaigns</h3>
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
                                <th class="text-right">Views</th>
                                <th class="text-right">Clicks</th>
                                <th class="text-right">Conv.</th>
                                <th class="text-right">Revenue</th>
                                <th class="text-right">Cost</th>
                                <th class="text-right">Profit</th>
                                <th class="text-right">ROI</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topCampaigns)): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted" style="padding: 3rem;">
                                    No campaigns yet. <a href="/campaigns/create">Create your first campaign</a>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($topCampaigns as $campaign): ?>
                            <tr>
                                <td>
                                    <a href="/campaigns/edit?id=<?= $campaign['CampaignID'] ?>" class="fw-medium" style="color: var(--text-primary);">
                                        <?= e($campaign['CampaignName']) ?>
                                    </a>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                                        <?= e($campaign['CampaignKey']) ?>
                                    </div>
                                </td>
                                <td><?= e($campaign['TrafficSource'] ?? '-') ?></td>
                                <td class="text-right"><?= number_format($campaign['Views']) ?></td>
                                <td class="text-right"><?= number_format($campaign['Clicks']) ?></td>
                                <td class="text-right"><?= number_format($campaign['Conversions']) ?></td>
                                <td class="text-right"><?= format_money($campaign['Revenue']) ?></td>
                                <td class="text-right"><?= format_money($campaign['Cost']) ?></td>
                                <td class="text-right <?= $campaign['Profit'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= format_money($campaign['Profit']) ?>
                                </td>
                                <td class="text-right <?= $campaign['ROI'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= format_percent($campaign['ROI']) ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $campaign['Active'] ? 'success' : 'secondary' ?>">
                                        <?= $campaign['Active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ECharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<script>
// Chart data from PHP
const chartData = <?= json_encode($chartData) ?>;

// Initialize chart
const chartDom = document.getElementById('performanceChart');
const myChart = echarts.init(chartDom, 'dark');

function renderChart(data) {
    const dates = data.map(d => {
        const date = new Date(d.date);
        return (date.getMonth() + 1) + '/' + date.getDate();
    });
    const revenue = data.map(d => parseFloat(d.revenue) || 0);
    const cost = data.map(d => parseFloat(d.cost) || 0);
    const profit = data.map(d => parseFloat(d.profit) || 0);
    const conversions = data.map(d => parseInt(d.conversions) || 0);
    
    const option = {
        backgroundColor: 'transparent',
        tooltip: {
            trigger: 'axis',
            backgroundColor: 'rgba(31, 31, 35, 0.95)',
            borderColor: 'rgba(63, 63, 70, 0.5)',
            textStyle: {
                color: '#fafafa'
            },
            formatter: function(params) {
                let html = `<div style="font-weight: 600; margin-bottom: 8px;">${params[0].axisValue}</div>`;
                params.forEach(p => {
                    const value = p.seriesName === 'Conversions' ? p.value : '$' + p.value.toFixed(2);
                    html += `<div style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: ${p.color};"></span>
                        <span style="flex: 1;">${p.seriesName}</span>
                        <span style="font-weight: 600;">${value}</span>
                    </div>`;
                });
                return html;
            }
        },
        legend: {
            data: ['Revenue', 'Cost', 'Profit', 'Conversions'],
            bottom: 0,
            textStyle: {
                color: '#a1a1aa'
            },
            itemStyle: {
                borderWidth: 0
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            top: '10%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: dates,
            axisLine: {
                lineStyle: {
                    color: 'rgba(63, 63, 70, 0.5)'
                }
            },
            axisLabel: {
                color: '#71717a'
            }
        },
        yAxis: [
            {
                type: 'value',
                name: 'Amount ($)',
                nameTextStyle: {
                    color: '#71717a'
                },
                axisLine: {
                    show: false
                },
                splitLine: {
                    lineStyle: {
                        color: 'rgba(63, 63, 70, 0.3)'
                    }
                },
                axisLabel: {
                    color: '#71717a',
                    formatter: '${value}'
                }
            },
            {
                type: 'value',
                name: 'Conversions',
                nameTextStyle: {
                    color: '#71717a'
                },
                axisLine: {
                    show: false
                },
                splitLine: {
                    show: false
                },
                axisLabel: {
                    color: '#71717a'
                }
            }
        ],
        series: [
            {
                name: 'Revenue',
                type: 'line',
                smooth: true,
                data: revenue,
                lineStyle: {
                    color: '#22c55e',
                    width: 2
                },
                itemStyle: {
                    color: '#22c55e'
                },
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [
                            { offset: 0, color: 'rgba(34, 197, 94, 0.3)' },
                            { offset: 1, color: 'rgba(34, 197, 94, 0)' }
                        ]
                    }
                }
            },
            {
                name: 'Cost',
                type: 'line',
                smooth: true,
                data: cost,
                lineStyle: {
                    color: '#ef4444',
                    width: 2
                },
                itemStyle: {
                    color: '#ef4444'
                }
            },
            {
                name: 'Profit',
                type: 'line',
                smooth: true,
                data: profit,
                lineStyle: {
                    color: '#f97316',
                    width: 3
                },
                itemStyle: {
                    color: '#f97316'
                },
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0, y: 0, x2: 0, y2: 1,
                        colorStops: [
                            { offset: 0, color: 'rgba(249, 115, 22, 0.2)' },
                            { offset: 1, color: 'rgba(249, 115, 22, 0)' }
                        ]
                    }
                }
            },
            {
                name: 'Conversions',
                type: 'bar',
                yAxisIndex: 1,
                data: conversions,
                itemStyle: {
                    color: 'rgba(59, 130, 246, 0.6)',
                    borderRadius: [4, 4, 0, 0]
                },
                barWidth: '40%'
            }
        ]
    };
    
    myChart.setOption(option);
}

// Initial render
if (chartData.length > 0) {
    renderChart(chartData);
} else {
    // Show placeholder
    chartDom.innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: var(--text-muted);">No data available yet. Create a campaign to start tracking.</div>';
}

// Date range buttons
document.querySelectorAll('.date-range-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.date-range-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const days = parseInt(this.dataset.days);
        
        // Fetch new data via API
        fetch(`/api/stats/range?days=${days}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    renderChart(data);
                }
            })
            .catch(err => console.error('Failed to load stats:', err));
    });
});

// Resize chart on window resize
window.addEventListener('resize', function() {
    myChart.resize();
});
</script>

<?php
$content = ob_get_clean();

// Include layout
require __DIR__ . '/layouts/app.php';
