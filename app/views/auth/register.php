<?php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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

        .form-input.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
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

        /* Field Error Message */
        .error-message-field {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
            min-height: 18px;
        }

        .error-message-field.show {
            display: block;
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

            <form method="POST" action="/auth/register" id="registerForm" novalidate>
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
                        <div id="username-error" class="error-message-field"></div>
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
                        <div id="full_name-error" class="error-message-field"></div>
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
                        <div id="email-error" class="error-message-field"></div>
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
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="password-error" class="error-message-field"></div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            <span class="required">*</span> Nhập lại mật khẩu
                        </label>
                        <div class="password-input-container">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-input"
                                placeholder="Nhập lại mật khẩu"
                                required
                                autocomplete="new-password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="password_confirmation-error" class="error-message-field"></div>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        <?php if (!empty($_SESSION['errors'])) : ?>
            toastr.error(<?= json_encode($_SESSION['errors'], JSON_UNESCAPED_UNICODE); ?>);
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        // Utility function to show/hide error and update input border
        function setFieldError(inputId, message) {
            const $input = $('#' + inputId);
            const $errorEl = $('#' + inputId + '-error');
            if (message) {
                $input.addClass('error');
                $errorEl.text(message).addClass('show');
            } else {
                $input.removeClass('error');
                $errorEl.text('').removeClass('show');
            }
        }

        // Validate Username
        function validateUsername() {
            const val = $('#username').val().trim();
            if (!val) {
                setFieldError('username', 'Trường này là bắt buộc.');
                return false;
            }
            if (val.length < 3) {
                setFieldError('username', 'Tên đăng nhập phải ít nhất 3 ký tự.');
                return false;
            }
            if (val.length > 50) {
                setFieldError('username', 'Tên đăng nhập không được vượt quá 50 ký tự.');
                return false;
            }
            setFieldError('username', '');
            return true;
        }

        // Validate Full Name
        function validateFullName() {
            const val = $('#full_name').val().trim();
            if (!val) {
                setFieldError('full_name', 'Trường này là bắt buộc.');
                return false;
            }
            if (val.length < 5) {
                setFieldError('full_name', 'Họ và tên phải ít nhất 5 ký tự.');
                return false;
            }
            if (val.length > 50) {
                setFieldError('full_name', 'Họ và tên không được vượt quá 50 ký tự.');
                return false;
            }
            setFieldError('full_name', '');
            return true;
        }

        // Validate Email
        function validateEmail() {
            const val = $('#email').val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!val) {
                setFieldError('email', 'Trường này là bắt buộc.');
                return false;
            }
            if (!emailRegex.test(val)) {
                setFieldError('email', 'Email không hợp lệ.');
                return false;
            }
            setFieldError('email', '');
            return true;
        }

        // Validate Password
        function validatePassword() {
            const val = $('#password').val();
            if (!val) {
                setFieldError('password', 'Trường này là bắt buộc.');
                return false;
            }
            if (val.length < 6) {
                setFieldError('password', 'Mật khẩu phải ít nhất 6 ký tự.');
                return false;
            }
            setFieldError('password', '');
            return true;
        }

        // Validate Confirm Password
        function validateConfirmPassword() {
            const password = $('#password').val();
            const confirmVal = $('#password_confirmation').val();
            if (!confirmVal) {
                setFieldError('password_confirmation', 'Trường này là bắt buộc.');
                return false;
            }
            if (password !== confirmVal) {
                setFieldError('password_confirmation', 'Mật khẩu xác nhận không khớp.');
                return false;
            }
            setFieldError('password_confirmation', '');
            return true;
        }

        // Toggle password visibility
        function togglePassword(inputId) {
            const $passwordInput = $('#' + inputId);
            const $toggleButton = $passwordInput.parent().find('.password-toggle');

            if ($passwordInput.attr('type') === 'password') {
                $passwordInput.attr('type', 'text');
                $toggleButton.html('<i class="bi bi-eye-slash"></i>');
            } else {
                $passwordInput.attr('type', 'password');
                $toggleButton.html('<i class="bi bi-eye"></i>');
            }
        }

        // Real-time validation on blur
        $('#username').on('blur', validateUsername);
        $('#full_name').on('blur', validateFullName);
        $('#email').on('blur', validateEmail);
        $('#password').on('blur', validatePassword);
        $('#password_confirmation').on('blur', validateConfirmPassword);

        // Password confirmation validation on input (existing + enhanced)
        $('#password, #password_confirmation').on('input', function() {
            validatePassword(); // Re-validate password on change
            validateConfirmPassword(); // Re-validate confirmation
        });

        // Form submission with loading state and full validation
        $('#registerForm').on('submit', function(e) {
            let isValid = true;

            isValid &= validateUsername();
            isValid &= validateFullName();
            isValid &= validateEmail();
            isValid &= validatePassword();
            isValid &= validateConfirmPassword();

            if (!isValid) {
                e.preventDefault();
                toastr.error('Vui lòng kiểm tra lại các trường thông tin.');
                return false;
            }

            const $button = $('#registerButton');
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
    </script>

</body>

</html>