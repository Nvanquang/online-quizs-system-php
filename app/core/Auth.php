<?php

/**
 * Auth Class - Quản lý authentication với cookies (dữ liệu user lưu trong cookie), vẫn khởi tạo session
 */
class Auth
{
    private static $instance = null;
    private $user = null;

    private function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Đăng nhập user
     */
    public function login($user)
    {
        // Lưu thông tin user vào cookies
        $this->setUserCookie('id', $user->getId());
        $this->setUserCookie('username', $user->getUsername());
        $this->setUserCookie('email', $user->getEmail());
        $this->setUserCookie('full_name', $user->getFullName());
        $this->setUserCookie('role', $user->isAdmin() ? 'admin' : 'user');
        $this->setUserCookie('avatar', $user->getAvatarUrl());
        
        // Tạo session ID mới để tránh session fixation (vẫn giữ session cho các mục đích khác nếu cần)
        session_regenerate_id(true);
        
        // Lưu thời gian đăng nhập vào cookie
        $this->setUserCookie('login_time', time());
        
        return true;
    }

    /**
     * Đăng xuất user
     */
    public function logout()
    {
        // Xóa tất cả user cookies
        $this->clearUserCookies();
        $_SESSION = array();
        
        // Xóa session cookie (nếu vẫn dùng session cho mục đích khác)
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Hủy session 
        session_destroy();
        
        return true;
    }

    /**
     * Kiểm tra user đã đăng nhập chưa
     */
    public function check()
    {
        return isset($_COOKIE['user_id']) && !empty($_COOKIE['user_id']);
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function user()
    {
        if (!$this->check()) {
            return null;
        }

        if ($this->user === null) {
            // Tạo user object từ cookie data
            $this->user = new User([
                'id' => $_COOKIE['user_id'],
                'username' => $_COOKIE['user_username'],
                'email' => $_COOKIE['user_email'],
                'full_name' => $_COOKIE['user_full_name'],
                'is_admin' => $_COOKIE['user_role'] === 'admin',
                'avatar_url' => $_COOKIE['user_avatar']
            ]);
        }

        return $this->user;
    }

    /**
     * Lấy user ID
     */
    public function id()
    {
        return $_COOKIE['user_id'] ?? null;
    }

    /**
     * Kiểm tra user có phải admin không
     */
    public function isAdmin()
    {
        return $this->check() && ($_COOKIE['user_role'] ?? '') === 'admin';
    }

    /**
     * Kiểm tra user có phải guest không
     */
    public function isGuest()
    {
        return !$this->check();
    }

    /**
     * Cập nhật thông tin user trong cookies
     */
    public function updateUser($user)
    {
        if ($this->check()) {
            $this->setUserCookie('user_username', $user->getUsername());
            $this->setUserCookie('user_email', $user->getEmail());
            $this->setUserCookie('user_full_name', $user->getFullName());
            $this->setUserCookie('user_avatar', $user->getAvatarUrl());
            
            // Reset user object để load lại từ cookies
            $this->user = null;
        }
    }

    /**
     * Lấy thời gian đăng nhập
     */
    public function getLoginTime()
    {
        return $_COOKIE['user_login_time'] ?? null;
    }

    /**
     * Kiểm tra session có hết hạn không
     */
    public function isSessionExpired($maxLifetime = 3600) // 1 hours default
    {
        if (!$this->check()) {
            return true;
        }

        $loginTime = $this->getLoginTime();
        if (!$loginTime) {
            return true;
        }

        return (time() - $loginTime) > $maxLifetime;
    }

    /**
     * Gia hạn cookies (tương đương refresh session)
     */
    public function refreshSession()
    {
        if ($this->check()) {
            $this->setUserCookie('login_time', time());
            // Không regenerate session ID vì dữ liệu chính ở cookies, nhưng có thể regenerate nếu cần cho session khác
            session_regenerate_id(false);
        }
    }

    /**
     * Lưu URL để redirect sau khi login (vẫn dùng session cho redirect để tránh lộ URL trong cookie)
     */
    public function setRedirectAfterLogin($url)
    {
        $_SESSION['redirect_after_login'] = $url;
    }

    /**
     * Lấy và xóa URL redirect
     */
    public function getRedirectAfterLogin()
    {
        $url = $_SESSION['redirect_after_login'] ?? '/';
        unset($_SESSION['redirect_after_login']);
        return $url;
    }

    /**
     * Helper: Set cookie cho user data với config an toàn
     */
    private function setUserCookie($name, $value, $expire = 86400 * 7) // 7 days default
    {
        $path = '/';
        $domain = '';
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $httponly = true;

        setcookie("user_$name", $value, [
            'expires' => time() + $expire,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => 'Strict'
        ]);
    }

    /**
     * Helper: Clear tất cả user cookies
     */
    private function clearUserCookies()
    {
        $userCookies = ['user_id', 'user_username', 'user_email', 'user_full_name', 'user_role', 'user_avatar', 'user_login_time'];
        $path = '/';
        $domain = '';
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $httponly = true;

        foreach ($userCookies as $cookie) {
            setcookie($cookie, '', [
                'expires' => time() - 3600,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => 'Strict'
            ]);
        }
    }
}