<?php


class QuestionRepository extends BaseRepository
{
    protected $model;
    private static $instance = null;
    
    protected function getModelInstance(): Question
    {
        $this->model = new Question();
        return $this->model;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function findByCreator(int $userId, $orderBy = 'created_at DESC', $limit = null)
    {
        return $this->model->findAll(['created_by' => $userId], $orderBy, $limit);
    }

    public function findByQuiz(int $quizId)
    {
        $sql = "
        SELECT 
            q.id AS question_id,
            q.content,
            q.answer_a,
            q.answer_b,
            q.answer_c,
            q.answer_d,
            q.correct_answer,
            q.explanation,
            q.image_url,
            COALESCE(qq.time_limit, q.time_limit) AS time_limit,
            qq.order_number
        FROM questions q
        INNER JOIN quiz_questions qq ON q.id = qq.question_id
        WHERE qq.quiz_id = :quiz_id
        ORDER BY qq.order_number ASC
    ";
        return $this->model->fetchAll($sql, ['quiz_id' => $quizId]);
    }
}
