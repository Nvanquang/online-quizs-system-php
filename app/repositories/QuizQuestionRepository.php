<?php


class QuizQuestionRepository extends BaseRepository
{
    protected $model;
    private static $instance = null;
    
    protected function getModelInstance(): QuizQuestion
    {
        $this->model = new QuizQuestion();
        return $this->model;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function findByQuiz(int $quizId, $orderBy = 'order_number ASC')
    {
        return $this->model->findAll(['quiz_id' => $quizId], $orderBy);
    }

    public function findLastQuestionByQuizId(int $quizId): ?object
    {
        $results = $this->model->findAll(['quiz_id' => $quizId], 'order_number DESC', 1);
        return $results[0] ?? null;
    }
}