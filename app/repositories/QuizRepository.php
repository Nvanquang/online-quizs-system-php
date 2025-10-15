<?php

class QuizRepository extends BaseRepository
{
    /**
     * @var Quiz
     */
    protected $model;

    /**
     * @return Quiz
     */
    protected function getModelInstance(): Quiz
    {
        return new Quiz();
    }

    // Chỉ giữ các method custom, không duplicate CRUD vì BaseRepository đã có
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