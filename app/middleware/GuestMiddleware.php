<?php

/**
 * GuestMiddleware - Chỉ cho phép user chưa đăng nhập truy cập
 * 
 * Middleware này sẽ:
 * 1. Kiểm tra user đã đăng nhập chưa
 * 2. Nếu đã đăng nhập, redirect về trang chủ
 * 3. Nếu chưa đăng nhập, cho phép tiếp tục (thường dùng cho trang login/register)
 */
class GuestMiddleware
{
    public function handle($params = [])
    {
        // Load Auth class
        require_once __DIR__ . '/../core/Auth.php';
        
        $auth = Auth::getInstance();

        // Kiểm tra user đã đăng nhập chưa
        if ($auth->check()) {
            // Nếu đã đăng nhập, redirect về trang chủ
            header('Location: /');
            exit();
        }

        // Nếu chưa đăng nhập, tiếp tục xử lý request
        return true;
    }
}
