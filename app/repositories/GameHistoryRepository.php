<?php

class GameHistoryRepository extends BaseRepository
{
    /**
     * @var GameHistory
     */
    protected $model;

    /**
     * @return GameHistory
     */
    protected function getModelInstance(): GameHistory
    {
        return new GameHistory();
    }

    // Chỉ giữ các method custom, không duplicate CRUD vì BaseRepository đã có
    public function findByUser(int $userId, $orderBy = 'played_at DESC', $limit = 50)
    {
        return $this->model->findAll(['user_id' => $userId], $orderBy, $limit);
    }
}