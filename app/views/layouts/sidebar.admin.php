<!-- Sidebar Start -->
<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a href="/" class="navbar-brand mx-4 mb-3">
            <img src="../../../public/images/logo/quiz-multicolor.svg" alt="Quiz.com" style="height:32px;width:auto;display:inline-block;" />
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle" src="../../../public/uploads/avatars/<?php echo Auth::getInstance()->user()->getAvatarUrl(); ?>" alt="" style="width: 40px; height: 40px;">
                <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0"><?php echo Auth::getInstance()->user()->getUsername(); ?></h6>
                <span>Admin</span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            <?php
            $current = $_SERVER['REQUEST_URI'];
            ?>

            <a href="/admin/dashboard" class="nav-item nav-link <?= str_contains($current, '/admin/dashboard') ? 'active' : '' ?>">
                <i class="fa fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="/admin/users" class="nav-item nav-link <?= str_contains($current, '/admin/users') ? 'active' : '' ?>">
                <i class="fa fa-users me-2"></i>Users
            </a>
            <a href="/admin/quizzes" class="nav-item nav-link <?= str_contains($current, '/admin/quizzes') ? 'active' : '' ?>">
                <i class="fa fa-book me-2"></i>Quizzes
            </a>
            <a href="/admin/questions" class="nav-item nav-link <?= str_contains($current, '/admin/questions') ? 'active' : '' ?>">
                <i class="fa fa-question me-2"></i>Questions
            </a>

        </div>
    </nav>
</div>
<!-- Sidebar End -->