<!-- includes/header.php -->
<header class="header-main">
    <nav class="navbar navbar-expand navbar-light bg-white fixed-top">
        <div class="container-fluid px-4 d-flex align-items-center justify-content-between flex-nowrap">
            <!-- Logo -->
            <a class="logo" href="/">
                <img src="../../../public/images/logo/quiz-multicolor.svg" alt="Quiz.com" style="height:32px;width:auto;display:inline-block;" />
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
                            <img src="../../../public/uploads/avatars/<?= htmlspecialchars($user->getAvatarUrl()) ?>" alt="Avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($user->isAdmin()): ?>
                                <li><a class="dropdown-item" href="/admin/dashboard">Admin</a></li>
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
                    <a href="/auth/login"><button class="btn btn-signin"><i class="bi bi-box-arrow-in-right"></i> Đăng nhập</button></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    
</header>