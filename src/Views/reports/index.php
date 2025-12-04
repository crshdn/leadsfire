<?php
$pageTitle = 'Reports';
$currentPage = 'reports';

use LeadsFire\Models\Stats;
use LeadsFire\Models\Campaign;

// Date range
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

$overallStats = [];
$trendData = [];
$campaignStats = [];
$trafficSourceStats = [];

try {
    $statsModel = new Stats();
    $overallStats = $statsModel->getDashboardStats($startDate, $endDate);
    $trendData = $statsModel->getDateRangeStats($startDate, $endDate);
    $campaignStats = $statsModel->getTopCampaigns(50, 'profit', $startDate, $endDate);
    $trafficSourceStats = $statsModel->getByTrafficSource($startDate, $endDate);
} catch (Exception $e) {
    // Handle error
}

ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Reports</h1>
    <div class="page-actions">
        <form method="GET" class="d-flex gap-2 align-items-center">
            <input type="date" name="start_date" value="<?= e($startDate) ?>" class="form-control">
            <span>to</span>
            <input type="date" name="end_date" value="<?= e($endDate) ?>" class="form-control">
            <button type="submit" class="btn btn-primary">Apply</button>
        </form>
    </div>
</div>

<!-- Quick Date Filters -->
<div class="mb-4">
    <div class="btn-group">
        <a href="?start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>" class="btn btn-outline btn-sm">Today</a>
        <a href="?start_date=<?= date('Y-m-d', strtotime('-1 day')) ?>&end_date=<?= date('Y-m-d', strtotime('-1 day')) ?>" class="btn btn-outline btn-sm">Yesterday</a>
        <a href="?start_date=<?= date('Y-m-d', strtotime('-7 days')) ?>&end_date=<?= date('Y-m-d') ?>" class="btn btn-outline btn-sm">Last 7 Days</a>
        <a href="?start_date=<?= date('Y-m-d', strtotime('-30 days')) ?>&end_date=<?= date('Y-m-d') ?>" class="btn btn-outline btn-sm">Last 30 Days</a>
        <a href="?start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-d') ?>" class="btn btn-outline btn-sm">This Month</a>
    </div>
</div>

<!-- Overall Stats -->
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-card-value"><?= number_format($overallStats['total_views'] ?? 0) ?></div>
        <div class="stat-card-label">Views</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value"><?= number_format($overallStats['total_conversions'] ?? 0) ?></div>
        <div class="stat-card-label">Conversions</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value">$<?= number_format($overallStats['total_revenue'] ?? 0, 2) ?></div>
        <div class="stat-card-label">Revenue</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value">$<?= number_format($overallStats['total_cost'] ?? 0, 2) ?></div>
        <div class="stat-card-label">Cost</div>
    </div>
    <div class="stat-card <?= ($overallStats['total_profit'] ?? 0) >= 0 ? 'success' : 'danger' ?>">
        <div class="stat-card-value">$<?= number_format($overallStats['total_profit'] ?? 0, 2) ?></div>
        <div class="stat-card-label">Profit</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-value"><?= number_format($overallStats['cvr'] ?? 0, 2) ?>%</div>
        <div class="stat-card-label">CVR</div>
    </div>
</div>

<!-- Trend Chart -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Performance Trend</h3>
    </div>
    <div class="card-body">
        <div id="trendChart" style="height: 300px;"></div>
    </div>
</div>

<!-- Campaign Performance -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Campaign Performance</h3>
    </div>
    <div class="card-body">
        <?php if (empty($campaignStats)): ?>
        <div class="empty-state">
            <p>No campaign data for the selected date range.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Campaign</th>
                    <th class="text-right">Views</th>
                    <th class="text-right">Conversions</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Cost</th>
                    <th class="text-right">Profit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($campaignStats as $row): ?>
                <tr>
                    <td>
                        <a href="/campaigns/edit?id=<?= $row['id'] ?>"><?= e($row['name']) ?></a>
                    </td>
                    <td class="text-right"><?= number_format($row['views'] ?? 0) ?></td>
                    <td class="text-right"><?= number_format($row['conversions'] ?? 0) ?></td>
                    <td class="text-right">$<?= number_format($row['revenue'] ?? 0, 2) ?></td>
                    <td class="text-right">$<?= number_format($row['cost'] ?? 0, 2) ?></td>
                    <td class="text-right <?= ($row['profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                        $<?= number_format($row['profit'] ?? 0, 2) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Traffic Source Performance -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Traffic Source Performance</h3>
    </div>
    <div class="card-body">
        <?php if (empty($trafficSourceStats)): ?>
        <div class="empty-state">
            <p>No traffic source data for the selected date range.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Traffic Source</th>
                    <th class="text-right">Views</th>
                    <th class="text-right">Conversions</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Cost</th>
                    <th class="text-right">Profit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trafficSourceStats as $row): ?>
                <tr>
                    <td><?= e($row['name']) ?></td>
                    <td class="text-right"><?= number_format($row['views'] ?? 0) ?></td>
                    <td class="text-right"><?= number_format($row['conversions'] ?? 0) ?></td>
                    <td class="text-right">$<?= number_format($row['revenue'] ?? 0, 2) ?></td>
                    <td class="text-right">$<?= number_format($row['cost'] ?? 0, 2) ?></td>
                    <td class="text-right <?= ($row['profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger' ?>">
                        $<?= number_format($row['profit'] ?? 0, 2) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
<script>
// Trend Chart
var trendChart = echarts.init(document.getElementById('trendChart'));
var trendData = <?= json_encode($trendData) ?>;

var dates = trendData.map(d => d.date);
var views = trendData.map(d => parseInt(d.views) || 0);
var conversions = trendData.map(d => parseInt(d.conversions) || 0);
var profit = trendData.map(d => parseFloat(d.profit) || 0);

trendChart.setOption({
    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data: ['Views', 'Conversions', 'Profit'],
        textStyle: { color: '#888' }
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: {
        type: 'category',
        data: dates,
        axisLine: { lineStyle: { color: '#444' } },
        axisLabel: { color: '#888' }
    },
    yAxis: [
        {
            type: 'value',
            name: 'Count',
            axisLine: { lineStyle: { color: '#444' } },
            axisLabel: { color: '#888' },
            splitLine: { lineStyle: { color: '#333' } }
        },
        {
            type: 'value',
            name: 'Profit ($)',
            axisLine: { lineStyle: { color: '#444' } },
            axisLabel: { color: '#888', formatter: '${value}' },
            splitLine: { show: false }
        }
    ],
    series: [
        {
            name: 'Views',
            type: 'bar',
            data: views,
            itemStyle: { color: '#4a9eff' }
        },
        {
            name: 'Conversions',
            type: 'bar',
            data: conversions,
            itemStyle: { color: '#00c853' }
        },
        {
            name: 'Profit',
            type: 'line',
            yAxisIndex: 1,
            data: profit,
            itemStyle: { color: '#ff9800' },
            lineStyle: { width: 2 }
        }
    ]
});

window.addEventListener('resize', function() {
    trendChart.resize();
});
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/app.php';
?>
