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
                <span class="join-text">Tham gia Game? Nhập PIN:</span>
                <input type="text" class="form-control join-input" placeholder="123 456" maxlength="7">
            </div>

            <!-- Right Side (push phải, chứa toggle search và search input, user dropdown) -->
            <div class="right-section d-flex align-items-center ms-auto">
                <div class="search-wrapper">
                    <button class="btn btn-search-toggle me-3 d-block" id="searchToggle">
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="search-section d-none d-flex align-items-center me-3" id="searchSection">
                        <div class="search-container position-relative">
                            <input type="text" class="form-control search-input" placeholder="Tìm kiếm..." id="searchInput">
                            <button class="btn btn-search-icon position-absolute" type="submit" id="searchSubmit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php $auth = Auth::getInstance(); ?>
                <?php if ($auth->check()): ?>
                    <?php $user = $auth->user(); ?>
                    <div class="dropdown">
                        <a href="#" class="d-inline-flex align-items-center p-0 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="../../../public/uploads/avatars/<?= htmlspecialchars($user->getAvatarUrl()) ?>" alt="Avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
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
                    <a href="/auth/login"><button class="btn btn-signin"><i class="bi bi-box-arrow-in-right"></i> Đăng nhập</button></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchToggle = document.getElementById('searchToggle');
    const searchSection = document.getElementById('searchSection');
    const searchInput = document.getElementById('searchInput');

    // Toggle search on button click
    searchToggle.addEventListener('click', function() {
        const isHidden = searchSection.classList.contains('d-none');
        if (isHidden) {
            // Show search: hide toggle, show search section
            searchToggle.classList.add('d-none');
            searchSection.classList.remove('d-none');
            searchInput.focus(); // Auto-focus input
        } else {
            // Hide search: show toggle, hide search section
            searchToggle.classList.remove('d-none');
            searchSection.classList.add('d-none');
        }
    });

    // Handle search submission (Enter key or icon click)
    function handleSearch() {
        const query = searchInput.value.trim();
        if (query) {
            // Redirect with GET to /search?q=query
            window.location.href = `/search?query=${encodeURIComponent(query)}`;
        }
    }

    // Enter key on input
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            handleSearch();
        }
    });

    // Click on search icon
    document.getElementById('searchSubmit').addEventListener('click', handleSearch);

    // Optional: Hide search when clicking outside (but not on dropdown/user area)
    document.addEventListener('click', function(e) {
        const rightSection = document.querySelector('.right-section');
        if (!rightSection.contains(e.target)) {
            // If search is open and click outside right-section, close it
            if (!searchSection.classList.contains('d-none')) {
                searchToggle.classList.remove('d-none');
                searchSection.classList.add('d-none');
            }
        }
    });
});
</script>