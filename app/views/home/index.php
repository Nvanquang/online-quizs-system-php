<?php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">
    <title>Quiz.com - Play Free Quizzes</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../public/css/homepage.css">
    <link rel="stylesheet" href="../../../public/css/layout.css">
</head>

<body>
    <?php include __DIR__ . '/../layouts/header.php'; ?>

    <!-- Category Navigation -->
    <div class="category-nav bg-white border-bottom">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div class="category-item active">
                    <img src="../../../public/images/navigation/start.svg" alt="Home" style="width:24px;height:24px;object-fit:contain;">
                    <span>Start</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/art-and-literature.svg" alt="Art & Literature" style="width:24px;height:24px;object-fit:contain;">
                    <span>Art & Literature</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/entertainment.svg" alt="Entertainment" style="width:24px;height:24px;object-fit:contain;">
                    <span>Entertainment</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/geography.svg" alt="Geography" style="width:24px;height:24px;object-fit:contain;">
                    <span>Geography</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/history.svg" alt="History" style="width:24px;height:24px;object-fit:contain;">
                    <span>History</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/languages.svg" alt="Languages" style="width:24px;height:24px;object-fit:contain;">
                    <span>Languages</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/science-and-nature.svg" alt="Science & Nature" style="width:24px;height:24px;object-fit:contain;">
                    <span>Science & Nature</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/sports.svg" alt="Sports" style="width:24px;height:24px;object-fit:contain;">
                    <span>Sports</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/trivia.svg" alt="Trivia" style="width:24px;height:24px;object-fit:contain;">
                    <span>Trivia</span>
                </div>
            </div>
        </div>
    </div>

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

        <!-- Recently Published (Enhanced Carousel) -->
        <section class="quiz-section py-5 bg-light">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <h3 class="section-title mb-0 me-2">Recently published</h3>
                        <a href="#" class="see-all-link text-decoration-none">See all (<?php echo count($quizzes); ?>)</a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-secondary btn-sm" id="quizPrevBtn" aria-label="Previous quizzes">‹</button>
                        <button class="btn btn-outline-secondary btn-sm" id="quizPlayPauseBtn" aria-pressed="false" aria-label="Pause auto slide">Pause</button>
                        <button class="btn btn-outline-secondary btn-sm" id="quizNextBtn" aria-label="Next quizzes">›</button>
                    </div>
                </div>

                <div id="quizCarouselRegion" class="position-relative" role="region" aria-roledescription="carousel" aria-label="Recently published quizzes">
                    <div id="quizCarouselViewport" class="overflow-hidden w-100">
                        <div id="quizCarouselTrack" class="d-flex" style="will-change: transform; transition: transform 400ms ease;">
                            <?php foreach ($quizzes as $quiz): ?>
                                <div class="quiz-col px-2 flex-shrink-0" style="width:auto;">
                                    <div class="quiz-card">
                                        <div class="quiz-image-wrapper">
                                            <?php
                                            $hasImage = $quiz->getImage() != null && $quiz->getImage() && file_exists(__DIR__ . "/../../../public/uploads/quizzes/{$quiz->getImage()}");
                                            $fallback = "background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);";
                                            ?>
                                            <div class="quiz-image" style="<?= $hasImage ? '' : $fallback ?>">
                                                <?php if ($hasImage): ?>
                                                    <img src="../../../public/uploads/quizzes/<?= htmlspecialchars($quiz->getImage()) ?>" alt="<?= htmlspecialchars($quiz->getTitle()) ?>" style="width:100%;height:100%;object-fit:cover;">
                                                <?php endif; ?>
                                                <form action="/game/lobby/<?= $quiz->getId() ?>" method="post" style="display:inline;">
                                                    <button type="submit" class="play-now-btn">Chơi ngay</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="quiz-info">
                                            <h5 class="quiz-title"><?= $quiz->getTitle() ?></h5>
                                            <div class="quiz-meta">
                                                <span class="quiz-rating">
                                                    <i class="fas fa-star"></i>
                                                </span>
                                                <span class="quiz-author">By <?= $quiz->getAuthor() ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div id="quizEmptyAlert" class="alert alert-warning mt-3 d-none" role="alert">
                    No quizzes available.
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
                                    $hasImage = isset($quiz['image']) && $quiz['image'] && file_exists(__DIR__ . "/../../../public/uploads/quizzes/{$quiz['image']}");
                                    $fallback = "background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);";
                                    ?>
                                    <div class="quiz-image" style="<?= $hasImage ? '' : $fallback ?>">
                                        <?php if ($hasImage): ?>
                                            <img src="../../../public/uploads/quizzes/<?= htmlspecialchars($quiz['image']) ?>" alt="<?= htmlspecialchars($quiz['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
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
                        ['title' => 'Italian Brainrot', 'rating' => 3.9, 'author' => 'Thomas', 'image' => 'xe.jpeg'],
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
                                    $hasImage = isset($quiz['image']) && $quiz['image'] && file_exists(__DIR__ . "/../../../public/uploads/quizzes/{$quiz['image']}");
                                    $fallback = "background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);";
                                    ?>
                                    <div class="quiz-image" style="<?= $hasImage ? '' : $fallback ?>">
                                        <?php if ($hasImage): ?>
                                            <img src="../../../public/uploads/quizzes/<?= htmlspecialchars($quiz['image']) ?>" alt="<?= htmlspecialchars($quiz['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
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
                                    $hasImage = isset($quiz['image']) && $quiz['image'] && file_exists(__DIR__ . "/../../../public/uploads/quizzes/{$quiz['image']}");
                                    $fallback = "background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);";
                                    ?>
                                    <div class="quiz-image" style="<?= $hasImage ? '' : $fallback ?>">
                                        <?php if ($hasImage): ?>
                                            <img src="../../../public/uploads/quizzes/<?= htmlspecialchars($quiz['image']) ?>" alt="<?= htmlspecialchars($quiz['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../../../public/js/main.js"></script>

    <?php if (isset($login_success) && $login_success): ?>
        <script>
            toastr.success(<?php echo json_encode($login_success ?? ''); ?>);
        </script>
    <?php endif; ?>

</body>

</html>