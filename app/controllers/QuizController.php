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
        try {
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            if ($title === '') {
                http_response_code(422);
                echo 'Title is required';
                echo json_encode(['message' => 'Chủ đề không được rỗng'], JSON_UNESCAPED_UNICODE);
                return;
            }

            $isPublic = isset($_POST['is_public']) && (string)$_POST['is_public'] === '1' ? 1 : 0;

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

            $quizId = method_exists($created, 'getId') ? $created->getId() : null;

            if ($quizId) {
                echo json_encode(['message' => 'Tạo quiz thành công'], JSON_UNESCAPED_UNICODE);
                $this->redirect("/quiz/edit/" . urlencode($quizId));
                return;
            }
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    public function edit($quizId) // giao diện tao cau hoi
    {
        try {
            $quiz = $this->quizService->findById((int)$quizId);
            $questions = $this->questionService->findByQuiz((int)$quizId);
            echo $this->renderPartial('quizzes/edit', ['quizId' => $quizId, 'quiz' => $quiz, 'questions' => $questions]);
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    public function editQuiz($quizId)
    {
        try {
            $quiz = $this->quizService->findById((int)$quizId);
            echo $this->renderPartial('quizzes/create', ['quiz' => $quiz]);
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    public function doEditQuiz($quizId)
    {
        try {
            $title = isset($_POST['title']) ? trim($_POST['title']) : '';
            if ($title === '') {
                http_response_code(422);
                echo json_encode(['message' => 'Chủ đề không được rỗng'], JSON_UNESCAPED_UNICODE);
                return;
            }

            $isPublic = (int) (!empty($input['is_public']));

            $savedImageName = null;

            if (isset($_FILES['image']) && is_array($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['image']['tmp_name'];
                $savedImageName = $this->uploadFileService->saveFileToFolder($tmpPath, 'quizzes', $_FILES['image']['name']);
            }


            $updated = $this->quizService->update((int)$quizId, [
                'title' => $title,
                'is_public' => $isPublic,
                'image' => $savedImageName,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($updated) {
                echo json_encode(['message' => 'Cập nhật quiz thành công'], JSON_UNESCAPED_UNICODE);
                $this->redirect("/quiz/edit/" . urlencode($quizId));
                return;
            }
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            return;
        }
    }

    public function doDelete($quizId)
    {
        try {
            $quizId = (int)$quizId;
            $quiz = $this->quizService->findById($quizId);
            if($quiz->getImage() != null){
                $this->uploadFileService->deleteFileFromFolder('quizzes', $quiz->getImage());
            }
            $this->quizService->delete($quizId);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Quiz deleted successfully'], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public function view($quizId)
    {
        $quiz = $this->quizService->findById((int)$quizId);
        $questions = $this->questionService->findByQuiz((int)$quizId);
        echo $this->renderPartial('quizzes/view', ['quiz' => $quiz, 'questions' => $questions]);
    }
}
