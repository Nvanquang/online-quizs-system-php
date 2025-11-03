<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/ico" href="../../../public/images/logo/favicon.ico">
    <title>404 - Page Not Found | QUIZ.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-pink: #FFB4C8;
            --color-yellow: #FFE180;
            --color-green: #B8E986;
            --color-blue: #89CFF0;
            --color-teal: #2D5F5D;
            --color-dark: #1a1a1a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="50" font-size="20" fill="rgba(255,255,255,0.05)">?</text></svg>');
            background-size: 100px;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { background-position: 0 0; }
            100% { background-position: 100px 100px; }
        }

        .error-container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
        }

        .error-number {
            font-size: 12rem;
            font-weight: bold;
            color: white;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .error-number .zero {
            display: inline-block;
            position: relative;
        }

        .error-number .zero::before {
            content: '?';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 4rem;
            color: var(--color-yellow);
            animation: spin 3s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .error-title {
            font-size: 3rem;
            font-weight: bold;
            color: white;
            margin-bottom: 1rem;
            animation: fadeInUp 0.6s ease;
        }

        .error-subtitle {
            font-size: 1.5rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease;
        }

        .error-message {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.8);
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 1s ease;
        }

        .btn-group-404 {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1.2s ease;
        }

        .btn-home,
        .btn-search {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-home {
            background: var(--color-green);
            color: white;
        }

        .btn-home:hover {
            background: white;
            color: var(--color-green);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .btn-search {
            background: var(--color-pink);
            color: white;
        }

        .btn-search:hover {
            background: white;
            color: var(--color-pink);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .floating-icons {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-icon {
            position: absolute;
            font-size: 3rem;
            opacity: 0.2;
            animation: floatIcon 15s infinite;
        }

        .floating-icon:nth-child(1) {
            left: 10%;
            top: 20%;
            animation-delay: 0s;
            color: var(--color-pink);
        }

        .floating-icon:nth-child(2) {
            left: 80%;
            top: 30%;
            animation-delay: 2s;
            color: var(--color-yellow);
        }

        .floating-icon:nth-child(3) {
            left: 20%;
            top: 70%;
            animation-delay: 4s;
            color: var(--color-green);
        }

        .floating-icon:nth-child(4) {
            left: 70%;
            top: 80%;
            animation-delay: 6s;
            color: var(--color-blue);
        }

        @keyframes floatIcon {
            0%, 100% { 
                transform: translateY(0) rotate(0deg);
                opacity: 0.2;
            }
            50% { 
                transform: translateY(-30px) rotate(180deg);
                opacity: 0.4;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo Animation */
        .logo-404 {
            display: inline-block;
            margin-bottom: 2rem;
            animation: fadeInDown 0.6s ease;
        }

        .logo-404 span {
            font-size: 3rem;
            font-weight: bold;
            display: inline-block;
            animation: colorChange 3s infinite;
        }

        .logo-404 .q { color: var(--color-pink); animation-delay: 0s; }
        .logo-404 .u { color: var(--color-yellow); animation-delay: 0.2s; }
        .logo-404 .i { color: var(--color-green); animation-delay: 0.4s; }
        .logo-404 .z { color: var(--color-blue); animation-delay: 0.6s; }

        @keyframes colorChange {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .error-number {
                font-size: 8rem;
            }

            .error-number .zero::before {
                font-size: 3rem;
            }

            .error-title {
                font-size: 2rem;
            }

            .error-subtitle {
                font-size: 1.2rem;
            }

            .error-message {
                font-size: 1rem;
            }

            .btn-home,
            .btn-search {
                padding: 0.8rem 2rem;
                font-size: 1rem;
            }

            .logo-404 span {
                font-size: 2rem;
            }
        }

        @media (max-width: 576px) {
            .error-number {
                font-size: 6rem;
            }

            .error-number .zero::before {
                font-size: 2rem;
            }

            .btn-group-404 {
                flex-direction: column;
                width: 100%;
            }

            .btn-home,
            .btn-search {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    
    <!-- Floating Icons -->
    <div class="floating-icons">
        <i class="fas fa-question-circle floating-icon"></i>
        <i class="fas fa-brain floating-icon"></i>
        <i class="fas fa-lightbulb floating-icon"></i>
        <i class="fas fa-trophy floating-icon"></i>
    </div>

    <!-- Error Container -->
    <div class="error-container">
        <!-- Logo -->
        <div class="logo-404">
            <span class="q">Q</span><span class="u">U</span><span class="i">I</span><span class="z">Z</span><span style="color: white;">.com</span>
        </div>

        <!-- Error Number -->
        <div class="error-number">
            4<span class="zero">0</span>4
        </div>

        <!-- Error Title -->
        <h1 class="error-title">Oops! Trang không tồn tại!</h1>

        <!-- Error Subtitle -->
        <p class="error-subtitle">Có vẻ như trang này không tồn tại</p>

        <!-- Error Message -->
        <p class="error-message">
            Trang bạn đang tìm kiếm không tồn tại hoặc đã di chuyển. 
            Hãy thử tìm kiếm lại hoặc quay lại trang chủ.
        </p>

        <!-- Buttons -->
        <div class="btn-group-404">
            <a href="/" class="btn-home">
                <i class="fas fa-home"></i>
                Trở về Trang chủ
            </a>
            <a href="/search" class="btn-search">
                <i class="fas fa-search"></i>
                Tìm kiếm Quiz
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>