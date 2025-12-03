<?php

namespace LeadsFire\Services;

/**
 * Authentication Service
 */
class Auth
{
    private static ?Auth $instance = null;
    private ?array $user = null;
    
    private function __construct()
    {
        $this->startSession();
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Start secure session
     */
    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionName = config('app.session.name', 'leadsfire_session');
            $lifetime = config('app.session.lifetime', 86400);
            
            session_name($sessionName);
            
            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            
            session_start();
            
            // Regenerate session ID periodically
            if (!isset($_SESSION['_created'])) {
                $_SESSION['_created'] = time();
            } elseif (time() - $_SESSION['_created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['_created'] = time();
            }
        }
    }
    
    /**
     * Attempt to log in a user
     * Handles both CPVLab schema and our schema
     */
    public function attempt(string $username, string $password): array
    {
        $db = Database::getInstance();
        
        // Detect schema type
        $isCpvlabSchema = $this->isCpvlabSchema($db);
        
        // Get user by username
        if ($isCpvlabSchema) {
            // CPVLab schema doesn't have Active column
            $user = $db->fetch(
                "SELECT * FROM users WHERE Username = ?",
                [$username]
            );
        } else {
            $user = $db->fetch(
                "SELECT * FROM users WHERE Username = ? AND Active = 1",
                [$username]
            );
        }
        
        if (!$user) {
            $this->logAttempt($username, false);
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Check if account is locked (only if column exists)
        if (!$isCpvlabSchema && isset($user['LockedUntil']) && $user['LockedUntil'] && strtotime($user['LockedUntil']) > time()) {
            $remainingMinutes = ceil((strtotime($user['LockedUntil']) - time()) / 60);
            return [
                'success' => false, 
                'message' => "Account is locked. Try again in {$remainingMinutes} minutes."
            ];
        }
        
        // Verify password
        if (!password_verify($password, $user['Password'])) {
            if (!$isCpvlabSchema) {
                $this->incrementLoginAttempts($user['UserID']);
            }
            $this->logAttempt($username, false);
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Successful login
        if (!$isCpvlabSchema) {
            $this->resetLoginAttempts($user['UserID']);
        }
        $this->updateLastLogin($user['UserID']);
        $this->logAttempt($username, true);
        
        // Store user in session
        $_SESSION['user_id'] = $user['UserID'];
        $_SESSION['username'] = $user['Username'];
        // CPVLab uses UserRoleID, our schema uses Role
        $_SESSION['role'] = $user['Role'] ?? ($user['UserRoleID'] == 1 ? 'admin' : 'user');
        $_SESSION['logged_in_at'] = time();
        
        // Regenerate session ID on login
        session_regenerate_id(true);
        
        $this->user = $user;
        
        return ['success' => true, 'message' => 'Login successful'];
    }
    
    /**
     * Check if using CPVLab schema
     */
    private function isCpvlabSchema(Database $db): bool
    {
        static $isCpvlab = null;
        
        if ($isCpvlab === null) {
            try {
                $result = $db->fetch("SHOW COLUMNS FROM `users` LIKE 'UserRoleID'");
                $isCpvlab = $result !== false;
            } catch (\Exception $e) {
                $isCpvlab = false;
            }
        }
        
        return $isCpvlab;
    }
    
    /**
     * Check if user is logged in
     */
    public function check(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout
        $lifetime = config('app.session.lifetime', 86400);
        if (isset($_SESSION['logged_in_at']) && (time() - $_SESSION['logged_in_at']) > $lifetime) {
            $this->logout();
            return false;
        }
        
        return true;
    }
    
    /**
     * Get current user
     */
    public function user(): ?array
    {
        if (!$this->check()) {
            return null;
        }
        
        if ($this->user === null) {
            $db = Database::getInstance();
            $this->user = $db->fetch(
                "SELECT * FROM users WHERE UserID = ?",
                [$_SESSION['user_id']]
            );
        }
        
        return $this->user;
    }
    
    /**
     * Get user ID
     */
    public function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Log out user
     */
    public function logout(): void
    {
        $this->user = null;
        
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
    }
    
    /**
     * Check IP restriction
     */
    public function checkIpRestriction(): bool
    {
        $allowedIps = config('app.security.allowed_ips', []);
        
        if (empty($allowedIps)) {
            return true; // No restriction
        }
        
        $clientIp = get_client_ip();
        
        foreach ($allowedIps as $ip) {
            if ($this->ipMatches($clientIp, $ip)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if IP matches (supports CIDR notation)
     */
    private function ipMatches(string $ip, string $pattern): bool
    {
        if (strpos($pattern, '/') !== false) {
            // CIDR notation
            list($subnet, $bits) = explode('/', $pattern);
            $ip = ip2long($ip);
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - $bits);
            return ($ip & $mask) === ($subnet & $mask);
        }
        
        return $ip === $pattern;
    }
    
    /**
     * Increment failed login attempts
     */
    private function incrementLoginAttempts(int $userId): void
    {
        $db = Database::getInstance();
        $maxAttempts = config('app.security.rate_limit.login_attempts', 5);
        $window = config('app.security.rate_limit.login_window', 300);
        
        $user = $db->fetch("SELECT LoginAttempts FROM users WHERE UserID = ?", [$userId]);
        $attempts = ($user['LoginAttempts'] ?? 0) + 1;
        
        $lockUntil = null;
        if ($attempts >= $maxAttempts) {
            $lockUntil = date('Y-m-d H:i:s', time() + $window);
        }
        
        $db->update('users', [
            'LoginAttempts' => $attempts,
            'LockedUntil' => $lockUntil,
        ], 'UserID = ?', [$userId]);
    }
    
    /**
     * Reset login attempts
     */
    private function resetLoginAttempts(int $userId): void
    {
        $db = Database::getInstance();
        $db->update('users', [
            'LoginAttempts' => 0,
            'LockedUntil' => null,
        ], 'UserID = ?', [$userId]);
    }
    
    /**
     * Update last login time
     */
    private function updateLastLogin(int $userId): void
    {
        $db = Database::getInstance();
        $db->update('users', [
            'LastLogin' => date('Y-m-d H:i:s'),
        ], 'UserID = ?', [$userId]);
    }
    
    /**
     * Log login attempt
     */
    private function logAttempt(string $username, bool $success): void
    {
        $logLevel = config('app.logging.level', 'verbose');
        
        if ($logLevel === 'verbose') {
            $logger = Logger::getInstance();
            $logger->info($success ? 'Login successful' : 'Login failed', [
                'username' => $username,
                'ip' => get_client_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            ]);
        }
    }
    
    /**
     * Hash a password
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Verify a password
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}

