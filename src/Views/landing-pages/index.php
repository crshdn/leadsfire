<?php
$pageTitle = 'Landing Pages';
$currentPage = 'landing-pages';

use LeadsFire\Models\LandingPage;

$landingPages = [];
try {
    $model = new LandingPage();
    $landingPages = $model->getAll();
} catch (Exception $e) {
    // Handle error
}

ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Landing Pages</h1>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="showAddModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Landing Page
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($landingPages)): ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
            </svg>
            <h3>No Landing Pages</h3>
            <p>Create your first landing page to use in campaigns.</p>
            <button class="btn btn-primary" onclick="showAddModal()">Add Landing Page</button>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Group</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($landingPages as $lp): ?>
                <tr>
                    <td><?= e($lp['name']) ?></td>
                    <td>
                        <a href="<?= e($lp['url']) ?>" target="_blank" class="text-muted">
                            <?= e(strlen($lp['url']) > 50 ? substr($lp['url'], 0, 50) . '...' : $lp['url']) ?>
                        </a>
                    </td>
                    <td><?= e($lp['group_name'] ?? '-') ?></td>
                    <td>
                        <span class="badge badge-<?= $lp['is_active'] ? 'success' : 'secondary' ?>">
                            <?= $lp['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y', strtotime($lp['created_at'])) ?></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline" onclick="editLandingPage(<?= $lp['id'] ?>)">Edit</button>
                            <button class="btn btn-sm btn-outline text-danger" onclick="deleteLandingPage(<?= $lp['id'] ?>)">Delete</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script>
function showAddModal() {
    alert('Add Landing Page modal - Coming soon');
}

function editLandingPage(id) {
    alert('Edit Landing Page ' + id + ' - Coming soon');
}

function deleteLandingPage(id) {
    if (confirm('Are you sure you want to delete this landing page?')) {
        alert('Delete Landing Page ' + id + ' - Coming soon');
    }
}
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/app.php';
?>

