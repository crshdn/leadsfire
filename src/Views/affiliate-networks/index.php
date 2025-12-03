<?php
$pageTitle = 'Affiliate Networks';
$currentPage = 'affiliate-networks';

use LeadsFire\Controllers\AffiliateNetworkController;

$networks = [];
try {
    if (is_installed()) {
        $controller = new AffiliateNetworkController();
        $data = $controller->index();
        $networks = $data['networks'] ?? [];
    }
} catch (Exception $e) {
    // Database not ready
}

ob_start();
?>

<style>
    .asset-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }
    
    .asset-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1rem;
    }
    
    .asset-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        transition: all var(--transition-fast);
    }
    
    .asset-card:hover {
        border-color: var(--primary);
        box-shadow: 0 0 0 1px var(--primary);
    }
    
    .asset-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .asset-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }
    
    .asset-card-actions {
        display: flex;
        gap: 0.25rem;
    }
    
    .asset-card-meta {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .asset-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8125rem;
        color: var(--text-muted);
    }
    
    .asset-meta-label {
        color: var(--text-secondary);
    }
    
    .asset-meta-value {
        font-family: monospace;
        color: var(--primary);
        background: rgba(249, 115, 22, 0.1);
        padding: 0.125rem 0.375rem;
        border-radius: 4px;
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
    }
    
    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: var(--dark-200);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    
    .empty-state-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    
    .empty-state-text {
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }
    
    .modal {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
    }
    
    .modal-content {
        position: relative;
        width: 100%;
        max-width: 500px;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        background: var(--bg-card);
    }
    
    .modal-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0;
    }
    
    .modal-close {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: var(--radius-sm);
        transition: all var(--transition-fast);
    }
    
    .modal-close:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        position: sticky;
        bottom: 0;
        background: var(--bg-card);
    }
</style>

<div class="asset-header">
    <div>
        <p class="text-muted m-0">Manage your affiliate networks and postback settings.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Network
    </button>
</div>

<?php if (empty($networks)): ?>
<div class="empty-state">
    <div class="empty-state-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <line x1="20" y1="8" x2="20" y2="14"></line>
            <line x1="23" y1="11" x2="17" y2="11"></line>
        </svg>
    </div>
    <h3 class="empty-state-title">No affiliate networks yet</h3>
    <p class="empty-state-text">Add your affiliate networks to track conversions and revenue.</p>
    <button type="button" class="btn btn-primary" onclick="openModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Network
    </button>
</div>
<?php else: ?>
<div class="asset-grid">
    <?php foreach ($networks as $network): ?>
    <div class="asset-card">
        <div class="asset-card-header">
            <h3 class="asset-card-title"><?= e($network['Affiliate']) ?></h3>
            <div class="asset-card-actions">
                <button class="table-action-btn" onclick="editNetwork(<?= $network['AffiliateSourceID'] ?>)" title="Edit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
                <button class="table-action-btn danger" onclick="deleteNetwork(<?= $network['AffiliateSourceID'] ?>)" title="Delete">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="asset-card-meta">
            <?php if (!empty($network['RevenueParam'])): ?>
            <div class="asset-meta-item">
                <span class="asset-meta-label">Revenue Param:</span>
                <span class="asset-meta-value"><?= e($network['RevenueParam']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($network['PostbackURL'])): ?>
            <div class="asset-meta-item">
                <span class="asset-meta-label">Has Postback URL</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal -->
<div id="networkModal" class="modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add Affiliate Network</h3>
            <button type="button" class="modal-close" onclick="closeModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <form id="networkForm" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="networkId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" class="form-control" name="name" id="networkName" required placeholder="e.g., MaxBounty, ClickBank">
                </div>
                
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Revenue Parameter</label>
                        <input type="text" class="form-control" name="revenue_param" id="networkRevenueParam" value="revenue" placeholder="revenue">
                    </div>
                    <div class="form-group">
                        <label class="form-label">SubID Separator</label>
                        <input type="text" class="form-control" name="subid_separator" id="networkSubidSeparator" value="_" placeholder="_">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Offer URL Template</label>
                    <input type="text" class="form-control" name="offer_template" id="networkOfferTemplate" placeholder="https://network.com/offer?aff_sub={subid}">
                    <span class="form-text">Use {subid} placeholder for click ID</span>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Postback URL</label>
                    <input type="text" class="form-control" name="postback_url" id="networkPostbackUrl" placeholder="https://...">
                    <span class="form-text">URL to receive conversion notifications from this network</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id = null) {
    document.getElementById('networkModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = id ? 'Edit Affiliate Network' : 'Add Affiliate Network';
    document.getElementById('networkForm').reset();
    document.getElementById('networkId').value = id || '';
    
    if (id) {
        fetch('/api/affiliate-networks/' + id)
            .then(res => res.json())
            .then(data => {
                if (data) {
                    document.getElementById('networkName').value = data.Affiliate || '';
                    document.getElementById('networkRevenueParam').value = data.RevenueParam || '';
                    document.getElementById('networkSubidSeparator').value = data.SubIdSeparator || '';
                    document.getElementById('networkOfferTemplate').value = data.OfferTemplate || '';
                    document.getElementById('networkPostbackUrl').value = data.PostbackURL || '';
                }
            });
    }
}

function closeModal() {
    document.getElementById('networkModal').style.display = 'none';
}

function editNetwork(id) {
    openModal(id);
}

function deleteNetwork(id) {
    if (confirm('Are you sure you want to delete this affiliate network?')) {
        fetch('/api/affiliate-networks/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to delete network');
            }
        });
    }
}

document.getElementById('networkForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('networkId').value;
    const url = id ? '/api/affiliate-networks/' + id : '/api/affiliate-networks';
    const method = id ? 'PUT' : 'POST';
    
    const formData = new FormData(this);
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.errors ? Object.values(data.errors).join(', ') : 'Unknown error'));
        }
    });
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';

