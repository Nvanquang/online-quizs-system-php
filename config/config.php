<?php

require_once __DIR__ . '/env.php';

// Load .env file from project root if present
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    loadEnv($envPath);
}

return [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'db' => getenv('DB_NAME') ?: 'quiz_system',
    'user' => getenv('DB_USER') ?: 'root',
    'pass' => getenv('DB_PASS') ?: '',
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
];


