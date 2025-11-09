<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Editor - Quiz Game</title>
    <meta name="csrf-token" content="<?= CSRFMiddleware::getToken() ?>">
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/edit-quiz.css">
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
            </div>
        </div>
        <div class="header-right">
            <?php $auth = Auth::getInstance(); ?>
            <?php if ($auth->check()): ?>
                <?php $user = $auth->user(); ?>
                <div class="dropdown">
                    <a href="#" class="d-inline-flex align-items-center p-0 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../../../public/uploads/avatars/<?= $user->getAvatarUrl() ?>" alt="Avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="user/profile">Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="user/my-quizzes">Quiz của tôi</a></li>
                        <li><a class="dropdown-item" href="user/history">Lịch sử</a></li>
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
    </header>

    <!-- Main Content -->
    <div class="container-fluid main-container">
        <input type="hidden" id="quizId" value="<?= (int)$quizId ?>">
        <div class="row h-100">
            <!-- Left Sidebar -->
            <div class="col-lg-3 col-md-4 sidebar">
                <div class="quiz-card">
                    <!-- Cover Image -->
                    <div class="cover-image-container mb-4">
                        <img id="coverImage" src="../../../public/uploads/quizzes/<?= $quiz->getImage() ?>" alt="Quiz Cover" class="cover-image">
                        <div class="cover-overlay">
                            <button class="btn btn-sm btn-light" id="changeCoverBtn" onclick="document.getElementById('coverInput').click()">
                                <i class="fas fa-image"></i> Change Cover
                            </button>
                            <input type="file" id="coverInput" accept="image/*" style="display: none;">
                        </div>
                    </div>

                    <!-- Quiz Info -->
                    <div class="quiz-info mb-4">
                        <h3 id="quizTitle" class="quiz-title"><?= $quiz->getTitle() ?></h3>
                        <div class="privacy-setting d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-lock"></i>
                            <span id="privacyText"><?= $quiz->isPublic() ? 'Public' : 'Private' ?></span>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <button class="btn btn-teal btn-lg w-100 mb-3">
                        <i class="fas fa-save"></i> Save Set
                    </button>

                    <!-- Action Buttons -->
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <button class="btn btn-sm btn-light w-100">
                                <i class="fas fa-edit"></i> Edit Info
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-sm btn-light w-100" id="btnTimeLimit">
                                <i class="fas fa-hourglass"></i> Time Limit
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons Below Card -->
                <div class="action-buttons">
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-action btn-purple w-100">
                                <i class="fas fa-plus"></i><br>Add<br>Question
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-action btn-blue w-100">
                                <i class="fas fa-book"></i><br>Quizlet<br>Import
                            </button>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <button class="btn btn-action btn-green w-100">
                                <i class="fas fa-table"></i><br>Spreadsheet<br>Import
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-action btn-info w-100">
                                <i class="fas fa-building"></i><br>Question<br>Bank
                            </button>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-8 mx-auto">
                            <button class="btn btn-action btn-teal w-100">
                                <i class="fas fa-eye-slash"></i> Hide Answers
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9 col-md-8 main-content">
                <div class="content-area">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <div class="questions-count">
                            <span class="text-muted" style="font-size: 18px;">3 Questions</span>
                        </div>
                        <button class="btn btn-add-question">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                    </div>

                    <!-- Questions List (fake data) -->
                    <div id="questionsList" class="d-flex flex-column gap-3">
                        <?php $index = 1; foreach ($questions as $question): ?>
                        <div class="card shadow-sm">
                            <div class="card-body d-flex align-items-start">
                                <div class="me-3 d-flex flex-column">
                                    <button class="btn btn-light btn-sm mb-1"><i class="fas fa-chevron-up"></i></button>
                                    <button class="btn btn-light btn-sm"><i class="fas fa-chevron-down"></i></button>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <button class="btn btn-info btn-sm text-white btn-edit-question"
                                                data-question="<?= htmlspecialchars($question->content) ?>"
                                                data-time="<?= (int)$question->time_limit ?>"
                                                data-type="multiple"
                                                data-random="0"
                                                data-image="<?= '../../../public/uploads/questions/' . htmlspecialchars($question->image_url) ?>"
                                                data-ans1="<?= htmlspecialchars($question->answer_a) ?>"
                                                data-ans2="<?= htmlspecialchars($question->answer_b) ?>"
                                                data-ans3="<?= htmlspecialchars($question->answer_c) ?>"
                                                data-ans4="<?= htmlspecialchars($question->answer_d) ?>"
                                                data-correct="<?= htmlspecialchars($question->correct_answer) ?>"
                                                data-question-id="<?= (int)$question->question_id ?>">
                                                <i class="fas fa-pen"></i> Edit
                                            </button>
                                            <h6 class="mb-0">Question <?php echo $index; ?></h6>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="btn btn-light btn-sm"><i class="fas fa-random"></i></button>
                                            <span class="badge bg-secondary"><?php echo (int)$question->time_limit; ?> sec</span>
                                        </div>
                                    </div>
                                    <div class="mt-2"><?php echo htmlspecialchars($question->content); ?></div>
                                    <div class="mt-2 d-flex gap-2">
                                        <button class="btn btn-light btn-sm btn-delete-question" data-question-id="<?= (int)$question->question_id ?>"><i class="fas fa-trash"></i></button>
                                        <button class="btn btn-light btn-sm"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $index++; endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Editor Modal -->
    <div class="modal fade" id="questionEditorModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen-md-down modal-xl">
            <div class="modal-content border-0">
                <div class="modal-header" style="background:#7c3aed;color:#fff;">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-light" id="qeTimeBtn">
                            <i class="fas fa-clock me-1"></i> <span>Time Limit</span>
                            <input type="number" class="form-control text-center" id="qeTimeInput" min="1" value="10">
                        </button>
                        <div class="form-check form-check-inline text-white">
                            <input class="form-check-input" type="checkbox" id="qeRandomOrder">
                            <label class="form-check-label" for="qeRandomOrder">Random Order</label>
                        </div>
                        <div class="ms-2">
                            <select id="qeType" class="form-select form-select-sm">
                                <option value="multiple" selected>Multiple Choice</option>
                                <option value="truefalse">True / False</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-light btn-sm" id="qeCancelBtn" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cancel</button>
                        <button type="button" class="btn btn-light btn-sm" id="qeSaveBtn"><i class="fas fa-save me-1"></i>Save</button>
                    </div>
                </div>
                <div class="modal-body p-3 p-md-4 qe-modal-body">
                    <div class="d-flex gap-3">
                        <div class="d-flex flex-column gap-2 qe-side-tools">
                            <button id="qeImageBtn" class="btn btn-primary btn-sm w-100"><i class="fas fa-image me-1"></i>Image</button>
                            <button class="btn btn-primary btn-sm w-100"><i class="fas fa-superscript me-1"></i>Math</button>
                            <button class="btn btn-primary btn-sm w-100"><i class="fas fa-microphone me-1"></i>Audio</button>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex gap-3 align-items-start">
                                <div id="qeImageContainer" style="display:none;">
                                    <img id="qeImagePreview" src="" alt="" style="max-width:220px;max-height:140px;object-fit:cover;border-radius:4px;display:block;">
                                    <button type="button" class="btn btn-light btn-sm mt-2" id="qeImageRemove">Remove</button>
                                </div>
                                <textarea id="qeQuestionText" class="form-control" rows="4" placeholder="Question Text"></textarea>
                            </div>
                            <input type="file" id="qeImageInput" accept="image/*" style="display:none;">
                        </div>
                    </div>

                    <div class="row g-3 mt-3 qe-answers">
                        <div class="col-md-6">
                            <div class="qe-answer card qe-answer-a">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <input class="form-check-input" type="radio" name="qeCorrect" value="A" aria-label="Correct answer 1">
                                    <textarea class="form-control form-control-lg qe-answer-input" id="qeAns1" rows="2" placeholder="Answer 1"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="qe-answer card qe-answer-b">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <input class="form-check-input" type="radio" name="qeCorrect" value="B" aria-label="Correct answer 2">
                                    <textarea class="form-control form-control-lg qe-answer-input" id="qeAns2" rows="2" placeholder="Answer 2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="qe-answer card qe-answer-c">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <input class="form-check-input" type="radio" name="qeCorrect" value="C" aria-label="Correct answer 3">
                                    <textarea class="form-control form-control-lg qe-answer-input" id="qeAns3" rows="2" placeholder="Answer 3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="qe-answer card qe-answer-d">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <input class="form-check-input" type="radio" name="qeCorrect" value="D" aria-label="Correct answer 4">
                                    <textarea class="form-control form-control-lg qe-answer-input" id="qeAns4" rows="2" placeholder="Answer 4"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Limit Modal -->
    <div class="modal fade" id="timeLimitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0">
                <div class="modal-body text-center p-4">
                    <h6 class="mb-3">Set the time limit for all questions (in seconds):</h6>
                    <div class="input-group justify-content-center mb-3" style="max-width: 200px; margin: 0 auto;">
                        <input type="number" id="timeLimitInput" class="form-control text-center" min="1" value="10">
                        <span class="input-group-text"><i class="fas fa-clock"></i></span>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
                        <button type="button" class="btn btn-teal" id="confirmTimeLimitBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../public/js/edit-quiz.js"></script>
</body>
</html>
