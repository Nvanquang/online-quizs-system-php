<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Quiz System'; ?></title>
    
    <!-- Bootstrap CSS (optional) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
        }
        
        .error-message {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
    
    <?php if (isset($additional_css)): ?>
        <?php echo $additional_css; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="bg-light border-bottom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center py-3">
                <h1 class="h4 mb-0">
                    <a href="/" class="text-decoration-none text-primary">Quiz System</a>
                </h1>
                <nav class="nav">
                    <?php if (isset($user) && $user): ?>
                        <span class="nav-link">Xin chào, <strong><?php echo htmlspecialchars($user->getFullName() ?: $user->getUsername()); ?></strong></span>
                        <a href="/user/profile" class="nav-link">Hồ sơ</a>
                        <a href="/auth/logout" class="nav-link">Đăng xuất</a>
                    <?php else: ?>
                        <a href="/auth/login" class="nav-link">Đăng nhập</a>
                        <a href="/auth/register" class="nav-link">Đăng ký</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mt-4">
        <?php if (isset($error) && $error): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success) && $success): ?>
            <div class="success-message">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($content)): ?>
            <?php echo $content; ?>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-light border-top mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Quiz System. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="/about" class="text-decoration-none">Giới thiệu</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Auto-hide messages after 5 seconds
        setTimeout(function() {
            const messages = document.querySelectorAll('.error-message, .success-message');
            messages.forEach(function(message) {
                message.style.transition = 'opacity 0.5s';
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 500);
            });
        }, 5000);
    </script>
    
    <?php if (isset($additional_js)): ?>
        <?php echo $additional_js; ?>
    <?php endif; ?>
</body>
</html>