<?php

class SessionPlayerRepository extends BaseRepository
{
    /**
     * @var SessionPlayer
     */
    protected $model;

    /**
     * @return SessionPlayer
     */
    protected function getModelInstance(): SessionPlayer
    {
        return new SessionPlayer();
    }

    // Chỉ giữ các method custom, không duplicate CRUD vì BaseRepository đã có
    public function findBySession(int $sessionId, $orderBy = 'total_score DESC', $limit = null)
    {
        return $this->model->findAll(['session_id' => $sessionId], $orderBy, $limit);
    }
}