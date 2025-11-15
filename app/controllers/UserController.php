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
            echo json_encode(['message' => 'Method not allowed. Use POST.']);
            return;
        }

        // Đọc input từ JSON body (ưu tiên cho API) hoặc fallback $_POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;  // Fallback nếu form data
        }

        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $full_name = trim($input['full_name'] ?? '');
        $is_admin = (int) (!empty($input['is_admin']));

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
            echo json_encode(['message' => $errors]);
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
            echo json_encode(['message' => 'User created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'User creation failed']);
        }
    }

    public function updateUser()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {  // Hoặc PUT cho update, nhưng giữ POST như form gốc
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed. Use POST.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Đọc input từ JSON body hoặc $_POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $user_id = intval($input['user_id'] ?? 0);
        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid user ID',
                'data' => null
            ]);
            return;
        }

        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $full_name = trim($input['full_name'] ?? '');
        $is_admin = (int) (!empty($input['is_admin']));

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

        // Tìm user hiện tại qua service (giả sử có findById trong service)
        $currentUser = $this->userService->findById($user_id);  // Thêm method này nếu chưa có
        if (!$currentUser) {
            http_response_code(404);
            echo json_encode('User not found');
            return;
        }

        if (empty($errors)) {
            // Kiểm tra unique username (trừ chính nó)
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
            echo json_encode(['message' => $errors]);
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
            echo json_encode(['message' => 'User updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'User update failed']);
        }
    }

    public function deleteUser()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {  // Hoặc PUT cho update, nhưng giữ POST như form gốc
            http_response_code(405);
            echo json_encode(['message' => 'Method not allowed. Use POST.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // Đọc input từ JSON body hoặc $_POST
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $user_id = intval($input['user_id'] ?? 0);
        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid user ID']);
            return;
        }
        $user = $this->userService->delete($user_id);
        if($user) {
            http_response_code(200);
            echo json_encode(['message' => 'User deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'User deletion failed']);
        }
    }
}
