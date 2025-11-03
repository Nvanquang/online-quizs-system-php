<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">
    <title>Quiz Lobby</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../../public/css/lobby.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <a href="/">
                <img src="../../../public/images/logo/quiz-multicolor.svg" alt="Quiz.com"/>
            </a>
        </div>
        <div class="header-right">
            <div class="header-item">
                PIN <strong><?php echo $gameSession->getPinCode(); ?></strong>
            </div>
            <div class="header-item">
                <span class="user-icon">👤</span>
                <strong>0</strong>
            </div>
            <button class="fullscreen-btn" onclick="toggleFullscreen()">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Left Panel -->
        <div class="left-panel">
            <div class="top-row">
                <div class="join-box">
                    <div class="join-label">Join at:</div>
                    <div class="logo-img">
                        <img src="../../../public/images/logo/quiz-vertical-multicolor.svg" alt="Quiz.com" />
                    </div>
                </div>
                <div class="pin-box">
                    <div class="pin-label">PIN code:</div>
                    <div class="pin-number" id="pinNumber"><?php echo $gameSession->getPinCode(); ?></div>
                    <div class="pin-actions">
                        <div class="pin-action" onclick="copyPin()">
                            <i class="fas fa-link"></i>
                            <span>Copy</span>
                        </div>
                        <div class="pin-action" onclick="togglePin()">
                            <i class="fas fa-eye-slash"></i>
                            <span id="hideText">Hide</span>
                        </div>
                    </div>
                    <div class="hidden-info" id="hiddenInfo" style="display: none;">
                        <div class="hidden-title">The code is hidden</div>
                        <div class="hidden-sub"><i class="fas fa-lock"></i> Prevent more players from joining</div>
                        <div class="hidden-show" onclick="togglePin()"><i class="fas fa-eye"></i> Show</div>
                    </div>
                </div>
                <div class="qr-box">
                    <div class="qr-code">
                        <div class="qr-pattern"></div>
                    </div>
                    <div class="qr-placeholder" style="display: none;"></div>
                </div>
            </div>

            <!-- <div class="panel-separator"></div> -->

            <!-- Waiting Section -->
            <div class="waiting-section">
                <div class="waiting-text">Waiting for players<span id="waitingDots" aria-hidden="true"></span></div>
                <div class="join-device">
                    <i class="fas fa-user-circle"></i>
                    <span>Join on this device</span>
                </div>
                <button class="start-btn" onclick="startGame()">Start game</button>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="right-panel">
            <!-- Game Card -->
            <div class="game-card">
                <div class="game-image">
                    <div class="game-controls">
                        <button class="control-btn"><i class="fas fa-times"></i></button>
                        <button class="control-btn active"><i class="fas fa-check"></i></button>
                        <button class="control-btn"><i class="fas fa-lightbulb"></i></button>
                        <button class="control-btn"><i class="fas fa-ellipsis-v"></i></button>
                    </div>  
                    <img src="<?php echo htmlspecialchars('../../../public/uploads/quizzes/' . $quiz->getImage()); ?>" alt="<?php echo htmlspecialchars($quiz->getTitle()); ?>" style="width:100%; height:100%; object-fit: cover; border-radius: 12px;" />
                </div>
                <div class="game-title"><?php echo $quiz->getTitle(); ?></div>
                <div class="game-info">
                    <div class="info-item"><?php echo $quiz->getTotalQuestions(); ?> slides</div>
                    <div class="info-item">
                        <i class="fas fa-search"></i> Preview
                    </div>
                    <div class="info-item">
                        <i class="fas fa-globe"></i> English
                    </div>
                </div>
            </div>

            <!-- Sound Section -->
            <div class="settings-section">
                <div class="section-title">Sound</div>
                <div class="slider-group">
                    <label class="slider-label">Music</label>
                    <div class="slider-container">
                        <div class="slider-bar" style="width: 75%;"></div>
                        <div class="slider-handle" style="left: 75%;"></div>
                    </div>
                </div>
                <div class="slider-group">
                    <label class="slider-label">YouTube</label>
                    <div class="slider-container">
                        <div class="slider-bar" style="width: 75%;"></div>
                        <div class="slider-handle" style="left: 75%;"></div>
                    </div>
                </div>
                <div class="slider-group">
                    <label class="slider-label">Voice</label>
                    <div class="slider-container">
                        <div class="slider-bar" style="width: 90%;"></div>
                        <div class="slider-handle" style="left: 90%;"></div>
                    </div>
                </div>
                <div class="slider-group">
                    <label class="slider-label">Effects</label>
                    <div class="slider-container">
                        <div class="slider-bar" style="width: 75%;"></div>
                        <div class="slider-handle" style="left: 75%;"></div>
                    </div>
                </div>
            </div>

            <!-- Gameplay Section -->
            <div class="settings-section">
                <div class="section-title">Gameplay</div>
                
                <label class="checkbox-option">
                    <input type="checkbox" id="teamMode">
                    <div class="checkbox-box">
                        <i class="fas fa-check" style="font-size: 11px; color: white; display: none;"></i>
                    </div>
                    <div class="checkbox-text">
                        <div class="option-name">
                            Team mode
                            <span class="new-badge">NEW!</span>
                        </div>
                        <div class="option-description">All players compete in teams</div>
                    </div>
                </label>

                <label class="checkbox-option">
                    <input type="checkbox" id="hideLeaderboard">
                    <div class="checkbox-box">
                        <i class="fas fa-check" style="font-size: 11px; color: white; display: none;"></i>
                    </div>
                    <div class="checkbox-text">
                        <div class="option-name">Hide leaderboard</div>
                        <div class="option-description">Hide during game</div>
                    </div>
                </label>

                <label class="checkbox-option">
                    <input type="checkbox" id="hideFlags">
                    <div class="checkbox-box">
                        <i class="fas fa-check" style="font-size: 11px; color: white; display: none;"></i>
                    </div>
                    <div class="checkbox-text">
                        <div class="option-name">Hide country flags</div>
                        <div class="option-description">Don't show player locations</div>
                    </div>
                </label>
            </div>
        </div>
    </div>

    <!-- Start Game Modal -->
    <div id="startModal" class="start-modal-overlay" style="display: none;">
        <div class="start-modal-content">
            <!-- Top Bar (576x88px) -->
            <div class="start-modal-top">
                <button class="modal-btn-clear btn-clear-avatar">Clear</button>
                <div class="modal-pin-section">
                    <span class="modal-pin-label">PIN code:</span>
                    <span class="modal-pin-number"><?php echo htmlspecialchars($gameSession->getPinCode()); ?></span>
                </div>
                <button class="btn-shuffle-avatar modal-btn-shuffle">Shuffle</button>
            </div>

            <!-- Input Section -->
            <div class="modal-input-section">
                <div class="modal-input-wrapper">
                    <input type="text" class="modal-input" placeholder="Enter player name" value="Mrs Police" id="nickname">
                    <button class="modal-input-clear" id="btn-clear-nickname">&times;</button>
                </div>
            </div>

            <!-- Large Circle -->
            <div class="modal-circle">
                <div class="modal-circle-inner" id="avatarCircle"></div>
            </div>

            <!-- Bottom Bar -->
            <div class="modal-bottom-bar">
                <button class="btn-shuffle-avatar modal-bottom-btn">
                    <span><i class="bi bi-shuffle"></i></span>
                </button>
                <button class="modal-bottom-btn">
                    <span><i class="bi bi-zoom-in"></i></span>
                </button>
                <button class="modal-bottom-btn">
                    <span><i class="bi bi-zoom-out"></i></span>
                </button>
                <button class="modal-bottom-btn">
                    <span><i class="bi bi-trash"></i></span>
                </button>
                <button class="modal-bottom-btn">
                    <span><i class="bi bi-arrow-90deg-left"></i></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden form for join submission -->
    <form id="joinForm" method="POST" action="/game/join/<?php echo urlencode($sessionCode); ?>" style="display:none;">
        <?php echo CSRFMiddleware::getTokenField(); ?>
        <input type="hidden" name="nickname" id="joinNickname" />
        <input type="hidden" name="avatar" id="joinAvatar" />
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php
        $avatarDirPath = realpath(__DIR__ . '/../../../public/images/avatar-plays');
        $files = [];
        if ($avatarDirPath && is_dir($avatarDirPath)) {
            foreach (scandir($avatarDirPath) as $f) {
                if ($f === '.' || $f === '..') continue;
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (in_array($ext, ['png','jpg'])) $files[] = $f;
            }
        }
        $authUserId = Auth::getInstance()->id();
        $sessionCode = isset($code) ? $code : (method_exists($gameSession, 'getSessionCode') ? $gameSession->getSessionCode() : '');
    ?>
    <script>
        window.AVATAR_BASE_URL = '../../../public/images/avatar-plays/';
        window.AVATAR_LIST = <?php echo json_encode(array_values($files)); ?>;
    </script>
    <script src="../../../public/js/game.js"></script>
</body>
</html>