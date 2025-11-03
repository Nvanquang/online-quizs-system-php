<?php

class QuizRepository extends BaseRepository
{
    protected $model;
    private static $instance = null;
    
    protected function getModelInstance(): Quiz
    {
        $this->model = new Quiz();
        return $this->model;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    
    public function findByCode(string $quizCode)
    {
        return $this->model->findOne(['quiz_code' => $quizCode]);
    }

    public function findByPin(?string $pinCode)
    {
        if ($pinCode === null) return null;
        return $this->model->findOne(['pin_code' => $pinCode]);
    }
}