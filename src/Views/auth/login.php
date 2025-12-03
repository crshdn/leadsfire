<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= e(config('app.name', 'LeadsFire Click Tracker')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/theme.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            box-shadow: var(--glass-shadow);
            overflow: hidden;
        }
        
        .login-header {
            padding: 2.5rem 2rem 1.5rem;
            text-align: center;
        }
        
        .login-logo {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--fire-500) 0%, var(--fire-600) 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            box-shadow: var(--shadow-glow);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 20px rgba(249, 115, 22, 0.3); }
            50% { box-shadow: 0 0 40px rgba(249, 115, 22, 0.5); }
        }
        
        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 0.5rem;
        }
        
        .login-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        
        .login-body {
            padding: 1.5rem 2rem 2.5rem;
        }
        
        .login-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .login-form .form-label {
            font-weight: 500;
            margin-bottom: 0.625rem;
        }
        
        .login-form .form-control {
            height: 48px;
            padding: 0 1rem;
            font-size: 0.9375rem;
        }
        
        .login-form .btn-primary {
            width: 100%;
            height: 48px;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .login-remember {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .login-remember a {
            font-size: 0.875rem;
        }
        
        .login-footer {
            padding: 1.5rem 2rem;
            text-align: center;
            border-top: 1px solid var(--border-color);
            background: rgba(0, 0, 0, 0.1);
        }
        
        .login-footer p {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin: 0;
        }
        
        .password-wrapper {
            position: relative;
        }
        
        .password-wrapper .form-control {
            padding-right: 3rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.25rem;
            transition: color var(--transition-fast);
        }
        
        .password-toggle:hover {
            color: var(--text-primary);
        }
        
        .input-icon-wrapper {
            position: relative;
        }
        
        .input-icon-wrapper .form-control {
            padding-left: 3rem;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">🔥</div>
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to your account</p>
            </div>
            
            <div class="login-body">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-4">
                    <?= e($error) ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="/login" class="login-form">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <div class="input-icon-wrapper">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= e(old('username')) ?>" required autofocus 
                                   placeholder="Enter your username">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="password-wrapper input-icon-wrapper">
                            <svg class="input-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input type="password" class="form-control" id="password" name="password" 
                                   required placeholder="Enter your password">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="eye-icon">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="login-remember">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        Sign In
                    </button>
                </form>
            </div>
            
            <div class="login-footer">
                <p>LeadsFire Click Tracker v<?= e(config('app.version', '1.0.0')) ?></p>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }
    </script>
</body>
</html>

