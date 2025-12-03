<?php

namespace LeadsFire\Installer;

use PDO;
use PDOException;

/**
 * Installation Wizard Handler
 */
class Installer
{
    private array $errors = [];
    private array $warnings = [];
    
    /**
     * Check system requirements
     */
    public function checkRequirements(): array
    {
        $requirements = [];
        
        // PHP Version
        $requirements['php_version'] = [
            'name' => 'PHP Version',
            'required' => '8.0.0',
            'current' => PHP_VERSION,
            'passed' => version_compare(PHP_VERSION, '8.0.0', '>='),
        ];
        
        // Required Extensions
        $extensions = ['pdo', 'pdo_mysql', 'curl', 'json', 'mbstring', 'openssl'];
        foreach ($extensions as $ext) {
            $requirements["ext_$ext"] = [
                'name' => "PHP Extension: $ext",
                'required' => 'Installed',
                'current' => extension_loaded($ext) ? 'Installed' : 'Not installed',
                'passed' => extension_loaded($ext),
            ];
        }
        
        // Optional Extensions
        $optionalExt = ['gd', 'intl', 'zip', 'xml'];
        foreach ($optionalExt as $ext) {
            $requirements["ext_$ext"] = [
                'name' => "PHP Extension: $ext (optional)",
                'required' => 'Recommended',
                'current' => extension_loaded($ext) ? 'Installed' : 'Not installed',
                'passed' => true, // Optional, always passes
                'warning' => !extension_loaded($ext),
            ];
        }
        
        // Writable directories
        $writablePaths = [
            'storage' => dirname(__DIR__, 2) . '/storage',
            'storage/logs' => dirname(__DIR__, 2) . '/storage/logs',
            'storage/cache' => dirname(__DIR__, 2) . '/storage/cache',
            'storage/sessions' => dirname(__DIR__, 2) . '/storage/sessions',
        ];
        
        foreach ($writablePaths as $name => $path) {
            $writable = is_dir($path) && is_writable($path);
            $requirements["writable_$name"] = [
                'name' => "Writable: $name",
                'required' => 'Writable',
                'current' => $writable ? 'Writable' : 'Not writable',
                'passed' => $writable,
            ];
        }
        
        return $requirements;
    }
    
    /**
     * Test database connection
     */
    public function testDatabaseConnection(array $config): array
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;charset=utf8mb4',
                $config['host'],
                $config['port']
            );
            
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);
            
            // Check if database exists
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($config['database']));
            $exists = $stmt->fetch() !== false;
            
            return [
                'success' => true,
                'database_exists' => $exists,
                'message' => $exists 
                    ? 'Connection successful. Database exists.' 
                    : 'Connection successful. Database will be created.',
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'database_exists' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Create database if not exists
     */
    public function createDatabase(array $config): bool
    {
        try {
            $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $config['host'], $config['port']);
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            return true;
        } catch (PDOException $e) {
            $this->errors[] = 'Failed to create database: ' . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Import database schema
     */
    public function importSchema(array $config): bool
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['port'],
                $config['database']
            );
            
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            
            $schemaPath = dirname(__DIR__, 2) . '/database/schema.sql';
            if (!file_exists($schemaPath)) {
                $this->errors[] = 'Schema file not found: ' . $schemaPath;
                return false;
            }
            
            $sql = file_get_contents($schemaPath);
            
            // Execute multi-query
            $pdo->exec($sql);
            
            return true;
        } catch (PDOException $e) {
            $this->errors[] = 'Failed to import schema: ' . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Create admin user
     * Handles both CPVLab schema (imported) and fresh install schema
     */
    public function createAdminUser(array $config, string $username, string $password, string $email): bool
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['port'],
                $config['database']
            );
            
            $pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            
            // Check if users table exists and what schema it has
            $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
            $tableExists = $stmt->fetch() !== false;
            
            if ($tableExists) {
                // Check if it's CPVLab schema (has UserRoleID) or our schema (has Email)
                $stmt = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'UserRoleID'");
                $isCpvlabSchema = $stmt->fetch() !== false;
                
                if ($isCpvlabSchema) {
                    // CPVLab schema - need to alter table to support longer passwords (bcrypt)
                    // and add Email column if not exists
                    $pdo->exec("ALTER TABLE `users` MODIFY `Password` varchar(255) NOT NULL");
                    
                    // Check if Email column exists
                    $stmt = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'Email'");
                    if ($stmt->fetch() === false) {
                        $pdo->exec("ALTER TABLE `users` ADD COLUMN `Email` varchar(255) DEFAULT NULL AFTER `Password`");
                    }
                    
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    
                    // Insert into CPVLab schema
                    $stmt = $pdo->prepare("
                        INSERT INTO `users` (Username, Password, Email, UserRoleID, DateAdded, Timezone)
                        VALUES (?, ?, ?, 1, NOW(), 'America/New_York')
                    ");
                    $stmt->execute([$username, $hashedPassword, $email]);
                } else {
                    // Our schema - insert normally
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO `users` (Username, Password, Email, Role, Active, CreatedAt)
                        VALUES (?, ?, ?, 'admin', 1, NOW())
                    ");
                    $stmt->execute([$username, $hashedPassword, $email]);
                }
            } else {
                // No users table - create our schema
                $pdo->exec("
                    CREATE TABLE `users` (
                        `UserID` int(10) unsigned NOT NULL AUTO_INCREMENT,
                        `Username` varchar(100) NOT NULL,
                        `Password` varchar(255) NOT NULL,
                        `Email` varchar(255) NOT NULL,
                        `Role` varchar(50) NOT NULL DEFAULT 'admin',
                        `Active` tinyint(1) unsigned NOT NULL DEFAULT 1,
                        `LastLogin` datetime DEFAULT NULL,
                        `LoginAttempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
                        `LockedUntil` datetime DEFAULT NULL,
                        `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `UpdatedAt` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                        PRIMARY KEY (`UserID`),
                        UNIQUE KEY `Username` (`Username`),
                        UNIQUE KEY `Email` (`Email`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
                ");
                
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                
                $stmt = $pdo->prepare("
                    INSERT INTO `users` (Username, Password, Email, Role, Active, CreatedAt)
                    VALUES (?, ?, ?, 'admin', 1, NOW())
                ");
                $stmt->execute([$username, $hashedPassword, $email]);
            }
            
            return true;
        } catch (PDOException $e) {
            $this->errors[] = 'Failed to create admin user: ' . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Generate .env file
     */
    public function generateEnvFile(array $settings): bool
    {
        $envPath = dirname(__DIR__, 2) . '/.env';
        $templatePath = dirname(__DIR__, 2) . '/.env.example';
        
        if (!file_exists($templatePath)) {
            $this->errors[] = '.env.example template not found';
            return false;
        }
        
        $content = file_get_contents($templatePath);
        
        // Replace placeholders
        $replacements = [
            'APP_NAME="LeadsFire Click Tracker"' => 'APP_NAME="' . addslashes($settings['app_name'] ?? 'LeadsFire Click Tracker') . '"',
            'APP_ENV=production' => 'APP_ENV=' . ($settings['app_env'] ?? 'production'),
            'APP_DEBUG=false' => 'APP_DEBUG=' . ($settings['app_debug'] ?? 'false'),
            'APP_URL=https://your-domain.com' => 'APP_URL=' . ($settings['app_url'] ?? 'https://your-domain.com'),
            'APP_TIMEZONE=America/New_York' => 'APP_TIMEZONE=' . ($settings['timezone'] ?? 'America/New_York'),
            'DB_HOST=localhost' => 'DB_HOST=' . ($settings['db_host'] ?? 'localhost'),
            'DB_PORT=3306' => 'DB_PORT=' . ($settings['db_port'] ?? '3306'),
            'DB_DATABASE=click_tracker' => 'DB_DATABASE=' . ($settings['db_database'] ?? 'click_tracker'),
            'DB_USERNAME=your_db_user' => 'DB_USERNAME=' . ($settings['db_username'] ?? ''),
            'DB_PASSWORD=your_db_password' => 'DB_PASSWORD=' . ($settings['db_password'] ?? ''),
            'ALLOWED_IPS=127.0.0.1,::1' => 'ALLOWED_IPS=' . ($settings['allowed_ips'] ?? ''),
            'MAIL_FROM_ADDRESS=noreply@your-domain.com' => 'MAIL_FROM_ADDRESS=' . ($settings['mail_from'] ?? 'noreply@example.com'),
            'MAIL_TO_ADDRESS=admin@your-domain.com' => 'MAIL_TO_ADDRESS=' . ($settings['mail_to'] ?? ''),
        ];
        
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        if (file_put_contents($envPath, $content) === false) {
            $this->errors[] = 'Failed to write .env file';
            return false;
        }
        
        // Secure the file
        chmod($envPath, 0600);
        
        return true;
    }
    
    /**
     * Mark installation as complete
     */
    public function markComplete(): bool
    {
        $basePath = dirname(__DIR__, 2);
        $installedPath = $basePath . '/storage/.installed';
        $envPath = $basePath . '/.env';
        
        $content = json_encode([
            'installed_at' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
        ]);
        
        if (file_put_contents($installedPath, $content) === false) {
            $this->errors[] = 'Failed to create installation marker';
            return false;
        }
        
        chmod($installedPath, 0600);
        
        // Lock down .env file after installation (read-only for web server)
        if (file_exists($envPath)) {
            chmod($envPath, 0640);
        }
        
        return true;
    }
    
    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get warnings
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }
    
    /**
     * Get list of timezones
     */
    public static function getTimezones(): array
    {
        $timezones = [];
        $regions = [
            'Africa' => \DateTimeZone::AFRICA,
            'America' => \DateTimeZone::AMERICA,
            'Asia' => \DateTimeZone::ASIA,
            'Atlantic' => \DateTimeZone::ATLANTIC,
            'Australia' => \DateTimeZone::AUSTRALIA,
            'Europe' => \DateTimeZone::EUROPE,
            'Pacific' => \DateTimeZone::PACIFIC,
        ];
        
        foreach ($regions as $name => $mask) {
            $zones = \DateTimeZone::listIdentifiers($mask);
            foreach ($zones as $zone) {
                $timezones[$name][] = $zone;
            }
        }
        
        return $timezones;
    }
}

