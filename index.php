<?php

// Start session early
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic autoloader for core and app classes (no Composer)
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/app/core/' . $class . '.php',
        __DIR__ . '/app/controllers/' . $class . '.php',
        __DIR__ . '/app/models/' . $class . '.php',
        __DIR__ . '/app/services/' . $class . '.php',
        __DIR__ . '/app/services/impl/' . $class . '.php',
        __DIR__ . '/app/repositories/' . $class . '.php',
        __DIR__ . '/app/middleware/' . $class . '.php',
        __DIR__ . '/app/utils/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Load env and config (optional)
if (file_exists(__DIR__ . '/config/env.php')) {
    require_once __DIR__ . '/config/env.php';
    $envPath = __DIR__ . '/.env';
    if (file_exists($envPath)) {
        loadEnv($envPath);
    }
}

// Ensure Database and Router classes are loaded
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/core/View.php';

// Initialize router
$router = new Router();

// Routes công khai (không cần middleware)
$router->get('/', 'Home@index');
$router->get('/test', 'Home@test');
$router->get('/study', 'Home@study');

// Routes chỉ dành cho guest (chưa đăng nhập)
$router->get('/auth/login', 'Auth@login', ['GuestMiddleware']);
$router->post('/auth/login', 'Auth@doLogin', ['GuestMiddleware', 'CSRFMiddleware']);
$router->get('/auth/register', 'Auth@register', ['GuestMiddleware']);
$router->post('/auth/register', 'Auth@doRegister', ['GuestMiddleware', 'CSRFMiddleware']);

// Routes cần đăng nhập
$router->get('/auth/logout', 'Auth@logout', ['AuthMiddleware']);
$router->get('/user/profile', 'User@profile', ['AuthMiddleware']);
$router->get('/user/history', 'User@history', ['AuthMiddleware']);
$router->post('/user/update-profile', 'User@updateProfile', ['AuthMiddleware', 'CSRFMiddleware']);

// Routes game cần đăng nhập
$router->post('/game/lobby/{quizId}', 'Game@startLobby', ['AuthMiddleware']); // Tạo/khởi động session theo quizId rồi redirect
$router->get('/game/lobby/{sessionCode}', 'Game@lobby', ['AuthMiddleware']); // Phòng chờ của chủ tạo quiz (GET render theo sessionCode)
$router->post('/game/join/{sessionCode}', 'Game@doJoin', ['AuthMiddleware', 'CSRFMiddleware']); // Yêu cầu tham gia quiz
$router->get('/game/waiting/{sessionCode}', 'Game@waiting', ['AuthMiddleware', 'CSRFMiddleware']); // Phòng chờ của người chơi
$router->get('/game/play/{sessionCode}', 'Game@play', ['AuthMiddleware']); // Trang chơi
$router->post('/game/end/{sessionCode}', 'Game@endGame', ['AuthMiddleware', 'CSRFMiddleware']); // Kết thúc game

// Routes edit cần đăng nhập
$router->get('/quiz/create', 'Quiz@create', ['AuthMiddleware']);
$router->post('/quiz/create', 'Quiz@doCreate', ['AuthMiddleware', 'CSRFMiddleware']);
$router->post('/quiz/create-question/{quizId}', 'Quiz@doCreateQuestion', ['AuthMiddleware', 'CSRFMiddleware']);
$router->get('/quiz/edit/{quizId}', 'Quiz@edit', ['AuthMiddleware']);
$router->post('/quiz/edit/{quizId}', 'Quiz@doEdit', ['AuthMiddleware', 'CSRFMiddleware']);

$router->post('/question/create', 'Question@doCreate', ['AuthMiddleware', 'CSRFMiddleware']);
$router->post('/question/edit/{questionId}', 'Question@doEdit', ['AuthMiddleware', 'CSRFMiddleware']);
$router->post('/question/delete/{questionId}', 'Question@doDelete', ['AuthMiddleware', 'CSRFMiddleware']);

// Dispatch current request
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$router->dispatch($method, $uri);


