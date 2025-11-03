<?php

class GameHistoryRepository extends BaseRepository
{
    protected $model;
    private static $instance = null;

    protected function getModelInstance(): GameHistory
    {
        $this->model = new GameHistory();
        return $this->model;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function findByUser(int $userId, $orderBy = 'played_at DESC', $limit = 50)
    {
        return $this->model->findAll(['user_id' => $userId], $orderBy, $limit);
    }
}