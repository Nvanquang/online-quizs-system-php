<?php

class HomeController extends Controller
{
    public function index()
    {
        // Get current user
        $user = null;
        try {
            $auth = Auth::getInstance();
            $user = $auth->user();
        } catch (Exception $e) {
            // User not logged in or error
        }

        $login_success = null;
        if (isset($_SESSION['login_success'])) {
            $login_success = $_SESSION['login_success'];
            unset($_SESSION['login_success']);  // Xóa để tránh lặp nếu refresh
        }

        echo $this->render('home/index', [
            'title' => 'Trang Chủ - Quiz System',
            'login_success' => $login_success,
            'user' => $user,
        ]);
    }

    public function test()
    {
        echo $this->render('home/test', [
            'title' => 'Test PHP Info',
        ]);
    }
}
