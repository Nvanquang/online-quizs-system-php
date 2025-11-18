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
        try {
            $data = [
                'quiz_id' => $quizId
            ];

            $validated = $this->validate($data, [
                'quiz_id' => 'required|integer'
            ]);

            if ($validated) {
                if ($this->auth->user() != null) {
                    $hostId = $this->auth->user()->getId();
                } else {
                    $hostId = null;
                }

                if (!$hostId) {
                    $this->redirectWithError('/', 'Bạn cần đăng nhập');
                }

                $gameSession = $this->gameSessionService->createSession(
                    $hostId,
                    (int)$validated['quiz_id']
                );

                $sessionCode = $gameSession->getSessionCode();
                return $this->redirect('/game/lobby/' . urlencode($sessionCode));
            }
        } catch (Exception $e) {
            $this->redirectWithError('/', $e->getMessage());
        }
    }

    public function lobby($sessionCode)
    {
        try {
            $sessionPlayer = new SessionPlayer();
            $gameSession = $this->gameSessionService->findBySessionCode($sessionCode);
            if (!$gameSession) {
                $this->redirectWithError('/', 'Phiên trò chơi không tồn tại!');
            }

            $quiz = $this->quizService->findById($gameSession->getQuizId());
            echo $this->renderPartial('game/lobby', ['sessionCode' => $sessionCode, 'gameSession' => $gameSession, 'sessionPlayer' => $sessionPlayer, 'quiz' => $quiz]);
        } catch (Exception $e) {
            $this->redirectWithError('/', $e->getMessage());
        }
    }

    public function waiting($sessionCode)
    {
        echo $this->renderPartial('game/waiting', ['sessionCode' => $sessionCode]);
    }

    public function doJoin($sessionCode)
    {
        try {
            $gameSession = $this->gameSessionService->findBySessionCode($sessionCode);

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
                $_SESSION['errors'] = 'Nickname là bắt buộc!';
                $this->redirectBack();
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
            $_SESSION['errors'] = $e->getMessage();
            $this->redirectBack();
        }
    }

    public function play($sessionCode)
    {
        try {
            $gameSession = $this->gameSessionService->findBySessionCode($sessionCode);

            $quiz = $this->quizService->findById($gameSession->getQuizId());

            $sessionPlayer = $this->sessionPlayerService->findByUserId($this->auth->id());
            $questions = $this->questionService->findByQuiz($gameSession->getQuizId());
            echo $this->renderPartial('game/play', ['sessionCode' => $sessionCode, 'gameSession' => $gameSession, 'quiz' => $quiz, 'questions' => $questions, 'sessionPlayer' => $sessionPlayer]);
        } catch (Exception $e) {
            $this->redirectWithError('/game/waiting', $e->getMessage());
        }
    }

    public function endGame($sessionCode)
    {
        try {
            $validated = $this->validate($_POST, [
                'total_questions' => 'required|integer',
                'correct_answers' => 'required|integer',
                'total_score' => 'required|integer',
                'id' => 'required|integer',
            ]);

            if ($validated) {
                $totalQuestions = (int)$validated['total_questions'];
                $correctAnswers = (int)$validated['corect_answers'];
                $totalScore = (int)$validated['total_score'];
                $sessionPlayerId = (int)$validated['id'];

                $gameSession = $this->gameSessionService->findBySessionCode($sessionCode);

                // update game session
                $this->gameSessionService->update($sessionCode, [
                    'status' => 'finished',
                    'ended_at' => date('Y-m-d H:i:s'),
                    'actual_mode' => null,
                    'started_at' => $gameSession->getStartedAt(),
                    'current_question' => $gameSession->getCurrentQuestion(),
                    'total_players' => $gameSession->getTotalPlayers(),
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
            }
        } catch (Exception $e) {
            $this->redirectWithError('/', $e->getMessage());
        }
    }
}
