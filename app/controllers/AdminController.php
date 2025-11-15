<?php

class AdminController extends Controller
{
    private $userService;
    private $quizService;
    private $questionService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = UserServiceImpl::getInstance();
        $this->quizService = QuizServiceImpl::getInstance();
        $this->questionService = QuestionServiceImpl::getInstance();
    }

    public function index()
    {
        $totalUser = $this->userService->getTotalUsers();
        $totalQuiz = $this->quizService->getTotalQuizzes();
        $totalQuestion = $this->questionService->getTotalQuestions();
        echo $this->renderPartial('admin/index', ['totalUser' => $totalUser, 'totalQuiz' => $totalQuiz, 'totalQuestion' => $totalQuestion]);
    }

    public function users()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $result = $this->userService->findAllWithPagination($page, $perPage);

        echo $this->renderPartial('admin/users', [
            'users' => $result['data'],
            'total' => $result['total'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'total_pages' => $result['total_pages']
        ]);
    }

    public function quizzes()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $result = $this->quizService->findAllWithPagination($page, $perPage);

        echo $this->renderPartial('admin/quizzes', [
            'quizzes' => $result['data'],
            'total' => $result['total'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'total_pages' => $result['total_pages']
        ]);
    }

    public function questions()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        $result = $this->questionService->findAllWithPagination($page, $perPage);

        echo $this->renderPartial('admin/questions', [
            'questions' => $result['data'],
            'total' => $result['total'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'total_pages' => $result['total_pages']
        ]);
    }
}
