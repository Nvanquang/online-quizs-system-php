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
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        if ($title === '') {
            http_response_code(422);
            echo 'Title is required';
            return;
        }

        $isPublic = isset($_POST['is_public']) && (string)$_POST['is_public'] === '1' ? 1 : 0;

        $auth = Auth::getInstance();
        $userId = $auth->id();
        $user = $auth->user();
        $authorName = $user ? $user->getUsername() : '';

        $savedImageName = null;

        if (isset($_FILES['cover_image_file']) && is_array($_FILES['cover_image_file']) && $_FILES['cover_image_file']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['cover_image_file']['tmp_name'];
            $savedImageName = $this->uploadFileService->saveFileToFolder($tmpPath, 'quizzes', $_FILES['cover_image_file']['name']);
        }

        $quizModel = new Quiz();
        $quizCode = strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));
        $now = date('Y-m-d H:i:s');

        $created = $quizModel->create([
            'title' => $title,
            'quiz_code' => $quizCode,
            'thumbnail_url' => null,
            'created_by' => $userId,
            'is_public' => $isPublic,
            'total_questions' => 0,
            'created_at' => $now,
            'author' => $authorName,
            'image' => $savedImageName,
        ]);

        $quizId = method_exists($created, 'getId') ? $created->getId() : null;

        if ($quizId) {
            $this->redirect("/quiz/edit/". urlencode($quizId));
            return;
        }

        http_response_code(500);
        echo 'Failed to create quiz';
    }

    // public function doCreateQuestion($quizId)
    // {
    //     echo $this->renderPartial('quizzes/edit', ['quizId' => $quizId]);
    // }

    public function edit($quizId)
    {
        $quiz = $this->quizService->findById((int)$quizId);
        $questions = $this->questionService->findByQuiz((int)$quizId);
        echo $this->renderPartial('quizzes/edit', ['quizId' => $quizId, 'quiz' => $quiz, 'questions' => $questions]);
    }

    public function doEdit($quizId)
    {
        // echo $this->renderPartial('quizzes/edit', ['quizId' => $quizId]);
    }
}