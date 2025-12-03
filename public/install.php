<?php
/**
 * LeadsFire Click Tracker - Installation Wizard
 * 
 * This script handles the initial setup of the application.
 */

// Error reporting for installation
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Check if already installed
$installedPath = BASE_PATH . '/storage/.installed';
if (file_exists($installedPath)) {
    header('Location: /');
    exit;
}

// Autoload
require_once BASE_PATH . '/vendor/autoload.php';

use LeadsFire\Installer\Installer;

// Initialize installer
$installer = new Installer();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'check_requirements':
            echo json_encode([
                'success' => true,
                'requirements' => $installer->checkRequirements(),
            ]);
            exit;
            
        case 'test_database':
            $config = [
                'host' => $_POST['db_host'] ?? 'localhost',
                'port' => (int)($_POST['db_port'] ?? 3306),
                'database' => $_POST['db_database'] ?? '',
                'username' => $_POST['db_username'] ?? '',
                'password' => $_POST['db_password'] ?? '',
            ];
            echo json_encode($installer->testDatabaseConnection($config));
            exit;
            
        case 'install':
            $dbConfig = [
                'host' => $_POST['db_host'] ?? 'localhost',
                'port' => (int)($_POST['db_port'] ?? 3306),
                'database' => $_POST['db_database'] ?? '',
                'username' => $_POST['db_username'] ?? '',
                'password' => $_POST['db_password'] ?? '',
            ];
            
            $steps = [];
            
            // Step 1: Create database
            if ($installer->createDatabase($dbConfig)) {
                $steps[] = ['step' => 'Create database', 'success' => true];
            } else {
                echo json_encode(['success' => false, 'errors' => $installer->getErrors(), 'steps' => $steps]);
                exit;
            }
            
            // Step 2: Import schema
            if ($installer->importSchema($dbConfig)) {
                $steps[] = ['step' => 'Import database schema', 'success' => true];
            } else {
                echo json_encode(['success' => false, 'errors' => $installer->getErrors(), 'steps' => $steps]);
                exit;
            }
            
            // Step 3: Create admin user
            if ($installer->createAdminUser(
                $dbConfig,
                $_POST['admin_username'] ?? 'admin',
                $_POST['admin_password'] ?? '',
                $_POST['admin_email'] ?? ''
            )) {
                $steps[] = ['step' => 'Create admin user', 'success' => true];
            } else {
                echo json_encode(['success' => false, 'errors' => $installer->getErrors(), 'steps' => $steps]);
                exit;
            }
            
            // Step 4: Generate .env file
            $envSettings = [
                'app_name' => $_POST['app_name'] ?? 'LeadsFire Click Tracker',
                'app_url' => $_POST['app_url'] ?? '',
                'app_env' => 'production',
                'app_debug' => 'false',
                'timezone' => $_POST['timezone'] ?? 'America/New_York',
                'db_host' => $dbConfig['host'],
                'db_port' => $dbConfig['port'],
                'db_database' => $dbConfig['database'],
                'db_username' => $dbConfig['username'],
                'db_password' => $dbConfig['password'],
                'allowed_ips' => $_POST['allowed_ips'] ?? '',
                'mail_from' => $_POST['mail_from'] ?? '',
                'mail_to' => $_POST['admin_email'] ?? '',
            ];
            
            if ($installer->generateEnvFile($envSettings)) {
                $steps[] = ['step' => 'Generate configuration', 'success' => true];
            } else {
                echo json_encode(['success' => false, 'errors' => $installer->getErrors(), 'steps' => $steps]);
                exit;
            }
            
            // Step 5: Mark as installed
            if ($installer->markComplete()) {
                $steps[] = ['step' => 'Complete installation', 'success' => true];
            } else {
                echo json_encode(['success' => false, 'errors' => $installer->getErrors(), 'steps' => $steps]);
                exit;
            }
            
            echo json_encode(['success' => true, 'steps' => $steps]);
            exit;
    }
    
    echo json_encode(['success' => false, 'error' => 'Unknown action']);
    exit;
}

// Get requirements for initial display
$requirements = $installer->checkRequirements();
$allPassed = !in_array(false, array_column($requirements, 'passed'));
$timezones = Installer::getTimezones();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - LeadsFire Click Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/theme.css">
    <style>
        .install-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .install-card {
            width: 100%;
            max-width: 600px;
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            box-shadow: var(--glass-shadow);
            overflow: hidden;
        }
        
        .install-header {
            padding: 2rem;
            text-align: center;
            border-bottom: 1px solid var(--border-color);
            background: rgba(0, 0, 0, 0.2);
        }
        
        .install-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--fire-500) 0%, var(--fire-600) 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            box-shadow: var(--shadow-glow);
        }
        
        .install-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        
        .install-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }
        
        .install-body {
            padding: 2rem;
        }
        
        .install-steps {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .step-indicator {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            background: var(--dark-300);
            color: var(--text-muted);
            transition: all var(--transition-base);
        }
        
        .step-indicator.active {
            background: var(--primary);
            color: white;
            box-shadow: var(--shadow-glow);
        }
        
        .step-indicator.completed {
            background: var(--success);
            color: white;
        }
        
        .step-connector {
            width: 40px;
            height: 2px;
            background: var(--dark-300);
            align-self: center;
        }
        
        .step-connector.completed {
            background: var(--success);
        }
        
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .requirement-list {
            list-style: none;
            padding: 0;
            margin: 0 0 1.5rem;
        }
        
        .requirement-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 0.5rem;
            background: rgba(0, 0, 0, 0.2);
        }
        
        .requirement-name {
            font-size: 0.875rem;
            color: var(--text-primary);
        }
        
        .requirement-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
        }
        
        .requirement-status.passed {
            color: var(--success);
        }
        
        .requirement-status.failed {
            color: var(--danger);
        }
        
        .requirement-status.warning {
            color: var(--warning);
        }
        
        .check-icon {
            width: 16px;
            height: 16px;
        }
        
        .install-actions {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .install-actions .btn {
            flex: 1;
        }
        
        .progress-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .progress-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-light);
        }
        
        .progress-item:last-child {
            border-bottom: none;
        }
        
        .progress-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .progress-icon.pending {
            background: var(--dark-300);
        }
        
        .progress-icon.running {
            background: var(--info);
        }
        
        .progress-icon.success {
            background: var(--success);
        }
        
        .progress-icon.error {
            background: var(--danger);
        }
        
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.25rem;
        }
        
        .password-wrapper {
            position: relative;
        }
        
        .password-wrapper .form-control {
            padding-right: 2.5rem;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-card">
            <div class="install-header">
                <div class="install-logo">🔥</div>
                <h1 class="install-title">LeadsFire Click Tracker</h1>
                <p class="install-subtitle">Installation Wizard</p>
            </div>
            
            <div class="install-body">
                <div class="install-steps">
                    <div class="step-indicator active" data-step="1">1</div>
                    <div class="step-connector"></div>
                    <div class="step-indicator" data-step="2">2</div>
                    <div class="step-connector"></div>
                    <div class="step-indicator" data-step="3">3</div>
                    <div class="step-connector"></div>
                    <div class="step-indicator" data-step="4">4</div>
                </div>
                
                <!-- Step 1: Requirements -->
                <div class="step-content active" data-step="1">
                    <h3 class="mb-3">System Requirements</h3>
                    <ul class="requirement-list">
                        <?php foreach ($requirements as $key => $req): ?>
                        <li class="requirement-item">
                            <span class="requirement-name"><?= htmlspecialchars($req['name']) ?></span>
                            <span class="requirement-status <?= $req['passed'] ? (($req['warning'] ?? false) ? 'warning' : 'passed') : 'failed' ?>">
                                <?= htmlspecialchars($req['current']) ?>
                                <?php if ($req['passed']): ?>
                                    <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                <?php else: ?>
                                    <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                <?php endif; ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <?php if (!$allPassed): ?>
                    <div class="alert alert-danger">
                        Please fix the requirements above before continuing.
                    </div>
                    <?php endif; ?>
                    
                    <div class="install-actions">
                        <button type="button" class="btn btn-secondary" onclick="location.reload()">
                            Refresh
                        </button>
                        <button type="button" class="btn btn-primary" onclick="nextStep(2)" <?= !$allPassed ? 'disabled' : '' ?>>
                            Continue
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Database -->
                <div class="step-content" data-step="2">
                    <h3 class="mb-3">Database Configuration</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Database Host</label>
                        <input type="text" class="form-control" id="db_host" value="localhost">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Database Port</label>
                        <input type="number" class="form-control" id="db_port" value="3306">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Database Name</label>
                        <input type="text" class="form-control" id="db_database" value="leadsfire_tracker">
                        <span class="form-text">Will be created if it doesn't exist</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Database Username</label>
                        <input type="text" class="form-control" id="db_username">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Database Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="db_password">
                            <button type="button" class="password-toggle" onclick="togglePassword('db_password')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div id="db_test_result"></div>
                    
                    <div class="install-actions">
                        <button type="button" class="btn btn-secondary" onclick="prevStep(1)">
                            Back
                        </button>
                        <button type="button" class="btn btn-outline" onclick="testDatabase()">
                            Test Connection
                        </button>
                        <button type="button" class="btn btn-primary" onclick="nextStep(3)" id="db_continue" disabled>
                            Continue
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Admin & Settings -->
                <div class="step-content" data-step="3">
                    <h3 class="mb-3">Admin Account & Settings</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Application URL</label>
                        <input type="url" class="form-control" id="app_url" placeholder="https://admin.yourdomain.com">
                        <span class="form-text">The URL where the admin panel will be accessible</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Timezone</label>
                        <select class="form-control form-select" id="timezone">
                            <?php foreach ($timezones as $region => $zones): ?>
                            <optgroup label="<?= $region ?>">
                                <?php foreach ($zones as $zone): ?>
                                <option value="<?= $zone ?>" <?= $zone === 'America/New_York' ? 'selected' : '' ?>>
                                    <?= str_replace('_', ' ', $zone) ?>
                                </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <hr style="border-color: var(--border-color); margin: 1.5rem 0;">
                    
                    <div class="form-group">
                        <label class="form-label">Admin Username</label>
                        <input type="text" class="form-control" id="admin_username" value="admin">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Admin Email</label>
                        <input type="email" class="form-control" id="admin_email" placeholder="admin@yourdomain.com">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Admin Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="admin_password" minlength="8">
                            <button type="button" class="password-toggle" onclick="togglePassword('admin_password')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                        <span class="form-text">Minimum 8 characters</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="admin_password_confirm">
                            <button type="button" class="password-toggle" onclick="togglePassword('admin_password_confirm')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <hr style="border-color: var(--border-color); margin: 1.5rem 0;">
                    
                    <div class="form-group">
                        <label class="form-label">Allowed IPs (optional)</label>
                        <input type="text" class="form-control" id="allowed_ips" placeholder="e.g., 192.168.1.1,10.0.0.1">
                        <span class="form-text">Comma-separated list of IPs allowed to access admin panel. Leave empty to allow all.</span>
                    </div>
                    
                    <div class="install-actions">
                        <button type="button" class="btn btn-secondary" onclick="prevStep(2)">
                            Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="validateAndInstall()">
                            Install
                        </button>
                    </div>
                </div>
                
                <!-- Step 4: Complete -->
                <div class="step-content" data-step="4">
                    <h3 class="mb-3">Installation Progress</h3>
                    
                    <ul class="progress-list" id="progress_list">
                        <li class="progress-item">
                            <div class="progress-icon pending" id="progress_1">
                                <div class="spinner" style="width: 16px; height: 16px; border-width: 2px; display: none;"></div>
                            </div>
                            <span>Create database</span>
                        </li>
                        <li class="progress-item">
                            <div class="progress-icon pending" id="progress_2">
                                <div class="spinner" style="width: 16px; height: 16px; border-width: 2px; display: none;"></div>
                            </div>
                            <span>Import database schema</span>
                        </li>
                        <li class="progress-item">
                            <div class="progress-icon pending" id="progress_3">
                                <div class="spinner" style="width: 16px; height: 16px; border-width: 2px; display: none;"></div>
                            </div>
                            <span>Create admin user</span>
                        </li>
                        <li class="progress-item">
                            <div class="progress-icon pending" id="progress_4">
                                <div class="spinner" style="width: 16px; height: 16px; border-width: 2px; display: none;"></div>
                            </div>
                            <span>Generate configuration</span>
                        </li>
                        <li class="progress-item">
                            <div class="progress-icon pending" id="progress_5">
                                <div class="spinner" style="width: 16px; height: 16px; border-width: 2px; display: none;"></div>
                            </div>
                            <span>Complete installation</span>
                        </li>
                    </ul>
                    
                    <div id="install_result" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let currentStep = 1;
        let dbTestPassed = false;
        
        function showStep(step) {
            // Update indicators
            document.querySelectorAll('.step-indicator').forEach((el, i) => {
                el.classList.remove('active', 'completed');
                if (i + 1 < step) el.classList.add('completed');
                if (i + 1 === step) el.classList.add('active');
            });
            
            document.querySelectorAll('.step-connector').forEach((el, i) => {
                el.classList.toggle('completed', i + 1 < step);
            });
            
            // Show content
            document.querySelectorAll('.step-content').forEach(el => {
                el.classList.remove('active');
            });
            document.querySelector(`.step-content[data-step="${step}"]`).classList.add('active');
            
            currentStep = step;
        }
        
        function nextStep(step) {
            showStep(step);
        }
        
        function prevStep(step) {
            showStep(step);
        }
        
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
        
        function testDatabase() {
            const resultDiv = document.getElementById('db_test_result');
            resultDiv.innerHTML = '<div class="alert" style="background: var(--info-bg); color: var(--info);">Testing connection...</div>';
            
            const formData = new FormData();
            formData.append('action', 'test_database');
            formData.append('db_host', document.getElementById('db_host').value);
            formData.append('db_port', document.getElementById('db_port').value);
            formData.append('db_database', document.getElementById('db_database').value);
            formData.append('db_username', document.getElementById('db_username').value);
            formData.append('db_password', document.getElementById('db_password').value);
            
            fetch('/install.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    document.getElementById('db_continue').disabled = false;
                    dbTestPassed = true;
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    document.getElementById('db_continue').disabled = true;
                    dbTestPassed = false;
                }
            })
            .catch(err => {
                resultDiv.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
            });
        }
        
        function validateAndInstall() {
            // Validate form
            const appUrl = document.getElementById('app_url').value;
            const adminEmail = document.getElementById('admin_email').value;
            const adminPassword = document.getElementById('admin_password').value;
            const adminPasswordConfirm = document.getElementById('admin_password_confirm').value;
            
            if (!appUrl) {
                alert('Please enter the application URL');
                return;
            }
            
            if (!adminEmail) {
                alert('Please enter the admin email');
                return;
            }
            
            if (adminPassword.length < 8) {
                alert('Password must be at least 8 characters');
                return;
            }
            
            if (adminPassword !== adminPasswordConfirm) {
                alert('Passwords do not match');
                return;
            }
            
            // Show progress step
            showStep(4);
            runInstallation();
        }
        
        function runInstallation() {
            const formData = new FormData();
            formData.append('action', 'install');
            formData.append('db_host', document.getElementById('db_host').value);
            formData.append('db_port', document.getElementById('db_port').value);
            formData.append('db_database', document.getElementById('db_database').value);
            formData.append('db_username', document.getElementById('db_username').value);
            formData.append('db_password', document.getElementById('db_password').value);
            formData.append('app_url', document.getElementById('app_url').value);
            formData.append('timezone', document.getElementById('timezone').value);
            formData.append('admin_username', document.getElementById('admin_username').value);
            formData.append('admin_email', document.getElementById('admin_email').value);
            formData.append('admin_password', document.getElementById('admin_password').value);
            formData.append('allowed_ips', document.getElementById('allowed_ips').value);
            
            // Animate progress
            let step = 1;
            const interval = setInterval(() => {
                if (step <= 5) {
                    const icon = document.getElementById(`progress_${step}`);
                    icon.classList.remove('pending');
                    icon.classList.add('running');
                    icon.querySelector('.spinner').style.display = 'block';
                    step++;
                }
            }, 500);
            
            fetch('/install.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                clearInterval(interval);
                
                // Update all progress icons
                data.steps.forEach((s, i) => {
                    const icon = document.getElementById(`progress_${i + 1}`);
                    icon.classList.remove('pending', 'running');
                    icon.classList.add(s.success ? 'success' : 'error');
                    icon.querySelector('.spinner').style.display = 'none';
                    icon.innerHTML = s.success 
                        ? '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>'
                        : '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
                });
                
                const resultDiv = document.getElementById('install_result');
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <strong>Installation Complete!</strong><br>
                            Your LeadsFire Click Tracker is ready to use.
                        </div>
                        <div class="install-actions">
                            <a href="/" class="btn btn-primary w-100">Go to Login</a>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Installation Failed</strong><br>
                            ${data.errors ? data.errors.join('<br>') : 'An unknown error occurred'}
                        </div>
                        <div class="install-actions">
                            <button type="button" class="btn btn-secondary" onclick="showStep(3)">Back</button>
                            <button type="button" class="btn btn-primary" onclick="runInstallation()">Retry</button>
                        </div>
                    `;
                }
            })
            .catch(err => {
                clearInterval(interval);
                document.getElementById('install_result').innerHTML = `
                    <div class="alert alert-danger">
                        <strong>Error</strong><br>
                        ${err.message}
                    </div>
                `;
            });
        }
    </script>
</body>
</html>

