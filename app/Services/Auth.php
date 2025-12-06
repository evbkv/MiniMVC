<?php
class Auth {
    public static function check() {
        return isset($_SESSION['user_id']);
    }
    
    public static function user() {
        if (!self::check()) {
            return null;
        }
        return User::find($_SESSION['user_id']);
    }
    
    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function attempt($email, $password) {
        $user = User::where(['email' => $email])[0] ?? null;
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user['email'];
            $_SESSION['role'] = $user['role'] ?? ROLE_USER;
            return true;
        }
        
        return false;
    }
    
    public static function logout() {
        session_destroy();
    }
    
    public static function can($permission) {
        $user = self::user();
        if (!$user) {
            return false;
        }
        
        $role = $user['role'] ?? ROLE_USER;
        $permissions = self::getPermissions($role);
        
        return in_array($permission, $permissions);
    }
    
    public static function authorize($permission) {
        if (!self::can($permission)) {
            http_response_code(403);
            die('Access denied');
        }
    }
    
    protected static function getPermissions($role) {
        $permissions = [
            ROLE_ADMIN => ['create_article', 'edit_article', 'delete_article', 'view_article', 'manage_users'],
            ROLE_USER => ['view_article', 'edit_own_article'],
        ];
        
        return $permissions[$role] ?? [];
    }
    
    public static function middleware($permission = null) {
        if (!self::check()) {
            self::redirectToLogin();
        }
        
        if ($permission && !self::can($permission)) {
            http_response_code(403);
            die('Access denied');
        }
    }
    
    protected static function redirectToLogin() {
        header('Location: ' . BASE_URL . 'login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}
?>