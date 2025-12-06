<?php
class Controller {
    protected $params = [];
    
    public function __construct() {
        $this->params = [];
    }
    
    protected function setParams($params) {
        if (count($params) > MAX_PARAM_COUNT) {
            $params = array_slice($params, 0, MAX_PARAM_COUNT);
        }
        $this->params = Security::escape($params);
    }
    
    protected function getParam($key, $default = null) {
        return $this->params[$key] ?? $default;
    }
    
    protected function getAllParams() {
        return $this->params;
    }
    
    protected function auth() {
        return Auth::user();
    }
    
    protected function checkAuth($permission = null) {
        Auth::middleware($permission);
    }
    
    protected function render($view, $data = []) {
        $data['csrf_token'] = Security::csrfToken();
        $data['current_user'] = Auth::user();
        $data = array_map(['Security', 'escape'], $data);
        $data['params'] = $this->params;
        extract($data);
        
        $viewPath = $this->resolveViewPath($view);
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            die('View not found: ' . htmlspecialchars($view));
        }
    }
    
    private function resolveViewPath($view) {
        $view = str_replace(['..', "\0"], '', $view);
        $view = str_replace('/', DIRECTORY_SEPARATOR, $view);
        
        $viewPath = VIEWS_PATH . "/$view.php";
        
        $realViewPath = realpath($viewPath);
        $realViewsDir = realpath(VIEWS_PATH);
        
        if ($realViewPath === false || strpos($realViewPath, $realViewsDir) !== 0) {
            die('Invalid view path: ' . htmlspecialchars($view));
        }
        
        return $viewPath;
    }
    
    protected function redirect($path) {
        header('Location: ' . BASE_URL . ltrim($path, '/'));
        exit;
    }
}
?>