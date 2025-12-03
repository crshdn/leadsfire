<?php
/**
 * LeadsFire Click Tracker - Conversion Postback Endpoint
 * 
 * URL Format: /postback?subid={subid}&revenue={payout}
 * 
 * Supported Parameters:
 * - subid/clickid/aff_sub: The click identifier
 * - revenue/payout/amount: The conversion revenue
 * - status: Conversion status (approved, pending, rejected)
 * - txid/transaction_id: Transaction ID for deduplication
 * - custom1-5/aff_sub2-5: Custom fields
 */

// Disable error display for production
error_reporting(0);
ini_set('display_errors', 0);

// Define base path
define('BASE_PATH', dirname(__DIR__));

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

use LeadsFire\Services\ClickTracker\ConversionHandler;

// Collect all parameters (GET and POST)
$params = array_merge($_GET, $_POST);

// Process the conversion
$handler = new ConversionHandler();
$result = $handler->processPostback($params);

// Return response
header('Content-Type: text/plain');

if ($result['success']) {
    echo 'OK';
} else {
    http_response_code(400);
    echo 'ERROR: ' . ($result['error'] ?? 'Unknown error');
}

