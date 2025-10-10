<?php
// index.php - Front controller and very simple router for the CRUD app

// Show all errors in development
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Get action from query string, default to list page
$action = $_GET['action'] ?? 'index';

// Include controller
require_once __DIR__ . '/controllers/ArticleController.php';

// Dispatch
$controller = new ArticleController();

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        http_response_code(404);
        echo 'Page not found.';
}
