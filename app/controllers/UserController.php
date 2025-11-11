<?php

class UserController extends Controller
{
    private $quizService;

    public function __construct()
    {
        parent::__construct();
        $this->quizService = QuizServiceImpl::getInstance();
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

    
}