<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Profile</title>
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <link rel="stylesheet" href="../../../public/css/layout.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
        }

        .hide {
            display: none !important;
        }

        .main-container {
            display: flex;
            min-height: 100vh;
            max-width: 1248px;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #fff;
            border-right: 1px solid #e0e0e0;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            margin-top: 50px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 30px;
            color: #333;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu a:hover {
            background-color: #f8f9fa;
            border-left-color: #8e44ad;
        }

        .sidebar-menu a.active {
            background-color: #f8f9fa;
            border-left-color: #8e44ad;
            font-weight: 600;
        }

        .sidebar-menu i {
            margin-right: 12px;
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        /* Content Area */
        .content-area {
            margin-left: 250px;
            flex: 1;
            padding: 40px;
            margin-top: 50px;
        }

        .section-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .section-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .section-title {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        .section-title i {
            margin-right: 12px;
            font-size: 26px;
        }

        /* Profile Section */
        .profile-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .profile-item {
            display: flex;
            font-size: 16px;
        }

        .profile-label {
            font-weight: 600;
            color: #333;
            min-width: 180px;
        }

        .profile-value {
            color: #666;
        }

        /* Plan Section */
        .plan-info {
            margin-bottom: 20px;
        }

        .plan-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .plan-name .plan-type {
            color: #00bcd4;
        }

        .upgrade-btn {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            color: #fff;
            border: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
        }

        .upgrade-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 107, 53, 0.4);
        }

        /* Edit Info Section */
        .edit-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .edit-link {
            color: #00bcd4;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .edit-link:hover {
            color: #0097a7;
            transform: translateX(5px);
        }

        /* Social Section */
        .social-links {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .social-link {
            display: flex;
            align-items: center;
            color: #00bcd4;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .social-link:hover {
            color: #0097a7;
            transform: translateX(5px);
        }

        .social-link i {
            margin-right: 10px;
            font-size: 20px;
        }

        .bi-twitter {
            color: #1DA1F2;
        }

        .bi-instagram {
            color: #E4405F;
        }

        .bi-facebook {
            color: #1877F2;
        }

        /* Support Section */
        .support-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .support-link {
            color: #00bcd4;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .support-link:hover {
            color: #0097a7;
            transform: translateX(5px);
        }

        .contact-info {
            margin-top: 12px;
            font-size: 16px;
            color: #666;
        }

        .contact-info a {
            color: #00bcd4;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        /* Privacy Section */
        .privacy-links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .privacy-link {
            color: #00bcd4;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .privacy-link:hover {
            color: #0097a7;
            transform: translateX(5px);
        }

        /* Watermark */
        .watermark {
            position: fixed;
            bottom: 20px;
            right: 20px;
            color: rgba(0, 0, 0, 0.2);
            font-size: 13px;
            text-align: right;
            line-height: 1.4;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
            }

            .content-area {
                margin-left: 0;
                padding: 20px;
            }

            .main-container {
                flex-direction: column;
            }

            .profile-item {
                flex-direction: column;
                gap: 5px;
            }

            .profile-label {
                min-width: auto;
            }
        }
    </style>
</head>

<body>
    <?php include __DIR__ . '/../layouts/header.php'; ?>

    <div class="main-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="#profile" class="active">
                        <i class="bi bi-person-fill"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a href="#edit-info">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit Info</span>
                    </a>
                </li>
                <li>
                    <a href="#social">
                        <i class="bi bi-chat-dots"></i>
                        <span>Social</span>
                    </a>
                </li>
                <li>
                    <a href="#support">
                        <i class="bi bi-question-circle"></i>
                        <span>Support</span>
                    </a>
                </li>
                <li>
                    <a href="#privacy">
                        <i class="bi bi-lock"></i>
                        <span>Privacy</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main class="content-area">
            <!-- Breadcrumb -->
            <div class="bg-body-tertiary border rounded px-3 py-2 mb-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '>';">
                        <ol class="breadcrumb mb-0 small">
                            <li class="breadcrumb-item"><a class="text-decoration-none" href="/">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Profile</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Profile Section -->
            <section id="profile" class="section-card">
                <h2 class="section-title">
                    <i class="bi bi-person-fill"></i>
                    Profile
                </h2>
                <div class="profile-info">
                    <?php $user = Auth::getInstance()->user(); ?>
                    <div class="profile-item">
                        <span class="profile-label">Username:</span>
                        <span class="profile-value"><?= $user->getUsername() ?></span>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Email:</span>
                        <span class="profile-value"><?= $user->getEmail() ?></span>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Dashboard Layout:</span>
                        <span class="profile-value">Teacher</span>
                    </div>
                    <div class="profile-item">
                        <span class="profile-label">Joined:</span>
                        <span class="profile-value"><?= DateUtils::formatPrettyDate((string)$user->getCreatedAt()) ?></span>
                    </div>
                </div>
            </section>

            <!-- Edit Info Section -->
            <section id="edit-info" class="section-card">
                <h2 class="section-title">
                    <i class="bi bi-pencil-square"></i>
                    Edit Info
                </h2>
                <div class="edit-links">
                    <a href="#change-name" class="edit-link" data-action="change_name">Change Name</a>
                    <a href="#reset-password" class="edit-link" data-action="reset_password">Request Password Reset</a>
                    <a href="#change-email" class="edit-link" data-action="change_email">Change Email</a>
                </div>
            </section>

            <!-- Social Section -->
            <section id="social" class="section-card">
                <h2 class="section-title">
                    <i class="bi bi-chat-dots"></i>
                    Social
                </h2>
                <div class="social-links">
                    <a href="https://twitter.com/PlayQuiz" target="_blank" class="social-link">
                        <i class="bi bi-twitter"></i>
                        Twitter (@PlayQuiz)
                    </a>
                    <a href="https://instagram.com/PlayQuiz" target="_blank" class="social-link">
                        <i class="bi bi-instagram"></i>
                        Instagram (@PlayQuiz)
                    </a>
                    <a href="https://facebook.com/PlayQuiz" target="_blank" class="social-link">
                        <i class="bi bi-facebook"></i>
                        Facebook (@PlayQuiz)
                    </a>
                </div>
            </section>

            <!-- Support Section -->
            <section id="support" class="section-card">
                <h2 class="section-title">
                    <i class="bi bi-question-circle"></i>
                    Support
                </h2>
                <div class="support-links">
                    <a href="#help-center" class="support-link">View Our Help Center</a>
                    <a href="#faq" class="support-link">Frequently Asked Questions</a>
                </div>
                <div class="contact-info">
                    Contact us at: <a href="mailto:contact-us@quiz.com">contact-us@quiz.com</a>
                </div>
            </section>

            <!-- Privacy Section -->
            <section id="privacy" class="section-card">
                <h2 class="section-title">
                    <i class="bi bi-lock"></i>
                    Privacy
                </h2>
                <div class="privacy-links">
                    <a href="#privacy-policy" class="privacy-link">Privacy Policy</a>
                    <a href="#terms" class="privacy-link">Terms of Service</a>
                </div>
            </section>
        </main>
    </div>

    <!-- Watermark -->
    <div class="watermark">
        Activate Windows<br>
        <span style="font-size: 11px;">Go to Settings to activate Windows.</span>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            document.querySelector('.join-game-section').classList.add('hide');
            document.querySelector('.btn-search').classList.add('hide');
            $('.sidebar-menu a, .edit-link, .support-link, .privacy-link').on('click', function(e) {
                if ($(this).data('action')) {
                    return;
                }
                if (this.hash !== '') {
                    e.preventDefault();
                    const hash = this.hash;

                    const $target = $(hash);
                    if ($target.length) {
                        // Instant jump (no animation)
                        window.scrollTo({ top: $target.offset().top - 20, left: 0, behavior: 'auto' });
                        // Update active menu item immediately
                        $('.sidebar-menu a').removeClass('active');
                        $('.sidebar-menu a[href="' + hash + '"]').addClass('active');
                    }
                }
            });

            // Highlight active section on scroll
            $(window).on('scroll', function() {
                const scrollPos = $(window).scrollTop() + 300;

                $('.section-card').each(function() {
                    const sectionTop = $(this).offset().top;
                    const sectionBottom = sectionTop + $(this).outerHeight();
                    const sectionId = $(this).attr('id');

                    if (scrollPos >= sectionTop && scrollPos < sectionBottom) {
                        $('.sidebar-menu a').removeClass('active');
                        $('.sidebar-menu a[href="#' + sectionId + '"]').addClass('active');
                    }
                });
            });

            // Upgrade button animation
            $('.upgrade-btn').on('click', function() {
                $(this).html('<i class="bi bi-check-circle me-2"></i>Processing...');

                setTimeout(() => {
                    alert('Upgrade feature coming soon!');
                    $(this).html('Upgrade Now!');
                }, 1000);
            });

            // Add hover effect to cards
            $('.section-card').hover(
                function() {
                    $(this).css('transform', 'translateY(-2px)');
                },
                function() {
                    $(this).css('transform', 'translateY(0)');
                }
            );

            // Link click effects
            $('.edit-link, .social-link, .support-link, .privacy-link').on('click', function(e) {
                if ($(this).attr('target') !== '_blank' && !$(this).attr('href').startsWith('#')) {
                    e.preventDefault();

                    const linkText = $(this).text();
                    alert('Navigating to: ' + linkText);
                }
            });

            // Social links external indicator
            $('.social-link').each(function() {
                if ($(this).attr('target') === '_blank') {
                    $(this).append(' <i class="bi bi-box-arrow-up-right" style="font-size: 12px; margin-left: 5px;"></i>');
                }
            });

            // Add animation to sections on load
            $('.section-card').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(20px)'
                });

                setTimeout(() => {
                    $(this).animate({
                        'opacity': '1'
                    }, 500).css('transform', 'translateY(0)');
                }, index * 100);
            });

            // Open reusable modal for edit actions
            $('.edit-link[data-action]').on('click', function(e) {
                e.preventDefault();
                const action = $(this).data('action');
                const modalEl = document.getElementById('commonActionModal');
                const modalTitle = modalEl.querySelector('.modal-title');
                const modalDesc = modalEl.querySelector('.modal-desc');
                const modalBody = modalEl.querySelector('.modal-body');
                const submitBtn = modalEl.querySelector('.modal-submit');

                // Default state
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submit';

                if (action === 'change_name') {
                    modalTitle.textContent = 'Change Name';
                    modalDesc.textContent = 'Enter your new username';
                    modalBody.innerHTML = '<input type="text" name="username" class="form-control form-control-lg" placeholder="Username">';
                    const inp = modalBody.querySelector('input[name="username"]');
                    inp.addEventListener('input', function() { submitBtn.disabled = this.value.trim().length === 0; });
                } else if (action === 'reset_password') {
                    modalTitle.textContent = 'Request Password Reset';
                    modalDesc.textContent = 'Enter your email to receive reset instructions';
                    modalBody.innerHTML = '<input type="email" name="email" class="form-control form-control-lg" placeholder="Email">';
                    const inp = modalBody.querySelector('input[name="email"]');
                    inp.addEventListener('input', function() { submitBtn.disabled = this.value.trim().length === 0; });
                    submitBtn.textContent = 'Send';
                } else if (action === 'change_email') {
                    modalTitle.textContent = 'Change Email';
                    modalDesc.textContent = 'Enter your new email';
                    modalBody.innerHTML = '<input type="email" name="new_email" class="form-control form-control-lg" placeholder="Email">';
                    const inp = modalBody.querySelector('input[name="new_email"]');
                    inp.addEventListener('input', function() { submitBtn.disabled = this.value.trim().length === 0; });
                }

                const bsModal = new bootstrap.Modal(modalEl);
                bsModal.show();
            });
        });
    </script>
</body>

<!-- Reusable Common Modal -->
<div class="modal fade" id="commonActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 justify-content-center">
                <h5 class="modal-title fw-bold"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="px-4 pb-2 text-secondary text-center modal-desc"></div>
            <div class="modal-body px-4 pt-0"></div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-secondary modal-submit" disabled>Submit</button>
            </div>
        </div>
    </div>
</div>

</html>