<?php

class QuestionController extends Controller
{

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

    public function doCreate()
    {
        header('Content-Type: application/json');
        try {
            $validated = $this->validate($_POST, [
                'quiz_id' => 'required|interger|positive',
                'content' => 'required|min:10',
                'answer_a' => 'required|min:1',
                'answer_b' => 'required|min:1',
                'answer_c' => 'required|min:1',
                'answer_d' => 'required|min:1',
                'correct_answer' => 'required|enum:A,B,C,D',
                'time_limit' => 'required|interger|positive',
            ], false);

            if (!$validated) {
                $errors = $_SESSION['errors'] ?? 'Dữ liệu không hợp lệ';
                unset($_SESSION['errors']);
                echo json_encode(['success' => false, 'message' => $errors], JSON_UNESCAPED_UNICODE);
                return;
            }
            $quizId = (int)$validated['quiz_id'];

            $storedImageName = null;
            if (isset($_FILES['image_file']) && is_array($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                $storedImageName = $this->uploadFileService->saveFileToFolder($_FILES['image_file']['tmp_name'], 'questions', $_FILES['image_file']['name']);
            }

            $auth = Auth::getInstance();
            $userId = $auth->id();
            $now = date('Y-m-d H:i:s');

            $created = $this->questionService->create([
                'content' => $validated['content'],
                'answer_a' => $validated['answer_a'],
                'answer_b' => $validated['answer_b'],
                'answer_c' => $validated['answer_c'],
                'answer_d' => $validated['answer_d'],
                'correct_answer' => strtoupper($validated['correct_answer']),
                'explanation' => null,
                'image_url' => $storedImageName,
                'time_limit' => (int)$validated['time_limit'],
                'created_by' => $userId,
                'created_at' => $now,
            ]);

            $questionId = $created->getId();

            $lastQuestion = $this->quizQuestionService->findLastQuestionByQuizId($quizId);
            if ($lastQuestion == null) {
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
                'rating' => null,
                'total_questions' => $nextOrder,
                'is_public' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Tạo mới câu hỏi thành công'], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(response_code: 500);
            echo json_encode(['success' => false, 'message', $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public function doEdit($questionId)
    {
        header('Content-Type: application/json');
        try {

            $validated = $this->validate($_POST, [
                'content' => 'required|min:10',
                'answer_a' => 'required|min:1',
                'answer_b' => 'required|min:1',
                'answer_c' => 'required|min:1',
                'answer_d' => 'required|min:1',
                'correct_answer' => 'required|enum:A,B,C,D',
                'time_limit' => 'required|interger|positive',
            ], false);

            if (!$validated) {
                $errors = $_SESSION['errors'] ?? 'Dữ liệu không hợp lệ';
                unset($_SESSION['errors']);
                echo json_encode(['success' => false, 'message' => $errors], JSON_UNESCAPED_UNICODE);
                return;
            }
            $questionId = (int)$questionId;

            $existing = $this->questionService->findById($questionId);

            // Handle optional image upload
            $newImageName = null;
            if (isset($_FILES['image_file']) && is_array($_FILES['image_file']) && ($_FILES['image_file']['error'] === UPLOAD_ERR_OK)) {
                $newImageName = $this->uploadFileService->saveFileToFolder($_FILES['image_file']['tmp_name'], 'questions', $_FILES['image_file']['name']);
            }

            $updateData = [
                'content' => $validated['content'],
                'answer_a' => $validated['answer_a'],
                'answer_b' => $validated['answer_b'],
                'answer_c' => $validated['answer_c'],
                'answer_d' => $validated['answer_d'],
                'explanation' => null,
                'image_url' => $newImageName ?? $existing->getImageUrl(),
                'correct_answer' => strtoupper($validated['correct_answer']),
                'time_limit' => (int)$validated['time_limit'],
            ];

            // Perform update via repository
            $this->questionService->update($questionId, $updateData);

            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Cập nhật câu hỏi thành công'], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    public function doDelete($questionId)
    {
        header('Content-Type: application/json');
        try {
            $data = [
                'question_id' => $questionId
            ];
            $validated = $this->validate($data, [
                'question_id' => 'required|interger|positive',
            ], false);

            if (!$validated) {
                $errors = $_SESSION['errors'] ?? 'Dữ liệu không hợp lệ';
                unset($_SESSION['errors']);
                echo json_encode(['success' => false, 'message' => $errors], JSON_UNESCAPED_UNICODE);
                return;
            }

            $questionId = (int)$validated['question_id'];
            $question = $this->questionService->findById($questionId);
            if ($question->getImageUrl() != null) {
                $this->uploadFileService->deleteFileFromFolder('questions', $question->getImageUrl());
            }
            $this->questionService->delete($questionId);

            http_response_code(200);
            echo json_encode(['success' => true, 'message' => 'Xóa câu hỏi thành công'], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
