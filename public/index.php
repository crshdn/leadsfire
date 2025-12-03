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
use LeadsFire\Controllers\CampaignController;
use LeadsFire\Controllers\TrafficSourceController;
use LeadsFire\Controllers\AffiliateNetworkController;
use LeadsFire\Models\Stats;

$auth = Auth::getInstance();

// Check IP restriction
if (!$auth->checkIpRestriction()) {
    http_response_code(403);
    die('Access denied. Your IP is not allowed.');
}

// Get the request URI and method
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/') ?: '/';
$requestMethod = $_SERVER['REQUEST_METHOD'];

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
    
    if ($requestMethod === 'POST') {
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

// API routes (JSON responses)
if (strpos($requestUri, '/api/') === 0) {
    header('Content-Type: application/json');
    
    // Verify CSRF for POST/PUT/DELETE
    if (in_array($requestMethod, ['POST', 'PUT', 'DELETE'])) {
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['_csrf_token'] ?? null;
        if (!verify_csrf($csrfToken)) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }
    }
    
    $apiRoute = substr($requestUri, 5); // Remove '/api/'
    
    switch ($apiRoute) {
        case 'campaigns':
            $controller = new CampaignController();
            if ($requestMethod === 'GET') {
                echo json_encode($controller->index());
            } elseif ($requestMethod === 'POST') {
                echo json_encode($controller->store($_POST));
            }
            break;
            
        case preg_match('/^campaigns\/(\d+)$/', $apiRoute, $m) ? true : false:
            $controller = new CampaignController();
            $id = (int)$m[1];
            if ($requestMethod === 'GET') {
                echo json_encode($controller->edit($id));
            } elseif ($requestMethod === 'PUT' || $requestMethod === 'POST') {
                parse_str(file_get_contents('php://input'), $data);
                echo json_encode($controller->update($id, array_merge($_POST, $data)));
            } elseif ($requestMethod === 'DELETE') {
                echo json_encode(['success' => $controller->delete($id)]);
            }
            break;
            
        case preg_match('/^campaigns\/(\d+)\/clone$/', $apiRoute, $m) ? true : false:
            $controller = new CampaignController();
            $newId = $controller->clone((int)$m[1]);
            echo json_encode(['success' => $newId !== null, 'campaign_id' => $newId]);
            break;
            
        case preg_match('/^campaigns\/(\d+)\/toggle$/', $apiRoute, $m) ? true : false:
            $controller = new CampaignController();
            echo json_encode(['success' => $controller->toggleActive((int)$m[1])]);
            break;
        
        // Traffic Sources API
        case 'traffic-sources':
            $controller = new TrafficSourceController();
            if ($requestMethod === 'GET') {
                echo json_encode($controller->index());
            } elseif ($requestMethod === 'POST') {
                echo json_encode($controller->store($_POST));
            }
            break;
            
        case preg_match('/^traffic-sources\/(\d+)$/', $apiRoute, $m) ? true : false:
            $controller = new TrafficSourceController();
            $id = (int)$m[1];
            if ($requestMethod === 'GET') {
                echo json_encode($controller->show($id));
            } elseif ($requestMethod === 'PUT' || $requestMethod === 'POST') {
                parse_str(file_get_contents('php://input'), $data);
                echo json_encode($controller->update($id, array_merge($_POST, $data)));
            } elseif ($requestMethod === 'DELETE') {
                echo json_encode(['success' => $controller->delete($id)]);
            }
            break;
        
        // Affiliate Networks API
        case 'affiliate-networks':
            $controller = new AffiliateNetworkController();
            if ($requestMethod === 'GET') {
                echo json_encode($controller->index());
            } elseif ($requestMethod === 'POST') {
                echo json_encode($controller->store($_POST));
            }
            break;
            
        case preg_match('/^affiliate-networks\/(\d+)$/', $apiRoute, $m) ? true : false:
            $controller = new AffiliateNetworkController();
            $id = (int)$m[1];
            if ($requestMethod === 'GET') {
                echo json_encode($controller->show($id));
            } elseif ($requestMethod === 'PUT' || $requestMethod === 'POST') {
                parse_str(file_get_contents('php://input'), $data);
                echo json_encode($controller->update($id, array_merge($_POST, $data)));
            } elseif ($requestMethod === 'DELETE') {
                echo json_encode(['success' => $controller->delete($id)]);
            }
            break;
        
        // Stats API
        case 'stats/range':
            $days = (int)($_GET['days'] ?? 7);
            $startDate = date('Y-m-d', strtotime("-{$days} days"));
            $endDate = date('Y-m-d');
            $stats = new Stats();
            echo json_encode($stats->getDateRangeStats($startDate, $endDate));
            break;
        
        case 'stats/today':
            $stats = new Stats();
            echo json_encode($stats->getTodayStats());
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
    }
    exit;
}

// Web routes
switch ($requestUri) {
    case '/':
        require BASE_PATH . '/src/Views/dashboard.php';
        break;
        
    case '/campaigns':
        require BASE_PATH . '/src/Views/campaigns/index.php';
        break;
        
    case '/campaigns/create':
        $controller = new CampaignController();
        $viewData = $controller->create();
        
        if ($requestMethod === 'POST') {
            if (!verify_csrf($_POST['_csrf_token'] ?? null)) {
                flash('error', 'Invalid request. Please try again.');
            } else {
                $result = $controller->store($_POST);
                if ($result['success']) {
                    flash('success', 'Campaign created successfully!');
                    redirect('/campaigns/edit?id=' . $result['campaign_id']);
                } else {
                    $viewData['errors'] = $result['errors'];
                }
            }
        }
        
        extract($viewData);
        require BASE_PATH . '/src/Views/campaigns/create.php';
        break;
        
    case '/campaigns/edit':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            redirect('/campaigns');
        }
        
        $controller = new CampaignController();
        $viewData = $controller->edit($id);
        
        if (!$viewData) {
            http_response_code(404);
            require BASE_PATH . '/src/Views/errors/404.php';
            break;
        }
        
        if ($requestMethod === 'POST') {
            if (!verify_csrf($_POST['_csrf_token'] ?? null)) {
                flash('error', 'Invalid request. Please try again.');
            } else {
                $result = $controller->update($id, $_POST);
                if ($result['success']) {
                    flash('success', 'Campaign updated successfully!');
                    redirect('/campaigns/edit?id=' . $id);
                } else {
                    $viewData['errors'] = $result['errors'];
                }
            }
        }
        
        extract($viewData);
        require BASE_PATH . '/src/Views/campaigns/edit.php';
        break;
        
    case '/campaigns/delete':
        if ($requestMethod === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id && verify_csrf($_POST['_csrf_token'] ?? null)) {
                $controller = new CampaignController();
                $controller->delete($id);
                flash('success', 'Campaign deleted successfully!');
            }
        }
        redirect('/campaigns');
        break;
        
    case '/settings':
        require BASE_PATH . '/src/Views/settings.php';
        break;
    
    case '/traffic-sources':
        require BASE_PATH . '/src/Views/traffic-sources/index.php';
        break;
    
    case '/affiliate-networks':
        require BASE_PATH . '/src/Views/affiliate-networks/index.php';
        break;
    
    case '/offers':
        require BASE_PATH . '/src/Views/offers/index.php';
        break;
    
    case '/landing-pages':
        require BASE_PATH . '/src/Views/landing-pages/index.php';
        break;
        
    default:
        http_response_code(404);
        require BASE_PATH . '/src/Views/errors/404.php';
}
