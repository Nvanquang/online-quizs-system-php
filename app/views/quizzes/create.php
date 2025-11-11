<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Set Creator</title>
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/create-quiz.css">
</head>

<body>
    <div style="display:none;">
        <?= CSRFMiddleware::getTokenField(); ?>
    </div>
    <header class="quiz-header">
        <div class="header-content">
            <div class="header-left">
                <div class="logo-quiz">
                    <a href="/">
                        <img src="../../../public/images/logo/quiz-multicolor.svg" alt="Quiz.com" />
                    </a>
                </div>
            </div>
            <div class="header-right">
                <?php $auth = Auth::getInstance(); ?>
                <?php if ($auth->check()): ?>
                    <?php $user = $auth->user(); ?>
                    <div class="dropdown d-inline-flex align-items-center gap-2">
                        <span class="fw-semibold m-0">
                            <?= htmlspecialchars($user->getUsername()) ?>
                        </span>
                        <a href="#" class="d-inline-flex align-items-center p-0 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../../../public/uploads/avatars/<?= $user->getAvatarUrl() ?>" alt="Avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
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
    </header>

    <!-- Main Content -->
    <div class="container-lg py-5">
        <!-- Breadcrumb -->
        <div class="bg-body-tertiary border rounded px-3 py-2 mb-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '>';">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a class="text-decoration-none" href="/">Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo isset($quiz) ? 'Chỉnh sửa quiz' : 'Tạo quiz' ?></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-5">
                <!-- Cover Image Section -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="upload-area mb-3">
                            <div class="border-2 border-dashed rounded-3 p-5 text-center bg-light-custom position-relative<?= (isset($quiz) && $quiz->getImage()) ? ' has-image' : '' ?>"
                                id="dropZone">
                                <img id="previewImage" src="<?= (isset($quiz) && $quiz->getImage()) ? ('../../../public/uploads/quizzes/' . htmlspecialchars($quiz->getImage())) : '/placeholder.svg' ?>" alt="Cover Preview" class="img-fluid rounded-3 <?= (isset($quiz) && $quiz->getImage()) ? '' : 'd-none' ?>">
                                <div class="remove-overlay">
                                    <button type="button" class="btn btn-danger btn-sm" id="removeImageBtn">
                                        <i class="fas fa-trash me-1"></i>Remove image
                                    </button>
                                </div>
                                <div id="uploadContent" class="<?= (isset($quiz) && $quiz->getImage()) ? 'd-none' : '' ?>">
                                    <h5 class="text-dark fw-bold mb-3">Cover Image</h5>
                                    <p class="text-muted mb-4">Drag and Drop or</p>

                                    <div class="d-flex flex-column gap-2">
                                        <button class="btn btn-light btn-upload" onclick="document.getElementById('fileUpload').click()">
                                            <i class="fas fa-file me-2"></i>Upload a File
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <input type="file" id="imageGallery" accept="image/*" style="display: none;">
                            <input type="file" id="fileUpload" accept="image/*" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-7">
                <!-- Title Section -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <label for="titleInput" class="form-label fw-bold text-dark">
                            Title <span class="text-danger">(required)</span>
                        </label>
                        <input type="text" class="form-control form-control-lg border-0 bg-light-input"
                            id="titleInput" placeholder="Add a descriptive title" value="<?= isset($quiz) && $quiz->getTitle() ? htmlspecialchars($quiz->getTitle()) : '' ?>">
                    </div>
                </div>

                <!-- Privacy Setting Section -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h6 class="text-dark fw-bold mb-2">Privacy Setting</h6>
                        <p class="text-muted small mb-3">This decides who can find and play your question set</p>

                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-switch" type="checkbox" id="privacyToggle" <?= isset($quiz) && $quiz->isPublic() ? 'checked' : '' ?>>
                            </div>
                            <div>
                                <label for="privacyToggle" class="text-dark fw-bold mb-0">Public</label>
                                <p class="text-muted small mb-0">Playable by Everyone</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Button -->
                <div class="d-flex justify-content-end mt-5">
                    <button class="btn btn-create btn-lg" id="createBtn">
                        <i class="fas fa-check me-2"></i>Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- URL Modal -->
    <div class="modal fade" id="urlModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Upload by URL</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="url" class="form-control" id="urlInput" placeholder="Enter image URL">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-create" onclick="loadImageFromUrl()">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.QUIZ_IS_EDIT = <?= isset($quiz) ? 'true' : 'false' ?>;
        window.QUIZ_ID = <?= isset($quiz) ? (int)$quiz->getId() : 'null' ?>;
        window.QUIZ_FORM_ACTION = <?= isset($quiz)
                                        ? "'" . '/quiz/edit-quiz/' . (int)$quiz->getId() . "'"
                                        : "'" . '/quiz/create' . "'" ?>;
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../../public/js/create-quiz.js"></script>
</body>

</html>