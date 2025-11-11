<?php

interface UploadFileService
{
    public function validateFile(array $file): bool;
    public function sanitizeFileName(string $fileName): string;
    public function validateAndSanitizeFolder(string $folder): string|false;
    public function generateUniqueName(string $extension): string;
    public function saveFileToFolder(string $tempPath, string $folder, string $fileName): string;
    public function processImage(string $sourcePath, string $destPath, int $maxWidth = 1920, int $maxHeight = 1080): bool;
    public function deleteFileFromFolder(string $folder, string $fileName): bool;
}