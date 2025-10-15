<?php


class GameSessionRepository extends BaseRepository
{
    /**
     * @var GameSession
     */
    protected $model;

    /**
     * @return GameSession
     */
    protected function getModelInstance(): GameSession
    {
        return new GameSession();
    }

    // Chỉ giữ các method custom, không duplicate CRUD vì BaseRepository đã có
    public function findBySessionCode(string $sessionCode)
    {
        return $this->model->findOne(['session_code' => $sessionCode]);
    }
}