<?php
require_once __DIR__ . '/../config/app.php';

if (!function_exists('spl_autoload_register')) {
    spl_autoload_register(function($class) {
        $file = APP_PATH . "/Models/$class.php";
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

if (ENVIRONMENT !== 'development' && (!isset($_GET['key']) || $_GET['key'] !== 'migrate123')) {
    die('Access denied');
}

try {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $migrations = [
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'user',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($migrations as $migration) {
        $db->exec($migration);
    }
        
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>