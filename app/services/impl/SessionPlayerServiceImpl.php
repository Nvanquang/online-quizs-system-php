<?php

class SessionPlayerServiceImpl implements SessionPlayerService
{
    private static $instant = null;
    private $sessionPlayerRepository;
    private $userRepository;
    private $gameSessionRepository;

    public function __construct()
    {
        $this->sessionPlayerRepository = SessionPlayerRepository::getInstance();
        $this->userRepository = UserRepository::getInstance();
        $this->gameSessionRepository = GameSessionRepository::getInstance();
    }

    public static function getInstance()
    {
        if (self::$instant === null) {
            self::$instant = new self();
        }
        return self::$instant;
    }

    protected function getRepositoryInstance()
    {
        return SessionPlayerRepository::getInstance();
    }

    public function create($sessionPlayer)
    {
        if (!$this->userRepository->exists(['id' => $sessionPlayer['user_id']])) {
            throw new Exception("Người dùng không tồn tại!");
        }
        if (!$this->gameSessionRepository->exists(['id' => $sessionPlayer['session_id']])) {
            throw new Exception("Phiên chơi không tồn tại");
        }
        return $this->sessionPlayerRepository->create($sessionPlayer);
    }

    public function findByUserId(int $userId)
    {
        $sessionPlayer = $this->sessionPlayerRepository->findByUserId($userId);
        if (!$sessionPlayer) {
            throw new Exception("Phiên người chơi không tồn tại!");
        }
        return $sessionPlayer;
    }

    public function update($sessionPlayerId, $data)
    {
        $sessionPlayer = $this->sessionPlayerRepository->findById($sessionPlayerId);

        if (!$sessionPlayer) {
            throw new Exception("Phiên người chơi không tồn tại!");
        }

        if ($data['total_score'] != null) {
            $sessionPlayer->setTotalScore($data['total_score']);
        }
        if ($data['rank_position'] != null) {
            $sessionPlayer->setRankPosition($data['rank_position']);
        }
        return $this->sessionPlayerRepository->update($sessionPlayerId, $data);
    }
}
