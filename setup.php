<?php
require_once __DIR__ . '/config/app.php';

if (file_exists(DB_PATH) && filesize(DB_PATH) > 0) {
    die("Setup already completed!");
}

session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    if (empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        try {
            require_once DATABASE_PATH . '/migrations.php';
            
            $db = new PDO('sqlite:' . DB_PATH);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$email, $password_hash, ROLE_ADMIN]);
            
            $lastId = $db->lastInsertId();
            
            $_SESSION['user'] = $email;
            $_SESSION['user_id'] = $lastId;
            $_SESSION['role'] = ROLE_ADMIN;
            
            $success = "Setup completed! Admin user '$email' created and logged in.";
            
            header("Refresh: 10; URL=" . BASE_URL . "home");
            
        } catch (Exception $e) {
            $error = "Setup error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup</title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
</head>
<style>
    body {
        width: 250px;
        margin: auto;
        text-align: center;
        font-family: sans-serif;
        padding-top: 40px;
    }
    form, p, input, button {
        width: 100%;
        text-align: center;
        font-size: 14px;
    }
    form {
        margin: 30px 0;
    }
    input {
        width: calc(100% - 14px);
    }
    h1, h2 {
        margin: 0;
    }
    h2, p, a {
        color: gray;
        text-decoration: none;
    }
    input, button {
        border: 1px solid gray;
        margin-top: 5px;
        padding: 6px;
    }
    button {
        padding: 12px;
        cursor: pointer;
        background-color: lightgrayig;
    }
</style>
<body>
    <h1>MiniMVC</h1>
    <h2>Initial Setup</h2>
    
    <?php if ($error): ?>
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <p><?php echo htmlspecialchars($success); ?></p>
        <p>Redirecting to home page...</p>
    <?php else: ?>
        <form method="POST">
            <p>
                Admin Email:<br>
                <input type="email" name="email" required>
            </p>
            
            <p>
                Password (min 8 characters):<br>
                <input type="password" name="password" required minlength="8">
                <input type="password" name="confirm_password" required minlength="8">
            </p>
            
            <p>
                <button type="submit">Create Database & Admin User</button>
            </p>
        </form>
        <p>© <a href="https://github.com/evbkv">evbkv</a></p>
    <?php endif; ?>
</body>
</html>