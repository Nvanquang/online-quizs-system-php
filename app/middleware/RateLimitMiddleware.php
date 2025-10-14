<?php

/**
 * RateLimitMiddleware - Giới hạn số lượng request
 * 
 * Middleware này sẽ:
 * 1. Kiểm tra số lượng request trong một khoảng thời gian
 * 2. Nếu vượt quá giới hạn, trả về lỗi 429 (Too Many Requests)
 * 3. Sử dụng file để lưu trữ thông tin rate limit
 */
class RateLimitMiddleware
{
    private $maxRequests;
    private $timeWindow; // seconds
    private $storageFile;

    public function __construct($maxRequests = 100, $timeWindow = 3600) // 100 requests per hour
    {
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
        $this->storageFile = __DIR__ . '/../storage/rate_limit.json';
    }

    public function handle($params = [])
    {
        // Tạo thư mục storage nếu chưa có
        $storageDir = dirname($this->storageFile);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $clientIp = $this->getClientIp();
        $currentTime = time();
        
        // Đọc dữ liệu rate limit hiện tại
        $rateLimitData = $this->loadRateLimitData();
        
        // Làm sạch dữ liệu cũ
        $rateLimitData = $this->cleanOldData($rateLimitData, $currentTime);
        
        // Kiểm tra rate limit cho IP hiện tại
        if (isset($rateLimitData[$clientIp])) {
            $clientData = $rateLimitData[$clientIp];
            
            // Kiểm tra xem có vượt quá giới hạn không
            if (count($clientData['requests']) >= $this->maxRequests) {
                // Vượt quá giới hạn
                http_response_code(429);
                echo "<h1>429 - Too Many Requests</h1>";
                echo "<p>You have exceeded the rate limit. Please try again later.</p>";
                echo "<p>Limit: {$this->maxRequests} requests per {$this->timeWindow} seconds</p>";
                exit();
            }
        } else {
            // Tạo dữ liệu mới cho IP này
            $rateLimitData[$clientIp] = [
                'requests' => []
            ];
        }
        
        // Thêm request hiện tại
        $rateLimitData[$clientIp]['requests'][] = $currentTime;
        
        // Lưu dữ liệu
        $this->saveRateLimitData($rateLimitData);
        
        return true;
    }

    private function getClientIp()
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    private function loadRateLimitData()
    {
        if (file_exists($this->storageFile)) {
            $data = file_get_contents($this->storageFile);
            return json_decode($data, true) ?: [];
        }
        return [];
    }

    private function saveRateLimitData($data)
    {
        file_put_contents($this->storageFile, json_encode($data));
    }

    private function cleanOldData($data, $currentTime)
    {
        foreach ($data as $ip => $clientData) {
            $data[$ip]['requests'] = array_filter($clientData['requests'], function($timestamp) use ($currentTime) {
                return ($currentTime - $timestamp) < $this->timeWindow;
            });
            
            // Xóa IP nếu không còn request nào
            if (empty($data[$ip]['requests'])) {
                unset($data[$ip]);
            }
        }
        
        return $data;
    }
}
