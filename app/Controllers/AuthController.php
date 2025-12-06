<?php
class AuthController extends Controller {
    public function login($params = []) {
        $this->setParams($params);
        $error = null;
        
        $redirect = $this->getParam('redirect', 'home');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::validateCsrf($_POST['csrf_token'] ?? '');
            
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = "Email and password are required";
            } elseif (!Security::validateEmail($email)) {
                $error = "Please enter a valid email address";
            } else {
                if (Auth::attempt($email, $password)) {
                    $this->redirect($redirect);
                } else {
                    $error = "Invalid email or password";
                }
            }
        }
        
        $this->render('auth/login', [
            'error' => $error,
            'redirect' => $redirect
        ]);
    }
    
    public function logout($params = []) {
        $this->setParams($params);
        Auth::logout();
        $this->redirect('home');
    }
}
?>