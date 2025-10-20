<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Exception;

class S3AttachmentHandler
{
    /**
     * Download attachments from S3 and prepare for email
     */
    public function prepareAttachmentsFromS3(array $files): array
    {
        $attachments = [];
        $tempFiles = [];

        try {
            foreach ($files as $file) {
                $attachment = $this->downloadFromS3($file);

                if ($attachment !== null) {
                    $attachments[] = $attachment;
                    $tempFiles[] = $attachment['path'];
                }
            }

            return [
                'success' => true,
                'attachments' => $attachments,
                'temp_files' => $tempFiles
            ];
        } catch (Exception $e) {
            $this->cleanupTempFiles($tempFiles);

            logger()->error('Failed to prepare attachments from S3', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'attachments' => [],
                'temp_files' => []
            ];
        }
    }

    /**
     * Download single file from S3
     */
    protected function downloadFromS3($file): ?array
    {
        try {
            $s3Url = $file->s3_url ?? $file['s3_url'];
            $fileName = $file->file ?? $file['file'];
            $mimeType = $file->mimetype ?? $file['mime_type'] ?? 'application/octet-stream';

            if (filter_var($s3Url, FILTER_VALIDATE_URL)) {
                return $this->downloadFromUrl($s3Url, $fileName, $mimeType);
            } else {
                return $this->downloadFromStorage($s3Url, $fileName, $mimeType);
            }
        } catch (Exception $e) {
            logger()->error('Failed to download file from S3', [
                'file' => $fileName ?? 'unknown',
                's3_url' => $s3Url ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Download from S3 using Laravel Storage
     */
    protected function downloadFromStorage(string $s3Path, string $fileName, string $mimeType): ?array
    {
        try {
            if (!Storage::disk('s3')->exists($s3Path)) {
                logger()->error('File not found in S3', ['path' => $s3Path]);
                return null;
            }

            $fileContents = Storage::disk('s3')->get($s3Path);
            $tempPath = storage_path('app/temp_attachments/' . uniqid() . '_' . $fileName);

            $directory = dirname($tempPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($tempPath, $fileContents);

            return [
                'path' => $tempPath,
                'name' => $fileName,
                'mime_type' => $mimeType,
                'size' => filesize($tempPath)
            ];
        } catch (Exception $e) {
            logger()->error('Failed to download from S3 storage', [
                's3_path' => $s3Path,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Download from full S3 URL
     */
    protected function downloadFromUrl(string $url, string $fileName, string $mimeType): ?array
    {
        try {
            $response = Http::timeout(60)->get($url);

            if (!$response->successful()) {
                logger()->error('Failed to download file from URL', [
                    'url' => $url,
                    'status' => $response->status()
                ]);
                return null;
            }

            $tempPath = storage_path('app/temp_attachments/' . uniqid() . '_' . $fileName);
            $directory = dirname($tempPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($tempPath, $response->body());

            return [
                'path' => $tempPath,
                'name' => $fileName,
                'mime_type' => $mimeType,
                'size' => filesize($tempPath)
            ];
        } catch (Exception $e) {
            logger()->error('Failed to download from URL', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Download from S3 with presigned URL
     */
    public function downloadWithPresignedUrl(string $s3Path, string $fileName, string $mimeType, int $expiresIn = 3600): ?array
    {
        try {
            $url = Storage::disk('s3')->temporaryUrl($s3Path, now()->addSeconds($expiresIn));

            return $this->downloadFromUrl($url, $fileName, $mimeType);
        } catch (Exception $e) {
            logger()->error('Failed to create presigned URL', [
                's3_path' => $s3Path,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Cleanup temporary files
     */
    public function cleanupTempFiles(array $filePaths): void
    {
        foreach ($filePaths as $path) {
            if (file_exists($path)) {
                try {
                    unlink($path);
                } catch (Exception $e) {
                    logger()->warning('Failed to cleanup temp file', [
                        'path' => $path,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    /**
     * Validate file before download
     */
    protected function validateFile($file): bool
    {
        $maxSize = 10 * 1024 * 1024; // 10MB

        $fileSize = $file->file_size ?? $file['file_size'] ?? 0;

        if ($fileSize > $maxSize) {
            return false;
        }

        return true;
    }

    /**
     * Get file extension from mime type
     */
    protected function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeMap = [
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'text/plain' => 'txt',
            'text/csv' => 'csv',
        ];

        return $mimeMap[$mimeType] ?? 'bin';
    }
}
