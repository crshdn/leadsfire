<?php
$pageTitle = 'Edit Campaign: ' . e($campaign['CampaignName']);
$currentPage = 'campaigns';

// Generate tracking URL
$trackingDomain = config('app.url', 'https://your-domain.com');
$trackingUrl = rtrim($trackingDomain, '/') . '/c/' . $campaign['CampaignKey'];

// Start output buffering for content
ob_start();
?>

<style>
    .campaign-form {
        max-width: 900px;
    }
    
    .form-section {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        margin-bottom: 1.5rem;
    }
    
    .form-section-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .form-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-section-body {
        padding: 1.5rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .form-row.three-col {
        grid-template-columns: repeat(3, 1fr);
    }
    
    @media (max-width: 768px) {
        .form-row, .form-row.three-col {
            grid-template-columns: 1fr;
        }
    }
    
    .rotation-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        margin-bottom: 0.75rem;
    }
    
    .rotation-item:last-child {
        margin-bottom: 0;
    }
    
    .rotation-item-main {
        flex: 1;
        min-width: 0;
    }
    
    .rotation-item-weight {
        width: 80px;
    }
    
    .rotation-item-actions {
        display: flex;
        gap: 0.25rem;
    }
    
    .add-item-btn {
        width: 100%;
        padding: 0.75rem;
        background: rgba(249, 115, 22, 0.1);
        border: 1px dashed var(--primary);
        border-radius: var(--radius-md);
        color: var(--primary);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .add-item-btn:hover {
        background: rgba(249, 115, 22, 0.15);
    }
    
    .tracking-url-box {
        background: var(--dark-50);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    
    .tracking-url-content {
        flex: 1;
        min-width: 0;
    }
    
    .tracking-url-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }
    
    .tracking-url {
        font-family: monospace;
        font-size: 0.875rem;
        color: var(--primary);
        word-break: break-all;
    }
    
    .copy-btn {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        white-space: nowrap;
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: space-between;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
        margin-top: 1.5rem;
    }
    
    .form-actions-left {
        display: flex;
        gap: 0.5rem;
    }
    
    .form-actions-right {
        display: flex;
        gap: 1rem;
    }
    
    .campaign-stats-mini {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-mini {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-md);
        padding: 1rem;
        text-align: center;
    }
    
    .stat-mini-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    
    .stat-mini-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }
</style>

<!-- Mini Stats -->
<div class="campaign-stats-mini">
    <div class="stat-mini">
        <div class="stat-mini-value">0</div>
        <div class="stat-mini-label">Views</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-value">0</div>
        <div class="stat-mini-label">Clicks</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-value">0</div>
        <div class="stat-mini-label">Conversions</div>
    </div>
    <div class="stat-mini">
        <div class="stat-mini-value">$0.00</div>
        <div class="stat-mini-label">Profit</div>
    </div>
</div>

<!-- Tracking URL -->
<div class="tracking-url-box mb-4">
    <div class="tracking-url-content">
        <div class="tracking-url-label">Campaign Tracking URL</div>
        <div class="tracking-url" id="trackingUrl"><?= e($trackingUrl) ?></div>
    </div>
    <button type="button" class="btn btn-secondary copy-btn" onclick="copyTrackingUrl()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
        </svg>
        Copy
    </button>
</div>

<form method="POST" action="/campaigns/edit?id=<?= $campaign['CampaignID'] ?>" class="campaign-form">
    <?= csrf_field() ?>
    
    <!-- Basic Info -->
    <div class="form-section">
        <div class="form-section-header">
            <h3 class="form-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Basic Information
            </h3>
            <span class="badge badge-<?= $campaign['Active'] ? 'success' : 'secondary' ?>">
                <?= $campaign['Active'] ? 'Active' : 'Inactive' ?>
            </span>
        </div>
        <div class="form-section-body">
            <div class="form-group">
                <label class="form-label">Campaign Name *</label>
                <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                       name="name" value="<?= e($campaign['CampaignName']) ?>" required>
                <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback"><?= e($errors['name']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Traffic Source</label>
                    <select class="form-control form-select" name="traffic_source_id">
                        <option value="">-- Select Traffic Source --</option>
                        <?php foreach ($trafficSources as $source): ?>
                        <option value="<?= $source['id'] ?>" <?= $campaign['CPVSourceID'] == $source['id'] ? 'selected' : '' ?>>
                            <?= e($source['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="form-check" style="margin-top: 0.5rem;">
                        <input type="checkbox" class="form-check-input" name="active" id="active" 
                               <?= $campaign['Active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="active">Active</label>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Campaign Key</label>
                    <input type="text" class="form-control" value="<?= e($campaign['CampaignKey']) ?>" readonly disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Created</label>
                    <input type="text" class="form-control" value="<?= format_date($campaign['DateAdded']) ?>" readonly disabled>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cost Settings -->
    <div class="form-section">
        <div class="form-section-header">
            <h3 class="form-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                Cost Settings
            </h3>
        </div>
        <div class="form-section-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Cost Model</label>
                    <select class="form-control form-select" name="cost_model" id="costModel">
                        <option value="1" <?= ($campaign['CostModel'] ?? 1) == 1 ? 'selected' : '' ?>>Do Not Track Cost</option>
                        <option value="2" <?= ($campaign['CostModel'] ?? 1) == 2 ? 'selected' : '' ?>>CPV (Cost Per View)</option>
                        <option value="3" <?= ($campaign['CostModel'] ?? 1) == 3 ? 'selected' : '' ?>>CPC (Cost Per Click)</option>
                        <option value="4" <?= ($campaign['CostModel'] ?? 1) == 4 ? 'selected' : '' ?>>CPA (Cost Per Action)</option>
                        <option value="5" <?= ($campaign['CostModel'] ?? 1) == 5 ? 'selected' : '' ?>>RevShare (%)</option>
                        <option value="6" <?= ($campaign['CostModel'] ?? 1) == 6 ? 'selected' : '' ?>>Auto (from URL param)</option>
                    </select>
                </div>
                
                <div class="form-group" id="costValueGroup" style="<?= ($campaign['CostModel'] ?? 1) > 1 && ($campaign['CostModel'] ?? 1) < 6 ? '' : 'display: none;' ?>">
                    <label class="form-label">Cost Value</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" name="cost_value" 
                               step="0.0001" min="0" value="<?= $campaign['CostValue'] ?? 0 ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Landing Pages -->
    <div class="form-section">
        <div class="form-section-header">
            <h3 class="form-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                </svg>
                Landing Pages
            </h3>
            <select class="form-control form-select" name="rotation_type" style="width: auto;">
                <option value="2" <?= ($campaign['RotationType'] ?? 2) == 2 ? 'selected' : '' ?>>Probabilistic Rotation</option>
                <option value="1" <?= ($campaign['RotationType'] ?? 2) == 1 ? 'selected' : '' ?>>Exact Rotation</option>
            </select>
        </div>
        <div class="form-section-body">
            <div id="landingPages">
                <?php if (empty($destinations)): ?>
                <div class="rotation-item">
                    <div class="rotation-item-main">
                        <select class="form-control form-select lp-select" name="destinations[0][predef_id]">
                            <option value="">-- Select Landing Page or Enter URL --</option>
                            <?php foreach ($landingPages as $lp): ?>
                            <option value="<?= $lp['id'] ?>" data-url="<?= e($lp['url']) ?>">
                                <?= e($lp['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" class="form-control mt-2 lp-url" name="destinations[0][url]" 
                               placeholder="Or enter custom URL">
                    </div>
                    <div class="rotation-item-weight">
                        <label class="form-label" style="font-size: 0.75rem;">Weight</label>
                        <input type="number" class="form-control" name="destinations[0][weight]" 
                               value="100" min="0" max="100">
                    </div>
                    <div class="rotation-item-actions">
                        <button type="button" class="table-action-btn danger remove-item" title="Remove">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <?php foreach ($destinations as $i => $dest): ?>
                <div class="rotation-item">
                    <div class="rotation-item-main">
                        <select class="form-control form-select lp-select" name="destinations[<?= $i ?>][predef_id]">
                            <option value="">-- Select Landing Page or Enter URL --</option>
                            <?php foreach ($landingPages as $lp): ?>
                            <option value="<?= $lp['id'] ?>" data-url="<?= e($lp['url']) ?>"
                                    <?= ($dest['PredefLPID'] ?? '') == $lp['id'] ? 'selected' : '' ?>>
                                <?= e($lp['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" class="form-control mt-2 lp-url" name="destinations[<?= $i ?>][url]" 
                               value="<?= e($dest['DestinationURL'] ?? '') ?>"
                               placeholder="Or enter custom URL">
                    </div>
                    <div class="rotation-item-weight">
                        <label class="form-label" style="font-size: 0.75rem;">Weight</label>
                        <input type="number" class="form-control" name="destinations[<?= $i ?>][weight]" 
                               value="<?= $dest['Weight'] ?? 100 ?>" min="0" max="100">
                    </div>
                    <div class="rotation-item-actions">
                        <button type="button" class="table-action-btn danger remove-item" title="Remove">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <button type="button" class="add-item-btn" onclick="addLandingPage()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Landing Page
            </button>
        </div>
    </div>
    
    <!-- Offers -->
    <div class="form-section">
        <div class="form-section-header">
            <h3 class="form-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                Offers
            </h3>
        </div>
        <div class="form-section-body">
            <div id="offers">
                <?php if (empty($offers)): ?>
                <div class="rotation-item">
                    <div class="rotation-item-main">
                        <div class="form-row" style="margin-bottom: 0.5rem;">
                            <select class="form-control form-select offer-select" name="offers[0][predef_id]">
                                <option value="">-- Select Offer or Enter URL --</option>
                                <?php foreach ($predefOffers as $offer): ?>
                                <option value="<?= $offer['id'] ?>" data-url="<?= e($offer['url']) ?>" data-payout="<?= $offer['payout'] ?>">
                                    <?= e($offer['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-control form-select" name="offers[0][affiliate_id]">
                                <option value="">-- Affiliate Network --</option>
                                <?php foreach ($affiliateNetworks as $network): ?>
                                <option value="<?= $network['id'] ?>"><?= e($network['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="text" class="form-control offer-url" name="offers[0][url]" 
                               placeholder="Offer URL with {subid} placeholder">
                    </div>
                    <div class="rotation-item-weight">
                        <label class="form-label" style="font-size: 0.75rem;">Payout</label>
                        <input type="number" class="form-control offer-payout" name="offers[0][payout]" 
                               step="0.01" min="0" value="0" placeholder="$0.00">
                    </div>
                    <div class="rotation-item-weight">
                        <label class="form-label" style="font-size: 0.75rem;">Weight</label>
                        <input type="number" class="form-control" name="offers[0][weight]" 
                               value="100" min="0" max="100">
                    </div>
                    <div class="rotation-item-actions">
                        <button type="button" class="table-action-btn danger remove-item" title="Remove">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                <?php else: ?>
                <?php foreach ($offers as $i => $offer): ?>
                <div class="rotation-item">
                    <div class="rotation-item-main">
                        <div class="form-row" style="margin-bottom: 0.5rem;">
                            <select class="form-control form-select offer-select" name="offers[<?= $i ?>][predef_id]">
                                <option value="">-- Select Offer or Enter URL --</option>
                                <?php foreach ($predefOffers as $po): ?>
                                <option value="<?= $po['id'] ?>" data-url="<?= e($po['url']) ?>" data-payout="<?= $po['payout'] ?>"
                                        <?= ($offer['PredefOfferID'] ?? '') == $po['id'] ? 'selected' : '' ?>>
                                    <?= e($po['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-control form-select" name="offers[<?= $i ?>][affiliate_id]">
                                <option value="">-- Affiliate Network --</option>
                                <?php foreach ($affiliateNetworks as $network): ?>
                                <option value="<?= $network['id'] ?>"
                                        <?= ($offer['AffiliateSourceID'] ?? '') == $network['id'] ? 'selected' : '' ?>>
                                    <?= e($network['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="text" class="form-control offer-url" name="offers[<?= $i ?>][url]" 
                               value="<?= e($offer['OfferURL'] ?? '') ?>"
                               placeholder="Offer URL with {subid} placeholder">
                    </div>
                    <div class="rotation-item-weight">
                        <label class="form-label" style="font-size: 0.75rem;">Payout</label>
                        <input type="number" class="form-control offer-payout" name="offers[<?= $i ?>][payout]" 
                               step="0.01" min="0" value="<?= $offer['Payout'] ?? 0 ?>">
                    </div>
                    <div class="rotation-item-weight">
                        <label class="form-label" style="font-size: 0.75rem;">Weight</label>
                        <input type="number" class="form-control" name="offers[<?= $i ?>][weight]" 
                               value="<?= $offer['Weight'] ?? 100 ?>" min="0" max="100">
                    </div>
                    <div class="rotation-item-actions">
                        <button type="button" class="table-action-btn danger remove-item" title="Remove">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <button type="button" class="add-item-btn" onclick="addOffer()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Offer
            </button>
        </div>
    </div>
    
    <!-- Form Actions -->
    <div class="form-actions">
        <div class="form-actions-left">
            <button type="button" class="btn btn-secondary" onclick="cloneCampaign()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                Clone
            </button>
            <button type="button" class="btn btn-secondary" style="color: var(--danger);" onclick="deleteCampaign()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                Delete
            </button>
        </div>
        <div class="form-actions-right">
            <a href="/campaigns" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Save Changes
            </button>
        </div>
    </div>
</form>

<!-- Delete Form (hidden) -->
<form id="deleteForm" method="POST" action="/campaigns/delete" style="display: none;">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $campaign['CampaignID'] ?>">
</form>

<script>
let lpIndex = <?= max(count($destinations), 1) ?>;
let offerIndex = <?= max(count($offers), 1) ?>;

// Copy tracking URL
function copyTrackingUrl() {
    const url = document.getElementById('trackingUrl').textContent;
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.querySelector('.copy-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> Copied!';
        setTimeout(() => btn.innerHTML = originalText, 2000);
    });
}

// Cost model toggle
document.getElementById('costModel').addEventListener('change', function() {
    const costValueGroup = document.getElementById('costValueGroup');
    costValueGroup.style.display = this.value > 1 && this.value < 6 ? 'block' : 'none';
});

// Landing page select change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('lp-select')) {
        const option = e.target.selectedOptions[0];
        const urlInput = e.target.closest('.rotation-item').querySelector('.lp-url');
        if (option && option.dataset.url) {
            urlInput.value = option.dataset.url;
        }
    }
    
    if (e.target.classList.contains('offer-select')) {
        const option = e.target.selectedOptions[0];
        const item = e.target.closest('.rotation-item');
        const urlInput = item.querySelector('.offer-url');
        const payoutInput = item.querySelector('.offer-payout');
        if (option && option.dataset.url) {
            urlInput.value = option.dataset.url;
        }
        if (option && option.dataset.payout) {
            payoutInput.value = option.dataset.payout;
        }
    }
});

// Remove item
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        const item = e.target.closest('.rotation-item');
        const container = item.parentElement;
        if (container.children.length > 1) {
            item.remove();
        }
    }
});

function addLandingPage() {
    const container = document.getElementById('landingPages');
    const template = container.children[0].cloneNode(true);
    
    template.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${lpIndex}]`);
        if (el.tagName === 'INPUT') el.value = el.type === 'number' ? '100' : '';
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
    });
    
    container.appendChild(template);
    lpIndex++;
}

function addOffer() {
    const container = document.getElementById('offers');
    const template = container.children[0].cloneNode(true);
    
    template.querySelectorAll('[name]').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${offerIndex}]`);
        if (el.tagName === 'INPUT') el.value = el.type === 'number' ? (el.classList.contains('offer-payout') ? '0' : '100') : '';
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
    });
    
    container.appendChild(template);
    offerIndex++;
}

function cloneCampaign() {
    if (confirm('Clone this campaign? Stats will not be copied.')) {
        fetch('/api/campaigns/<?= $campaign['CampaignID'] ?>/clone', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/campaigns/edit?id=' + data.campaign_id;
            } else {
                alert('Failed to clone campaign');
            }
        });
    }
}

function deleteCampaign() {
    if (confirm('Are you sure you want to delete this campaign? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php
$content = ob_get_clean();

// Include layout
require __DIR__ . '/../layouts/app.php';

