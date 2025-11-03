<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <title><?php echo $title ?? 'Đăng Nhập'; ?></title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
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

        /* Login Card */
        .login-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .login-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            text-align: center;
            margin-bottom: 30px;
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

        /* Login Button */
        .login-button {
            width: 100%;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .login-button:hover {
            background-color: #0056b3;
        }

        .login-button:active {
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

        /* Register Link */
        .register-link {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .register-link a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
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
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }

            .login-title {
                font-size: 24px;
            }
        }

        /* Loading State */
        .login-button:disabled {
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
    <div class="login-container">
        <!-- Error Message -->
        <?php if (isset($error) && $error): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Login Card -->
        <div class="login-card">
            <h1 class="login-title">Đăng Nhập</h1>

            <form method="POST" action="/auth/login" id="loginForm">
                <!-- CSRF Token -->
                <?php echo CSRFMiddleware::getTokenField(); ?>

                <!-- Email Field -->
                <div class="form-group">
                    <label for="username" class="form-label">
                        <span class="required">*</span> Email
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-input"
                        placeholder="Nhập email"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        required
                        autocomplete="username">
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
                            autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Login Button -->
                <button type="submit" class="login-button" id="loginButton">
                    <span id="buttonText">Đăng nhập</span>
                    <span id="loadingSpinner" class="loading" style="display: none;"></span>
                </button>
            </form>

            <!-- Divider -->
            <div class="divider">
                <span>Or</span>
            </div>

            <!-- Register Link -->
            <div class="register-link">
                Chưa có tài khoản ? <a href="/auth/register">Đăng Ký</a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <?php if (isset($register_success) && $register_success): ?>
        <script>
            toastr.success(<?php echo json_encode($register_success); ?>);
        </script>
    <?php endif; ?>
    
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            function togglePassword() {
                const $passwordInput = $('#password');
                const $toggleButton = $('.password-toggle');

                if ($passwordInput.attr('type') === 'password') {
                    $passwordInput.attr('type', 'text');
                    $toggleButton.html('<i class="bi bi-eye-slash"></i>');
                } else {
                    $passwordInput.attr('type', 'password');
                    $toggleButton.html('<i class="bi bi-eye"></i>');
                }
            }

            // Form submission with loading state
            $('#loginForm').on('submit', function(e) {
                const $button = $('#loginButton');
                const $buttonText = $('#buttonText');
                const $loadingSpinner = $('#loadingSpinner');

                // Show loading state
                $button.prop('disabled', true);
                $buttonText.hide();
                $loadingSpinner.css('display', 'inline-block');
            });

            // Auto-focus on first input
            $(function() {
                const $firstInput = $('.form-input').first();
                if ($firstInput.length) {
                    $firstInput.trigger('focus');
                }
            });
        });
    </script>
</body>

</html>