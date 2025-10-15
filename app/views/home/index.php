<script src="/public/js/notify.js"></script>
<?php if (isset($login_success) && $login_success): ?>
    <script>
        notifySuccess(<?php echo json_encode($login_success); ?>);
    </script>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <?php if (isset($user) && $user): ?>
            <!-- User is logged in -->
            <div class="text-center mb-5">
                <h1 class="display-4">Chào mừng trở lại!</h1>
                <p class="lead">Hãy tham gia các quiz thú vị và cạnh tranh với bạn bè</p>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="card-title mb-0">Thông tin tài khoản</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <strong>Tên đăng nhập:</strong><br>
                                <span class="text-muted"><?php echo htmlspecialchars($user->getUsername()); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <strong>Email:</strong><br>
                                <span class="text-muted"><?php echo htmlspecialchars($user->getEmail()); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <strong>Họ tên:</strong><br>
                                <span class="text-muted"><?php echo htmlspecialchars($user->getFullName() ?: 'Chưa cập nhật'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <strong>Tổng điểm:</strong><br>
                                <span class="text-primary fs-5"><?php echo number_format($user->getTotalPoints()); ?> điểm</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <strong>Số game đã chơi:</strong><br>
                                <span class="text-info fs-5"><?php echo $user->getGamesPlayed(); ?> game</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3">
                                <strong>Vai trò:</strong><br>
                                <span class="badge bg-<?php echo $user->isAdmin() ? 'danger' : 'primary'; ?>">
                                    <?php echo $user->isAdmin() ? 'Quản trị viên' : 'Người dùng'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mb-5">
                <a href="/game/join" class="btn btn-primary btn-lg me-3">Tham gia game</a>
                <a href="/user/profile" class="btn btn-success btn-lg me-3">Cập nhật hồ sơ</a>
                <a href="/user/history" class="btn btn-info btn-lg me-3">Xem lịch sử</a>
                <?php if ($user->isAdmin()): ?>
                    <a href="/admin/dashboard" class="btn btn-danger btn-lg">Quản trị</a>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- Guest user -->
            <div class="text-center">
                <div class="card">
                    <div class="card-body py-5">
                        <h1 class="display-4 mb-4">Chào mừng đến với Quiz System!</h1>
                        <p class="lead mb-4">Tham gia các quiz thú vị, cạnh tranh với bạn bè và nâng cao kiến thức của bạn.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="/auth/login" class="btn btn-primary btn-lg">Đăng nhập</a>
                            <a href="/auth/register" class="btn btn-success btn-lg">Đăng ký ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Features -->
        <div class="row mt-5">
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="display-1 mb-3">🎯</div>
                        <h3 class="card-title">Quiz Đa Dạng</h3>
                        <p class="card-text">Tham gia các quiz về nhiều chủ đề khác nhau, từ kiến thức chung đến chuyên môn.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="display-1 mb-3">🏆</div>
                        <h3 class="card-title">Bảng Xếp Hạng</h3>
                        <p class="card-text">Cạnh tranh với người chơi khác và xem thứ hạng của bạn trên bảng xếp hạng toàn cầu.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <div class="display-1 mb-3">📊</div>
                        <h3 class="card-title">Thống Kê Chi Tiết</h3>
                        <p class="card-text">Theo dõi tiến độ học tập và xem thống kê chi tiết về kết quả của bạn.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>