<?php

/**
 * Auth Class - Quản lý authentication với session
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
        // Lưu thông tin user vào session
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_username'] = $user->getUsername();
        $_SESSION['user_email'] = $user->getEmail();
        $_SESSION['user_full_name'] = $user->getFullName();
        $_SESSION['user_role'] = $user->isAdmin() ? 'admin' : 'user';
        $_SESSION['user_avatar'] = $user->getAvatarUrl();
        $_SESSION['user_points'] = $user->getTotalPoints();
        $_SESSION['user_games_played'] = $user->getGamesPlayed();
        
        // Tạo session ID mới để tránh session fixation
        session_regenerate_id(true);
        
        // Lưu thời gian đăng nhập
        $_SESSION['login_time'] = time();
        
        return true;
    }

    /**
     * Đăng xuất user
     */
    public function logout()
    {
        // Xóa tất cả session data
        $_SESSION = array();
        
        // Xóa session cookie
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
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
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
            // Tạo user object từ session data
            $this->user = new User([
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['user_username'],
                'email' => $_SESSION['user_email'],
                'full_name' => $_SESSION['user_full_name'],
                'is_admin' => $_SESSION['user_role'] === 'admin',
                'avatar_url' => $_SESSION['user_avatar'],
                'total_points' => $_SESSION['user_points'],
                'games_played' => $_SESSION['user_games_played']
            ]);
        }

        return $this->user;
    }

    /**
     * Lấy user ID
     */
    public function id()
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Kiểm tra user có phải admin không
     */
    public function isAdmin()
    {
        return $this->check() && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Kiểm tra user có phải guest không
     */
    public function isGuest()
    {
        return !$this->check();
    }

    /**
     * Cập nhật thông tin user trong session
     */
    public function updateUser($user)
    {
        if ($this->check()) {
            $_SESSION['user_username'] = $user->getUsername();
            $_SESSION['user_email'] = $user->getEmail();
            $_SESSION['user_full_name'] = $user->getFullName();
            $_SESSION['user_avatar'] = $user->getAvatarUrl();
            $_SESSION['user_points'] = $user->getTotalPoints();
            $_SESSION['user_games_played'] = $user->getGamesPlayed();
            
            // Reset user object để load lại từ session
            $this->user = null;
        }
    }

    /**
     * Lấy thời gian đăng nhập
     */
    public function getLoginTime()
    {
        return $_SESSION['login_time'] ?? null;
    }

    /**
     * Kiểm tra session có hết hạn không
     */
    public function isSessionExpired($maxLifetime = 3600) // 1 hour default
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
     * Gia hạn session
     */
    public function refreshSession()
    {
        if ($this->check()) {
            $_SESSION['login_time'] = time();
            session_regenerate_id(false);
        }
    }

    /**
     * Lưu URL để redirect sau khi login
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
}
