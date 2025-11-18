<?php

class HomeController extends Controller
{
    private $quizService;

    public function __construct()
    {
        parent::__construct();
        $this->quizService = QuizServiceImpl::getInstance();
    }

    public function index()
    {
        // Get current user
        $user = null;
        try {
            $auth = Auth::getInstance();
            $user = $auth->user();
        } catch (Exception $e) {
            // User not logged in or error
        }

        // Get all quizzes
        $quizzes = $this->quizService->getAll();

        echo $this->renderPartial('home/index', [
            'title' => 'Trang Chủ - Quiz System',
            'user' => $user,
            'quizzes' => $quizzes,
        ]);
    }

    public function test()
    {
        echo $this->renderPartial('home/test', [
            'title' => 'Test PHP Info',
        ]);
    }

}
