<?php


class QuizQuestionRepository extends BaseRepository
{
    /**
     * @var QuizQuestion
     */
    protected $model;

    /**
     * @return QuizQuestion
     */
    protected function getModelInstance(): QuizQuestion
    {
        return new QuizQuestion();
    }

    // Chỉ giữ các method custom, không duplicate CRUD vì BaseRepository đã có
    public function findByQuiz(int $quizId, $orderBy = 'order_number ASC')
    {
        return $this->model->findAll(['quiz_id' => $quizId], $orderBy);
    }
}