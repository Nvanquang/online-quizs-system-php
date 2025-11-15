<?php

header('Content-Type: text/html; charset=utf-8');

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

// Routes home
$router->get('/', 'Home@index');
$router->get('/test', 'Home@test');
$router->get('/study', 'Home@study');

// Routes auth
$router->get('/auth/login', 'Auth@login', ['GuestMiddleware']);
$router->post('/auth/login', 'Auth@doLogin', ['GuestMiddleware', 'CSRFMiddleware']);
$router->get('/auth/register', 'Auth@register', ['GuestMiddleware']);
$router->post('/auth/register', 'Auth@doRegister', ['GuestMiddleware', 'CSRFMiddleware']);
$router->get('/auth/logout', 'Auth@logout', ['AuthMiddleware']);
$router->get('/auth/login-admin', 'Auth@loginAdmin', ['GuestMiddleware']);
$router->post('/auth/login-admin', 'Auth@doLoginAdmin', ['GuestMiddleware', 'CSRFMiddleware']);

// Routes user
$router->get('/user/my-quizzes', 'User@myQuizzes', ['AuthMiddleware']);
$router->get('/user/profile', 'User@profile', ['AuthMiddleware']);
$router->get('/user/history', 'User@history', ['AuthMiddleware']);
$router->post('/user/update-profile', 'User@updateProfile', ['AuthMiddleware', 'CSRFMiddleware']);
$router->post('/user/update-password', 'User@updatePassword', ['AuthMiddleware', 'CSRFMiddleware']);
$router->post('/user/update-username', 'User@updateUsername', ['AuthMiddleware', 'CSRFMiddleware']);
$router->post('/user/update-email', 'User@updateEmail', ['AuthMiddleware', 'CSRFMiddleware']);

$router->post('/user/create', 'User@createUser', ['AuthMiddleware', 'CSRFMiddleware', 'AdminMiddleware']);
$router->post('/user/update', 'User@updateUser', ['AuthMiddleware', 'CSRFMiddleware', 'AdminMiddleware']);
$router->post('/user/delete', 'User@deleteUser', ['AuthMiddleware', 'CSRFMiddleware', 'AdminMiddleware']);


// Routes game
$router->post('/game/lobby/{quizId}', 'Game@startLobby', ['AuthMiddleware']); // Tạo/khởi động session theo quizId rồi redirect
$router->get('/game/lobby/{sessionCode}', 'Game@lobby', ['AuthMiddleware']); // Phòng chờ của chủ tạo quiz (GET render theo sessionCode)
$router->post('/game/join/{sessionCode}', 'Game@doJoin', ['AuthMiddleware', 'CSRFMiddleware']); // Yêu cầu tham gia quiz
$router->get('/game/waiting/{sessionCode}', 'Game@waiting', ['AuthMiddleware', 'CSRFMiddleware']); // Phòng chờ của người chơi
$router->get('/game/play/{sessionCode}', 'Game@play', ['AuthMiddleware']); // Trang chơi
$router->post('/game/end/{sessionCode}', 'Game@endGame', ['AuthMiddleware', 'CSRFMiddleware']); // Kết thúc game

// Routes quiz
$router->get('/quiz/view/{quizId}', 'Quiz@view', ['AuthMiddleware']);
$router->get('/quiz/create', 'Quiz@create', ['AuthMiddleware']);
$router->post('/quiz/create', 'Quiz@doCreate', ['AuthMiddleware', 'CSRFMiddleware']);
$router->get('/quiz/edit-quiz/{quizId}', 'Quiz@editQuiz', ['AuthMiddleware']);
$router->post('/quiz/edit-quiz/{quizId}', 'Quiz@doEditQuiz', ['AuthMiddleware', 'CSRFMiddleware']);
$router->get('/quiz/edit/{quizId}', 'Quiz@edit', ['AuthMiddleware']); // Create questions
$router->post('/quiz/delete/{quizId}', 'Quiz@doDelete', ['AuthMiddleware', 'CSRFMiddleware']);

// Routes question
$router->post('/question/create', 'Question@doCreate', ['AuthMiddleware', 'CSRFMiddleware']);
$router->post('/question/edit/{questionId}', 'Question@doEdit', ['AuthMiddleware', 'CSRFMiddleware']);
$router->post('/question/delete/{questionId}', 'Question@doDelete', ['AuthMiddleware', 'CSRFMiddleware']);


// Routes admin
$router->group('/admin', function($router) {
    $router->get('/dashboard', 'Admin@index');
    $router->get('/users', 'Admin@users');
    $router->get('/questions', 'Admin@questions');
    $router->get('/quizzes', 'Admin@quizzes');
}, ['AuthMiddleware', 'AdminMiddleware']);

// Dispatch current request
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$router->dispatch($method, $uri);


