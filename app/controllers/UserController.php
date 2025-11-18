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
        try {
            $validated = $this->validate($_POST, [
                'username' => 'required|min:3',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'full_name' => 'required',
                'is_admin' => 'enum:0,1',
            ]);
            if ($validated) {
                $username = $validated['username'];
                $email = $validated['email'];
                $password = $validated['password'];
                $full_name = $validated['full_name'];
                $is_admin = (int) (!empty($validated['is_admin']));

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
                    echo json_encode(['success' => true, 'message' => 'Tạo mới user thành công'], JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Tạo mới user thất bại'], JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    public function updateUser()
    {
        header('Content-Type: application/json');
        try {
            $validated = $this->validate($_POST, [
                'user_id' => 'required|interger|positive',
                'username' => 'required|min:3',
                'email' => 'required|email',
                'full_name' => 'required',
                'is_admin' => 'enum:0,1',
            ]);
            if ($validated) {
                $user_id = (int)$validated['user_id'];
                $username = $validated['username'];
                $email = $validated['email'];
                $full_name = $validated['full_name'];
                $is_admin = (int) (!empty($validated['is_admin']));

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
                    echo json_encode(['success' => true, 'message' => 'Cập nhật user thành công'], JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Cập nhật user thất bại'], JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    public function deleteUser()
    {
        header('Content-Type: application/json');
        try {
            $validated = $this->validate($_POST, [
                'user_id' => 'required|interger|positive',
            ]);
            if($validated) {
                $user_id = (int)$validated['user_id'];

                $user = $this->userService->delete($user_id);
                if ($user) {
                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'Xóa user thành công'], JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Xóa user thất bại'], JSON_UNESCAPED_UNICODE);
                }
            }
        }
        catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            return;
        }
    }
}
