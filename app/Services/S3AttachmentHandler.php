<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Exception;

class S3AttachmentHandler
{
    /**
     * Upload a request file to S3 and return upload metadata.
     */
    public function uploadUploadedFile(UploadedFile $file, string $s3UploadPath): array
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Invalid file upload.');
        }

        $mimeType = $file->getClientMimeType();
        $filename = uniqid('file_', true) . '.' . $file->getClientOriginalExtension();
        $normalizedUploadPath = trim($s3UploadPath, '/');
        $uploadedFilePath = $normalizedUploadPath . '/' . $filename;

        $fileContents = file_get_contents($file->getRealPath());
        if ($fileContents === false) {
            throw new Exception('Failed to read uploaded file contents.');
        }

        $uploaded = Storage::disk('s3')->put(
            $uploadedFilePath,
            $fileContents,
            [
                'visibility' => 'public',
                'ContentType' => $mimeType,
            ]
        );

        if (!$uploaded) {
            throw new Exception('Failed to upload file to S3.');
        }

        if (!Storage::disk('s3')->exists($uploadedFilePath)) {
            throw new Exception('Failed to upload file to S3.');
        }

        return [
            'mimetype' => $mimeType,
            'filename' => $filename,
            'path' => $uploadedFilePath,
        ];
    }

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

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'attachments' => [],
                'temp_files' => []
            ];
        }
    }

    public function storeInS3($content, string $filename): array
    {
        /** @var Cloud $disk */
        $disk = Storage::disk('s3');

        if (is_object($content) && method_exists($content, 'output')) {
            $content = $content->output();
        }

        $disk->put($filename, $content, [
            'visibility' => 'public',
            'ContentType' => 'application/pdf',
            'Metadata' => [
                'opportunity_id' => $this->requestData['opportunity_id'] ?? 'unknown',
                'stage' => $this->requestData['current_stage'] ?? 'unknown',
                'generated_at' => Carbon::now()->toIso8601String(),
                'generated_by' => $this->userId ?? 'system',
            ]
        ]);

        $s3Url = $disk->url($filename);

        return ['filename' => $filename, 's3_url' => $s3Url];
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
            return null;
        }
    }

    /**
     * Download from S3 with presigned URL
     */
    public function downloadWithPresignedUrl(string $s3Path, string $fileName, string $mimeType, int $expiresIn = 3600): ?array
    {
        try {
            /** @var FilesystemAdapter $disk */
            $disk = Storage::disk('s3');
            $url = $disk->temporaryUrl($s3Path, now()->addSeconds($expiresIn));

            return $this->downloadFromUrl($url, $fileName, $mimeType);
        } catch (Exception $e) {
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
                    throw ($e);
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

    /**
     * Delete a single file from S3
     *
     * @param string $s3Path The S3 path/key of the file to delete
     * @return bool True if deleted successfully, false otherwise
     */
    public function deleteFromS3(string $s3Path): bool
    {
        try {
            if (!Storage::disk('s3')->exists($s3Path)) {
                return false;
            }

            $deleted = Storage::disk('s3')->delete($s3Path);

            return $deleted;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete multiple files from S3
     *
     * @param array $s3Paths Array of S3 paths/keys to delete
     * @return array Results with 'success' count, 'failed' count, and 'details'
     */
    public function deleteMultipleFromS3(array $s3Paths): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'details' => []
        ];

        foreach ($s3Paths as $path) {
            $deleted = $this->deleteFromS3($path);

            if ($deleted) {
                $results['success']++;
                $results['details'][$path] = 'deleted';
            } else {
                $results['failed']++;
                $results['details'][$path] = 'failed';
            }
        }

        return $results;
    }

    /**
     * Delete multiple files from S3 in a single operation (more efficient)
     *
     * @param array $s3Paths Array of S3 paths/keys to delete
     * @return bool True if all files deleted successfully
     */
    public function bulkDeleteFromS3(array $s3Paths): bool
    {
        try {
            if (empty($s3Paths)) {
                return true;
            }

            $existingPaths = array_filter($s3Paths, function ($path) {
                return Storage::disk('s3')->exists($path);
            });

            if (empty($existingPaths)) {
                return true;
            }

            $deleted = Storage::disk('s3')->delete($existingPaths);

            return $deleted;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete all files in a directory from S3
     *
     * @param string $directory The S3 directory path
     * @param bool $deleteDirectory Whether to delete the directory itself
     * @return array Results with file count and success status
     */
    public function deleteDirectoryFromS3(string $directory, bool $deleteDirectory = false): array
    {
        try {
            $files = Storage::disk('s3')->allFiles($directory);

            if (empty($files)) {
                return [
                    'success' => true,
                    'files_deleted' => 0,
                    'directory' => $directory
                ];
            }

            $deleted = Storage::disk('s3')->delete($files);

            if ($deleteDirectory) {
                Storage::disk('s3')->deleteDirectory($directory);
            }

            return [
                'success' => $deleted,
                'files_deleted' => count($files),
                'directory' => $directory,
                'directory_deleted' => $deleteDirectory
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'directory' => $directory
            ];
        }
    }

    /**
     * Delete file from S3 and local temp storage if exists
     *
     * @param string $s3Path S3 path of the file
     * @param string|null $localPath Optional local temp file path
     * @return array Results with S3 and local deletion status
     */
    public function deleteFromBothStorages(string $s3Path, ?string $localPath = null): array
    {
        $results = [
            's3_deleted' => false,
            'local_deleted' => false
        ];

        $results['s3_deleted'] = $this->deleteFromS3($s3Path);

        if ($localPath && file_exists($localPath)) {
            try {
                unlink($localPath);
                $results['local_deleted'] = true;
            } catch (Exception $e) {
                throw ($e);
            }
        }

        return $results;
    }

    /**
     * Soft delete - move file to archive/trash folder in S3
     *
     * @param string $s3Path Current S3 path
     * @param string $archivePrefix Prefix for archived files (default: 'trash/')
     * @return array Results with success status and new path
     */
    public function softDeleteFromS3(string $s3Path, string $archivePrefix = 'trash/'): array
    {
        try {
            if (!Storage::disk('s3')->exists($s3Path)) {
                return [
                    'success' => false,
                    'error' => 'File not found'
                ];
            }

            $fileName = basename($s3Path);
            $timestamp = now()->format('Y-m-d_His');
            $newPath = $archivePrefix . $timestamp . '_' . $fileName;

            $moved = Storage::disk('s3')->move($s3Path, $newPath);

            return [
                'success' => $moved,
                'original_path' => $s3Path,
                'archive_path' => $newPath
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete files older than specified days
     *
     * @param string $directory S3 directory to clean
     * @param int $days Delete files older than this many days
     * @return array Results with deletion statistics
     */
    public function deleteOldFilesFromS3(string $directory, int $days = 30): array
    {
        try {
            $files = Storage::disk('s3')->allFiles($directory);
            $cutoffTime = now()->subDays($days)->timestamp;
            $deletedFiles = [];

            foreach ($files as $file) {
                $lastModified = Storage::disk('s3')->lastModified($file);

                if ($lastModified < $cutoffTime) {
                    if ($this->deleteFromS3($file)) {
                        $deletedFiles[] = $file;
                    }
                }
            }

            return [
                'success' => true,
                'total_files' => count($files),
                'deleted_count' => count($deletedFiles),
                'deleted_files' => $deletedFiles
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
