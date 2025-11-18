<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DASHMIN - Bootstrap Admin Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <meta name="csrf-token" content="<?= CSRFMiddleware::getToken() ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../../../public/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../../../public/css/style.css" rel="stylesheet">
    <link href="../../../public/css/toastr-override.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Đang tải...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sidebar Start -->
        <?php include __DIR__ . '/../layouts/sidebar.admin.php'; ?>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <?php include __DIR__ . '/../layouts/navbar.admin.php'; ?>
            <!-- Navbar End -->

            <!-- Recent Sales Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Danh sách người dùng</h6>
                        <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()">
                            <i class="bi bi-plus-circle me-2"></i>Tạo mới người dùng
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0" id="usersTable">
                            <thead>
                                <tr class="text-dark">
                                    <th scope="col">ID</th>
                                    <th scope="col">Tên đăng nhập</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Họ và tên</th>
                                    <th scope="col">Ngày tạo</th>
                                    <th scope="col">Ngày cập nhật</th>
                                    <th scope="col">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr data-user-id="<?php echo $user->getId(); ?>">
                                        <td><?php echo $user->getId() ?></td>
                                        <td><?php echo $user->getUserName() ?></td>
                                        <td><?php echo $user->getEmail() ?></td>
                                        <td><?php echo $user->getFullName() ?></td>
                                        <td><?php echo $user->getCreatedAt() ?></td>
                                        <td><?php echo $user->getUpdatedAt() ?></td>
                                        <td>
                                            <a class="update-btn"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Cập nhật người dùng"
                                                data-user='<?php
                                                            $userData = [
                                                                'id' => $user->getId(),
                                                                'username' => $user->getUserName(),
                                                                'email' => $user->getEmail(),
                                                                'full_name' => $user->getFullName(),
                                                                'is_admin' => $user->isAdmin() === 1 ? 1 : 0,
                                                            ];
                                                            echo json_encode($userData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                                            ?>'>
                                                <i class="bi bi-pencil-fill me-2"></i>
                                            </a>
                                            <a class="delete-btn"
                                                style="color: red"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Xóa người dùng"
                                                data-user-id="<?php echo $user->getId(); ?>"
                                                data-username="<?php echo htmlspecialchars($user->getUserName()); ?>">
                                                <i class="bi bi-trash3-fill me-2"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Pagination Start -->
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center mt-3">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&per_page=<?php echo $per_page; ?>">Trước</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&per_page=<?php echo $per_page; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&per_page=<?php echo $per_page; ?>">Sau</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <!-- Pagination End -->
            <!-- Recent Sales End -->

            <!-- Footer Start -->
            <?php include __DIR__ . '/../layouts/footer.admin.php'; ?>
            <!-- Footer End -->
        </div>
        <!-- Content End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- Include Modal User -->
    <?php include __DIR__ . '/create-update/modalUser.php'; ?>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../public/lib/waypoints/waypoints.min.js"></script>

    <script>
        <?php if (!empty($_SESSION['errors'])) : ?>
            toastr.error(<?= json_encode($_SESSION['errors'], JSON_UNESCAPED_UNICODE); ?>);
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>
    </script>

    <!-- Template Javascript -->
    <script src="../../../public/js/main_2.js"></script>

    <!-- User Management API Script -->
    <script src="../../../public/js/user-admin.js"></script>
</body>

</html>