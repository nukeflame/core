<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Customer;
use App\Models\CustomerTypes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PartnerIntegration extends Command
{
    protected $signature = 'partner:glupdate {--limit=100 : Number of customers to process per batch} {--dry-run : Run without making actual API calls}';
    protected $description = 'Push all new Partners to Finance';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $limit = (int) $this->option('limit');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('Running in DRY RUN mode - no actual API calls will be made');
        }

        $processedCount = 0;
        $errorCount = 0;

        try {
            // Get total count for progress tracking
            $totalCustomers = Customer::where(function ($query) {
                $query->whereNull('partner_number')
                    ->orWhereRaw('TRIM(partner_number) = \'\'');
            })->count();

            if ($totalCustomers === 0) {
                $this->info("No customers to process.");
                return Command::SUCCESS;
            }

            $this->info("Found {$totalCustomers} customers to process");
            $progressBar = $this->output->createProgressBar($totalCustomers);
            $progressBar->start();

            Customer::where(function ($query) {
                $query->whereNull('partner_number')
                    ->orWhereRaw('TRIM(partner_number) = \'\'');
            })->orderBy('customer_id')
                ->chunk($limit, function ($customers) use (&$processedCount, &$errorCount, $dryRun, $progressBar) {
                    foreach ($customers as $customer) {
                        try {
                            $customer = Customer::where('customer_id', $customer->customer_id)->first();

                            if (!$customer) {
                                $this->warn("Customer with ID {$customer->customer_id} not found during refresh");
                                $progressBar->advance();
                                continue;
                            }

                            if (empty($customer->name)) {
                                $this->error("Customer ID {$customer->customer_id} missing required name field");
                                $errorCount++;
                                $progressBar->advance();
                                continue;
                            }

                            $customerTypes = [];
                            if (is_array($customer->customer_type)) {
                                $customerTypes = $customer->customer_type;
                            } elseif (is_string($customer->customer_type)) {
                                $decoded = json_decode($customer->customer_type, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $customerTypes = $decoded;
                                } else {
                                    $customerTypes = array_map('trim', explode(',', $customer->customer_type));
                                }
                            } elseif (!empty($customer->customer_type)) {
                                $customerTypes = [$customer->customer_type];
                            }

                            if (empty($customerTypes)) {
                                $this->warn("Customer ID {$customer->customer_id} has no customer types");
                                $progressBar->advance();
                                continue;
                            }

                            $channel_typeRecs = CustomerTypes::whereIn('type_id', $customerTypes)->get();

                            if ($channel_typeRecs->isEmpty()) {
                                $this->warn("No valid channel types found for Customer ID {$customer->customer_id} with types: " . implode(', ', $customerTypes));
                                $progressBar->advance();
                                continue;
                            }

                            $successfulUpdate = false;

                            foreach ($channel_typeRecs as $channel_typeRec) {
                                $requestPayload = [
                                    'partner' => [
                                        'partner_type' => 'C', // 'I' for Individual, 'C' for Corporate
                                        'channel_type' => $channel_typeRec->code ?? '',
                                        'partner_name' => $customer->name ?? '',
                                        'telephone' => $customer->telephone ? str_replace(' ', '', $customer->telephone) : '',
                                        'identity_number_type' => $customer->identity_number_type ?? '',
                                        'identity_number' => $customer->identity_number ?? '',
                                        'incorporation_no' => $customer->registration_no ?? '',
                                        'incorporation_date' => $customer->startdate ? Carbon::parse($customer->startdate)->format('Y-m-d') : null,
                                        'postal_address' => $customer->postal_address ?? '',
                                        'postal_street' => $customer->street ?? '',
                                        'postal_city' => $customer->city ?? '',
                                        'country_iso' => $customer->country_iso ?? '',
                                        'email' => $customer->email ?? '',
                                        'status' => 'A',
                                        'created_by' => $customer->created_by ?? 'system',
                                        'updated_by' => $customer->created_by ?? 'system',
                                        'created_date' => $customer->created_at ? Carbon::parse($customer->created_at)->format('Y-m-d') : now()->format('Y-m-d'),
                                        'updated_date' => $customer->created_at ? Carbon::parse($customer->created_at)->format('Y-m-d') : now()->format('Y-m-d'),
                                    ],
                                ];

                                if ($dryRun) {
                                    $this->info("DRY RUN: Would send API request for Customer ID {$customer->customer_id}");
                                    $successfulUpdate = true;
                                    break;
                                }

                                if (empty(env('FINANCE_URL'))) {
                                    throw new \Exception('FINANCE_URL environment variable not set');
                                }

                                $certPath = storage_path('certs/acfinance.local-cert.pem');
                                $keyPath = storage_path('certs/acfinance.local-key.pem');

                                if (!file_exists($certPath)) {
                                    throw new \Exception("Certificate file not found: {$certPath}");
                                }

                                if (!file_exists($keyPath)) {
                                    throw new \Exception("Private key file not found: {$keyPath}");
                                }

                                $response = Http::timeout(30)
                                    ->retry(3, 1000)
                                    ->withOptions([
                                        'cert' => [$certPath, 'UzMkgwdHseFXvKjB'],
                                        'ssl_key' => $keyPath,
                                        'verify' => false,
                                    ])->withHeaders([
                                        'Reference-ID' => '4fjaZ70kuZTCgTBpCu3aGbCVBNLkja5T',
                                        'Content-Type' => 'application/json',
                                        'Accept' => 'application/json',
                                    ])->post(env('FINANCE_URL') . '/api/postPartner', $requestPayload);

                                if ($response->successful()) {
                                    $responseData = $response->json();
                                    $partner_number = $responseData['partner_number'] ?? null;

                                    if (!$partner_number) {
                                        throw new \Exception('API response successful but no partner_number returned');
                                    }

                                    Customer::where('customer_id', $customer->customer_id)->update([
                                        'partner_number' => $partner_number,
                                        'partner_number_errors' => null,
                                    ]);

                                    $this->info("Customer ID {$customer->customer_id} processed successfully. Partner Number: {$partner_number}");

                                    $successfulUpdate = true;
                                    break;
                                } else {
                                    $statusCode = $response->status();
                                    $responseBody = $response->body();
                                    $errors = $response->json('errors') ?? 'No error details provided';

                                    $formattedErrors = $this->formatApiErrors($errors, $statusCode, $responseBody);

                                    $this->warn("API error for Customer ID {$customer->customer_id} with channel type {$channel_typeRec->code}: {$formattedErrors}");
                                }
                            }

                            // If no successful update occurred, save the last error
                            if (!$successfulUpdate && !$dryRun) {
                                $lastError = $formattedErrors ?? 'Failed to process any channel type';
                                Customer::where('customer_id', $customer->customer_id)->update([
                                    'partner_number_errors' => $lastError,
                                    'partner_number' => null,
                                ]);

                                $this->error("Failed to process Customer ID {$customer->customer_id}: {$lastError}");
                                $errorCount++;
                            } else {
                                $processedCount++;
                            }
                        } catch (\Exception $e) {
                            $errorMessage = "Exception processing Customer ID {$customer->customer_id}: " . $e->getMessage();
                            $this->error($errorMessage);

                            if (!$dryRun) {
                                Customer::where('customer_id', $customer->customer_id)->update([
                                    'partner_number_errors' => $e->getMessage(),
                                    'partner_number' => null,
                                ]);
                            }

                            $errorCount++;
                        }

                        $progressBar->advance();
                    }
                });

            $progressBar->finish();
            $this->newLine();

            $this->info("Processing completed!");
            $this->info("Successfully processed: {$processedCount} customers");
            $this->info("Errors encountered: {$errorCount} customers");

            return $errorCount > 0 ? Command::FAILURE : Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Critical error processing customers: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Format API errors into a readable string
     */
    private function formatApiErrors($errors, $statusCode = null, $responseBody = null): string
    {
        if (is_array($errors)) {
            $formatted = collect($errors)->map(function ($messages, $field) {
                $msg = is_array($messages) ? $messages : [$messages];
                return "$field: " . implode(', ', $msg);
            })->implode('; ');
        } elseif (is_string($errors)) {
            $formatted = $errors;
        } else {
            $formatted = 'Unexpected error structure';
        }

        if ($statusCode) {
            $formatted = "HTTP {$statusCode}: {$formatted}";
        }

        return $formatted;
    }
}
