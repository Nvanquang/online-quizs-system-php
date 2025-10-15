<?php

class PlayerAnswerRepository extends BaseRepository
{
    /**
     * @var PlayerAnswer
     */
    protected $model;

    /**
     * @return PlayerAnswer
     */
    protected function getModelInstance(): PlayerAnswer
    {
        return new PlayerAnswer();
    }

    // Chỉ giữ các method custom, không duplicate CRUD vì BaseRepository đã có
    public function findBySessionAndUser(int $sessionId, int $userId)
    {
        return $this->model->findAll(['session_id' => $sessionId, 'user_id' => $userId]);
    }
}