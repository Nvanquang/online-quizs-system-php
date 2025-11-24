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

        // 12 câu hỏi mới tạo gần nhất
        $recentlyCreatedQuizzes  = $this->quizService->findByCondition([], 'created_at DESC', 12);

        // 12 câu hỏi đánh giá cao nhất
        $highestRatedQuizzes = $this->quizService->findByCondition([], '(rating_sum / NULLIF(rating_count, 0)) DESC', 12);

        echo $this->renderPartial('home/index', [
            'title' => 'Trang Chủ - Quiz System',
            'user' => $user,
            'recentlyCreatedQuizzes' => $recentlyCreatedQuizzes,
            'highestRatedQuizzes' => $highestRatedQuizzes,
        ]);
    }

    public function test()
    {
        echo $this->renderPartial('home/test', [
            'title' => 'Test PHP Info',
        ]);
    }
}
