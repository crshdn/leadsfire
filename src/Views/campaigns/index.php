<?php
$pageTitle = 'Campaigns';
$currentPage = 'campaigns';

use LeadsFire\Controllers\CampaignController;

// Get campaigns from controller
$campaigns = [];
$trafficSources = [];
try {
    if (is_installed()) {
        $controller = new CampaignController();
        $data = $controller->index();
        $campaigns = $data['campaigns'] ?? [];
        $trafficSources = $data['trafficSources'] ?? [];
    }
} catch (Exception $e) {
    // Database not ready yet
}

// Start output buffering for content
ob_start();
?>

<style>
    .campaigns-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .campaigns-filters {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .filter-search {
        position: relative;
        width: 280px;
    }
    
    .filter-search input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-primary);
        font-size: 0.875rem;
    }
    
    .filter-search input:focus {
        outline: none;
        border-color: var(--primary);
    }
    
    .filter-search-icon {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }
    
    .filter-select {
        padding: 0.5rem 2rem 0.5rem 0.75rem;
        background: var(--bg-input);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        color: var(--text-primary);
        font-size: 0.875rem;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23a1a1aa' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
    }
    
    .campaign-status {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        border-radius: var(--radius-full);
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .campaign-status.active {
        background: var(--success-bg);
        color: var(--success);
    }
    
    .campaign-status.inactive {
        background: rgba(113, 113, 122, 0.15);
        color: var(--text-muted);
    }
    
    .campaign-status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }
    
    .campaign-name {
        font-weight: 500;
        color: var(--text-primary);
    }
    
    .campaign-name:hover {
        color: var(--primary);
    }
    
    .campaign-key {
        font-size: 0.75rem;
        color: var(--text-muted);
        font-family: monospace;
    }
    
    .metric-positive {
        color: var(--success);
    }
    
    .metric-negative {
        color: var(--danger);
    }
    
    .metric-neutral {
        color: var(--text-muted);
    }
    
    .table-actions {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .table-action-btn {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        border-radius: var(--radius-sm);
        transition: all var(--transition-fast);
    }
    
    .table-action-btn:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
    }
    
    .table-action-btn.danger:hover {
        background: var(--danger-bg);
        color: var(--danger);
    }
    
    .bulk-actions {
        display: none;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: var(--dark-200);
        border-radius: var(--radius-md);
        margin-bottom: 1rem;
    }
    
    .bulk-actions.show {
        display: flex;
    }
    
    .bulk-count {
        font-size: 0.875rem;
        color: var(--text-secondary);
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
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

<div class="campaigns-header">
    <div class="campaigns-filters">
        <div class="filter-search">
            <svg class="filter-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" placeholder="Search campaigns..." id="searchInput">
        </div>
        
        <select class="filter-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        
        <select class="filter-select" id="sourceFilter">
            <option value="">All Traffic Sources</option>
        </select>
    </div>
    
    <div class="page-actions">
        <a href="/campaigns/create" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            New Campaign
        </a>
    </div>
</div>

<div class="bulk-actions" id="bulkActions">
    <span class="bulk-count"><span id="selectedCount">0</span> selected</span>
    <button class="btn btn-secondary btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
        </svg>
        Edit
    </button>
    <button class="btn btn-secondary btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
        </svg>
        Clone
    </button>
    <button class="btn btn-secondary btn-sm" style="color: var(--danger);">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        </svg>
        Delete
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($campaigns)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                </svg>
            </div>
            <h3 class="empty-state-title">No campaigns yet</h3>
            <p class="empty-state-text">Create your first campaign to start tracking clicks and conversions.</p>
            <a href="/campaigns/create" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Create Campaign
            </a>
        </div>
        <?php else: ?>
        <div class="table-wrapper" style="border: none; border-radius: 0;">
            <table class="table" id="campaignsTable">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
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
                        <th style="width: 100px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($campaigns as $campaign): ?>
                    <tr data-id="<?= $campaign['CampaignID'] ?>">
                        <td>
                            <input type="checkbox" class="form-check-input row-select">
                        </td>
                        <td>
                            <a href="/campaigns/edit?id=<?= $campaign['CampaignID'] ?>" class="campaign-name">
                                <?= e($campaign['CampaignName']) ?>
                            </a>
                            <div class="campaign-key"><?= e($campaign['CampaignKey']) ?></div>
                        </td>
                        <td><?= e($campaign['TrafficSource'] ?? '-') ?></td>
                        <td class="text-right"><?= number_format($campaign['Views']) ?></td>
                        <td class="text-right"><?= number_format($campaign['Clicks']) ?></td>
                        <td class="text-right"><?= number_format($campaign['Conversions']) ?></td>
                        <td class="text-right"><?= format_money($campaign['Revenue']) ?></td>
                        <td class="text-right"><?= format_money($campaign['Cost']) ?></td>
                        <td class="text-right <?= $campaign['Profit'] >= 0 ? 'metric-positive' : 'metric-negative' ?>">
                            <?= format_money($campaign['Profit']) ?>
                        </td>
                        <td class="text-right <?= $campaign['ROI'] >= 0 ? 'metric-positive' : 'metric-negative' ?>">
                            <?= format_percent($campaign['ROI']) ?>
                        </td>
                        <td>
                            <span class="campaign-status <?= $campaign['Active'] ? 'active' : 'inactive' ?>">
                                <span class="campaign-status-dot"></span>
                                <?= $campaign['Active'] ? 'Active' : 'Inactive' ?>
                            </span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="/campaigns/edit?id=<?= $campaign['CampaignID'] ?>" class="table-action-btn" title="Edit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <button class="table-action-btn" title="Clone">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                </button>
                                <button class="table-action-btn danger" title="Delete">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const rowSelects = document.querySelectorAll('.row-select');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');
    
    function updateBulkActions() {
        const checked = document.querySelectorAll('.row-select:checked').length;
        selectedCount.textContent = checked;
        bulkActions.classList.toggle('show', checked > 0);
    }
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            rowSelects.forEach(cb => cb.checked = this.checked);
            updateBulkActions();
        });
    }
    
    rowSelects.forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });
    
    // Search filter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('#campaignsTable tbody tr').forEach(row => {
                const name = row.querySelector('.campaign-name')?.textContent.toLowerCase() || '';
                const key = row.querySelector('.campaign-key')?.textContent.toLowerCase() || '';
                row.style.display = (name.includes(term) || key.includes(term)) ? '' : 'none';
            });
        });
    }
});
</script>

<?php
$content = ob_get_clean();

// Include layout
require __DIR__ . '/../layouts/app.php';

