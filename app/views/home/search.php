<?php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">
    <title>Quiz.com</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../../public/css/search.css">
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
        <?php if ($quizzes): ?>
            <div class="quiz-results-container">
                <div class="results-header">
                    <h2>Hiển thị kết quả tìm kiếm cho "<?php echo ($value !== 'null') ? $value : (($keyword !== 'null') ? $keyword : 'null') ?>"</h2>
                </div>

                <div class="filter-dropdown mb-3">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Khớp nhất
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?value=bestMatch">Khớp nhất</a></li>
                        <li><a class="dropdown-item" href="?value=mostPopular">Phổ biến nhất</a></li>
                        <li><a class="dropdown-item" href="?value=hightestRated">Đánh giá cao nhất</a></li>
                        <li><a class="dropdown-item" href="?value=newest">Mới nhất</a></li>
                    </ul>
                </div>

                <div class="quiz-list">
                    <?php foreach ($quizzes as $quiz): ?>
                        <form action="/game/lobby/<?= htmlspecialchars($quiz->getId()) ?>" method="post" class="quiz-item" onclick="this.submit()">
                            <img src="../../../public/uploads/quizzes/<?= htmlspecialchars($quiz->getImage()) ?>" alt="<?php echo $quiz->getTitle() ?>" class="quiz-thumbnail">
                            <div class="quiz-content">
                                <div class="quiz-title"><?php echo $quiz->getTitle() ?></div>
                                <div class="quiz-meta">
                                    <div class="quiz-rating">
                                        <i class="bi bi-star-fill"></i>
                                        <span>
                                            <?php if ($quiz->getRatingCount() > 0): ?>
                                                <?= round($quiz->getRatingSum() / $quiz->getRatingCount(), 2) ?>
                                            <?php endif; ?>
                                            <i class="fas fa-star"></i>
                                        </span>
                                    </div>
                                    <div class="quiz-players">
                                        <i class="bi bi-people-fill"></i>
                                        <span>4</span>
                                    </div>
                                    <div class="quiz-date"><?= DateUtils::formatPrettyDate((string)$quiz->getCreatedAt()) ?></div>
                                    <div class="quiz-tags">
                                        <span class="quiz-tag">science & nature, maths, physics quiz, physics, mathematics, math</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination Start -->
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center mt-3">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&per_page=<?php echo $per_page; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&per_page=<?php echo $per_page; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&per_page=<?php echo $per_page; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php else: ?>
            <div class="text-center" style="padding: 40px; font-size: 35px;">
                <p class="">Xin lỗi, chúng tôi không tìm thấy kết quả nào phù hợp cho "<?php echo ($value !== 'null') ? $value : (($keyword !== 'null') ? $keyword : 'null') ?>"</p>
            </div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Toastr -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../../../public/js/main.js"></script>


</body>

</html>