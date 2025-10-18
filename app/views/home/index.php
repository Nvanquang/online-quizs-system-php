<?php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz.com - Play Free Quizzes</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../public/css/homepage.css">
    <link rel="stylesheet" href="../../../public/css/layout.css">
</head>

<body>
    <?php include __DIR__ . '/../layouts/header.php'; ?>

    <!-- includes/main-content.php -->
    <main class="main-content">
        <!-- Create Quiz Section -->
        <section class="create-section py-5">
            <div class="container-fluid px-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="create-card card-manual">
                            <h2 class="create-title">Tạo Quiz</h2>
                            <p class="create-subtitle">Chơi miễn phí với<br>300 người chơi</p>
                            <a href="/quiz/create" class="btn btn-create-manual">Tạo Quiz</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="create-card card-ai">
                            <h2 class="create-title">AI</h2>
                            <p class="create-subtitle">Tạo quiz từ<br> bất kỳ chủ đề nào</p>
                            <button class="btn btn-create-ai">Tạo Quiz</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recently Published -->
        <section class="quiz-section py-5 bg-light">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <h3 class="section-title mb-0" style="margin-right:10px;">Recently published</h3>
                    <a href="#" class="see-all-link">See all (65)</a>
                </div>
                <div class="row g-4">
                    <?php
                    $recentQuizzes = [
                        ['title' => 'Guess the App Logo', 'rating' => 2.9, 'author' => 'Zeezo', 'image' => 'xe.jpeg', 'ai' => true, 'code' => '123'],
                        ['title' => 'Hellenism', 'rating' => 4.0, 'author' => 'kevino', 'image' => 'hellenism.jpg', 'ai' => false, 'code' => '123'],
                        ['title' => 'Brain Draining - Think Outside the Box', 'rating' => 0, 'author' => 'Anonymous', 'image' => 'brain.jpg', 'ai' => false, 'code' => '123'],
                        ['title' => 'Blackpink Quiz', 'rating' => 3.8, 'author' => 'SiSiTurtle27', 'image' => 'blackpink.jpg', 'ai' => true, 'code' => '123'],
                        ['title' => 'UK Sports and Leisure', 'rating' => 3.6, 'author' => 'Julie09', 'image' => 'sports.jpg', 'ai' => false, 'code' => '123'],
                        ['title' => 'Trap Soda "For The Culture" Trivia #8', 'rating' => 0, 'author' => 'SODA.online', 'image' => 'trap-soda.jpg', 'ai' => false, 'code' => '123']
                    ];

                    foreach ($recentQuizzes as $quiz): ?>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="quiz-card">
                                <div class="quiz-image-wrapper">
                                    <?php
                                        $hasImage = isset($quiz['image']) && $quiz['image'] && file_exists(__DIR__ . "/../../../public/images/{$quiz['image']}");
                                        $fallback = "background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);";
                                    ?>
                                    <div class="quiz-image" style="<?= $hasImage ? '' : $fallback ?>">
                                        <?php if ($hasImage): ?>
                                            <img src="../../../public/images/<?= htmlspecialchars($quiz['image']) ?>" alt="<?= htmlspecialchars($quiz['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
                                        <?php endif; ?>
                                        <?php
                                            $auth = Auth::getInstance();
                                            $isAdmin = $auth->isAdmin();
                                            $playUrl = ($isAdmin)
                                                ? "/game/lobby/" . htmlspecialchars($quiz['code'])
                                                : "/game/waiting/" . htmlspecialchars($quiz['code']);
                                        ?>
                                        <a href="<?= $playUrl ?>" class="play-now-btn">Chơi ngay</a>
                                        <?php if ($quiz['ai']): ?>
                                            <span class="ai-badge">AI GENERATED</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="quiz-info">
                                    <h5 class="quiz-title"><?= $quiz['title'] ?></h5>
                                    <div class="quiz-meta">
                                        <span class="quiz-rating">
                                            <?= $quiz['rating'] > 0 ? $quiz['rating'] : '-' ?>
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="quiz-author">By <?= $quiz['author'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Best Rating -->
        <section class="quiz-section py-5">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <h3 class="section-title mb-0" style="margin-right:10px;">Best rating right now</h3>
                    <a href="#" class="see-all-link">See all (23)</a>
                </div>
                <div class="row g-4">
                    <?php
                    $bestQuizzes = [
                        ['title' => 'Famous ASEAN Tourist Attractions', 'rating' => 4.6, 'author' => 'HuiLam', 'image' => 'xe.jpeg'],
                        ['title' => '🚗 More Car Slogans 🚗', 'rating' => 4.3, 'author' => 'CandyQueen', 'image' => 'xe.jpeg'],
                        ['title' => 'Recognize a city by its monument', 'rating' => 4.3, 'author' => 'Lord_Buba', 'image' => 'xe.jpeg'],
                        ['title' => 'Recognize a car by its interior', 'rating' => 4.3, 'author' => 'Lord_Buba', 'image' => 'xe.jpeg'],
                        ['title' => 'Movie Emoji Trivia', 'rating' => 4.2, 'author' => 'yaroslav', 'image' => 'xe.jpeg'],
                        ['title' => 'Guess the car by its front view', 'rating' => 4.2, 'author' => 'Lord_Buba', 'image' => 'xe.jpeg']
                    ];

                    foreach ($bestQuizzes as $quiz): ?>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="quiz-card">
                                <div class="quiz-image-wrapper">
                                    <?php
                                        $hasImage = isset($quiz['image']) && $quiz['image'] && file_exists(__DIR__ . "/../../../public/images/{$quiz['image']}");
                                        $fallback = "background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);";
                                    ?>
                                    <div class="quiz-image" style="<?= $hasImage ? '' : $fallback ?>">
                                        <?php if ($hasImage): ?>
                                            <img src="../../../public/images/<?= htmlspecialchars($quiz['image']) ?>" alt="<?= htmlspecialchars($quiz['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
                                        <?php endif; ?>
                                        <a href="#" class="play-now-btn">Chơi ngay</a>
                                        <?php if (isset($quiz['ai']) && $quiz['ai']): ?>
                                            <span class="ai-badge">AI GENERATED</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="quiz-info">
                                    <h5 class="quiz-title"><?= $quiz['title'] ?></h5>
                                    <div class="quiz-meta">
                                        <span class="quiz-rating"><?= $quiz['rating'] ?> <i class="fas fa-star"></i></span>
                                        <span class="quiz-author">By <?= $quiz['author'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Popular Right Now -->
        <section class="quiz-section py-5 bg-light">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <h3 class="section-title mb-0" style="margin-right:10px;">Popular right now</h3>
                    <a href="#" class="see-all-link">See all (55)</a>
                </div>
                <div class="row g-4">
                    <?php
                    $popularQuizzes = [
                        ['title' => 'Italian Brainrot', 'rating' => 3.9, 'author' => 'ThomasNguyen1405', 'image' => 'xe.jpeg'],
                        ['title' => 'Guess the logo', 'rating' => 4.1, 'author' => 'Indy', 'image' => 'xe.jpeg'],
                        ['title' => 'Flags of World Quiz', 'rating' => 4.0, 'author' => 'kinzaa', 'image' => 'xe.jpeg'],
                        ['title' => 'World Geography Quiz', 'rating' => 4.1, 'author' => 'haanthu', 'image' => 'xe.jpeg'],
                        ['title' => '🤓 Are You Smarter Than A 5th Grader? 🤓', 'rating' => 4.0, 'author' => 'brittanyk', 'image' => 'xe.jpeg'],
                        ['title' => 'General Knowledge!!!', 'rating' => 4.2, 'author' => 'Savithma', 'image' => 'xe.jpeg']
                    ];

                    foreach ($popularQuizzes as $quiz): ?>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="quiz-card">
                                <div class="quiz-image-wrapper">
                                    <?php
                                        $hasImage = isset($quiz['image']) && $quiz['image'] && file_exists(__DIR__ . "/../../../public/images/{$quiz['image']}");
                                        $fallback = "background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);";
                                    ?>
                                    <div class="quiz-image" style="<?= $hasImage ? '' : $fallback ?>">
                                        <?php if ($hasImage): ?>
                                            <img src="../../../public/images/<?= htmlspecialchars($quiz['image']) ?>" alt="<?= htmlspecialchars($quiz['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
                                        <?php endif; ?>
                                        <a href="#" class="play-now-btn">Chơi ngay</a>
                                        <?php if (isset($quiz['ai']) && $quiz['ai']): ?>
                                            <span class="ai-badge">AI GENERATED</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="quiz-info">
                                    <h5 class="quiz-title"><?= $quiz['title'] ?></h5>
                                    <div class="quiz-meta">
                                        <span class="quiz-rating"><?= $quiz['rating'] ?> <i class="fas fa-star"></i></span>
                                        <span class="quiz-author">By <?= $quiz['author'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Trivia Section -->
        <section class="quiz-section py-5">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <h3 class="section-title mb-0" style="margin-right:10px;">Trivia</h3>
                    <a href="#" class="see-all-link">See all (67)</a>
                </div>
                <div class="row g-4">
                    <?php
                    $triviaQuizzes = [
                        ['title' => 'Fun Quiz', 'rating' => 3.9, 'author' => 'BobbyBob', 'image' => 'xe.jpeg'],
                        ['title' => 'FAST: TOP 100 Crypto tokens', 'rating' => 3.9, 'author' => 'Taidzokai', 'hard' => true, 'image' => 'xe.jpeg'],
                        ['title' => 'FAST: Even MORE Crypto tokens', 'rating' => 1.0, 'author' => 'Taidzokai', 'hard' => true, 'image' => 'xe.jpeg'],
                        ['title' => 'Fast Food Logos', 'rating' => 4.0, 'author' => 'brittanyk', 'image' => 'xe.jpeg'],
                        ['title' => 'Guess the app logo!', 'rating' => 4.0, 'author' => 'Nias', 'image' => 'xe.jpeg'],
                        ['title' => 'Guess anime part 2(medium)', 'rating' => 4.1, 'author' => 'scypthe', 'image' => 'xe.jpeg']
                    ];

                    foreach ($triviaQuizzes as $quiz): ?>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <div class="quiz-card">
                                <div class="quiz-image-wrapper">
                                    <?php
                                        $hasImage = isset($quiz['image']) && $quiz['image'] && file_exists(__DIR__ . "/../../../public/images/{$quiz['image']}");
                                        $fallback = "background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);";
                                    ?>
                                    <div class="quiz-image" style="<?= $hasImage ? '' : $fallback ?>">
                                        <?php if ($hasImage): ?>
                                            <img src="../../../public/images/<?= htmlspecialchars($quiz['image']) ?>" alt="<?= htmlspecialchars($quiz['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
                                        <?php endif; ?>
                                        <a href="#" class="play-now-btn">Chơi ngay</a>
                                        <?php if (isset($quiz['ai']) && $quiz['ai']): ?>
                                            <span class="ai-badge">AI GENERATED</span>
                                        <?php endif; ?>
                                        <?php if (isset($quiz['hard']) && $quiz['hard']): ?>
                                            <span class="hard-badge">HARD</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="quiz-info">
                                    <h5 class="quiz-title"><?= $quiz['title'] ?></h5>
                                    <div class="quiz-meta">
                                        <span class="quiz-rating"><?= $quiz['rating'] ?> <i class="fas fa-star"></i></span>
                                        <span class="quiz-author">By <?= $quiz['author'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <?php if (isset($login_success) && $login_success): ?>
        <script>
            // Enqueue a site-wide notification (works even if notify.js hasn't loaded yet)
            window._notifyQueue = window._notifyQueue || [];
            window._notifyQueue.push({ message: <?php echo json_encode($login_success); ?>, type: 'success' });
        </script>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../../../public/js/main.js"></script>
    <script src="../../../public/js/notify.js"></script>
</body>

</html>