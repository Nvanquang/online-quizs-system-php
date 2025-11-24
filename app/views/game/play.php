<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz.com</title>
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/play.css">
</head>

<body>
    <!-- Header -->
    <header class="quiz-header">
        <div class="header-content">
            <div class="header-left">
                <div class="logo-quiz">
                    <a href="/">
                        <img src="../../../public/images/logo/quiz-multicolor.svg" alt="Quiz.com" />
                    </a>
                </div>
                <span class="header-divider">|</span>
                <span class="header-item pin-code">PIN <?php echo $gameSession->getPinCode(); ?></span>
                <span class="header-item quiz-name"><?php echo $quiz->getTitle(); ?></span>
            </div>
            <div class="header-right">
                <span class="header-item" id="player-count-info">
                    <i class="fas fa-user-circle user-icon"></i> 0 (1)
                </span>
                <span class="slide-info" id="slide-info">Slide 1/<?php echo $quiz->getTotalQuestions(); ?></span>
                <button class="control-btn" id="pause-btn" title="Pause">
                    <i class="fas fa-pause"></i>
                </button>
                <button class="control-btn" id="next-btn" title="Next">
                    <i class="fas fa-step-forward"></i>
                </button>
                <button class="control-btn" title="Settings">
                    <i class="fas fa-cog"></i>
                </button>
                <button class="control-btn" id="fullscreen-btn" title="Fullscreen">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Container - Quiz Play -->
    <div class="quiz-container" id="quiz-play-container">
        <!-- Left Panel: Question & Answers -->
        <div class="left-panel">
            <!-- Question -->
            <div class="question-box">
                <h2 id="question-text" class="question-text"></h2>
            </div>

            <!-- Answers -->
            <div class="answers-container">
                <button class="answer-btn answer-green" data-answer="0">
                    <span class="answer-text"></span>
                    <span class="answer-check">
                        <i class="fas fa-check"></i>
                    </span>
                </button>
                <button class="answer-btn answer-yellow" data-answer="1">
                    <span class="answer-text"></span>
                    <span class="answer-check">
                        <i class="fas fa-check"></i>
                    </span>
                </button>
                <button class="answer-btn answer-orange" data-answer="2">
                    <span class="answer-text"></span>
                    <span class="answer-check">
                        <i class="fas fa-check"></i>
                    </span>
                </button>
                <button class="answer-btn answer-pink" data-answer="3">
                    <span class="answer-text"></span>
                    <span class="answer-check">
                        <i class="fas fa-check"></i>
                    </span>
                </button>
            </div>

            <!-- Score Bar -->
            <div class="score-container">
                <div class="score-bar-wrapper">
                    <div class="score-bar" id="score-bar"></div>
                </div>
                <span class="score-value" id="score-value">1000</span>
            </div>
        </div>

        <!-- Right Panel: Image -->
        <div class="right-panel">
            <div class="image-container">
                <img id="question-image" src="<?php echo '../../../public/uploads/questions/' . $questions[0]->image_url; ?>" alt="Question image" class="question-image">
                <div id="explanation-box" class="explanation-box" style="display:none;"></div>
            </div>
        </div>
    </div>

    <!-- Results Container (Hidden initially) -->
    <div class="results-container" id="results-container" style="display: none;">
        <div class="results-content">
            <!-- Player Badge -->
            <div class="player-badge">
                <div class="player-avatar-wrapper">
                    <img id="result-avatar"
                        src="<?php echo $sessionPlayer->getAvatar(); ?>"
                        alt="<?php echo htmlspecialchars($sessionPlayer->getNickname()); ?>"
                        class="player-avatar">
                    <div class="rank-badge">
                        <span class="rank-number">1</span>
                        <span class="rank-label">HOST</span>
                    </div>
                </div>
                <h2 class="player-name" id="result-name"><?php echo htmlspecialchars($sessionPlayer->getNickname()); ?></h2>
                <div class="player-score" id="result-score">0</div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn-action btn-report" id="view-report-btn">
                    <i class="fas fa-chart-bar"></i>
                    View report
                </button>
                <button class="btn-action btn-done" id="done-btn">
                    <i class="fas fa-check"></i>
                    Done
                </button>
            </div>
        </div>

        <!-- Decorative Background Elements -->
        <div class="bg-decoration">
            <div class="decoration-circle circle-1"></div>
            <div class="decoration-circle circle-2"></div>
            <div class="decoration-circle circle-3"></div>
        </div>
    </div>

    <!-- Rating Modal -->
    <div class="rating-modal-overlay" id="rating-modal-overlay" style="display: none;">
        <div class="rating-modal">
            <h2 class="rating-title">How many stars do you give these questions?</h2>

            <div class="rating-preview">
                <div class="quiz-thumbnail">
                    <div class="thumbnail-placeholder">
                        <?php if ($quiz->getImage() && file_exists(__DIR__ . "/../../../public/uploads/quizzes/" . $quiz->getImage())): ?>
                            <img src="../../../public/uploads/quizzes/<?= htmlspecialchars($quiz->getImage()) ?>"
                                alt="<?= htmlspecialchars($quiz->getTitle()) ?>"
                                style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
                        <?php else: ?>
                            <i class="fas fa-question-circle"></i>
                        <?php endif; ?>
                    </div>
                </div>
                <h3 class="quiz-title-rating" id="quiz-title-rating"></h3>
            </div>

            <div class="stars-container" id="stars-container">
                <button class="star-btn" data-rating="1">
                    <i class="far fa-star"></i>
                </button>
                <button class="star-btn" data-rating="2">
                    <i class="far fa-star"></i>
                </button>
                <button class="star-btn" data-rating="3">
                    <i class="far fa-star"></i>
                </button>
                <button class="star-btn" data-rating="4">
                    <i class="far fa-star"></i>
                </button>
                <button class="star-btn" data-rating="5">
                    <i class="far fa-star"></i>
                </button>
            </div>

            <button class="rating-cancel-btn" id="rating-cancel-btn">Cancel</button>
        </div>
    </div>

    <!-- Report Container (Hidden initially) -->
    <div class="report-container" id="report-container" style="display: none;">
        <div class="report-content">
            <!-- Report Header -->
            <div class="report-header">
                <div class="report-table-wrapper">
                    <table class="report-table">
                        <thead>
                            <tr id="report-header-row">
                                <th class="col-player">Player name</th>
                                <th class="col-score">Score</th>
                                <th class="col-accuracy">Accuracy</th>
                                <!-- Question columns will be appended here dynamically as separate <th> elements -->
                            </tr>
                        </thead>
                        <tbody id="report-table-body">
                            <!-- Player row will be added dynamically -->
                        </tbody>
                    </table>
                </div>

                <!-- Action Links -->
                <div class="report-actions">
                    <a href="#" class="report-action-link" id="download-csv">
                        <i class="fas fa-download"></i> Download CSV
                    </a>
                    <a href="#" class="report-action-link" id="print-report">
                        <i class="fas fa-print"></i> Print
                    </a>
                </div>
            </div>

            <!-- View Leaderboard Button -->
            <div class="report-footer">
                <button class="btn-leaderboard" id="view-leaderboard-btn">
                    <i class="fas fa-trophy"></i>
                    View leaderboard
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.QUIZ_QUESTIONS = <?php
                                $jsQuestions = [];
                                if (!empty($questions)) {
                                    foreach ($questions as $q) {
                                        $jsQuestions[] = [
                                            'id' => isset($q->question_id) ? (int)$q->question_id : null,
                                            'text' => isset($q->content) ? $q->content : '',
                                            'image' => isset($q->image_url) && $q->image_url !== '' ? ('../../../public/uploads/questions/' . $q->image_url) : '',
                                            'explanation' => isset($q->explanation) ? $q->explanation : '',
                                            'answers' => [
                                                isset($q->answer_a) ? $q->answer_a : '',
                                                isset($q->answer_b) ? $q->answer_b : '',
                                                isset($q->answer_c) ? $q->answer_c : '',
                                                isset($q->answer_d) ? $q->answer_d : '',
                                            ],
                                            'correctAnswer' => isset($q->correct_answer) ? $q->correct_answer : null,
                                            'timeLimit' => (method_exists($q, 'getTimeLimit') && is_numeric($q->getTimeLimit()))
                                                ? (int)$q->getTimeLimit()
                                                : (isset($q->time_limit) && is_numeric($q->time_limit) ? (int)$q->time_limit : null),
                                            'order' => isset($q->order_number) ? (int)$q->order_number : null,
                                        ];
                                    }
                                }
                                echo json_encode($jsQuestions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                                ?>;
        window.SESSION_CODE = <?php echo json_encode($gameSession->getSessionCode()); ?>;
        window.CSRF_TOKEN = <?php echo json_encode(CSRFMiddleware::getToken()); ?>;
        window.SESSION_PLAYER_ID = <?php echo isset($sessionPlayer) && method_exists($sessionPlayer, 'getId') ? (int)$sessionPlayer->getId() : 'null'; ?>;
    </script>
    <script src="../../../public/js/play.js"></script>
</body>

</html>