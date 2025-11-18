<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sets - Quiz.com</title>
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">
    <meta name="csrf-token" content="<?= CSRFMiddleware::getToken() ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="../../../public/css/toastr-override.css">
    <link rel="stylesheet" href="../../../public/css/my-quizzes.css">

</head>

<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="row align-items-center g-3">
                <a class="logo col-auto" href="/">
                    <img src="../../../public/images/logo/quiz-multicolor.svg" alt="Quiz.com" />
                </a>
                <div class="col-auto">
                    <button class="btn btn-create" data-action="create" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tạo quiz">
                        <a href="/quiz/create" style="text-decoration: none;color:inherit">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                    </button>
                </div>
                <div class="col">
                    <div class="search-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="form-control search-input" placeholder="Tìm kiếm các bộ quiz của bạn...">
                    </div>
                </div>
                <div class="col-auto">
                    <?php $auth = Auth::getInstance(); ?>
                    <?php if ($auth->check()): ?>
                        <?php $user = $auth->user(); ?>
                        <div class="dropdown d-inline-flex align-items-center gap-2">
                            <a href="#" class="d-inline-flex align-items-center p-0 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="../../../public/uploads/avatars/<?= $user->getAvatarUrl() ?>" alt="Avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($user->isAdmin()): ?>
                                    <li><a class="dropdown-item" href="/admin/dashboard">Quản trị</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="/user/profile">Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="/user/my-quizzes">Quiz của tôi</a></li>
                                <li><a class="dropdown-item" href="/user/history">Lịch sử</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="/auth/logout">Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="/auth/login"><button class="btn btn-signin">Đăng nhập</button></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container my-5">
        <!-- Breadcrumb -->
        <div class="bg-body-tertiary border rounded px-3 py-2 mb-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '>';">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a class="text-decoration-none" href="/">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Quiz của tôi</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4">
            <!-- Blooket Card -->
            <?php foreach ($quizzes as $quiz) : ?>
                <div class="col">
                    <div class="quiz-card card-blooket">
                        <div class="card-image-wrapper">
                            <img src="<?php echo htmlspecialchars('../../../public/uploads/quizzes/' . $quiz->getImage()); ?>" alt="<?php echo htmlspecialchars($quiz->getTitle()); ?>">
                            <div class="card-views-badge" data-bs-toggle="tooltip" data-bs-placement="top" title="Xem Quiz">
                                <a href="/quiz/view/<?= $quiz->getId() ?>" style="text-decoration: none;color:inherit">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </div>
                            <div class="card-questions-badge"><?php echo $quiz->getTotalQuestions(); ?> Câu hỏi</div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $quiz->getTitle(); ?></h3>
                            <div class="card-edited">Đã chỉnh sửa <?php echo DateUtils::daysFromNow($quiz->getUpdatedAt()); ?> ngày trước</div>

                            <div class="card-actions">
                                <a href="/quiz/edit/<?= $quiz->getId() ?>" class="btn action-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Chỉnh sửa">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <button class="btn action-btn openModalDelete" data-action="delete" data-bs-toggle="tooltip" data-bs-placement="top" title="Xóa" data-quiz-id="<?= (int)$quiz->getId() ?>">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                                <button class="btn action-btn" data-action="settings" data-bs-toggle="tooltip" data-bs-placement="top" title="Cài đặt">
                                    <i class="bi bi-gear-fill"></i>
                                </button>
                            </div>

                            <div class="assign-host-group mt-0">
                                <span class="btn btn-assign flex-fill d-flex justify-content-center align-items-center gap-2" role="button" tabindex="0">
                                    <i class="bi bi-clipboard"></i>
                                    <span>Chia</span>
                                </span>
                                <form action="/game/lobby/<?= $quiz->getId() ?>" method="post" class="flex-fill" style="margin:0;">
                                    <button type="submit" class="btn btn-host d-flex justify-content-center align-items-center gap-2 w-100">
                                        <i class="bi bi-play-fill"></i>
                                        <span>Chơi</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="mb-0">Bạn có thực sự muốn xóa bộ quiz này không?</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Không</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Có</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Watermark -->
    <div class="watermark">
        <div class="watermark-title">Activate Windows</div>
        <div class="watermark-text">Go to Settings to activate Windows.</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Bootstrap tooltips
            const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
            // Search functionality
            $('.search-input').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.quiz-card').each(function() {
                    const title = $(this).find('.card-title').text().toLowerCase();
                    if (title.includes(searchTerm)) {
                        $(this).closest('.col').show();
                    } else {
                        $(this).closest('.col').hide();
                    }
                });
            });

            // Delete confirm flow
            let pendingQuizId = null;
            $(document).on('click', '.openModalDelete', function(e) {
                e.preventDefault();
                e.stopPropagation();
                pendingQuizId = $(this).data('quiz-id') ?? null;
                const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                modal.show();
                const $confirm = $('#confirmDeleteBtn');
                $confirm.off('click').on('click', function() {
                    if (!pendingQuizId) return;
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    $confirm.prop('disabled', true);
                    $.ajax({
                        url: `/quiz/delete/${pendingQuizId}`,
                        method: 'POST',
                        headers: csrf ? {
                            'X-CSRF-Token': csrf
                        } : {},
                        success: (res) => {
                            if (res.success) {
                                modal.hide();
                                toastr.options = {
                                    "timeout": 2000
                                }
                                toastr.success(res.message);
                                setTimeout(() => {
                                    location.reload()
                                }, 2000)
                            } else {
                                toastr.options = {
                                    "timeout": 2000
                                }
                                toastr.error(res.message)
                            }
                        },
                        error: (xhr) => {
                            const msg = xhr?.responseText || 'Không thể xóa quiz';
                            alert(msg);
                            $confirm.prop('disabled', false);
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>