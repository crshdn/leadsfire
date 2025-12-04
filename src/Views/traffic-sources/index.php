<?php
$pageTitle = 'Traffic Sources';
$currentPage = 'traffic-sources';

use LeadsFire\Models\TrafficSource;

$trafficSources = [];
try {
    $model = new TrafficSource();
    $trafficSources = $model->getAll();
} catch (Exception $e) {
    // Handle error
}

ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Traffic Sources</h1>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="showAddModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Traffic Source
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($trafficSources)): ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="2" y1="12" x2="22" y2="12"></line>
                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            </svg>
            <h3>No Traffic Sources</h3>
            <p>Add your first traffic source to start tracking where your visitors come from.</p>
            <button class="btn btn-primary" onclick="showAddModal()">Add Traffic Source</button>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Postback URL</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trafficSources as $source): ?>
                <tr>
                    <td><?= e($source['name']) ?></td>
                    <td><code><?= e($source['slug']) ?></code></td>
                    <td>
                        <?php if (!empty($source['postback_url'])): ?>
                        <span class="text-muted"><?= e(strlen($source['postback_url']) > 40 ? substr($source['postback_url'], 0, 40) . '...' : $source['postback_url']) ?></span>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-<?= $source['is_active'] ? 'success' : 'secondary' ?>">
                            <?= $source['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y', strtotime($source['created_at'])) ?></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline" onclick="editSource(<?= $source['id'] ?>)">Edit</button>
                            <button class="btn btn-sm btn-outline text-danger" onclick="deleteSource(<?= $source['id'] ?>)">Delete</button>
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
    alert('Add Traffic Source modal - Coming soon');
}

function editSource(id) {
    alert('Edit Traffic Source ' + id + ' - Coming soon');
}

function deleteSource(id) {
    if (confirm('Are you sure you want to delete this traffic source?')) {
        alert('Delete Traffic Source ' + id + ' - Coming soon');
    }
}
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/app.php';
?>
