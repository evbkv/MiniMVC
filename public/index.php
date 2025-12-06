<?php
require_once __DIR__ . '/../config/app.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

spl_autoload_register(function($class) {
    $files = [
        APP_PATH . "/Controllers/$class.php",
        APP_PATH . "/Models/$class.php"
    ];
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    if (ENVIRONMENT === 'development') {
        error_log("Class $class not found in controllers/ or models/");
    }
});

require_once APP_PATH . '/Services/Auth.php';
require_once APP_PATH . '/Services/Security.php';
startSecureSession();

$route = trim($_GET['route'] ?? DEFAULT_ROUTE, '/');
$params = $_GET;
unset($params['route']);

$routes = require CONFIG_PATH . '/routes.php';

if (isset($routes[$route])) {
    [$controllerClass, $method] = $routes[$route];
    
    if (class_exists($controllerClass) && method_exists($controllerClass, $method)) {
        $controller = new $controllerClass();
        $controller->$method($params);
    } else {
        die("Controller or method not found: $controllerClass::$method");
    }
} else {
    http_response_code(404);
    echo 'Page not found: ' . htmlspecialchars($route);
}
?>