<?php
/**
 * LeadsFire Click Tracker - Click Tracking Endpoint
 * 
 * URL Format: /c/{CAMPAIGN_KEY}
 * 
 * This is the main entry point for tracking clicks.
 * It should be as fast as possible.
 */

// Disable error display for production
error_reporting(0);
ini_set('display_errors', 0);

// Define base path
define('BASE_PATH', dirname(__DIR__, 2));

// Quick check if installed
if (!file_exists(BASE_PATH . '/storage/.installed')) {
    http_response_code(503);
    die('Service unavailable');
}

// Autoload
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/src/Helpers/functions.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Set timezone
date_default_timezone_set(config('app.timezone', 'America/New_York'));

use LeadsFire\Services\ClickTracker\ClickTracker;

// Get campaign key from URL
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($requestUri, PHP_URL_PATH);

// Extract campaign key
// Supports: /c/CAMPAIGN_KEY or /c/CAMPAIGN_KEY/
preg_match('#/c/([a-zA-Z0-9]+)/?#', $path, $matches);
$campaignKey = $matches[1] ?? '';

if (empty($campaignKey)) {
    http_response_code(400);
    die('Invalid request');
}

// Collect all parameters
$params = array_merge($_GET, $_POST);

// Process the click
$tracker = new ClickTracker();
$result = $tracker->processClick($campaignKey, $params);

if (!$result['success']) {
    // Handle error - redirect to fallback or show error
    $fallbackUrl = config('app.url', '/');
    header('Location: ' . $fallbackUrl, true, 302);
    exit;
}

// Redirect to destination
$redirectType = $result['redirect_type'] ?? 302;
$redirectUrl = $result['redirect_url'];

// Validate redirect URL
if (empty($redirectUrl) || !filter_var($redirectUrl, FILTER_VALIDATE_URL)) {
    $redirectUrl = config('app.url', '/');
}

// Perform redirect
header('Location: ' . $redirectUrl, true, $redirectType);
exit;

