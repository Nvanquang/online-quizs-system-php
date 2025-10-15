<?php


class QuestionRepository extends BaseRepository
{
    /**
     * @var Question
     */
    protected $model;

    /**
     * @return Question
     */
    protected function getModelInstance(): Question
    {
        return new Question();
    }

    // Chỉ giữ các method custom, không duplicate CRUD vì BaseRepository đã có
    public function findByCreator(int $userId, $orderBy = 'created_at DESC', $limit = null)
    {
        return $this->model->findAll(['created_by' => $userId], $orderBy, $limit);
    }
}