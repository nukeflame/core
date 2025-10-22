<?php

namespace App\Console\Commands;

use App\Imports\PipelineImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class ImportPipelineData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pipeline:import {file : Path to the Excel file} {--dry-run : Run without saving data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import pipeline data from an Excel file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $isDryRun = $this->option('dry-run');

        $this->info('Starting pipeline data import...');

        if ($isDryRun) {
            $this->warn('Running in dry-run mode, no data will be saved.');
        }

        try {
            // Validate file exists
            if (!file_exists($filePath)) {
                throw new \Exception('File does not exist: ' . $filePath);
            }

            // Validate file type
            $mimeType = mime_content_type($filePath);
            $validMimes = [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ];

            if (!in_array($mimeType, $validMimes)) {
                throw new \Exception('Invalid file type. Please upload an Excel file or CSV. Detected type: ' . $mimeType);
            }

            // Start import
            $startTime = microtime(true);

            $import = new PipelineImport();

            if ($isDryRun) {
                // For dry run, manually process without saving
                $this->info('Validating file structure...');
                Excel::toCollection($import, $filePath);
            } else {
                // Perform the actual import
                Excel::import($import, $filePath);
            }

            $errors = $import->getErrors();
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            // Display results
            if (empty($errors)) {
                $this->info('Pipeline data import completed successfully!');
                $this->info("Import time: {$executionTime} seconds");
                return self::SUCCESS;
            } else {
                $this->warn('Import completed with warnings:');
                foreach ($errors as $error) {
                    $this->warn(" - {$error}");
                }

                return self::SUCCESS;
            }
        } catch (ValidationException $e) {
            $this->error('Validation failed:');
            foreach ($e->failures() as $failure) {
                $this->error(" - Row {$failure->row()}: {$failure->errors()[0]}");
            }

            return self::FAILURE;
        } catch (Throwable $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
