<?php
class HomeController extends Controller {
    public function index() {
        $user = $_SESSION['user'] ?? null;
        $this->render('home', ['user' => $user]);
    }
}
?>