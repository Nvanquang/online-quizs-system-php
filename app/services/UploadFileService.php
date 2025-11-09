<?php
/**
 * UploadFileService.php
 * Interface for file upload service focused on images.
 * Defines methods for validation, sanitization, saving, and processing images securely.
 * Supports saving to subfolders with folder validation to prevent path traversal.
 */

interface UploadFileService
{
    /**
     * @return bool True if valid, false otherwise.
     */
    public function validateFile(array $file): bool;

    /**
     * Sanitizes the original filename to prevent path traversal and invalid chars.
     */
    public function sanitizeFileName(string $fileName): string;

    /**
     * Validates and sanitizes a folder name to prevent path traversal.
     */
    public function validateAndSanitizeFolder(string $folder): string|false;

    /**
     * Generates a unique filename to avoid collisions.
     */
    public function generateUniqueName(string $extension): string;

    /**
     * Saves the uploaded file to a specific subfolder in the upload directory.
     * Uses move_uploaded_file for security. Creates subfolder if it doesn't exist.
     */
    public function saveFileToFolder(string $tempPath, string $folder, string $fileName): string;

    /**
     * Processes the image (e.g., resize) using GD library.
     * Only for valid images; resizes if exceeding max dimensions.
     */
    public function processImage(string $sourcePath, string $destPath, int $maxWidth = 1920, int $maxHeight = 1080): bool;
}