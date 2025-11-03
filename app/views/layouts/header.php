<!-- includes/header.php -->
<header class="header-main">
    <nav class="navbar navbar-expand navbar-light bg-white">
        <div class="container-fluid px-4 d-flex align-items-center justify-content-between flex-nowrap">
            <!-- Logo -->
            <a class="logo" href="/">
                <img src="../../../public/images/logo/quiz-multicolor.svg" alt="Quiz.com" />
            </a>

            <!-- Join Game Section (giữa, flex-grow để chiếm space, shrink khi hẹp) -->
            <div class="join-game-section d-flex justify-content-center align-items-center mx-auto flex-grow-1">
                <span class="join-text">Join Game? Enter PIN:</span>
                <input type="text" class="form-control join-input" placeholder="123 456" maxlength="7">
            </div>

            <!-- Right Side (push phải) -->
            <div class="d-flex align-items-center ms-auto">
                <button class="btn btn-search me-3">
                    <i class="fas fa-search"></i>
                </button>
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
        </div>
    </nav>

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
</header>