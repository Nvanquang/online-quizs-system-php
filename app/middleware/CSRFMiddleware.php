<?php

/**
 * CSRFMiddleware - Bảo vệ chống CSRF attacks
 * 
 * Middleware này sẽ:
 * 1. Tạo CSRF token cho form
 * 2. Kiểm tra CSRF token khi submit form
 * 3. Chỉ áp dụng cho các request POST, PUT, DELETE, PATCH
 */
class CSRFMiddleware
{
    public function handle($params = [])
    {
        // Khởi tạo session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Chỉ kiểm tra CSRF cho các method có thể thay đổi dữ liệu
        $methodsToCheck = ['POST', 'PUT', 'DELETE', 'PATCH'];
        $currentMethod = $_SERVER['REQUEST_METHOD'];

        if (in_array($currentMethod, $methodsToCheck)) {
            // Lấy token từ form hoặc header
            $submittedToken = $this->getSubmittedToken();
            $sessionToken = $_SESSION['csrf_token'] ?? null;

            // Kiểm tra token
            if (!$submittedToken || !$sessionToken || !hash_equals($sessionToken, $submittedToken)) {
                // Token không hợp lệ
                http_response_code(403);
                echo "<h1>403 - CSRF Token Mismatch</h1>";
                echo "<p>Invalid or missing CSRF token. Please refresh the page and try again.</p>";
                echo "<a href='javascript:history.back()'>Go back</a>";
                exit();
            }
        }

        // Tạo token mới nếu chưa có
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = $this->generateToken();
        }

        return true;
    }

    private function getSubmittedToken()
    {
        // Kiểm tra trong POST data
        if (isset($_POST['csrf_token'])) {
            return $_POST['csrf_token'];
        }

        // Kiểm tra trong header
        $headers = getallheaders();
        if (isset($headers['X-CSRF-Token'])) {
            return $headers['X-CSRF-Token'];
        }

        return null;
    }

    private function generateToken()
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Lấy CSRF token hiện tại (để sử dụng trong form)
     */
    public static function getToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Tạo input hidden cho CSRF token
     */
    public static function getTokenField()
    {
        $token = self::getToken();
        return "<input type='hidden' name='csrf_token' value='{$token}'>";
    }
}
