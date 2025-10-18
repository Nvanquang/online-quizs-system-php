<?php

class QuizController extends Controller
{
    public function create()
    {
        echo $this->renderPartial('quizzes/create');
    }

    public function doCreate()
    {
        echo $this->renderPartial('quizzes/create');
    }

    public function edit()
    {
        echo $this->renderPartial('quizzes/edit');
    }

    public function doEdit()
    {
        echo $this->renderPartial('quizzes/edit');
    }

    public function view(){
        echo $this->renderPartial('quizzes/view');
    }

    public function doView(){
        echo $this->renderPartial('quizzes/view');
    }
}