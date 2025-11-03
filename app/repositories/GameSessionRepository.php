<?php


class GameSessionRepository extends BaseRepository
{
    protected $model;
    private static $instance = null;

    protected function getModelInstance(): GameSession
    {
        $this->model = new GameSession();
        return $this->model;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function findBySessionCode(string $sessionCode)
    {
        return $this->model->findOne(['session_code' => $sessionCode]);
    }
}