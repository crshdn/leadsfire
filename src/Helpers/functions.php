<?php
/**
 * LeadsFire Click Tracker - Global Helper Functions
 */

if (!function_exists('env')) {
    /**
     * Get environment variable value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);
        
        if ($value === false) {
            return $default;
        }

        // Convert string booleans
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            case 'empty':
            case '(empty)':
                return '';
        }

        // Remove quotes if present
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            return $matches[1];
        }
        if (preg_match("/^'(.*)'$/", $value, $matches)) {
            return $matches[1];
        }

        return $value;
    }
}

if (!function_exists('config')) {
    /**
     * Get configuration value using dot notation
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        static $config = [];
        
        $parts = explode('.', $key);
        $file = array_shift($parts);
        
        if (!isset($config[$file])) {
            $path = __DIR__ . '/../../config/' . $file . '.php';
            if (file_exists($path)) {
                $config[$file] = require $path;
            } else {
                return $default;
            }
        }
        
        $value = $config[$file];
        foreach ($parts as $part) {
            if (!is_array($value) || !isset($value[$part])) {
                return $default;
            }
            $value = $value[$part];
        }
        
        return $value;
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the base path of the application
     *
     * @param string $path
     * @return string
     */
    function base_path(string $path = ''): string
    {
        $base = dirname(__DIR__, 2);
        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the storage path
     *
     * @param string $path
     * @return string
     */
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the public path
     *
     * @param string $path
     * @return string
     */
    function public_path(string $path = ''): string
    {
        return base_path('public' . ($path ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('url')) {
    /**
     * Generate a URL
     *
     * @param string $path
     * @return string
     */
    function url(string $path = ''): string
    {
        $base = rtrim(config('app.url', ''), '/');
        return $path ? $base . '/' . ltrim($path, '/') : $base;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate an asset URL
     *
     * @param string $path
     * @return string
     */
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL
     *
     * @param string $url
     * @param int $status
     * @return void
     */
    function redirect(string $url, int $status = 302): void
    {
        header('Location: ' . $url, true, $status);
        exit;
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML entities
     *
     * @param string|null $value
     * @return string
     */
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate or get CSRF token
     *
     * @return string
     */
    function csrf_token(): string
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate CSRF hidden field
     *
     * @return string
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('verify_csrf')) {
    /**
     * Verify CSRF token
     *
     * @param string|null $token
     * @return bool
     */
    function verify_csrf(?string $token): bool
    {
        if (!isset($_SESSION['_csrf_token']) || !$token) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function old(string $key, $default = '')
    {
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    /**
     * Set or get flash message
     *
     * @param string|null $key
     * @param mixed $value
     * @return mixed
     */
    function flash(?string $key = null, $value = null)
    {
        if ($key === null) {
            $messages = $_SESSION['_flash'] ?? [];
            unset($_SESSION['_flash']);
            return $messages;
        }
        
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }
        
        $message = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $message;
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date
     *
     * @param string|int|DateTime $date
     * @param string|null $format
     * @return string
     */
    function format_date($date, ?string $format = null): string
    {
        if (is_string($date)) {
            $date = new DateTime($date);
        } elseif (is_int($date)) {
            $date = (new DateTime())->setTimestamp($date);
        }
        
        $format = $format ?? config('app.datetime_format', 'm/d/Y g:i A');
        return $date->format($format);
    }
}

if (!function_exists('format_money')) {
    /**
     * Format money value
     *
     * @param float $amount
     * @param string $currency
     * @return string
     */
    function format_money(float $amount, string $currency = 'USD'): string
    {
        return '$' . number_format($amount, 2);
    }
}

if (!function_exists('format_percent')) {
    /**
     * Format percentage value
     *
     * @param float $value
     * @param int $decimals
     * @return string
     */
    function format_percent(float $value, int $decimals = 2): string
    {
        return number_format($value, $decimals) . '%';
    }
}

if (!function_exists('is_installed')) {
    /**
     * Check if the application is installed
     *
     * @return bool
     */
    function is_installed(): bool
    {
        $envPath = base_path('.env');
        $installedPath = storage_path('.installed');
        
        return file_exists($envPath) && file_exists($installedPath);
    }
}

if (!function_exists('generate_key')) {
    /**
     * Generate a random key
     *
     * @param int $length
     * @return string
     */
    function generate_key(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('get_client_ip')) {
    /**
     * Get client IP address
     *
     * @return string
     */
    function get_client_ip(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // Common proxy header
            'HTTP_X_REAL_IP',            // Nginx proxy
            'HTTP_CLIENT_IP',            // Some proxies
            'REMOTE_ADDR',               // Direct connection
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

if (!function_exists('is_bot')) {
    /**
     * Basic bot detection
     *
     * @param string|null $userAgent
     * @return bool
     */
    function is_bot(?string $userAgent = null): bool
    {
        $userAgent = $userAgent ?? ($_SERVER['HTTP_USER_AGENT'] ?? '');
        
        $bots = [
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'sogou', 'exabot', 'facebot', 'facebookexternalhit',
            'ia_archiver', 'crawler', 'spider', 'robot', 'bot/', 'bot;',
            'headless', 'phantomjs', 'selenium', 'puppeteer', 'playwright',
            'curl', 'wget', 'python', 'java/', 'perl', 'ruby',
        ];
        
        $userAgentLower = strtolower($userAgent);
        
        foreach ($bots as $bot) {
            if (strpos($userAgentLower, $bot) !== false) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('is_prefetch')) {
    /**
     * Check if request is a prefetch request
     *
     * @return bool
     */
    function is_prefetch(): bool
    {
        $prefetchHeaders = [
            'HTTP_X_MOZ' => 'prefetch',
            'HTTP_X_PURPOSE' => 'prefetch',
            'HTTP_PURPOSE' => 'prefetch',
            'HTTP_SEC_PURPOSE' => 'prefetch',
        ];

        foreach ($prefetchHeaders as $header => $value) {
            if (isset($_SERVER[$header]) && strtolower($_SERVER[$header]) === $value) {
                return true;
            }
        }

        return false;
    }
}

