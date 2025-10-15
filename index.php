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
$router->get('/game/join/{code}', 'Game@join', ['AuthMiddleware']);
$router->post('/game/join/{code}', 'Game@doJoin', ['AuthMiddleware', 'CSRFMiddleware']);
$router->get('/game/play/{sessionId}', 'Game@play', ['AuthMiddleware']);
$router->post('/game/answer', 'Game@submitAnswer', ['AuthMiddleware', 'CSRFMiddleware']);

// Routes admin (cần quyền admin)
$router->get('/admin/dashboard', 'Admin@dashboard', ['AdminMiddleware']);
$router->get('/admin/quizzes', 'Admin@quizzes', ['AdminMiddleware']);
$router->get('/admin/questions', 'Admin@questions', ['AdminMiddleware']);
$router->post('/admin/quizzes/create', 'Admin@createQuiz', ['AdminMiddleware', 'CSRFMiddleware']);
$router->post('/admin/questions/create', 'Admin@createQuestion', ['AdminMiddleware', 'CSRFMiddleware']);

// Routes với rate limiting (ví dụ cho API)
$router->post('/api/submit-answer', 'Game@apiSubmitAnswer', ['AuthMiddleware', 'RateLimitMiddleware']);
$router->get('/api/leaderboard', 'Leaderboard@apiGetLeaderboard', ['RateLimitMiddleware']);


// Tất cả routes admin sẽ có AdminMiddleware
$router->group('/admin', function($router) {
    $router->get('/users', 'Admin@users');
    $router->get('/reports', 'Admin@reports');
    $router->post('/users/{id}/ban', 'Admin@banUser', ['CSRFMiddleware']);
}, ['AdminMiddleware']);


// Dispatch current request
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$router->dispatch($method, $uri);


