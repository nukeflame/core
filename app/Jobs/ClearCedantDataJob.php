<?php

namespace App\Jobs;

use App\Models\CoverRegister;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ClearCedantDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $customerId;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(int $customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $customer = Customer::find($this->customerId);

            if (!$customer) {
                return;
            }

            $this->clearCustomerData($customer);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Clear all customer-related data within a transaction
     */
    protected function clearCustomerData(Customer $customer): void
    {
        DB::transaction(function () use ($customer) {
            $coverCount = 0;
            $deletedRecords = [];
            $coverNumbers = [];

            CoverRegister::where('customer_id', $customer->customer_id)
                ->withTrashed()
                ->select(['cover_no'])
                ->chunk(100, function ($covers) use (&$coverCount, &$deletedRecords, &$coverNumbers) {
                    foreach ($covers as $cover) {
                        $coverNumbers[] = $cover->cover_no;
                        $deleted = $this->deleteCoverData($cover->cover_no);
                        $deletedRecords = $this->mergeDeletedRecords($deletedRecords, $deleted);
                        $coverCount++;
                    }
                });

            CoverRegister::where('customer_id', $customer->customer_id)
                ->forceDelete();

            $this->deleteCustomerScopedData($customer->customer_id);
            DB::table('customers')
                ->where('customer_id', $customer->customer_id)
                ->delete();

            $this->clearRelatedCaches($customer->customer_id, $coverNumbers);
        });
    }

    /**
     * Delete all related data for a specific cover number
     *
     * @return array Count of deleted records by model
     */
    protected function deleteCoverData(string $coverNo): array
    {
        $deletedCounts = [];

        $tableMappings = [
            'cover_attachments' => 'cover_no',
            'cover_classes' => 'cover_no',
            'cover_debits' => 'cover_no',
            'cover_installments' => 'cover_no',
            'cover_premiums' => 'cover_no',
            'cover_premtypes' => 'cover_no',
            'cover_reinclasses' => 'cover_no',
            'cover_risks' => 'cover_no',
            'cover_rein_layers' => 'cover_no',
            'cover_rein_props' => 'cover_no',
            'cover_riparts' => 'cover_no',
            'cover_slip_wordings' => 'cover_no',
            'claim_registers' => 'cover_no',
            'customer_acc_dets' => 'cover_no',
            'rein_notes' => 'cover_no',
            'cover_clauses' => 'cover_no',
            'policy_renewals' => 'policy_number',
        ];

        foreach ($tableMappings as $table => $column) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
                continue;
            }

            $count = DB::table($table)->where($column, $coverNo)->delete();
            if ($count > 0) {
                $deletedCounts[$table] = $count;
            }
        }

        return $deletedCounts;
    }

    protected function deleteCustomerScopedData(int $customerId): void
    {
        $tableMappings = [
            'customer_contacts' => 'customer_id',
            'customer_acc_dets' => 'customer_id',
            'claim_ntf_registers' => 'customer_id',
            'pipeline_opportunities' => 'customer_id',
            'reinsurers_declined' => 'customer_id',
            'quote_reinsurers' => 'reinsurer_id',
            'bd_fac_reinsurers' => 'reinsurer_id',
            'credit_notes' => 'reinsurer_id',
            'ar_customers' => 'customer_id',
        ];

        foreach ($tableMappings as $table => $column) {
            if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
                continue;
            }

            DB::table($table)->where($column, $customerId)->delete();
        }
    }

    /**
     * Merge deleted record counts
     */
    protected function mergeDeletedRecords(array $existing, array $new): array
    {
        foreach ($new as $key => $count) {
            $existing[$key] = ($existing[$key] ?? 0) + $count;
        }

        return $existing;
    }

    /**
     * Clear all caches related to the customer and their covers
     */
    protected function clearRelatedCaches(int $customerId, array $coverNumbers): void
    {
        $clearedCaches = [];

        $customerCacheKeys = [
            "debited_covers_{$customerId}",
            "customer_covers_{$customerId}",
            "customer_policies_{$customerId}",
            "customer_claims_{$customerId}",
            "customer_premiums_{$customerId}",
            "customer_summary_{$customerId}",
            "customer_transactions_{$customerId}",
            "customer_debit_notes_{$customerId}",
            "customer_statistics"
        ];

        foreach ($customerCacheKeys as $key) {
            if (Cache::has($key)) {
                Cache::forget($key);
                $clearedCaches[] = $key;
            }
        }

        foreach ($coverNumbers as $coverNo) {
            $coverCacheKeys = [
                "cover_details_{$coverNo}",
                "cover_premiums_{$coverNo}",
                "cover_claims_{$coverNo}",
                "cover_attachments_{$coverNo}",
                "cover_reinsurance_{$coverNo}",
                "cover_debits_{$coverNo}",
                "policy_renewals_{$coverNo}",
            ];

            foreach ($coverCacheKeys as $key) {
                if (Cache::has($key)) {
                    Cache::forget($key);
                    $clearedCaches[] = $key;
                }
            }
        }

        try {
            if (method_exists(Cache::store(), 'tags')) {
                Cache::tags(['customer_' . $customerId])->flush();
                Cache::tags(['covers'])->flush();
                $clearedCaches[] = "tags:customer_{$customerId}";
                $clearedCaches[] = "tags:covers";
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        logger()->critical("Cedant data clearance job failed permanently", [
            'customer_id' => $this->customerId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // Optionally notify administrators
        // Notification::route('mail', config('app.admin_email'))
        //     ->notify(new JobFailedNotification($this, $exception));
    }
}
