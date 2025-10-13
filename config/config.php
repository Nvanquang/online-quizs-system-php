<?php
require_once __DIR__ . '/env.php';

// Load file .env
loadEnv(__DIR__ . '/../.env');

return [
    'host' => getenv('DB_HOST'),
    'db' => getenv('DB_NAME'),
    'user' => getenv('DB_USER'),
    'pass' => getenv('DB_PASS'),
    'charset' => getenv('DB_CHARSET'),
];
