<?php
$pageTitle = 'Offers';
$currentPage = 'offers';

use LeadsFire\Models\Offer;
use LeadsFire\Models\AffiliateNetwork;

$offers = [];
$affiliateNetworks = [];
try {
    if (is_installed()) {
        $offerModel = new Offer();
        $offers = $offerModel->getAll();
        
        $networkModel = new AffiliateNetwork();
        $affiliateNetworks = $networkModel->getForSelect();
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
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
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
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    
    .asset-card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 0.25rem 0;
    }
    
    .asset-card-subtitle {
        font-size: 0.8125rem;
        color: var(--text-muted);
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
    
    .asset-meta-value.money {
        color: var(--success);
        background: rgba(34, 197, 94, 0.1);
    }
    
    .asset-url {
        font-size: 0.75rem;
        color: var(--text-muted);
        word-break: break-all;
        background: var(--dark-300);
        padding: 0.5rem;
        border-radius: var(--radius-sm);
        margin-top: 0.75rem;
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
</style>

<div class="asset-header">
    <div>
        <p class="text-muted m-0">Manage your offers and payouts.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Offer
    </button>
</div>

<?php if (empty($offers)): ?>
<div class="empty-state">
    <div class="empty-state-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5">
            <path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"></path>
            <path d="M4 6v12c0 1.1.9 2 2 2h14v-4"></path>
            <path d="M18 12a2 2 0 0 0-2 2c0 1.1.9 2 2 2h4v-4h-4z"></path>
        </svg>
    </div>
    <h3 class="empty-state-title">No offers yet</h3>
    <p class="empty-state-text">Add your first offer to start tracking conversions and revenue.</p>
    <button type="button" class="btn btn-primary" onclick="openModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Offer
    </button>
</div>
<?php else: ?>
<div class="asset-grid">
    <?php foreach ($offers as $offer): ?>
    <div class="asset-card">
        <div class="asset-card-header">
            <div>
                <h3 class="asset-card-title"><?= e($offer['OfferName']) ?></h3>
                <?php if (!empty($offer['Affiliate'])): ?>
                <span class="asset-card-subtitle"><?= e($offer['Affiliate']) ?></span>
                <?php endif; ?>
            </div>
            <div class="asset-card-actions">
                <button class="table-action-btn" onclick="editOffer(<?= $offer['PredefOfferID'] ?>)" title="Edit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
                <button class="table-action-btn danger" onclick="deleteOffer(<?= $offer['PredefOfferID'] ?>)" title="Delete">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="asset-card-meta">
            <div class="asset-meta-item">
                <span class="asset-meta-label">Payout:</span>
                <span class="asset-meta-value money">$<?= number_format($offer['Payout'], 2) ?></span>
            </div>
        </div>
        <?php if (!empty($offer['OfferUrl'])): ?>
        <div class="asset-url"><?= e(substr($offer['OfferUrl'], 0, 80)) ?><?= strlen($offer['OfferUrl']) > 80 ? '...' : '' ?></div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal -->
<div id="offerModal" class="modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add Offer</h3>
            <button type="button" class="modal-close" onclick="closeModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <form id="offerForm" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="offerId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Offer Name *</label>
                    <input type="text" class="form-control" name="name" id="offerName" required placeholder="e.g., Weight Loss Trial">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Offer URL *</label>
                    <input type="url" class="form-control" name="url" id="offerUrl" required placeholder="https://affiliate-network.com/offer?subid={subid}">
                    <span class="form-text">Use {subid} placeholder for tracking</span>
                </div>
                
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Payout ($)</label>
                        <input type="number" step="0.01" class="form-control" name="payout" id="offerPayout" value="0.00" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Affiliate Network</label>
                        <select class="form-select" name="affiliate_network_id" id="offerNetwork">
                            <option value="0">-- None --</option>
                            <?php foreach ($affiliateNetworks as $network): ?>
                            <option value="<?= $network['id'] ?>"><?= e($network['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" id="offerNotes" rows="2" placeholder="Optional notes about this offer"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
            </div>
        </form>
    </div>
</div>

<style>
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
    }
    
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
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
    }
</style>

<script>
function openModal(id = null) {
    document.getElementById('offerModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = id ? 'Edit Offer' : 'Add Offer';
    document.getElementById('offerForm').reset();
    document.getElementById('offerId').value = id || '';
    
    if (id) {
        // Load offer data via API
        fetch('/api/offers/' + id)
            .then(res => res.json())
            .then(data => {
                if (data) {
                    document.getElementById('offerName').value = data.OfferName || '';
                    document.getElementById('offerUrl').value = data.OfferUrl || '';
                    document.getElementById('offerPayout').value = data.Payout || '0.00';
                    document.getElementById('offerNetwork').value = data.AffiliateSourceID || '0';
                    document.getElementById('offerNotes').value = data.Notes || '';
                }
            });
    }
}

function closeModal() {
    document.getElementById('offerModal').style.display = 'none';
}

function editOffer(id) {
    openModal(id);
}

function deleteOffer(id) {
    if (confirm('Are you sure you want to delete this offer?')) {
        fetch('/api/offers/' + id, {
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
                alert('Failed to delete offer');
            }
        });
    }
}

document.getElementById('offerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('offerId').value;
    const url = id ? '/api/offers/' + id : '/api/offers';
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

// Close modal on escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';

