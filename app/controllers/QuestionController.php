<?php

class QuestionController extends Controller {

    private $uploadFileService;
    private $questionService;
    private $quizQuestionService;
    private $quizService;

    public function __construct()
    {
        parent::__construct();
        $this->uploadFileService = UploadFileServiceImpl::getInstance();
        $this->questionService = QuestionServiceImpl::getInstance();
        $this->quizQuestionService = QuizQuestionServiceImpl::getInstance();
        $this->quizService = QuizServiceImpl::getInstance();
    }

    public function doCreate() {
        try {
            $quizId = (int)($_POST['quiz_id'] ?? 0);
            $content = trim((string)($_POST['content'] ?? ''));
            $answerA = trim((string)($_POST['answer_a'] ?? ''));
            $answerB = trim((string)($_POST['answer_b'] ?? ''));
            $answerC = trim((string)($_POST['answer_c'] ?? ''));
            $answerD = trim((string)($_POST['answer_d'] ?? ''));
            $correct = strtoupper((string)($_POST['correct_answer'] ?? ''));
            $timeLimit = isset($_POST['time_limit']) ? (int)$_POST['time_limit'] : null;

            $errors = [];
            if ($quizId <= 0) $errors[] = 'quiz_id is required';
            if ($content === '') $errors[] = 'content is required';
            if ($answerA === '' || $answerB === '' || $answerC === '' || $answerD === '') $errors[] = 'all answers are required';
            if (!in_array($correct, ['A','B','C','D'], true)) $errors[] = 'correct_answer must be one of A,B,C,D';
            if (!is_int($timeLimit) || $timeLimit <= 0) $errors[] = 'time_limit must be a positive integer';
            if (!empty($errors)) {
                http_response_code(422);
                header('Content-Type: text/plain; charset=UTF-8');
                echo json_encode(['message' => $errors], JSON_UNESCAPED_UNICODE);
                return;
            }

            $storedImageName = null;
            if (isset($_FILES['image_file']) && is_array($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $storedImageName = $this->uploadFileService->saveFileToFolder($_FILES['image_file']['tmp_name'], 'questions', $_FILES['image_file']['name']);
            }

            $auth = Auth::getInstance();
            $userId = $auth->id();
            $now = date('Y-m-d H:i:s');

            $created = $this->questionService->create([
                'content' => $content,
                'answer_a' => $answerA,
                'answer_b' => $answerB,
                'answer_c' => $answerC,
                'answer_d' => $answerD,
                'correct_answer' => $correct,
                'explanation' => null,
                'image_url' => $storedImageName,
                'time_limit' => $timeLimit,
                'created_by' => $userId,
                'created_at' => $now,
            ]);

            $questionId = method_exists($created, 'getId') ? $created->getId() : null;
            if (!$questionId) {
                http_response_code(500);
                header('Content-Type: text/plain; charset=UTF-8');
                echo json_encode(['message' => 'Tạo câu hỏi thất bại'], JSON_UNESCAPED_UNICODE);
                return;
            }

            $lastQuestion = $this->quizQuestionService->findLastQuestionByQuizId($quizId);
            if($lastQuestion == null) {
                $nextOrder = 1;
            } else {
                $nextOrder = $lastQuestion->getOrderNumber() + 1;
            }

            $this->quizQuestionService->create([
                'quiz_id' => $quizId,
                'question_id' => $questionId,
                'order_number' => $nextOrder,
                'time_limit' => null,
            ]);

            // Update total questions in quiz
            $this->quizService->update($quizId, [
                'title' => null,
                'image' => null,
                'is_public' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Tạo mới câu hỏi thành công'], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message', $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public function doEdit($questionId) {
        try {
            $questionId = (int)$questionId;
            $content = trim((string)($_POST['content'] ?? ''));
            $answerA = trim((string)($_POST['answer_a'] ?? ''));
            $answerB = trim((string)($_POST['answer_b'] ?? ''));
            $answerC = trim((string)($_POST['answer_c'] ?? ''));
            $answerD = trim((string)($_POST['answer_d'] ?? ''));
            $correct = strtoupper((string)($_POST['correct_answer'] ?? ''));
            $timeLimit = isset($_POST['time_limit']) ? (int)$_POST['time_limit'] : null;

            $errors = [];
            if ($questionId <= 0) $errors[] = 'questionId is required';
            if ($content === '') $errors[] = 'content is required';
            if ($answerA === '' || $answerB === '' || $answerC === '' || $answerD === '') $errors[] = 'all answers are required';
            if (!in_array($correct, ['A','B','C','D'], true)) $errors[] = 'correct_answer must be one of A,B,C,D';
            if (!is_int($timeLimit) || $timeLimit <= 0) $errors[] = 'time_limit must be a positive integer';
            if (!empty($errors)) {
                http_response_code(422);
                header('Content-Type: text/plain; charset=UTF-8');
                echo json_encode(['message' => $errors], JSON_UNESCAPED_UNICODE);
                return;
            }

            // Load existing question to preserve fields like image_url if not replaced
            $existing = $this->questionService->findById($questionId);
            if (!$existing) {
                http_response_code(404);
                header('Content-Type: text/plain; charset=UTF-8');
                echo 'Question not found';
                echo json_encode(['message' => 'Câu hỏi không tồn tại!'], JSON_UNESCAPED_UNICODE);
                return;
            }

            // Handle optional image upload
            $newImageName = null;
            if (isset($_FILES['image_file']) && is_array($_FILES['image_file']) && ($_FILES['image_file']['error'] === UPLOAD_ERR_OK)) {
                $newImageName = $this->uploadFileService->saveFileToFolder($_FILES['image_file']['tmp_name'], 'questions', $_FILES['image_file']['name']);
            }

            $updateData = [
                'content' => $content,
                'answer_a' => $answerA,
                'answer_b' => $answerB,
                'answer_c' => $answerC,
                'answer_d' => $answerD,
                'explanation' => null,
                'image_url' => $newImageName ?? $existing->getImageUrl(),
                'correct_answer' => $correct,
                'time_limit' => $timeLimit,
            ];

            // Perform update via repository
            $this->questionService->update($questionId, $updateData);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Cập nhật câu hỏi thành công'], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public function doDelete($questionId) {
        try {
            $questionId = (int)$questionId;
            $question = $this->questionService->findById($questionId);
            if($question->getImageUrl() != null){
                $this->uploadFileService->deleteFileFromFolder('questions', $question->getImageUrl());
            }
            $this->questionService->delete($questionId);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Xóa câu hỏi thành công'], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo json_encode(['message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}