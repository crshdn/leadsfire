<?php
$pageTitle = 'Conversions';
$currentPage = 'conversions';

use LeadsFire\Services\Database;

// Date range
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');

$conversions = [];
try {
    $db = Database::getInstance();
    $conversions = $db->fetchAll(
        "SELECT 
            cv.id,
            cv.transaction_id,
            cv.revenue,
            cv.status,
            cv.created_at,
            c.name as campaign_name,
            c.key_code,
            cl.click_id,
            cl.subid,
            cl.country
         FROM conversions cv
         JOIN clicks cl ON cv.click_id = cl.id
         JOIN campaigns c ON cv.campaign_id = c.id
         WHERE DATE(cv.created_at) BETWEEN ? AND ?
         ORDER BY cv.created_at DESC
         LIMIT 500",
        [$startDate, $endDate]
    );
} catch (Exception $e) {
    // Handle error
}

ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Conversions</h1>
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
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($conversions)): ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <h3>No Conversions</h3>
            <p>No conversions recorded for the selected date range.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>Campaign</th>
                    <th>SubID</th>
                    <th>Click ID</th>
                    <th>Transaction ID</th>
                    <th>Country</th>
                    <th>Revenue</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conversions as $conv): ?>
                <tr>
                    <td><?= date('M j, Y g:i A', strtotime($conv['created_at'])) ?></td>
                    <td>
                        <a href="/campaigns/edit?id=<?= $conv['campaign_id'] ?? '' ?>">
                            <?= e($conv['campaign_name']) ?>
                        </a>
                    </td>
                    <td><?= e($conv['subid'] ?? '-') ?></td>
                    <td><code><?= e($conv['click_id']) ?></code></td>
                    <td><?= e($conv['transaction_id'] ?? '-') ?></td>
                    <td><?= e($conv['country'] ?? '-') ?></td>
                    <td class="text-success">$<?= number_format($conv['revenue'] ?? 0, 2) ?></td>
                    <td>
                        <?php
                        $statusClass = match($conv['status']) {
                            'approved' => 'success',
                            'pending' => 'warning',
                            'rejected' => 'danger',
                            default => 'secondary'
                        };
                        ?>
                        <span class="badge badge-<?= $statusClass ?>"><?= ucfirst($conv['status']) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/app.php';
?>

