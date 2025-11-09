<?php

class GameController extends Controller
{
    private $gameSessionService;
    private $sessionPlayerService;
    private $auth;
    private $quizService;
    private $questionService;
    private $gameHistoryService;

    public function __construct()
    {
        parent::__construct();
        $this->gameSessionService = GameSessionServiceImpl::getInstance();
        $this->sessionPlayerService = SessionPlayerServiceImpl::getInstance();
        $this->auth = Auth::getInstance();
        $this->quizService = QuizServiceImpl::getInstance();
        $this->questionService = QuestionServiceImpl::getInstance();
        $this->gameHistoryService = GameHistoryServiceImpl::getInstance();
    }

    public function startLobby($quizId)
    {
        $hostId = $this->auth->user()->getId();
        if (!$hostId) {
            throw new Exception("Host ID is required");
        }
        $gameSession = $this->gameSessionService->createSession($hostId, (int)$quizId);
        $sessionCode = $gameSession->getSessionCode();
        return $this->redirect('/game/lobby/' . urlencode($sessionCode));
    }

    public function lobby($sessionCode)
    {
        $sessionPlayer = new SessionPlayer();
        $gameSession = $this->gameSessionService->findBySessionCode($sessionCode);
        if (!$gameSession) {
            throw new Exception('Session not found');
        }

        $quiz = $this->quizService->findById($gameSession->getQuizId());
        echo $this->renderPartial('game/lobby', ['sessionCode' => $sessionCode, 'gameSession' => $gameSession, 'sessionPlayer' => $sessionPlayer, 'quiz' => $quiz]);
    }

    public function waiting($sessionCode)
    {
        echo $this->renderPartial('game/waiting', ['sessionCode' => $sessionCode]);
    }

    public function doJoin($sessionCode)
    {
        try {
            $gameSession = $this->gameSessionService->findBySessionCode($sessionCode);
            if (!$gameSession) {
                http_response_code(404);
                echo json_encode(['error' => 'Session not found']);
                return;
            }

            // update game session
            $actualMode = null;
            if ($gameSession->getTotalPlayers() > 0) {
                $actualMode = 'multiplayer';
            } else {
                $actualMode = 'solo';
            }
            $this->gameSessionService->update($sessionCode, [
                'actual_mode' => $actualMode,
                'status' => 'in_progress',
                'started_at' => date('Y-m-d H:i:s'),
                'ended_at' => null,
                'current_question' => 0,
                'total_players' => $gameSession->getTotalPlayers() + 1,
            ]);

            // create session player
            $userId = $this->auth->id();
            $nickname = $_POST['nickname'] ?? null;
            $avatar = $_POST['avatar'] ?? null;

            if (!$nickname) {
                http_response_code(422);
                echo json_encode(['error' => 'Nickname is required']);
                return;
            }

            $spData = [
                'session_id' => (int)$gameSession->getId(),
                'user_id' => $userId ? (int)$userId : null,
                'nickname' => $nickname,
                'avatar' => $avatar,
                'total_score' => 0,
                'rank_position' => null,
                'is_ready' => 1,
                'joined_at' => date('Y-m-d H:i:s'),
            ];

            $this->sessionPlayerService->create($spData);
            $this->redirect('/game/play/' . urlencode($sessionCode));
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }
    }

    public function play($sessionCode)
    {
        $gameSession = $this->gameSessionService->findBySessionCode($sessionCode);
        if ($gameSession === null) {
            throw new Exception('Session not found');
        }
        $quiz = $this->quizService->findById($gameSession->getQuizId());
        if ($quiz === null) {
            throw new Exception('Quiz not found');
        }
        $sessionPlayer = $this->sessionPlayerService->findByUserId($this->auth->id());
        $questions = $this->questionService->findByQuiz($gameSession->getQuizId());
        echo $this->renderPartial('game/play', ['sessionCode' => $sessionCode, 'gameSession' => $gameSession, 'quiz' => $quiz, 'questions' => $questions, 'sessionPlayer' => $sessionPlayer]);
    }

    public function endGame($sessionCode)
    {
        header('Content-Type: application/json');

        try {
            // Parse JSON body
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);

            if (!is_array($data)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid JSON body']);
                return;
            }

            // Validate session code consistency
            $bodySessionCode = isset($data['sessionCode']) ? (string)$data['sessionCode'] : null;
            if (!$bodySessionCode || $bodySessionCode !== $sessionCode) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'Session code mismatch']);
                return;
            }

            // Validate totals
            $totalQuestions = isset($data['totalQuestions']) ? (int)$data['totalQuestions'] : 0;
            $correctAnswers = isset($data['correctAnswers']) ? (int)$data['correctAnswers'] : 0;
            $totalScore     = isset($data['totalScore']) ? (int)$data['totalScore'] : 0;
            $sessionPlayerId = isset($data['sessionPlayerId']) ? (int)$data['sessionPlayerId'] : null;

            if ($totalQuestions < 0 || $correctAnswers < 0 || $correctAnswers > $totalQuestions || $totalScore < 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => 'Invalid totals']);
                return;
            }

            // Ensure session exists
            $gameSession = $this->gameSessionService->findBySessionCode($sessionCode);
            if (!$gameSession) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Session not found']);
                return;
            }

            // update game session
            $this->gameSessionService->update($sessionCode, [
                'status' => 'finished',
                'ended_at' => date('Y-m-d H:i:s'),
            ]);

            // update session player
            $this->sessionPlayerService->update($sessionPlayerId, [
                'total_score' => $totalScore,
                'rank_position' => 1,
            ]);

            // create game history
            $gameHistoryData = [
                'user_id' => $this->auth->id(),
                'session_id' => $gameSession->getId(),
                'quiz_id' => $gameSession->getQuizId(),
                'final_score' => $totalScore,
                'final_rank' => 1,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'played_at' => $gameSession->getStartedAt(),
            ];
            $this->gameHistoryService->create($gameHistoryData);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
