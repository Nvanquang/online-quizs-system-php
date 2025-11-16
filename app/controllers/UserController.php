<?php

class UserController extends Controller
{
    private $quizService;
    private $userService;

    public function __construct()
    {
        parent::__construct();
        $this->quizService = QuizServiceImpl::getInstance();
        $this->userService = UserServiceImpl::getInstance();
    }

    public function myQuizzes()
    {
        $auth = Auth::getInstance();
        $userId = $auth->id();

        $quizzes = $this->quizService->findAllByUserId($userId);
        echo $this->renderPartial('user/my-quizzes', ['quizzes' => $quizzes]);
    }

    public function profile()
    {
        echo $this->renderPartial('user/profile');
    }

    public function history()
    {
        echo $this->renderPartial('user/history');
    }

    public function updateProfile()
    {
        echo $this->renderPartial('user/update-profile');
    }

    public function createUser()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['message' => 'Phương thức không được phép.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $is_admin = (int) (!empty($_POST['is_admin']));

        // Validation cơ bản
        $errors = [];
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username phải có ít nhất 3 ký tự.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        }
        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'Password phải có ít nhất 6 ký tự.';
        }
        if (empty($full_name)) {
            $errors[] = 'Full name là bắt buộc.';
        }

        // Kiểm tra unique qua service
        if (empty($errors)) {
            $existingUser = $this->userService->findByUsername($username);
            if ($existingUser) {
                $errors[] = 'Username đã tồn tại.';
            }
        }
        if (empty($errors)) {
            $existingEmail = $this->userService->findByEmail($email);
            if ($existingEmail) {
                $errors[] = 'Email đã tồn tại.';
            }
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['message' => $errors], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Tạo user mới qua service
        $newUser = $this->userService->create([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'full_name' => $full_name,
            'is_admin' => $is_admin,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($newUser) {
            http_response_code(201);
            echo json_encode(['message' => 'Cập nhật user thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Cập nhật user thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function updateUser()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {  // Hoặc PUT cho update, nhưng giữ POST như form gốc
            http_response_code(405);
            echo json_encode(['message' => 'Phương thức không được phép.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $user_id = intval($_POST['user_id'] ?? 0);
        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'ID không hợp lệ.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $is_admin = (int) (!empty($_POST['is_admin']));

        // Validation (password optional)
        $errors = [];
        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username phải có ít nhất 3 ký tự.';
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        }
        if (empty($full_name)) {
            $errors[] = 'Full name là bắt buộc.';
        }
        if (!empty($password) && strlen($password) < 6) {
            $errors[] = 'Password phải có ít nhất 6 ký tự nếu thay đổi.';
        }


        $currentUser = $this->userService->findById($user_id); 
        if (!$currentUser) {
            http_response_code(404);
            echo json_encode('Người dùng không tồn tại.', JSON_UNESCAPED_UNICODE);
            return;
        }

        if (empty($errors)) {
            $existingUser = $this->userService->findByUsername($username);
            if ($existingUser && $existingUser->getId() != $user_id) {
                $errors[] = 'Username đã tồn tại.';
            }
        }
        if (empty($errors)) {
            // Kiểm tra unique email
            $existingEmail = $this->userService->findByEmail($email);
            if ($existingEmail && $existingEmail->getId() != $user_id) {
                $errors[] = 'Email đã tồn tại.';
            }
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['message' => $errors], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Chuẩn bị data update
        $updateData = [
            'username' => $username,
            'email' => $email,
            'full_name' => $full_name,
            'is_admin' => $is_admin,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Update qua service
        $updatedUser = $this->userService->update($user_id, $updateData);

        if ($updatedUser) {
            http_response_code(200);
            echo json_encode(['message' => 'Cập nhật user thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Cập nhật user thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }

    public function deleteUser()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
            http_response_code(405);
            echo json_encode(['message' => 'Phương thức không hợp lệ.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $user_id = intval($_POST['user_id'] ?? 0);
        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'ID không hợp lệ.'], JSON_UNESCAPED_UNICODE);
            return;
        }
        $user = $this->userService->delete($user_id);
        if($user) {
            http_response_code(200);
            echo json_encode(['message' => 'Xóa user thành công'], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Xóa user thất bại'], JSON_UNESCAPED_UNICODE);
        }
    }
}
