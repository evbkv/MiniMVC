<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
</head>
<body>
    <h1>Home Page</h1>
    
    <?php if (!empty($user)): ?>
        <p>Hello, <?php echo $user; ?>!</p>
        <form method="POST" action="<?php echo BASE_URL; ?>logout">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <button type="submit">Logout</button>
        </form>
    <?php else: ?>
        <a href="<?php echo BASE_URL; ?>login">Login</a>
    <?php endif; ?>
    
    <p><a href="<?php echo BASE_URL; ?>page">Go to Demo Page</a></p>
</body>
</html>