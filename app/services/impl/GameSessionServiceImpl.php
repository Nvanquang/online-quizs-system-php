<?php

class GameSessionServiceImpl implements GameSessionService
{
    private static $instant = null;
    private $gameSessionRepository;
    private $codeGenerator;
    private $userRepository;
    private $quizRepository;

    public function __construct()
    {
        $this->gameSessionRepository = GameSessionRepository::getInstance();
        $this->codeGenerator = CodeGenerator::getInstance();
        $this->userRepository = UserRepository::getInstance();
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
        return GameSessionRepository::getInstance();
    }

    public function createSession($hostId, $quizId, $actualMode = null)
    {
        if (!$this->userRepository->exists(['id' => $hostId])) {
            throw new Exception("Chủ phòng không tồn tại!");
        }
        if(!$this->quizRepository->exists(['id' => $quizId])) {
            throw new Exception("Trò chơi không tồn tại!");
        }
        if ($this->gameSessionRepository->exists(
            ['id' => $quizId, 
                        'host_id' => (int)$hostId,
                        'quiz_id' => (int)$quizId,
                        'status' => 'waiting'])) {
            // Nếu đã có session đang chờ cho host + quiz này thì dùng lại (idempotent)
            $existing = $this->gameSessionRepository->findOneBy([
                'host_id' => (int)$hostId,
                'quiz_id' => (int)$quizId,
                'status' => 'waiting',
            ]);
            if ($existing) {
                return $existing;
            }
        }

        // Tạo mã session và pin
        $sessionCode = $this->codeGenerator->generateUUID();
        $pinCode = $this->codeGenerator->generatePIN();

        $data = [
            'quiz_id' => (int)$quizId,
            'host_id' => (int)$hostId,
            'session_code' => $sessionCode,
            'pin_code' => $pinCode,
            'status' => 'waiting',
            'current_question' => 0,
            'total_players' => 0,
            'actual_mode' => $actualMode,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->gameSessionRepository->create($data);
    }

    public function findBySessionCode($sessionCode)
    {
        $gameSession = $this->gameSessionRepository->findBySessionCode($sessionCode);
        if(!$gameSession) {
            throw new Exception("Phiên chơi không tồn tại");
        }
        return $gameSession;
    }

    public function update($sessionCode, $data)
    {
        $session = $this->gameSessionRepository->findBySessionCode($sessionCode);
        if (!$session) {
            throw new Exception("Session not found");
        }

        if($data['actual_mode'] != null) {
            $session->setActualMode($data['actual_mode']);
        }
        if($data['status'] != 'waiting') {
            $session->setStatus($data['status']);
        }
        if($data['started_at'] != null) {
            $session->setStartedAt($data['started_at']);
        }
        if($data['ended_at'] != null) {
            $session->setEndedAt($data['ended_at']);
        }
        if($data['current_question'] != 0) {
            $session->setCurrentQuestion($data['current_question']);
        }
        if($data['total_players'] != 0) {
            $session->setTotalPlayers($data['total_players']);
        }
        return $this->gameSessionRepository->update((int)$session->getId(), $session->toArray());
    }
}
