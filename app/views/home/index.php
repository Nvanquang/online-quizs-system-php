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
                    <span>Bắt đầu</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/art-and-literature.svg" alt="Art & Literature" style="width:24px;height:24px;object-fit:contain;">
                    <span>Nghệ thuật & Văn học</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/entertainment.svg" alt="Entertainment" style="width:24px;height:24px;object-fit:contain;">
                    <span>Giải trí</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/geography.svg" alt="Geography" style="width:24px;height:24px;object-fit:contain;">
                    <span>Địa lý</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/history.svg" alt="History" style="width:24px;height:24px;object-fit:contain;">
                    <span>Lịch sử</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/languages.svg" alt="Languages" style="width:24px;height:24px;object-fit:contain;">
                    <span>Ngôn ngữ</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/science-and-nature.svg" alt="Science & Nature" style="width:24px;height:24px;object-fit:contain;">
                    <span>Khoa học & Tự nhiên</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/sports.svg" alt="Sports" style="width:24px;height:24px;object-fit:contain;">
                    <span>Thể thao</span>
                </div>
                <div class="category-item">
                    <img src="../../../public/images/navigation/trivia.svg" alt="Trivia" style="width:24px;height:24px;object-fit:contain;">
                    <span>Thú vị</span>
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

        <!-- Recently Published Section -->
<section class="quiz-section py-5 bg-light">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <h3 class="section-title mb-0 me-2">Mới tạo</h3>
                <a href="#" class="see-all-link text-decoration-none">
                    Xem tất cả (<?php echo count($recentlyCreatedQuizzes); ?>)
                </a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm carousel-prev-btn" aria-label="Previous quizzes">‹</button>
                <button class="btn btn-outline-secondary btn-sm carousel-next-btn" aria-label="Next quizzes">›</button>
            </div>
        </div>

        <div class="quiz-carousel-container" role="region" aria-roledescription="carousel" aria-label="Recently created quizzes">
            <div class="quiz-carousel-viewport overflow-hidden w-100">
                <div class="quiz-carousel-track d-flex" style="will-change: transform; transition: transform 400ms ease;">
                    <?php foreach ($recentlyCreatedQuizzes as $quiz): ?>
                        <div class="quiz-col px-2 flex-shrink-0">
                            <div class="quiz-card">
                                <div class="quiz-image-wrapper">
                                    <?php
                                    $hasImage = $quiz->getImage() != null && $quiz->getImage() && file_exists(__DIR__ . "/../../../public/uploads/quizzes/{$quiz->getImage()}");
                                    $fallback = "background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);";
                                    ?>
                                    <div class="quiz-image" style="<?= $hasImage ? '' : $fallback ?>">
                                        <?php if ($hasImage): ?>
                                            <img src="../../../public/uploads/quizzes/<?= htmlspecialchars($quiz->getImage()) ?>" 
                                                 alt="<?= htmlspecialchars($quiz->getTitle()) ?>" 
                                                 style="width:100%;height:100%;object-fit:cover;">
                                        <?php endif; ?>
                                        <form action="/game/lobby/<?= $quiz->getId() ?>" method="post" style="display:inline;">
                                            <button type="submit" class="play-now-btn">Chơi ngay</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="quiz-info">
                                    <h5 class="quiz-title"><?= htmlspecialchars($quiz->getTitle()) ?></h5>
                                    <div class="quiz-meta">
                                        <span class="quiz-rating">
                                            <?php if($quiz->getRatingCount() > 0): ?>
                                                <?= round($quiz->getRatingSum() / $quiz->getRatingCount(), 2) ?>
                                            <?php endif; ?>
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="quiz-author">Bởi <?= htmlspecialchars($quiz->getAuthor()) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="quiz-empty-alert alert alert-warning mt-3 d-none" role="alert">
            Không có quiz nào để hiển thị.
        </div>
    </div>
</section>

<!-- Best Rating Section -->
<section class="quiz-section py-5 bg-light">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <h3 class="section-title mb-0 me-2">Đánh giá cao nhất</h3>
                <a href="#" class="see-all-link text-decoration-none">
                    Xem tất cả (<?php echo count($highestRatedQuizzes); ?>)
                </a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary btn-sm carousel-prev-btn" aria-label="Previous quizzes">‹</button>
                <button class="btn btn-outline-secondary btn-sm carousel-next-btn" aria-label="Next quizzes">›</button>
            </div>
        </div>

        <div class="quiz-carousel-container" role="region" aria-roledescription="carousel" aria-label="Highest rated quizzes">
            <div class="quiz-carousel-viewport overflow-hidden w-100">
                <div class="quiz-carousel-track d-flex" style="will-change: transform; transition: transform 400ms ease;">
                    <?php foreach ($highestRatedQuizzes as $quiz): ?>
                        <div class="quiz-col px-2 flex-shrink-0">
                            <div class="quiz-card">
                                <div class="quiz-image-wrapper">
                                    <?php
                                    $hasImage = $quiz->getImage() != null && $quiz->getImage() && file_exists(__DIR__ . "/../../../public/uploads/quizzes/{$quiz->getImage()}");
                                    $fallback = "background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);";
                                    ?>
                                    <div class="quiz-image" style="<?= $hasImage ? '' : $fallback ?>">
                                        <?php if ($hasImage): ?>
                                            <img src="../../../public/uploads/quizzes/<?= htmlspecialchars($quiz->getImage()) ?>" 
                                                 alt="<?= htmlspecialchars($quiz->getTitle()) ?>" 
                                                 style="width:100%;height:100%;object-fit:cover;">
                                        <?php endif; ?>
                                        <form action="/game/lobby/<?= $quiz->getId() ?>" method="post" style="display:inline;">
                                            <button type="submit" class="play-now-btn">Chơi ngay</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="quiz-info">
                                    <h5 class="quiz-title"><?= htmlspecialchars($quiz->getTitle()) ?></h5>
                                    <div class="quiz-meta">
                                        <span class="quiz-rating">
                                            <?php if($quiz->getRatingCount() > 0): ?>
                                                <?= round($quiz->getRatingSum() / $quiz->getRatingCount(), 2) ?>
                                            <?php endif; ?>
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="quiz-author">Bởi <?= htmlspecialchars($quiz->getAuthor()) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="quiz-empty-alert alert alert-warning mt-3 d-none" role="alert">
            Không có quiz nào để hiển thị.
        </div>
    </div>
</section>

        <!-- Popular Right Now -->
        <section class="quiz-section py-5 bg-light">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <h3 class="section-title mb-0" style="margin-right:10px;">Phổ biến</h3>
                    <a href="#" class="see-all-link">Xem tất cả (55)</a>
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
                                        <span class="quiz-author">Bởi <?= $quiz['author'] ?></span>
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
    <script>
         <?php if (!empty($_SESSION['success'])) : ?>
            toastr.success(<?= json_encode($_SESSION['success'], JSON_UNESCAPED_UNICODE); ?>);
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['errors'])) : ?>
            toastr.error(<?= json_encode($_SESSION['errors'], JSON_UNESCAPED_UNICODE); ?>);
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>
    </script>

</body>

</html>