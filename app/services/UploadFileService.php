<?php

interface UploadFileService
{
    public function validateFile(array $file): bool;
    public function sanitizeFileName(string $fileName): string;
    public function validateAndSanitizeFolder(string $folder): string|false;
    public function generateUniqueName(string $extension): string;
    public function saveFileToFolder(string $tempPath, string $folder, string $fileName): string;
    public function deleteFileFromFolder(string $folder, string $fileName): bool;
}