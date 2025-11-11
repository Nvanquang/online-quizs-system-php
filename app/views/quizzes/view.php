<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Detail - Quiz.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap");

        * {
            font-family: "Nunito", sans-serif;
        }

        body {
            background: #f7f7f7;
        }

        .hide {
            display: none !important;
        }

        /* Sidebar */
        .sidebar {
            margin-top: 50px;
            background: white;
            min-height: 100vh;
            border-right: 1px solid #e2e8f0;
            padding: 24px;
            position: fixed;
            width: 350px;
            left: 0;
            top: 0;
            overflow-y: auto;
        }

        .quiz-thumbnail {
            background: linear-gradient(135deg, #26c6da 0%, #00acc1 100%);
            border-radius: 12px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            position: relative;
        }

        .quiz-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
        }

        .quiz-thumbnail-title {
            font-size: 48px;
            font-weight: 900;
            color: white;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .quiz-questions-badge {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.75);
            color: white;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
        }

        .quiz-title {
            font-size: 28px;
            font-weight: 900;
            color: #1a202c;
            margin-bottom: 12px;
        }

        .quiz-author {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #718096;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .action-icons {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
        }

        .action-icon-btn {
            width: 36px;
            height: 36px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
            color: #718096;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-host-game {
            width: 100%;
            background: #26c6da;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 12px;
            transition: all 0.2s;
        }

        .btn-host-game:hover {
            background: #00acc1;
        }

        .sidebar-buttons {
            display: flex;
            gap: 12px;
        }

        .btn-play-solo, .btn-assign-hw {
            flex: 1;
            background: #9333ea;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.2s;
        }

        .btn-play-solo:hover, .btn-assign-hw:hover {
            background: #7e22ce;
        }

        /* Main Content */
        .main-content {
            margin-left: 350px;
            padding: 32px;
            padding-top: 64px; /* offset for fixed header */
        }

        .user-badge {
            background: #9333ea;
            color: white;
            padding: 8px 16px;
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            position: fixed;
            top: 20px;
            right: 30px;
            z-index: 1000;
            border: none;
        }

        .user-badge:hover {
            background: #7e22ce;
        }

        .user-icon {
            width: 24px;
            height: 24px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9333ea;
        }

        .btn-show-answers {
            background: white;
            border: 2px solid #cbd5e0;
            border-radius: 10px;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 700;
            color: #4a5568;
            margin-bottom: 32px;
            transition: all 0.2s;
        }

        /* Question Card */
        .question-card {
            background: white;
            border-radius: 12px;
            padding: 12px 24px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .question-label {
            font-size: 16px;
            font-weight: 700;
            color: #718096;
            margin-bottom: 8px;
        }

        .question-text {
            font-size: 18px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0;
        }

        .question-actions {
            display: flex;
            gap: 8px;
        }

        .question-action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #2d3748;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .time-badge {
            background: #2d3748;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Answers */
        .answers-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-top: 20px;
        }

        .answer-option {
            background: #f1f1f1;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            font-size: 15px;
            font-weight: 600;
            color: #2d3748;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .answer-option:hover {
            background: #e8e8e8;
        }

        .answer-option.correct {
            background: #4caf50;
            color: white;
            border-color: #4caf50;
        }

        .answer-option.correct i {
            font-size: 20px;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            bottom: 20px;
            right: 30px;
            text-align: right;
            opacity: 0.25;
            pointer-events: none;
        }

        .watermark-title {
            font-size: 15px;
            font-weight: 600;
            color: #000;
        }

        .watermark-text {
            font-size: 13px;
            color: #000;
        }

        /* Hidden class */
        .d-none {
            display: none !important;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
            }

            .main-content {
                margin-left: 0;
            }

            .user-badge {
                position: relative;
                margin-bottom: 20px;
            }

            .answers-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../layouts/header.php'; ?>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="quiz-thumbnail">
            <img src="/public/uploads/quizzes/<?php echo htmlspecialchars($quiz->getImage()); ?>" alt="<?php echo htmlspecialchars($quiz->getTitle()); ?>">
            <div class="quiz-questions-badge"><?php echo $quiz->getTotalQuestions(); ?> Questions</div>
        </div>

        <h2 class="quiz-title"><?php echo htmlspecialchars($quiz->getTitle()); ?></h2>

        <div class="quiz-author">
            <i class="bi bi-person-fill"></i>
            <span><?php echo htmlspecialchars($quiz->getAuthor()); ?></span>
        </div>

        <div class="action-icons">
            <button class="btn action-icon-btn" data-action="favorite" data-bs-toggle="tooltip" data-bs-placement="top" title="Yêu thích">
                <i class="bi bi-star"></i>
            </button>
            <button class="btn action-icon-btn" data-action="files" data-bs-toggle="tooltip" data-bs-placement="top" title="Files">
                <i class="bi bi-files"></i>
            </button>
            <button class="btn action-icon-btn" data-action="edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Sửa">
                <a href="/quiz/edit/<?php echo $quiz->getId(); ?>" style="text-decoration: none;color:inherit">
                    <i class="bi bi-pencil-fill"></i>
                </a>
            </button>
        </div>

        <form action="/game/lobby/<?php echo $quiz->getId(); ?>" method="post" style="text-decoration: none;color:inherit">
            <button type="submit" class="btn btn-host-game">
                <i class="bi bi-play-fill"></i>
                Host Game
            </button>
        </form>

        <div class="sidebar-buttons">
            <button class="btn btn-play-solo">Play Solo</button>
            <button class="btn btn-assign-hw">Assign HW</button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <button class="btn btn-show-answers" id="btnShowAnswers">
            Show all answers
        </button>

        <?php $i = 1; foreach ($questions as $question) : ?>
        <!-- Question 1 - Simple View -->
        <div class="question-card question-simple" data-question="<?php echo $i; ?>">
            <div class="question-header">
                <div>
                    <div class="question-label">Question <?php echo $i; ?></div>
                    <p class="question-text"><?php echo htmlspecialchars($question->content); ?></p>
                </div>
                <div class="question-actions">
                    <button class="btn question-action-btn" data-action="show" data-bs-toggle="tooltip" data-bs-placement="top" title="Xem chi tiết">
                        <i class="bi bi-eye"></i>
                    </button>
                    <span class="time-badge">
                        <i class="bi bi-stopwatch"></i>
                        <?php echo $question->time_limit; ?> sec
                    </span>
                </div>
            </div>
        </div>

        <!-- Question 1 - Full View with Answers (Hidden by default) -->
        
        <div class="question-card question-full d-none" data-question="<?php echo $i; ?>" data-correct="<?php echo htmlspecialchars($question->correct_answer); ?>">
            <div class="question-header">
                <div>
                    <div class="question-label">Question <?php echo $i; ?></div>
                    <p class="question-text"><?php echo htmlspecialchars($question->content); ?></p>
                </div>
                <div class="question-actions">
                    <button class="btn question-action-btn" data-action="hide" data-bs-toggle="tooltip" data-bs-placement="top" title="Ẩn">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                    <span class="time-badge">
                        <i class="bi bi-stopwatch"></i>
                        <?php echo $question->time_limit; ?> sec
                    </span>
                </div>
            </div>

            <div class="answers-grid">
                <div class="answer-option" data-option="A">
                    A. <?php echo htmlspecialchars($question->answer_a); ?>
                </div>
                <div class="answer-option" data-option="B">
                    B. <?php echo htmlspecialchars($question->answer_b); ?>
                </div>
                <div class="answer-option" data-option="C">
                    C. <?php echo htmlspecialchars($question->answer_c); ?>
                </div>
                <div class="answer-option" data-option="D">
                    D. <?php echo htmlspecialchars($question->answer_d); ?>
                </div>
            </div>
        </div>
        <?php $i++; endforeach; ?>
    <!-- Watermark -->
    <div class="watermark">
        <div class="watermark-title">Activate Windows</div>
        <div class="watermark-text">Go to Settings to activate Windows.</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            const jgs = document.querySelector('.join-game-section');
            if (jgs) jgs.classList.add('hide');
            const bs = document.querySelector('.btn-search');
            if (bs) bs.classList.add('hide');

            let isShowingAnswers = false;
            function highlightCorrect() {
                $('.question-full').each(function() {
                    const correct = String($(this).data('correct') || '').trim().toUpperCase();
                    const $options = $(this).find('.answer-option');
                    $options.removeClass('correct');
                    const $opt = $options.filter('[data-option="' + correct + '"]');
                    if ($opt.length) {
                        if ($opt.find('i.bi-check-lg').length === 0) {
                            $opt.prepend('<i class="bi bi-check-lg"></i>');
                        }
                        $opt.addClass('correct');
                    }
                });
            }
            highlightCorrect();

            // Show/Hide all answers
            $('#btnShowAnswers').on('click', function() {
                isShowingAnswers = !isShowingAnswers;

                if (isShowingAnswers) {
                    // Hide simple views, show full views
                    $('.question-simple').addClass('d-none');
                    $('.question-full').removeClass('d-none');
                    $(this).text('Hide all answers');
                } else {
                    // Show simple views, hide full views
                    $('.question-simple').removeClass('d-none');
                    $('.question-full').addClass('d-none');
                    $(this).text('Show all answers');
                }
            });

            // Question action buttons: toggle details for that question
            $('.question-action-btn').on('click', function(e) {
                e.stopPropagation();
                const $card = $(this).closest('.question-card');
                const qn = $card.data('question');
                const $simple = $('.question-simple[data-question="' + qn + '"]');
                const $full = $('.question-full[data-question="' + qn + '"]');

                if ($card.hasClass('question-simple')) {
                    $simple.addClass('d-none');
                    $full.removeClass('d-none');
                } else if ($card.hasClass('question-full')) {
                    $full.addClass('d-none');
                    $simple.removeClass('d-none');
                }
            });
        });
    </script>
</body>
</html>