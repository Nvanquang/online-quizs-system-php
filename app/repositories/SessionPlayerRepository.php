<?php

class SessionPlayerRepository extends BaseRepository
{
    protected $model;
    private static $instance = null;
    
    protected function getModelInstance(): SessionPlayer
    {
        $this->model = new SessionPlayer();
        return $this->model;
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function findBySession(int $sessionId, $orderBy = 'total_score DESC', $limit = null): array
    {
        return $this->model->findAll(['session_id' => $sessionId], $orderBy, $limit);
    }

    public function findByUserId(int $userId): ?SessionPlayer
    {
        return $this->model->findOne(['user_id' => $userId]);
    }
    
    public function findById($id): ?SessionPlayer
    {
        /** @var SessionPlayer|null */
        return parent::findById($id);
    }
    
}