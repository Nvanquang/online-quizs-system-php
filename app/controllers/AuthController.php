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
        $this->userService = new UserServiceImpl();
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

        $register_success = null;
        if (isset($_SESSION['register_success'])) {
            $register_success = $_SESSION['register_success'];
            unset($_SESSION['register_success']);  // Xóa để tránh lặp nếu refresh
        }

        echo $this->renderPartial('auth/login', [
            'title' => 'Đăng Nhập',
            'register_success' => $register_success,
            'error' => $_SESSION['login_error'] ?? null
        ]);

        // Xóa error message sau khi hiển thị
        unset($_SESSION['login_error']);
    }

    /**
     * Xử lý đăng nhập
     */
    public function doLogin()
    {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validation
        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/auth/login');
        }

        try {
            // Sử dụng UserService để authenticate
            $user = $this->userService->authenticate($username, $password);
            
            if (!$user) {
                $_SESSION['login_error'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
                $this->redirect('/auth/login');
            }

            // Đăng nhập thành công
            $this->auth->login($user);
            $_SESSION['login_success'] = 'Đăng nhập thành công!';

            // Redirect về trang trước đó hoặc trang chủ
            $redirectUrl = $this->auth->getRedirectAfterLogin();
            $this->redirect($redirectUrl);

        } catch (Exception $e) {
            $_SESSION['login_error'] = $e->getMessage();
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
            'error' => $_SESSION['register_error'] ?? null,
            'success' => $_SESSION['register_success'] ?? null
        ]);

        // Xóa messages sau khi hiển thị
        unset($_SESSION['register_error'], $_SESSION['register_success']);
    }

    /**
     * Xử lý đăng ký
     */
    public function doRegister()
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $fullName = $_POST['full_name'] ?? '';

        // Validation
        $errors = $this->validateRegister($_POST);

        if (!empty($errors)) {
            $_SESSION['register_error'] = implode('<br>', $errors);
            $this->redirect('/auth/register');
        }

        try {

            // Tạo user mới
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
                $_SESSION['register_success'] = 'Đăng ký thành công!';
                $this->redirect('/auth/login');
            } else {
                $_SESSION['register_error'] = 'Có lỗi xảy ra khi tạo tài khoản';
                $this->redirect('/auth/register');
            }

        } catch (Exception $e) {
            $_SESSION['register_error'] = $e->getMessage();
            $this->redirect('/auth/register');
        }
    }

    private function validateRegister($_DATA) {

        $username = $_DATA['username'] ?? '';
        $email = $_DATA['email'] ?? '';
        $password = $_DATA['password'] ?? '';
        $confirmPassword = $_DATA['confirm_password'] ?? '';
        $fullName = $_DATA['full_name'] ?? '';
        // Validation
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Tên đăng nhập không được để trống';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Tên đăng nhập phải có ít nhất 3 ký tự';
        }

        if (empty($email)) {
            $errors[] = 'Email không được để trống';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        if (empty($fullName)) {
            $errors[] = 'Họ và tên không được để trống';
        }

        if (empty($password)) {
            $errors[] = 'Mật khẩu không được để trống';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Mật khẩu xác nhận không khớp';
        }

        return $errors;
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        $this->auth->logout();
        $this->redirect('/');
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
