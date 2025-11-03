<?php

class GameHistoryServiceImpl extends BaseService implements GameHistoryService
{
    private $gameHistoryRepository;

    public function __construct()
    {
        parent::__construct();
        $this->gameHistoryRepository = GameHistoryRepository::getInstance();
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
