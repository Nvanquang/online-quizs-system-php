<?php

class UploadFileServiceImpl implements UploadFileService
{
    private static $instant = null;
    private array $config;    

    // Default config
    private array $defaultConfig = [
        'allowedMimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'allowedExtensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'maxFileSize' => 3 * 1024 * 1024,
        'maxImageWidth' => 3000,
        'maxImageHeight' => 3000,
        'uploadDir' => 'C:\\xampp\\htdocs\\online-quizs-system-php\\public\\uploads\\',
        'enableProcessing' => true,
    ];

    public static function getInstance()
    {
        if (self::$instant === null) {
            self::$instant = new self();
        }
        return self::$instant;
    }

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->defaultConfig, $config);
        // Ensure upload dir exists and is writable
        if (!is_dir($this->config['uploadDir'])) {
            mkdir($this->config['uploadDir'], 0755, true);
        }
    }

    public function validateFile(array $file): bool
    {
        // Basic upload error check
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Check if file is actually uploaded (prevents direct access)
        if (!is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        // Size check
        if ($file['size'] > $this->config['maxFileSize']) {
            return false;
        }

        // Extension check (lowercase)
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->config['allowedExtensions'])) {
            return false;
        }

        // MIME type check using finfo (more reliable than $_FILES['type'])
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $this->config['allowedMimes'])) {
            return false;
        }

        // Image integrity check using getimagesize (detects non-images or corrupted)
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return false;
        }

        // Dimension check to prevent oversized images (DoS vector)
        if ($imageInfo[0] > $this->config['maxImageWidth'] || $imageInfo[1] > $this->config['maxImageHeight']) {
            return false;
        }

        // Additional security: Check for null bytes or path traversal in name
        if (strpos($file['name'], "\0") !== false || strpos($file['name'], '../') !== false) {
            return false;
        }

        return true;
    }

    public function sanitizeFileName(string $fileName): string
    {
        // Remove path info, null bytes, and special chars
        $fileName = basename($fileName);
        $fileName = str_replace(['\0', '/', '\\', '..'], '', $fileName);
        // Keep only alphanumeric, hyphen, underscore
        return preg_replace('/[^a-zA-Z0-9\-_]/', '', pathinfo($fileName, PATHINFO_FILENAME));
    }

    public function validateAndSanitizeFolder(string $folder): string|false
    {
        if (empty(trim($folder))) {
            return false; // Folder cannot be empty
        }

        // Remove path info, null bytes, traversal attempts
        $folder = trim(basename($folder)); // Strips any leading/trailing paths
        $folder = str_replace(['\0', '/', '\\', '..', '.'], '', $folder); // Remove dots, slashes, etc.

        // Keep only alphanumeric, hyphen, underscore (simple subfolder name)
        $sanitized = preg_replace('/[^a-zA-Z0-9\-_]/', '', $folder);

        // Must not be empty after sanitization
        if (empty($sanitized) || strlen($sanitized) > 50) { // Reasonable length limit
            return false;
        }

        // Final check: Ensure no traversal in the full path context
        $fullPath = realpath($this->config['uploadDir'] . '\\' . $sanitized);
        if (strpos($fullPath, realpath($this->config['uploadDir'])) !== 0) {
            return false; // If it escapes the root, block it
        }

        return $sanitized;
    }

    public function generateUniqueName(string $extension): string
    {
        // Use timestamp + random + sanitized name for uniqueness
        $sanitized = $this->sanitizeFileName(microtime(true) . '_' . bin2hex(random_bytes(8)));
        return $sanitized . '.' . strtolower($extension);
    }

    public function saveFileToFolder(string $tempPath, string $folder, string $fileName): string
    {
        // Generate unique name
        $fileName = $this->generateUniqueName($fileName);

        // Validate and sanitize folder first
        $sanitizedFolder = $this->validateAndSanitizeFolder($folder);
        if ($sanitizedFolder === false) {
            return false;
        }

        // Build full destination path
        $destDir = $this->config['uploadDir'] . $sanitizedFolder . '\\';
        $destinationPath = $destDir . $fileName;

        // Ensure destination dir exists and is writable
        if (!is_dir($destDir)) {
            if (!mkdir($destDir, 0755, true)) {
                return false;
            }
        }
        if (!is_writable($destDir)) {
            return false;
        }

        // Use move_uploaded_file for security (fails if not uploaded file)
        if (!move_uploaded_file($tempPath, $destinationPath)) {
            return false;
        }

        // Set secure permissions
        chmod($destinationPath, 0644);

        return $fileName;
    }


    public function processImage(string $sourcePath, string $destPath, int $maxWidth = 1920, int $maxHeight = 1080): bool
    {
        if (!$this->config['enableProcessing']) {
            return copy($sourcePath, $destPath); // Just copy if no processing
        }

        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            return false;
        }

        $srcWidth = $imageInfo[0];
        $srcHeight = $imageInfo[1];
        $mime = $imageInfo['mime'];

        // No need to resize if already small enough
        if ($srcWidth <= $maxWidth && $srcHeight <= $maxHeight) {
            return copy($sourcePath, $destPath);
        }

        // Calculate new dimensions (maintain aspect ratio)
        $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight);
        $newWidth = (int) ($srcWidth * $ratio);
        $newHeight = (int) ($srcHeight * $ratio);

        // Create image resource based on type
        switch ($mime) {
            case 'image/jpeg':
                $srcImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $srcImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $srcImage = imagecreatefromgif($sourcePath);
                break;
            case 'image/webp':
                $srcImage = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        if ($srcImage === false) {
            return false;
        }

        // Create new image
        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/GIF
        if (in_array($mime, ['image/png', 'image/gif'])) {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefill($destImage, 0, 0, $transparent);
        }

        // Resize
        if (!imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight)) {
            imagedestroy($srcImage);
            imagedestroy($destImage);
            return false;
        }

        // Save based on type
        $success = false;
        switch ($mime) {
            case 'image/jpeg':
                $success = imagejpeg($destImage, $destPath, 90); // Quality 90
                break;
            case 'image/png':
                $success = imagepng($destImage, $destPath, 6); // Compression 6
                break;
            case 'image/gif':
                $success = imagegif($destImage, $destPath);
                break;
            case 'image/webp':
                $success = imagewebp($destImage, $destPath, 80); // Quality 80
                break;
        }

        // Cleanup
        imagedestroy($srcImage);
        imagedestroy($destImage);

        return $success;
    }

    public function deleteFileFromFolder(string $folder, string $fileName): bool
    {
        $fullPath = $this->config['uploadDir'] . $folder . '\\' . $fileName;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
