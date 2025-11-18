<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DASHMIN - Quizzes</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../../../public/css/style.css" rel="stylesheet">
    <link href="../../../public/css/toastr-override.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sidebar Start -->
        <?php include __DIR__ . '/../layouts/sidebar.admin.php'; ?>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <?php include __DIR__ . '/../layouts/navbar.admin.php'; ?>
            <!-- Navbar End -->

            <!-- Recent Sales Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Danh sách trò chơi</h6>
                        <button class="btn btn-sm btn-primary" onclick="openCreateModal()">
                            <i class="bi bi-plus-circle me-2"></i>Tạo mới trò chơi
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-dark">
                                    <th scope="col">ID</th>
                                    <th scope="col">Chủ đề</th>
                                    <th scope="col">Người tạo</th>
                                    <th scope="col">Tổng câu hỏi</th>
                                    <th scope="col">Đánh giá</th>
                                    <th scope="col">Tạo bởi</th>
                                    <th scope="col">Tạo lúc</th>
                                    <th scope="col">Cập nhật lúc</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($quizzes as $quiz): ?>
                                    <tr>
                                        <td><?php echo $quiz->getId() ?></td>
                                        <td><?php echo $quiz->getTitle() ?></td>
                                        <td><?php echo $quiz->getAuthor() ?></td>
                                        <td><?php echo $quiz->getTotalQuestions() ?></td>
                                        <td><?php echo $quiz->getRating() ?></td>
                                        <td><?php echo $quiz->getCreatedBy() ?></td>
                                        <td><?php echo $quiz->getCreatedAt() ?></td>
                                        <td><?php echo $quiz->getUpdatedAt() ?></td>
                                        <td>
                                            <a href="javascript:void(0)"
                                                data-bs-toggle="modal"
                                                data-bs-placement="top"
                                                data-bs-target="#viewQuizModal_<?php echo $quiz->getId(); ?>"
                                                title="Xem chi tiết">
                                                <i class="bi bi-eye-fill me-2"></i>
                                            </a>
                                            <a href="javascript:void(0)"
                                                class="update-btn"
                                                style="color: purple"
                                                data-quiz='<?php echo htmlspecialchars(json_encode([
                                                                "id" => $quiz->getId(),
                                                                "title" => $quiz->getTitle(),
                                                                "is_public" => $quiz->isPublic(),
                                                                "image" => $quiz->getImage()
                                                            ]), ENT_QUOTES, 'UTF-8'); ?>'
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Cập nhật trò chơi">
                                                <i class="bi bi-pencil-fill me-2"></i>
                                            </a>
                                            <a href="javascript:void(0)"
                                                class="delete-btn"
                                                data-quiz-id="<?php echo $quiz->getId() ?>"
                                                data-quiz-title="<?php echo htmlspecialchars($quiz->getTitle()) ?>"
                                                style="color: red"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Xóa trò chơi">
                                                <i class="bi bi-trash3-fill me-2"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
            <!-- Pagination End -->
            <!-- Recent Sales End -->

            <!-- Footer Start -->
            <?php include __DIR__ . '/../layouts/footer.admin.php'; ?>
            <!-- Footer End -->
        </div>
        <!-- Content End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- Include Modal Create/Update -->
    <?php include __DIR__ . '/create-update/modalQuiz.php'; ?>

    <!-- Include Modal View -->
    <?php include __DIR__ . '/view/viewQuiz.php'; ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/lib/waypoints/waypoints.min.js"></script>

    <script>
        <?php if (!empty($_SESSION['errors'])) : ?>
            toastr.error(<?= json_encode($_SESSION['errors'], JSON_UNESCAPED_UNICODE); ?>);
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>
    </script>

    <!-- Template Javascript -->
    <script src="../../../public/js/main_2.js"></script>

    <!-- Quiz Management Script -->
    <script src="../../../public/js/quiz-admin.js"></script>
</body>

</html>