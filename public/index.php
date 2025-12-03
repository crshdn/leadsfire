<?php
/**
 * LeadsFire Click Tracker - Main Entry Point
 */

// Error reporting based on environment
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Check if installed
$installedPath = BASE_PATH . '/storage/.installed';
if (!file_exists($installedPath)) {
    header('Location: /install.php');
    exit;
}

// Autoload
require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/src/Helpers/functions.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

// Set timezone
date_default_timezone_set(config('app.timezone', 'America/New_York'));

// Initialize services
use LeadsFire\Services\Auth;
use LeadsFire\Services\Logger;

$auth = Auth::getInstance();

// Check IP restriction
if (!$auth->checkIpRestriction()) {
    http_response_code(403);
    die('Access denied. Your IP is not allowed.');
}

// Get the request URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/') ?: '/';

// Simple routing
$routes = [
    '/' => 'dashboard',
    '/login' => 'login',
    '/logout' => 'logout',
    '/campaigns' => 'campaigns/index',
    '/campaigns/create' => 'campaigns/create',
    '/campaigns/edit' => 'campaigns/edit',
    '/settings' => 'settings',
];

// Handle logout
if ($requestUri === '/logout') {
    $auth->logout();
    redirect('/login');
}

// Handle login
if ($requestUri === '/login') {
    if ($auth->check()) {
        redirect('/');
    }
    
    $error = null;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify CSRF
        if (!verify_csrf($_POST['_csrf_token'] ?? null)) {
            $error = 'Invalid request. Please try again.';
        } else {
            $result = $auth->attempt(
                $_POST['username'] ?? '',
                $_POST['password'] ?? ''
            );
            
            if ($result['success']) {
                redirect('/');
            } else {
                $error = $result['message'];
            }
        }
    }
    
    // Render login page
    require BASE_PATH . '/src/Views/auth/login.php';
    exit;
}

// Require authentication for all other routes
if (!$auth->check()) {
    redirect('/login');
}

// Get current user
$currentUser = $auth->user();

// Route to controller
$route = $routes[$requestUri] ?? '404';

$viewPath = BASE_PATH . '/src/Views/' . $route . '.php';
if (file_exists($viewPath)) {
    require $viewPath;
} else {
    // 404 page
    http_response_code(404);
    require BASE_PATH . '/src/Views/errors/404.php';
}

