<?php
$pageTitle = 'Affiliate Networks';
$currentPage = 'affiliate-networks';

use LeadsFire\Models\AffiliateNetwork;

$networks = [];
try {
    $model = new AffiliateNetwork();
    $networks = $model->getAll();
} catch (Exception $e) {
    // Handle error
}

ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Affiliate Networks</h1>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="showAddModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Network
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($networks)): ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <line x1="20" y1="8" x2="20" y2="14"></line>
                <line x1="23" y1="11" x2="17" y2="11"></line>
            </svg>
            <h3>No Affiliate Networks</h3>
            <p>Add your affiliate networks to track conversions and revenue.</p>
            <button class="btn btn-primary" onclick="showAddModal()">Add Network</button>
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
                <?php foreach ($networks as $network): ?>
                <tr>
                    <td><?= e($network['name']) ?></td>
                    <td><code><?= e($network['slug']) ?></code></td>
                    <td>
                        <?php if (!empty($network['postback_url'])): ?>
                        <span class="text-muted"><?= e(strlen($network['postback_url']) > 40 ? substr($network['postback_url'], 0, 40) . '...' : $network['postback_url']) ?></span>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-<?= $network['is_active'] ? 'success' : 'secondary' ?>">
                            <?= $network['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y', strtotime($network['created_at'])) ?></td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline" onclick="editNetwork(<?= $network['id'] ?>)">Edit</button>
                            <button class="btn btn-sm btn-outline text-danger" onclick="deleteNetwork(<?= $network['id'] ?>)">Delete</button>
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
    alert('Add Affiliate Network modal - Coming soon');
}

function editNetwork(id) {
    alert('Edit Affiliate Network ' + id + ' - Coming soon');
}

function deleteNetwork(id) {
    if (confirm('Are you sure you want to delete this affiliate network?')) {
        alert('Delete Affiliate Network ' + id + ' - Coming soon');
    }
}
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/app.php';
?>
