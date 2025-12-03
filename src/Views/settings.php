<?php
$pageTitle = 'Settings';
$currentPage = 'settings';

// Start output buffering for content
ob_start();
?>

<style>
    .settings-grid {
        display: grid;
        grid-template-columns: 250px 1fr;
        gap: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .settings-nav {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 0.5rem;
    }
    
    .settings-nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: var(--text-secondary);
        text-decoration: none;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 500;
        transition: all var(--transition-fast);
    }
    
    .settings-nav-item:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
    }
    
    .settings-nav-item.active {
        background: rgba(249, 115, 22, 0.1);
        color: var(--primary);
    }
    
    .settings-content {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
    }
    
    .settings-section {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .settings-section:last-child {
        border-bottom: none;
    }
    
    .settings-section-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    
    .settings-section-desc {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }
    
    .settings-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    @media (max-width: 640px) {
        .settings-row {
            grid-template-columns: 1fr;
        }
    }
    
    .postback-url-box {
        background: var(--dark-50);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .postback-url-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    
    .postback-url {
        font-family: monospace;
        font-size: 0.8125rem;
        color: var(--primary);
        word-break: break-all;
        background: var(--bg-input);
        padding: 0.75rem;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border-color);
    }
    
    .placeholder-list {
        margin-top: 1rem;
    }
    
    .placeholder-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border-light);
        font-size: 0.8125rem;
    }
    
    .placeholder-item:last-child {
        border-bottom: none;
    }
    
    .placeholder-code {
        font-family: monospace;
        color: var(--primary);
        background: rgba(249, 115, 22, 0.1);
        padding: 0.125rem 0.375rem;
        border-radius: 4px;
    }
    
    .placeholder-desc {
        color: var(--text-muted);
    }
</style>

<div class="settings-grid">
    <nav class="settings-nav">
        <a href="#general" class="settings-nav-item active">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
            </svg>
            General
        </a>
        <a href="#postback" class="settings-nav-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
            </svg>
            Postback
        </a>
        <a href="#tracking" class="settings-nav-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
            Tracking
        </a>
        <a href="#security" class="settings-nav-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
            Security
        </a>
    </nav>
    
    <div class="settings-content">
        <!-- General Settings -->
        <div class="settings-section" id="general">
            <h3 class="settings-section-title">General Settings</h3>
            <p class="settings-section-desc">Configure basic application settings.</p>
            
            <div class="settings-row">
                <div class="form-group">
                    <label class="form-label">Application Name</label>
                    <input type="text" class="form-control" value="<?= e(config('app.name', 'LeadsFire Click Tracker')) ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Timezone</label>
                    <input type="text" class="form-control" value="<?= e(config('app.timezone', 'America/New_York')) ?>" readonly>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Application URL</label>
                <input type="text" class="form-control" value="<?= e(config('app.url', '')) ?>" readonly>
            </div>
            
            <p class="form-text">To change these settings, edit the <code>.env</code> file on your server.</p>
        </div>
        
        <!-- Postback Settings -->
        <div class="settings-section" id="postback">
            <h3 class="settings-section-title">Postback URL</h3>
            <p class="settings-section-desc">Use this URL in your affiliate networks to track conversions.</p>
            
            <div class="postback-url-box">
                <div class="postback-url-label">Your Postback URL</div>
                <div class="postback-url" id="postbackUrl">
                    <?= e(rtrim(config('app.url', 'https://your-domain.com'), '/')) ?>/postback?subid={subid}&revenue={payout}
                </div>
            </div>
            
            <button type="button" class="btn btn-secondary btn-sm mt-3" onclick="copyPostbackUrl()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                Copy URL
            </button>
            
            <div class="placeholder-list">
                <h4 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem;">Available Placeholders</h4>
                
                <div class="placeholder-item">
                    <span class="placeholder-code">{subid}</span>
                    <span class="placeholder-desc">Click ID (required)</span>
                </div>
                <div class="placeholder-item">
                    <span class="placeholder-code">{payout}</span>
                    <span class="placeholder-desc">Conversion revenue</span>
                </div>
                <div class="placeholder-item">
                    <span class="placeholder-code">{revenue}</span>
                    <span class="placeholder-desc">Alternative for revenue</span>
                </div>
                <div class="placeholder-item">
                    <span class="placeholder-code">{txid}</span>
                    <span class="placeholder-desc">Transaction ID</span>
                </div>
                <div class="placeholder-item">
                    <span class="placeholder-code">{status}</span>
                    <span class="placeholder-desc">Conversion status</span>
                </div>
                <div class="placeholder-item">
                    <span class="placeholder-code">{aff_sub}</span>
                    <span class="placeholder-desc">Alternative subid parameter</span>
                </div>
            </div>
        </div>
        
        <!-- Tracking Settings -->
        <div class="settings-section" id="tracking">
            <h3 class="settings-section-title">Tracking Settings</h3>
            <p class="settings-section-desc">Configure click tracking behavior.</p>
            
            <div class="settings-row">
                <div class="form-group">
                    <label class="form-label">Cookie Timeout</label>
                    <input type="text" class="form-control" value="<?= config('app.tracking.cookie_timeout', 2592000) ?> seconds (30 days)" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Attribution Window</label>
                    <input type="text" class="form-control" value="<?= config('app.tracking.attribution_days', 30) ?> days" readonly>
                </div>
            </div>
            
            <div class="settings-row">
                <div class="form-group">
                    <label class="form-label">Deduplication</label>
                    <input type="text" class="form-control" value="<?= config('app.tracking.dedup_seconds', 0) ?> seconds" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">GeoIP Provider</label>
                    <input type="text" class="form-control" value="<?= config('app.geoip.provider', 'ip-api') ?>" readonly>
                </div>
            </div>
        </div>
        
        <!-- Security Settings -->
        <div class="settings-section" id="security">
            <h3 class="settings-section-title">Security Settings</h3>
            <p class="settings-section-desc">Security configuration for your tracker.</p>
            
            <div class="form-group">
                <label class="form-label">Allowed IPs</label>
                <?php 
                $allowedIps = config('app.security.allowed_ips', []);
                $ipsDisplay = empty($allowedIps) ? 'All IPs allowed (no restriction)' : implode(', ', $allowedIps);
                ?>
                <input type="text" class="form-control" value="<?= e($ipsDisplay) ?>" readonly>
            </div>
            
            <div class="settings-row">
                <div class="form-group">
                    <label class="form-label">Login Rate Limit</label>
                    <input type="text" class="form-control" value="<?= config('app.security.rate_limit.login_attempts', 5) ?> attempts / <?= config('app.security.rate_limit.login_window', 300) / 60 ?> minutes" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Session Lifetime</label>
                    <input type="text" class="form-control" value="<?= config('app.session.lifetime', 86400) / 3600 ?> hours" readonly>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyPostbackUrl() {
    const url = document.getElementById('postbackUrl').textContent.trim();
    navigator.clipboard.writeText(url).then(() => {
        alert('Postback URL copied to clipboard!');
    });
}

// Settings nav active state
document.querySelectorAll('.settings-nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
        document.querySelectorAll('.settings-nav-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>

<?php
$content = ob_get_clean();

// Include layout
require __DIR__ . '/layouts/app.php';

