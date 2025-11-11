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
            // $type = $_POST['type'] ?? 'multiple';
            // $randomOrder = isset($_POST['random_order']) ? (int)$_POST['random_order'] : 0;

            $errors = [];
            if ($quizId <= 0) $errors[] = 'quiz_id is required';
            if ($content === '') $errors[] = 'content is required';
            if ($answerA === '' || $answerB === '' || $answerC === '' || $answerD === '') $errors[] = 'all answers are required';
            if (!in_array($correct, ['A','B','C','D'], true)) $errors[] = 'correct_answer must be one of A,B,C,D';
            if (!is_int($timeLimit) || $timeLimit <= 0) $errors[] = 'time_limit must be a positive integer';
            if (!empty($errors)) {
                http_response_code(422);
                header('Content-Type: text/plain; charset=UTF-8');
                echo implode("\n", $errors);
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
                echo 'Failed to create question';
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
                'description' => null,
                'cover_image' => null,
                'total_questions' => $nextOrder,
            ]);

            $questions = $this->questionService->findByQuiz((int)$quizId);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['questions' => $questions], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Server error: ' . $e->getMessage();
        }
    }

    public function doEdit($questionId) {
        try {
            $questionId = (int)$questionId;
            $quizId = (int)($_POST['quiz_id'] ?? 0);
            $content = trim((string)($_POST['content'] ?? ''));
            $answerA = trim((string)($_POST['answer_a'] ?? ''));
            $answerB = trim((string)($_POST['answer_b'] ?? ''));
            $answerC = trim((string)($_POST['answer_c'] ?? ''));
            $answerD = trim((string)($_POST['answer_d'] ?? ''));
            $correct = strtoupper((string)($_POST['correct_answer'] ?? ''));
            $timeLimit = isset($_POST['time_limit']) ? (int)$_POST['time_limit'] : null;

            $errors = [];
            if ($questionId <= 0) $errors[] = 'questionId is required';
            if ($quizId <= 0) $errors[] = 'quiz_id is required';
            if ($content === '') $errors[] = 'content is required';
            if ($answerA === '' || $answerB === '' || $answerC === '' || $answerD === '') $errors[] = 'all answers are required';
            if (!in_array($correct, ['A','B','C','D'], true)) $errors[] = 'correct_answer must be one of A,B,C,D';
            if (!is_int($timeLimit) || $timeLimit <= 0) $errors[] = 'time_limit must be a positive integer';
            if (!empty($errors)) {
                http_response_code(422);
                header('Content-Type: text/plain; charset=UTF-8');
                echo implode("\n", $errors);
                return;
            }

            // Load existing question to preserve fields like image_url if not replaced
            $existing = $this->questionService->findById($questionId);
            if (!$existing) {
                http_response_code(404);
                header('Content-Type: text/plain; charset=UTF-8');
                echo 'Question not found';
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

            // Return updated list for this quiz
            $questions = $this->questionService->findByQuiz((int)$quizId);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['questions' => $questions], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Server error: ' . $e->getMessage();
        }
    }

    public function doDelete($questionId) {
        try {
            $questionId = (int)$questionId;
            $question = $this->questionService->findById($questionId);
            $this->uploadFileService->deleteFileFromFolder('questions', $question->getImageUrl());
            $this->questionService->delete($questionId);

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
            echo 'Server error: ' . $e->getMessage();
        }
    }
}