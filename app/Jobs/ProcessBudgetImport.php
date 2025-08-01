<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\BudgetIncome;
use App\Models\FiscalYear;

class ProcessBudgetImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobId;
    protected $filePath;
    protected $fiscalYearId;
    protected $overwriteExisting;
    protected $startRow;
    protected $chunkSize = 10; // Process 10 rows at a time

    /**
     * Create a new job instance.
     *
     * @param string $jobId
     * @param string $filePath
     * @param int $fiscalYearId
     * @param bool $overwriteExisting
     * @param int $startRow
     * @return void
     */
    public function __construct($jobId, $filePath, $fiscalYearId, $overwriteExisting = false, $startRow = 0)
    {
        $this->jobId = $jobId;
        $this->filePath = $filePath;
        $this->fiscalYearId = $fiscalYearId;
        $this->overwriteExisting = $overwriteExisting;
        $this->startRow = $startRow;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Check if job was cancelled
        $progressData = Cache::get("import_job_{$this->jobId}_status");
        if (!$progressData || $progressData['status'] === 'cancelled') {
            return;
        }

        try {
            // Update status to processing
            $this->updateProgress('processing', 'Processing import...', []);

            // Check if file exists
            if (!Storage::exists($this->filePath)) {
                $this->failImport('Import file not found.');
                return;
            }

            // Load the Excel file
            $spreadsheet = IOFactory::load(Storage::path($this->filePath));
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            // If startRow is 0, we need to skip the header row (start from row 2)
            $currentRow = $this->startRow > 0 ? $this->startRow : 2;

            // Clear existing data if overwriting
            if ($currentRow == 2 && $this->overwriteExisting) {
                BudgetIncome::where('fiscal_year_id', $this->fiscalYearId)->delete();
                $this->addMessage('Existing data for the selected fiscal year has been cleared.', 'info');
            }

            // Start transaction for batch processing
            DB::beginTransaction();

            $processedCount = 0;
            $endRow = min($currentRow + $this->chunkSize, $highestRow);

            // Process rows in chunks
            for ($rowIndex = $currentRow; $rowIndex <= $endRow; $rowIndex++) {
                // Check if job was cancelled during processing
                if ($this->checkIfCancelled()) {
                    DB::rollBack();
                    return;
                }

                $category = $worksheet->getCellByColumnAndRow(1, $rowIndex)->getValue();
                $subcategory = $worksheet->getCellByColumnAndRow(2, $rowIndex)->getValue();
                $amount = $worksheet->getCellByColumnAndRow(3, $rowIndex)->getValue();

                // Skip empty rows
                if (empty($category) && empty($subcategory) && empty($amount)) {
                    continue;
                }

                // Simple validation
                if (empty($category) || empty($subcategory) || !is_numeric($amount)) {
                    $this->addMessage("Row {$rowIndex}: Invalid data, skipping.", 'warning');
                    continue;
                }

                // Create or update budget income record
                BudgetIncome::updateOrCreate(
                    [
                        'fiscal_year_id' => $this->fiscalYearId,
                        'category' => $category,
                        'subcategory' => $subcategory
                    ],
                    ['amount' => (float) $amount]
                );

                $processedCount++;

                // Update progress every 5 rows
                if ($processedCount % 5 == 0 || $rowIndex == $endRow) {
                    $this->updateProcessedCount($rowIndex - 1);
                }

                // Add artificial delay for demonstration purposes (remove in production)
                // This helps visualize the progress for small files
                usleep(200000); // 0.2 seconds
            }

            // Commit the transaction
            DB::commit();

            // If there are more rows to process, dispatch a new job
            if ($endRow < $highestRow) {
                $this->updateProgress('processing', 'Continuing import...', []);

                // Dispatch a new job to process the next chunk
                self::dispatch($this->jobId, $this->filePath, $this->fiscalYearId, $this->overwriteExisting, $endRow);
            } else {
                // Complete the import
                $this->completeImport();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import job error: ' . $e->getMessage());
            $this->failImport('Error during import: ' . $e->getMessage());
        }
    }

    /**
     * Update the current progress in cache
     */
    protected function updateProgress($status, $statusMessage, $newMessages = [])
    {
        $progressData = Cache::get("import_job_{$this->jobId}_status");

        if ($progressData) {
            $progressData['status'] = $status;
            $progressData['status_message'] = $statusMessage;

            if (!empty($newMessages)) {
                $messages = $progressData['messages'] ?? [];
                foreach ($newMessages as $message) {
                    $messages[] = [
                        'text' => $message['text'],
                        'type' => $message['type'] ?? 'info',
                        'time' => now()->format('H:i:s')
                    ];
                }
                $progressData['messages'] = $messages;
            }

            Cache::put("import_job_{$this->jobId}_status", $progressData, 3600);
        }
    }

    /**
     * Update the processed row count
     */
    protected function updateProcessedCount($rowNumber)
    {
        $progressData = Cache::get("import_job_{$this->jobId}_status");

        if ($progressData) {
            $progressData['processed_rows'] = $rowNumber - 1; // Subtract header row
            Cache::put("import_job_{$this->jobId}_status", $progressData, 3600);
        }
    }

    /**
     * Add a message to the progress data
     */
    protected function addMessage($text, $type = 'info')
    {
        $this->updateProgress(null, null, [
            [
                'text' => $text,
                'type' => $type
            ]
        ]);
    }

    /**
     * Complete the import process
     */
    protected function completeImport()
    {
        // Get fiscal year info for message
        $fiscalYear = FiscalYear::find($this->fiscalYearId);
        $yearLabel = $fiscalYear ? $fiscalYear->year : 'the selected fiscal year';

        $this->updateProgress('completed', 'Import completed successfully!', [
            [
                'text' => "Budget income data for {$yearLabel} has been successfully imported.",
                'type' => 'success'
            ]
        ]);

        // Clean up stored file
        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        }
    }

    /**
     * Mark the import as failed
     */
    protected function failImport($message)
    {
        $this->updateProgress('failed', 'Import failed', [
            [
                'text' => $message,
                'type' => 'error'
            ]
        ]);

        // Clean up stored file
        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        }
    }

    /**
     * Check if the job was cancelled
     */
    protected function checkIfCancelled()
    {
        $progressData = Cache::get("import_job_{$this->jobId}_status");
        return !$progressData || $progressData['status'] === 'cancelled';
    }
}
