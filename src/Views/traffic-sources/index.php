<?php
$pageTitle = 'Traffic Sources';
$currentPage = 'traffic-sources';

use LeadsFire\Controllers\TrafficSourceController;

$trafficSources = [];
try {
    if (is_installed()) {
        $controller = new TrafficSourceController();
        $data = $controller->index();
        $trafficSources = $data['trafficSources'] ?? [];
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
</style>

<div class="asset-header">
    <div>
        <p class="text-muted m-0">Manage your traffic sources and tracking parameters.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Traffic Source
    </button>
</div>

<?php if (empty($trafficSources)): ?>
<div class="empty-state">
    <div class="empty-state-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="2" y1="12" x2="22" y2="12"></line>
            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
        </svg>
    </div>
    <h3 class="empty-state-title">No traffic sources yet</h3>
    <p class="empty-state-text">Add your first traffic source to start tracking where your visitors come from.</p>
    <button type="button" class="btn btn-primary" onclick="openModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Traffic Source
    </button>
</div>
<?php else: ?>
<div class="asset-grid">
    <?php foreach ($trafficSources as $source): ?>
    <div class="asset-card">
        <div class="asset-card-header">
            <h3 class="asset-card-title"><?= e($source['CPVSource']) ?></h3>
            <div class="asset-card-actions">
                <button class="table-action-btn" onclick="editSource(<?= $source['CPVSourceID'] ?>)" title="Edit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
                <button class="table-action-btn danger" onclick="deleteSource(<?= $source['CPVSourceID'] ?>)" title="Delete">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="asset-card-meta">
            <?php if (!empty($source['SubIdParameter'])): ?>
            <div class="asset-meta-item">
                <span class="asset-meta-label">SubID:</span>
                <span class="asset-meta-value"><?= e($source['SubIdParameter']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($source['KeywordParameter'])): ?>
            <div class="asset-meta-item">
                <span class="asset-meta-label">Keyword:</span>
                <span class="asset-meta-value"><?= e($source['KeywordParameter']) ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($source['CostParameter'])): ?>
            <div class="asset-meta-item">
                <span class="asset-meta-label">Cost:</span>
                <span class="asset-meta-value"><?= e($source['CostParameter']) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal -->
<div id="sourceModal" class="modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add Traffic Source</h3>
            <button type="button" class="modal-close" onclick="closeModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <form id="sourceForm" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="sourceId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" class="form-control" name="name" id="sourceName" required placeholder="e.g., Google Ads, Facebook">
                </div>
                
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">SubID Parameter</label>
                        <input type="text" class="form-control" name="subid_param" id="sourceSubidParam" value="subid" placeholder="subid">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keyword Parameter</label>
                        <input type="text" class="form-control" name="keyword_param" id="sourceKeywordParam" value="keyword" placeholder="keyword">
                    </div>
                </div>
                
                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Cost Parameter</label>
                        <input type="text" class="form-control" name="cost_param" id="sourceCostParam" placeholder="cost">
                    </div>
                    <div class="form-group">
                        <label class="form-label">External ID Parameter</label>
                        <input type="text" class="form-control" name="external_id_param" id="sourceExternalIdParam" placeholder="external_id">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Postback URL (optional)</label>
                    <input type="text" class="form-control" name="postback_url" id="sourcePostbackUrl" placeholder="https://...">
                    <span class="form-text">URL to send conversion data back to this traffic source</span>
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
    document.getElementById('sourceModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = id ? 'Edit Traffic Source' : 'Add Traffic Source';
    document.getElementById('sourceForm').reset();
    document.getElementById('sourceId').value = id || '';
    
    if (id) {
        // Load source data via API
        fetch('/api/traffic-sources/' + id)
            .then(res => res.json())
            .then(data => {
                if (data) {
                    document.getElementById('sourceName').value = data.CPVSource || '';
                    document.getElementById('sourceSubidParam').value = data.SubIdParameter || '';
                    document.getElementById('sourceKeywordParam').value = data.KeywordParameter || '';
                    document.getElementById('sourceCostParam').value = data.CostParameter || '';
                    document.getElementById('sourceExternalIdParam').value = data.ExternalIDParameter || '';
                    document.getElementById('sourcePostbackUrl').value = data.PostbackURL || '';
                }
            });
    }
}

function closeModal() {
    document.getElementById('sourceModal').style.display = 'none';
}

function editSource(id) {
    openModal(id);
}

function deleteSource(id) {
    if (confirm('Are you sure you want to delete this traffic source?')) {
        fetch('/api/traffic-sources/' + id, {
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
                alert('Failed to delete traffic source');
            }
        });
    }
}

document.getElementById('sourceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('sourceId').value;
    const url = id ? '/api/traffic-sources/' + id : '/api/traffic-sources';
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

