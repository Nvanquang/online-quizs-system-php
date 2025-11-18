<?php

class GameHistoryServiceImpl implements GameHistoryService
{
    private static $instant = null;
    private $gameHistoryRepository;
    private $userRepository;
    private $gameSessionRepository;
    private $quizRepository;

    public function __construct()
    {
        $this->gameHistoryRepository = GameHistoryRepository::getInstance();
        $this->userRepository = UserRepository::getInstance();
        $this->gameSessionRepository = GameSessionRepository::getInstance();
        $this->quizRepository = QuizRepository::getInstance();
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

    public function create($gameSesstion)
    {
        if (!$this->userRepository->exists(['id' => $gameSesstion['user_id']])) {
            throw new Exception("Người dùng không tồn tại!");
        }
        if (!$this->gameSessionRepository->exists(['id' => $gameSesstion['session_id']])) {
            throw new Exception("Phiên chơi không tồn tại");
        }
        if (!$this->quizRepository->exists(['id' => $gameSesstion['quiz_id']])) {
            throw new Exception("Trò chơi không tồn tại");
        }
        return $this->gameHistoryRepository->create($gameSesstion);
    }

    public function findByUser(int $userId)
    {
        if (!$this->userRepository->exists(['id' => $userId])) {
            throw new Exception("Người dùng không tồn tại!");
        }
        return $this->gameHistoryRepository->findByUser($userId);
    }
}
