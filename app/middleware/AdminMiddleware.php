<?php

/**
 * AdminMiddleware - Kiểm tra quyền admin
 * 
 * Middleware này sẽ:
 * 1. Kiểm tra user đã đăng nhập chưa (sử dụng Auth class)
 * 2. Kiểm tra user có quyền admin không
 * 3. Nếu không có quyền, hiển thị lỗi 403
 */
class AdminMiddleware
{
    public function handle($params = [])
    {
        // Load Auth class
        require_once __DIR__ . '/../core/Auth.php';
        
        $auth = Auth::getInstance();

        // Kiểm tra user đã đăng nhập chưa
        if (!$auth->check()) {
            $auth->setRedirectAfterLogin($_SERVER['REQUEST_URI']);
            header('Location: /auth/login');
            exit();
        }

        // Kiểm tra quyền admin
        if (!$auth->isAdmin()) {
            // Hiển thị lỗi 403 - Forbidden
            http_response_code(403);
            echo "<h1>403 - Access Denied</h1>";
            echo "<p>You don't have permission to access this page.</p>";
            echo "<a href='/'>Go back to home</a>";
            exit();
        }

        // Nếu có quyền admin, tiếp tục xử lý request
        return true;
    }
}
