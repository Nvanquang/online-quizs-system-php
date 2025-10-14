<?php

/**
 * AuthMiddleware - Kiểm tra xem user đã đăng nhập chưa
 * 
 * Middleware này sẽ:
 * 1. Kiểm tra session user
 * 2. Kiểm tra session timeout
 * 3. Nếu chưa đăng nhập, redirect về trang login
 * 4. Nếu đã đăng nhập, cho phép tiếp tục
 */
class AuthMiddleware
{
    public function handle($params = [])
    {
        // Load Auth class
        require_once __DIR__ . '/../core/Auth.php';
        
        $auth = Auth::getInstance();

        // Kiểm tra xem user đã đăng nhập chưa
        if (!$auth->check()) {
            // Lưu URL hiện tại để redirect sau khi login
            $auth->setRedirectAfterLogin($_SERVER['REQUEST_URI']);
            
            // Redirect về trang login
            header('Location: /auth/login');
            exit();
        }

        // Kiểm tra session timeout (1 hour)
        if ($auth->isSessionExpired(3600)) {
            $auth->logout();
            $auth->setRedirectAfterLogin($_SERVER['REQUEST_URI']);
            header('Location: /auth/login?expired=1');
            exit();
        }

        // Gia hạn session nếu cần (mỗi 30 phút)
        if ($auth->getLoginTime() && (time() - $auth->getLoginTime()) > 1800) {
            $auth->refreshSession();
        }

        // Nếu đã đăng nhập, tiếp tục xử lý request
        return true;
    }
}
