<?php

namespace LeadsFire\Services;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

/**
 * Logger Service - Wrapper around Monolog
 */
class Logger
{
    private static ?Logger $instance = null;
    private MonologLogger $logger;
    
    private function __construct()
    {
        $this->logger = new MonologLogger('leadsfire');
        
        $logPath = config('app.logging.path', base_path('storage/logs'));
        $channel = config('app.logging.channel', 'daily');
        $level = $this->getMonologLevel(config('app.logging.level', 'verbose'));
        
        // Custom format
        $format = "[%datetime%] %channel%.%level_name%: %message% %context%\n";
        $formatter = new LineFormatter($format, 'Y-m-d H:i:s', true, true);
        
        if ($channel === 'daily') {
            $handler = new RotatingFileHandler(
                $logPath . '/app.log',
                30, // Keep 30 days
                $level
            );
        } else {
            $handler = new StreamHandler(
                $logPath . '/app.log',
                $level
            );
        }
        
        $handler->setFormatter($formatter);
        $this->logger->pushHandler($handler);
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Convert config level to Monolog level
     */
    private function getMonologLevel(string $level): int
    {
        return match ($level) {
            'verbose' => MonologLogger::DEBUG,
            'standard' => MonologLogger::INFO,
            'minimal' => MonologLogger::WARNING,
            default => MonologLogger::INFO,
        };
    }
    
    /**
     * Log debug message
     */
    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }
    
    /**
     * Log info message
     */
    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }
    
    /**
     * Log notice message
     */
    public function notice(string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }
    
    /**
     * Log warning message
     */
    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }
    
    /**
     * Log error message
     */
    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
    
    /**
     * Log critical message
     */
    public function critical(string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }
    
    /**
     * Log alert message
     */
    public function alert(string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }
    
    /**
     * Log emergency message
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }
}

