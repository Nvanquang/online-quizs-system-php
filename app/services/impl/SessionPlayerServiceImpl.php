<?php

class SessionPlayerServiceImpl extends BaseService implements SessionPlayerService
{
    private static $instant = null;
    private $sessionPlayerRepository;

    public function __construct()
    {
        parent::__construct();
        $this->sessionPlayerRepository = SessionPlayerRepository::getInstance();
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

    public function create($data)
    {
        return $this->sessionPlayerRepository->create($data);
    }

    public function findByUserId(int $userId)
    {
        if(!$userId){
            throw new Exception("User ID is required");
        }
        if(!is_numeric($userId)){
            throw new Exception("User ID must be a number");
        }
        if($userId < 1){
            throw new Exception("User ID must be greater than 0");
        }
        return $this->sessionPlayerRepository->findByUserId($userId);
    }

    public function update($sessionPlayerId, $data)
    {
        $sessionPlayer = $this->sessionPlayerRepository->findById($sessionPlayerId);
        if (!$sessionPlayer) {
            throw new Exception("Session Player not found");
        }
        if($data['total_score'] != null) {
            $sessionPlayer->setTotalScore($data['total_score']);
        }
        if($data['rank_position'] != null) {
            $sessionPlayer->setRankPosition($data['rank_position']);
        }
        return $this->sessionPlayerRepository->update($sessionPlayerId, $data);
    }
}