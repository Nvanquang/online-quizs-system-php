<?php

class PlayerAnswerRepository extends BaseRepository
{
    protected $model;
    private static $instance = null;
    
    protected function getModelInstance(): PlayerAnswer
    {
        $this->model = new PlayerAnswer();
        return $this->model;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function findBySessionAndUser(int $sessionId, int $userId)
    {
        return $this->model->findAll(['session_id' => $sessionId, 'user_id' => $userId]);
    }
}