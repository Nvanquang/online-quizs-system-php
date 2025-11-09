<?php

class GameHistoryServiceImpl extends BaseService implements GameHistoryService
{
    private static $instant = null;
    private $gameHistoryRepository;

    public function __construct()
    {
        parent::__construct();
        $this->gameHistoryRepository = GameHistoryRepository::getInstance();
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
        return GameHistoryRepository::getInstance();
    }

    public function create($data)
    {
        return $this->gameHistoryRepository->create($data);
    }

    public function findByUser(int $userId)
    {
        return $this->gameHistoryRepository->findByUser($userId);
    }
}
