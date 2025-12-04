<?php
$pageTitle = 'Offers';
$currentPage = 'offers';

use LeadsFire\Models\Offer;

$offers = [];
try {
    $model = new Offer();
    $offers = $model->getAll();
} catch (Exception $e) {
    // Handle error
}

ob_start();
?>

<div class="page-header">
    <h1 class="page-title">Offers</h1>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="showAddModal()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add Offer
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($offers)): ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <line x1="12" y1="1" x2="12" y2="23"></line>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
            <h3>No Offers</h3>
            <p>Create your first offer to use in campaigns.</p>
            <button class="btn btn-primary" onclick="showAddModal()">Add Offer</button>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Network</th>
                    <th>Payout</th>
                    <th>URL</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($offers as $offer): ?>
                <tr>
                    <td><?= e($offer['name']) ?></td>
                    <td><?= e($offer['network_name'] ?? '-') ?></td>
                    <td>$<?= number_format($offer['payout'] ?? 0, 2) ?></td>
                    <td>
                        <a href="<?= e($offer['url']) ?>" target="_blank" class="text-muted">
                            <?= e(strlen($offer['url']) > 40 ? substr($offer['url'], 0, 40) . '...' : $offer['url']) ?>
                        </a>
                    </td>
                    <td>
                        <span class="badge badge-<?= $offer['is_active'] ? 'success' : 'secondary' ?>">
                            <?= $offer['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline" onclick="editOffer(<?= $offer['id'] ?>)">Edit</button>
                            <button class="btn btn-sm btn-outline text-danger" onclick="deleteOffer(<?= $offer['id'] ?>)">Delete</button>
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
    alert('Add Offer modal - Coming soon');
}

function editOffer(id) {
    alert('Edit Offer ' + id + ' - Coming soon');
}

function deleteOffer(id) {
    if (confirm('Are you sure you want to delete this offer?')) {
        alert('Delete Offer ' + id + ' - Coming soon');
    }
}
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/src/Views/layouts/app.php';
?>

