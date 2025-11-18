<?php

/**
 * AuthController - Xử lý authentication
 */
class AuthController extends Controller
{
    private $userService;
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = Auth::getInstance();
        $this->userService = UserServiceImpl::getInstance();
    }

    /**
     * Hiển thị trang đăng nhập
     */
    public function login()
    {
        // Nếu đã đăng nhập, redirect về trang chủ
        if ($this->auth->check()) {
            $this->redirect('/');
        }

        echo $this->renderPartial('auth/login');

        // Xóa error message sau khi hiển thị
        unset($_SESSION['errors']);
    }

    /**
     * Xử lý đăng nhập
     */
    public function doLogin()
    {
        try {
            $validated = $this->validate($_POST, [
                'username' => 'required',
                'password' => 'required'
            ]);
            if ($validated) {
                $username = $validated['username'];
                $password = $validated['password'];

                $user = $this->userService->authenticate($username, $password);

                // Đăng nhập thành công
                $this->auth->login($user);
                $_SESSION['success'] = 'Đăng nhập thành công!';

                // Redirect về trang trước đó hoặc trang chủ
                $redirectUrl = $this->auth->getRedirectAfterLogin();
                $this->redirect($redirectUrl);
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('/auth/login');
        }
    }

    /**
     * Hiển thị trang đăng ký
     */
    public function register()
    {
        // Nếu đã đăng nhập, redirect về trang chủ
        if ($this->auth->check()) {
            $this->redirect('/');
        }

        echo $this->renderPartial('auth/register', [
            'title' => 'Đăng Ký',
            'errors' => $_SESSION['errors'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ]);
    }

    /**
     * Xử lý đăng ký
     */
    public function doRegister()
    {
        try {
            $validated = $this->validate($_POST, [
                'username' => 'required|min:3|max:50',
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
                'full_name' => 'required|min:5|max:50'
            ]);

            if ($validated) {
                $username = $validated['username'];
                $email = $validated['email'];
                $password = $validated['password'];
                $fullName = $validated['full_name'];

                $userData = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'full_name' => $fullName,
                    'avatar_url' => 'avatar-default.jpg',
                    'is_admin' => 0,
                    'total_points' => 0,
                    'games_played' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $user = $this->userService->create($userData);

                if ($user) {
                    $_SESSION['success'] = 'Đăng ký thành công!';
                    $this->redirect('/auth/login');
                } else {
                    $_SESSION['errors'] = 'Có lỗi xảy ra khi tạo tài khoản';
                    $this->redirect('/auth/register');
                }
            }
        } catch (Exception $e) {
            $_SESSION['errors'] = $e->getMessage();
            $this->redirect('/auth/register');
        }
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        $this->auth->logout();
        $this->redirect('/');
    }

    public function loginAdmin()
    {
        // Nếu đã đăng nhập, redirect về trang chủ
        if ($this->auth->check()) {
            $this->redirect('/admin/dashboard');
        }

        echo $this->renderPartial('auth/login-admin');
    }

    public function doLoginAdmin()
    {
        try {
            $validated = $this->validate($_POST, [
                'username' => 'required',
                'password' => 'required'
            ]);
            if ($validated) {
                $username = $validated['username'];
                $password = $validated['password'];
                // Sử dụng UserService để authenticate
                $user = $this->userService->authenticate($username, $password);

                // Đăng nhập thành công
                $this->auth->login($user);
                $_SESSION['success'] = 'Đăng nhập thành công!';

                // Redirect về trang admin
                $this->redirect('/admin/dashboard');
            }
        } catch (Exception $e) {
            $_SESSION['errors'] = $e->getMessage();
            $this->redirect('/auth/login-admin');
        }
    }

    /**
     * Kiểm tra session timeout
     */
    public function checkSession()
    {
        if ($this->auth->isSessionExpired()) {
            $this->auth->logout();
            $this->json(['expired' => true]);
        } else {
            $this->json(['expired' => false, 'user' => $this->auth->user()]);
        }
    }

    /**
     * Gia hạn session
     */
    public function refreshSession()
    {
        if ($this->auth->check()) {
            $this->auth->refreshSession();
            $this->json(['success' => true]);
        } else {
            $this->json(['success' => false]);
        }
    }
}
