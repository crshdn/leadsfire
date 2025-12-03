<?php
$pageTitle = 'Landing Pages';
$currentPage = 'landing-pages';

use LeadsFire\Models\LandingPage;

$landingPages = [];
try {
    if (is_installed()) {
        $lpModel = new LandingPage();
        $landingPages = $lpModel->getAll();
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
        margin-bottom: 0.75rem;
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
    
    .asset-url {
        font-size: 0.75rem;
        color: var(--text-muted);
        word-break: break-all;
        background: var(--dark-300);
        padding: 0.5rem;
        border-radius: var(--radius-sm);
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
        <p class="text-muted m-0">Manage your landing pages for campaigns.</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="openModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Landing Page
    </button>
</div>

<?php if (empty($landingPages)): ?>
<div class="empty-state">
    <div class="empty-state-icon">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="3" y1="9" x2="21" y2="9"></line>
            <line x1="9" y1="21" x2="9" y2="9"></line>
        </svg>
    </div>
    <h3 class="empty-state-title">No landing pages yet</h3>
    <p class="empty-state-text">Add your first landing page to use in campaigns.</p>
    <button type="button" class="btn btn-primary" onclick="openModal()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        Add Landing Page
    </button>
</div>
<?php else: ?>
<div class="asset-grid">
    <?php foreach ($landingPages as $lp): ?>
    <div class="asset-card">
        <div class="asset-card-header">
            <h3 class="asset-card-title"><?= e($lp['LpName']) ?></h3>
            <div class="asset-card-actions">
                <button class="table-action-btn" onclick="editLP(<?= $lp['PredefLpID'] ?>)" title="Edit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
                <button class="table-action-btn danger" onclick="deleteLP(<?= $lp['PredefLpID'] ?>)" title="Delete">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
            </div>
        </div>
        <?php if (!empty($lp['LpUrl'])): ?>
        <div class="asset-url"><?= e(substr($lp['LpUrl'], 0, 80)) ?><?= strlen($lp['LpUrl']) > 80 ? '...' : '' ?></div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal -->
<div id="lpModal" class="modal" style="display: none;">
    <div class="modal-backdrop" onclick="closeModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Add Landing Page</h3>
            <button type="button" class="modal-close" onclick="closeModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <form id="lpForm" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="lpId">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" class="form-control" name="name" id="lpName" required placeholder="e.g., Diet LP v2">
                </div>
                
                <div class="form-group">
                    <label class="form-label">URL *</label>
                    <input type="url" class="form-control" name="url" id="lpUrl" required placeholder="https://yourdomain.com/lp/diet">
                    <span class="form-text">The URL of your landing page</span>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" id="lpNotes" rows="2" placeholder="Optional notes"></textarea>
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
    document.getElementById('lpModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = id ? 'Edit Landing Page' : 'Add Landing Page';
    document.getElementById('lpForm').reset();
    document.getElementById('lpId').value = id || '';
    
    if (id) {
        fetch('/api/landing-pages/' + id)
            .then(res => res.json())
            .then(data => {
                if (data) {
                    document.getElementById('lpName').value = data.LpName || '';
                    document.getElementById('lpUrl').value = data.LpUrl || '';
                    document.getElementById('lpNotes').value = data.Notes || '';
                }
            });
    }
}

function closeModal() {
    document.getElementById('lpModal').style.display = 'none';
}

function editLP(id) {
    openModal(id);
}

function deleteLP(id) {
    if (confirm('Are you sure you want to delete this landing page?')) {
        fetch('/api/landing-pages/' + id, {
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
                alert('Failed to delete landing page');
            }
        });
    }
}

document.getElementById('lpForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('lpId').value;
    const url = id ? '/api/landing-pages/' + id : '/api/landing-pages';
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

