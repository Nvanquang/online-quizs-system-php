<?php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Đăng Ký'; ?></title>
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
            padding: 20px;
        }

        .register-container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Success Message */
        .success-message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .success-message .icon {
            margin-right: 8px;
            font-size: 16px;
        }

        /* Register Card */
        .register-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .register-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        /* Form Grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .required {
            color: #dc3545;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            background-color: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: white;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 40px;
        }

        .form-select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .password-input-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 18px;
            padding: 4px;
        }

        .password-toggle:hover {
            color: #333;
        }

        /* Register Button */
        .register-button {
            width: 200px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .register-button:hover {
            background-color: #0056b3;
        }

        .register-button:active {
            transform: translateY(1px);
        }

        /* Divider */
        .divider {
            text-align: center;
            margin: 25px 0;
            color: #666;
            font-size: 14px;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background-color: #e0e0e0;
            z-index: 1;
        }

        .divider span {
            background-color: white;
            padding: 0 15px;
            position: relative;
            z-index: 2;
        }

        /* Login Link */
        .login-link {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Error Message */
        .error-message {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .register-button {
                width: 100%;
            }
        }

        /* Loading State */
        .register-button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <!-- Success Message -->
        <?php if (isset($success) && $success): ?>
            <div class="success-message">
                <span class="icon">✓</span>
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (isset($error) && $error): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Register Card -->
        <div class="register-card">
            <h1 class="register-title">Đăng Ký Tài Khoản</h1>

            <form method="POST" action="/auth/register" id="registerForm">
                <!-- CSRF Token -->
                <?php echo CSRFMiddleware::getTokenField(); ?>

                <div class="form-grid">
                    <!-- Username Field -->
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <span class="required">*</span> User name
                        </label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            class="form-input"
                            placeholder="Nhập tên đăng nhập"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                            required
                            autocomplete="username">
                    </div>

                    <!-- Full Name Field -->
                    <div class="form-group">
                        <label for="full_name" class="form-label">
                            <span class="required">*</span> Họ và tên
                        </label>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            class="form-input"
                            placeholder="Nhập họ và tên"
                            value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
                            required
                            autocomplete="name">
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <span class="required">*</span> Email
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            placeholder="Nhập email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required
                            autocomplete="email">
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <span class="required">*</span> Mật khẩu
                        </label>
                        <div class="password-input-container">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input"
                                placeholder="Nhập mật khẩu"
                                required
                                autocomplete="new-password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                👁️
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">
                            <span class="required">*</span> Nhập lại mật khẩu
                        </label>
                        <div class="password-input-container">
                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                class="form-input"
                                placeholder="Nhập lại mật khẩu"
                                required
                                autocomplete="new-password">
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                👁️
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Register Button -->
                <button type="submit" class="register-button" id="registerButton">
                    <span id="buttonText">Đăng ký</span>
                    <span id="loadingSpinner" class="loading" style="display: none;"></span>
                </button>
            </form>

            <!-- Divider -->
            <div class="divider">
                <span>Or</span>
            </div>

            <!-- Login Link -->
            <div class="login-link">
                Đã có tài khoản ? <a href="/auth/login">Đăng Nhập</a>
            </div>
        </div>
    </div>

    
    <?php if (isset($error) && $error): ?>
        <script>
            window._notifyQueue = window._notifyQueue || [];
            window._notifyQueue.push({ message: <?php echo json_encode($error); ?>, type: 'error' });
        </script>
    <?php endif; ?>

    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleButton = passwordInput.parentNode.querySelector('.password-toggle');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
            }
        }

        // Form submission with loading state
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const button = document.getElementById('registerButton');
            const buttonText = document.getElementById('buttonText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // Show loading state
            button.disabled = true;
            buttonText.style.display = 'none';
            loadingSpinner.style.display = 'inline-block';
        });

        // Auto-focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.querySelector('.form-input');
            if (firstInput) {
                firstInput.focus();
            }
        });

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            if (password !== confirmPassword) {
                this.setCustomValidity('Mật khẩu xác nhận không khớp');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>

    <script src="../../../public/js/notify.js"></script>
</body>

</html>