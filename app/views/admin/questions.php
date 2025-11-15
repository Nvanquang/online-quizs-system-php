<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DASHMIN - Questions</title>
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

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../../../public/css/style.css" rel="stylesheet">

    <style>
        /* Toast Notification Styles */
        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            background: white;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
            z-index: 9999;
        }

        .toast-notification.success {
            border-left: 4px solid #198754;
        }

        .toast-notification.error {
            border-left: 4px solid #dc3545;
        }

        .toast-notification .icon {
            font-size: 24px;
        }

        .toast-notification.success .icon {
            color: #198754;
        }

        .toast-notification.error .icon {
            color: #dc3545;
        }

        .toast-notification .content .title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .toast-notification .content .message {
            font-size: 14px;
            color: #666;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        /* View Modal Styles */
        .modal.modal-view .modal-dialog {
            position: fixed;
            right: 0;
            top: 0;
            margin: 0;
            height: 100vh;
            max-width: 40%;
        }

        .modal.modal-view .modal-content {
            height: 100%;
            border-radius: 0;
            border: none;
            border-left: 1px solid #dee2e6;
        }

        .modal.modal-view .modal-body {
            overflow-y: auto;
            max-height: calc(100vh - 120px);
        }

        .modal.modal-view .table td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        .modal.modal-view .table .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
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
                        <h6 class="mb-0">Danh sách questions</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-dark">
                                    <th scope="col">ID</th>
                                    <th scope="col">Câu hỏi</th>
                                    <th scope="col">Tạo lúc</th>
                                    <th scope="col">Cập nhật lúc</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($questions as $question): ?>
                                    <tr>
                                        <td><?php echo $question->getId() ?></td>
                                        <td><?php echo $question->getContent() ?></td>
                                        <td><?php echo $question->getCreatedAt() ?></td>
                                        <td><?php echo $question->getUpdatedAt() ?></td>
                                        <td>
                                            <a href="javascript:void(0)" 
                                               data-bs-toggle="modal"
                                               data-bs-target="#viewQuestionModal_<?php echo $question->getId(); ?>"
                                               title="Xem chi tiết">
                                                <i class="bi bi-eye-fill me-2"></i>
                                            </a>
                                            <a href="javascript:void(0)" 
                                               class="update-btn" 
                                               data-question='<?php echo htmlspecialchars(json_encode([
                                                   "id" => $question->getId(),
                                                   "content" => $question->getContent(),
                                                   "answer_a" => $question->getAnswerA(),
                                                   "answer_b" => $question->getAnswerB(),
                                                   "answer_c" => $question->getAnswerC(),
                                                   "answer_d" => $question->getAnswerD(),
                                                   "correct_answer" => $question->getCorrectAnswer(),
                                                   "explanation" => $question->getExplanation(),
                                                   "image_url" => $question->getImageUrl(),
                                                   "time_limit" => $question->getTimeLimit()
                                               ]), ENT_QUOTES, 'UTF-8'); ?>'
                                               style="color: purple" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Cập nhật câu hỏi">
                                                <i class="bi bi-pencil-fill me-2"></i>
                                            </a>
                                            <a href="javascript:void(0)" 
                                               class="delete-btn" 
                                               data-question-id="<?php echo $question->getId() ?>" 
                                               data-question-content="<?php echo htmlspecialchars(substr($question->getContent(), 0, 50)) ?>..." 
                                               style="color: red" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Xóa câu hỏi">
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
    <?php include __DIR__ . '/create-update/modalQuestion.php'; ?>

    <!-- Include Modal View -->
    <?php include __DIR__ . '/view/viewQuestion.php'; ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/lib/waypoints/waypoints.min.js"></script>

    <!-- Template Javascript -->
    <script src="../../../public/js/main_2.js"></script>

    <!-- Question Management Script -->
    <script src="../../../public/js/question-admin.js"></script>
</body>

</html>