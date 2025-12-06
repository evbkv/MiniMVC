<?php
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('DATABASE_PATH', ROOT_PATH . '/database');
define('VIEWS_PATH', APP_PATH . '/Views');

define('SESSION_LIFETIME', 86400);
define('SESSION_COOKIE_SAMESITE', 'Strict');
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_USE_STRICT_MODE', true);
define('SESSION_USE_ONLY_COOKIES', true);

define('DB_PATH', DATABASE_PATH . '/database.sqlite');
define('ENVIRONMENT', 'development');

define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_MAX_LENGTH', 72);
define('CSRF_TOKEN_LENGTH', 32);

define('DEFAULT_ROUTE', 'home');
define('MAX_PARAM_COUNT', 10);

define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');

$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$basePath = $scriptDir === '/' ? '' : $scriptDir;

if (str_ends_with($basePath, '/public')) {
    $basePath = substr($basePath, 0, -7);
}

if ($basePath === '/') {
    $basePath = '';
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
define('BASE_PATH', $basePath);
define('BASE_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . $basePath . '/');

function startSecureSession() {
    $params = [
        'cookie_httponly' => SESSION_COOKIE_HTTPONLY,
        'cookie_secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'cookie_samesite' => SESSION_COOKIE_SAMESITE,
        'use_strict_mode' => SESSION_USE_STRICT_MODE,
        'use_only_cookies' => SESSION_USE_ONLY_COOKIES,
        'cookie_lifetime' => SESSION_LIFETIME
    ];
    
    session_start($params);
    
    if (empty($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}
?>