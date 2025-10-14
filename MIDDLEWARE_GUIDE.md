# Hướng Dẫn Sử Dụng Middleware

## Tổng Quan

Middleware là các lớp xử lý được thực thi trước khi request đến controller. Chúng được sử dụng để:
- Xác thực người dùng
- Kiểm tra quyền truy cập
- Bảo vệ CSRF
- Giới hạn tần suất request
- Logging và monitoring

## Cấu Trúc Middleware

Tất cả middleware được đặt trong thư mục `app/middleware/` và phải có method `handle()`.

```php
class MyMiddleware
{
    public function handle($params = [])
    {
        // Logic xử lý middleware
        // Nếu return false hoặc exit(), request sẽ bị dừng
        return true;
    }
}
```

## Các Middleware Có Sẵn

### 1. AuthMiddleware
Kiểm tra xem user đã đăng nhập chưa.

```php
// Sử dụng
$router->get('/profile', 'User@profile', ['AuthMiddleware']);

// Nếu chưa đăng nhập, sẽ redirect về /login
```

### 2. AdminMiddleware
Kiểm tra quyền admin.

```php
// Sử dụng
$router->get('/admin/dashboard', 'Admin@dashboard', ['AdminMiddleware']);

// Nếu không có quyền admin, hiển thị lỗi 403
```

### 3. GuestMiddleware
Chỉ cho phép user chưa đăng nhập truy cập.

```php
// Sử dụng
$router->get('/login', 'Auth@login', ['GuestMiddleware']);

// Nếu đã đăng nhập, redirect về trang chủ
```

### 4. CSRFMiddleware
Bảo vệ chống CSRF attacks.

```php
// Sử dụng
$router->post('/submit', 'Form@submit', ['CSRFMiddleware']);

// Trong form, cần thêm CSRF token:
echo CSRFMiddleware::getTokenField();
```

### 5. RateLimitMiddleware
Giới hạn số lượng request.

```php
// Sử dụng với cấu hình mặc định (100 requests/hour)
$router->post('/api/submit', 'Api@submit', ['RateLimitMiddleware']);

// Hoặc tạo instance với cấu hình tùy chỉnh
$rateLimit = new RateLimitMiddleware(50, 1800); // 50 requests per 30 minutes
```

## Cách Sử Dụng

### 1. Middleware Đơn Lẻ

```php
// Route cần đăng nhập
$router->get('/profile', 'User@profile', ['AuthMiddleware']);

// Route cần quyền admin
$router->get('/admin/users', 'Admin@users', ['AdminMiddleware']);

// Route chỉ dành cho guest
$router->get('/login', 'Auth@login', ['GuestMiddleware']);
```

### 2. Nhiều Middleware

```php
// Route cần đăng nhập + CSRF protection
$router->post('/update-profile', 'User@updateProfile', [
    'AuthMiddleware', 
    'CSRFMiddleware'
]);

// Route admin + CSRF + Rate limit
$router->post('/admin/create-user', 'Admin@createUser', [
    'AdminMiddleware',
    'CSRFMiddleware', 
    'RateLimitMiddleware'
]);
```

### 3. Middleware Group

```php
// Tất cả routes trong group sẽ có AuthMiddleware
$router->group('/protected', function($router) {
    $router->get('/dashboard', 'User@dashboard');
    $router->get('/settings', 'User@settings');
    $router->post('/update', 'User@update', ['CSRFMiddleware']);
}, ['AuthMiddleware']);

// Tất cả routes admin sẽ có AdminMiddleware
$router->group('/admin', function($router) {
    $router->get('/users', 'Admin@users');
    $router->get('/reports', 'Admin@reports');
}, ['AdminMiddleware']);
```

## Tạo Middleware Mới

### Ví dụ: LoggingMiddleware

```php
<?php
// app/middleware/LoggingMiddleware.php

class LoggingMiddleware
{
    public function handle($params = [])
    {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'uri' => $_SERVER['REQUEST_URI'] ?? '/',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];
        
        // Ghi log
        $logFile = __DIR__ . '/../storage/logs/access.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND);
        
        return true;
    }
}
```

### Sử dụng LoggingMiddleware

```php
// Áp dụng cho tất cả routes
$router->group('', function($router) {
    // Tất cả routes ở đây
}, ['LoggingMiddleware']);

// Hoặc áp dụng cho từng route cụ thể
$router->get('/api/data', 'Api@getData', ['LoggingMiddleware']);
```

## Ví Dụ Thực Tế

### 1. Hệ Thống Quiz

```php
// Routes công khai
$router->get('/', 'Home@index');
$router->get('/about', 'Home@about');

// Routes chỉ dành cho guest
$router->get('/login', 'Auth@login', ['GuestMiddleware']);
$router->post('/login', 'Auth@doLogin', ['GuestMiddleware', 'CSRFMiddleware']);

// Routes cần đăng nhập
$router->get('/quiz/join/{code}', 'Quiz@join', ['AuthMiddleware']);
$router->post('/quiz/answer', 'Quiz@submitAnswer', [
    'AuthMiddleware', 
    'CSRFMiddleware',
    'RateLimitMiddleware'
]);

// Routes admin
$router->get('/admin/quizzes', 'Admin@quizzes', ['AdminMiddleware']);
$router->post('/admin/quizzes/create', 'Admin@createQuiz', [
    'AdminMiddleware',
    'CSRFMiddleware'
]);
```

### 2. API với Rate Limiting

```php
// API công khai với rate limit
$router->get('/api/leaderboard', 'Api@leaderboard', ['RateLimitMiddleware']);

// API cần đăng nhập
$router->post('/api/submit-score', 'Api@submitScore', [
    'AuthMiddleware',
    'RateLimitMiddleware'
]);

// API admin
$router->get('/api/admin/stats', 'Api@adminStats', [
    'AdminMiddleware',
    'RateLimitMiddleware'
]);
```

## Lưu Ý Quan Trọng

1. **Thứ tự middleware**: Middleware được thực thi theo thứ tự trong array
2. **Session**: Đảm bảo session được khởi tạo trước khi sử dụng AuthMiddleware
3. **CSRF Token**: Luôn thêm CSRF token vào form khi sử dụng CSRFMiddleware
4. **Rate Limiting**: Cấu hình phù hợp để tránh chặn user hợp lệ
5. **Error Handling**: Middleware nên xử lý lỗi một cách graceful

## Debugging

Để debug middleware, bạn có thể thêm logging:

```php
class DebugMiddleware
{
    public function handle($params = [])
    {
        error_log("DebugMiddleware: " . $_SERVER['REQUEST_URI']);
        return true;
    }
}
```

Hoặc sử dụng `var_dump()` để kiểm tra:

```php
public function handle($params = [])
{
    var_dump("Middleware executed for: " . $_SERVER['REQUEST_URI']);
    return true;
}
```
