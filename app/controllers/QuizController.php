<?php

class QuizController extends Controller
{
    private $quizService;
    private $uploadFileService;
    private $questionService;

    public function __construct()
    {
        parent::__construct();
        $this->quizService = QuizServiceImpl::getInstance();
        $this->uploadFileService = UploadFileServiceImpl::getInstance();
        $this->questionService = QuestionServiceImpl::getInstance();
    }

    public function create()
    {
        echo $this->renderPartial('quizzes/create');
    }

    public function doCreate()
    {
        header('Content-Type: application/json');
        try {
            $validated = $this->validate($_POST, [
                'title' => 'required',
                'is_public' => 'required|enum:0,1',
            ]);
            if (!$validated) {
                $errors = $_SESSION['errors'] ?? 'Dữ liệu không hợp lệ';
                unset($_SESSION['errors']);
                echo json_encode(['errors' => false, 'message' => $errors], JSON_UNESCAPED_UNICODE);
                return;
            }
            $title = $validated['title'];
            $isPublic = (int)$validated['is_public'];

            $auth = Auth::getInstance();
            $userId = $auth->id();
            $user = $auth->user();
            $authorName = $user ? $user->getUsername() : '';

            $savedImageName = null;

            if (isset($_FILES['image']) && is_array($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['image']['tmp_name'];
                $savedImageName = $this->uploadFileService->saveFileToFolder($tmpPath, 'quizzes', $_FILES['image']['name']);
            }

            $quizCode = strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));
            $now = date('Y-m-d H:i:s');

            $created = $this->quizService->create([
                'title' => $title,
                'quiz_code' => $quizCode,
                'created_by' => $userId,
                'is_public' => $isPublic,
                'total_questions' => 0,
                'rating' => 0,
                'created_at' => $now,
                'updated_at' => null,
                'author' => $authorName,
                'image' => $savedImageName,
            ]);

            $quizId = $created->getId();

            if ($quizId) {
                $successMessage = 'Tạo quiz thành công!';

                // Nếu là AJAX request
                if ($this->isAjaxRequest()) {
                    http_response_code(201);
                    echo json_encode([
                        'success' => true,
                        'message' => $successMessage
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }

                // Form submit thông thường
                $_SESSION['success'] = $successMessage;
                $this->redirect("/quiz/edit/" . urlencode($quizId));
                return;
            }
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();

            // Nếu là AJAX request
            if ($this->isAjaxRequest()) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $errorMessage
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Form submit thông thường
            $_SESSION['errors'] = $errorMessage;
            $this->redirect('/quiz/create');
            return;
        }
    }

    public function edit($quizId)
    {
        try {
            $data = [
                'quiz_id' => $quizId,
            ];
            $validated = $this->validate($data, [
                'quiz_id' => 'required|interger|positive',
            ]);
            if ($validated) {
                $quizId = $validated['quiz_id'];
                $quiz = $this->quizService->findById((int)$quizId);
                $questions = $this->questionService->findByQuiz((int)$quizId);
                echo $this->renderPartial('quizzes/edit', ['quizId' => $quizId, 'quiz' => $quiz, 'questions' => $questions]);
            }
        } catch (Throwable $e) {
            $_SESSION['errors'] = $e->getMessage();
            return;
        }
    }

    public function editQuiz($quizId)
    {
        try {
            $data = [
                'quiz_id' => $quizId,
            ];
            $validated = $this->validate($data, [
                'quiz_id' => 'required|interger|positive',
            ]);
            if ($validated) {
                $quizId = $validated['quiz_id'];
                $quiz = $this->quizService->findById((int)$quizId);
                echo $this->renderPartial('quizzes/create', ['quiz' => $quiz]);
            }
        } catch (Throwable $e) {
            $_SESSION['errors'] = $e->getMessage();
            return;
        }
    }

    public function doEditQuiz($quizId)
    {
        header('Content-Type: application/json');
        try {
            $validated = $this->validate($_POST, [
                'title' => 'required',
                'is_public' => 'required|enum:0,1',
            ]);
            if ($validated) {
                $title = $validated['title'];
                $isPublic = (int)$validated['is_public'];

                $savedImageName = null;

                if (isset($_FILES['image']) && is_array($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $tmpPath = $_FILES['image']['tmp_name'];
                    $savedImageName = $this->uploadFileService->saveFileToFolder($tmpPath, 'quizzes', $_FILES['image']['name']);
                }


                $updated = $this->quizService->update((int)$quizId, [
                    'title' => $title,
                    'is_public' => $isPublic,
                    'image' => $savedImageName,
                    'total_questions' => null,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                if ($updated) {
                    $message = 'Cập nhật quiz thành công!';

                    // Kiểm tra xem có phải AJAX request không
                    if ($this->isAjaxRequest()) {
                        echo json_encode([
                            'success' => true,
                            'message' => $message
                        ], JSON_UNESCAPED_UNICODE);
                        exit;
                    } else {
                        $_SESSION['success'] = $message;
                        $this->redirect("/quiz/edit/" . urlencode($quizId));
                        return;
                    }
                }
            }
        } catch (Throwable $e) {
            $errorMessage = $e->getMessage();

            if ($this->isAjaxRequest()) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => $errorMessage
                ], JSON_UNESCAPED_UNICODE);
                exit;
            } else {
                $_SESSION['errors'] = $errorMessage;
                $this->redirect("/quiz/edit/" . urlencode($quizId));
                return;
            }
        }
    }

    public function doDelete($quizId)
    {
        header('Content-Type: application/json');
        try {
            $data = [
                'quiz_id' => $quizId,
            ];
            $validated = $this->validate($data, [
                'quiz_id' => 'required|interger|positive',
            ]);
            if ($validated) {
                $quizId = $validated['quiz_id'];
                $quiz = $this->quizService->findById($quizId);
                if ($quiz->getImage() != null) {
                    $this->uploadFileService->deleteFileFromFolder('quizzes', $quiz->getImage());
                }
                $this->quizService->delete($quizId);

                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Xóa trò chơi thành công!'], JSON_UNESCAPED_UNICODE);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => true, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public function view($quizId)
    {
        try {
            $data = [
                'quiz_id' => $quizId,
            ];
            $validated = $this->validate($data, [
                'quiz_id' => 'required|interger|positive',
            ]);
            if ($validated) {
                $quizId = $validated['quiz_id'];
                $quiz = $this->quizService->findById((int)$quizId);
                $questions = $this->questionService->findByQuiz((int)$quizId);
                echo $this->renderPartial('quizzes/view', ['quiz' => $quiz, 'questions' => $questions]);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
