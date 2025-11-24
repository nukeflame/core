<?php

namespace App\Jobs;

use App\Models\ClaimRegister;
use App\Models\CoverAttachment;
use App\Models\CoverClass;
use App\Models\CoverClause;
use App\Models\CoverDebit;
use App\Models\CoverInstallments;
use App\Models\CoverPremium;
use App\Models\CoverPremtype;
use App\Models\CoverRegister;
use App\Models\CoverReinclass;
use App\Models\CoverReinLayer;
use App\Models\CoverReinProp;
use App\Models\CoverRipart;
use App\Models\CoverRisk;
use App\Models\CoverSlipWording;
use App\Models\Customer;
use App\Models\CustomerAccDet;
use App\Models\PolicyRenewal;
use App\Models\ReinNote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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
                ->chunk(100, function ($covers) use (&$coverCount, &$deletedRecords) {
                    foreach ($covers as $cover) {
                        $coverNumbers[] = $cover->cover_no;
                        $deleted = $this->deleteCoverData($cover->cover_no);
                        $deletedRecords = $this->mergeDeletedRecords($deletedRecords, $deleted);
                        $coverCount++;
                    }
                });

            CoverRegister::where('customer_id', $customer->customer_id)
                ->forceDelete();

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

        $modelMappings = [
            'cover_attachments' => [CoverAttachment::class, 'cover_no'],
            'cover_classes' => [CoverClass::class, 'cover_no'],
            'cover_debits' => [CoverDebit::class, 'cover_no'],
            'cover_installments' => [CoverInstallments::class, 'cover_no'],
            'cover_premiums' => [CoverPremium::class, 'cover_no'],
            'cover_premtypes' => [CoverPremtype::class, 'cover_no'],
            'cover_reinclasses' => [CoverReinclass::class, 'cover_no'],
            'cover_risks' => [CoverRisk::class, 'cover_no'],
            'cover_rein_layers' => [CoverReinLayer::class, 'cover_no'],
            'cover_rein_props' => [CoverReinProp::class, 'cover_no'],
            'cover_riparts' => [CoverRipart::class, 'cover_no'],
            'cover_slip_wordings' => [CoverSlipWording::class, 'cover_no'],
            'claim_registers' => [ClaimRegister::class, 'cover_no'],
            'customer_acc_dets' => [CustomerAccDet::class, 'cover_no'],
            'rein_notes' => [ReinNote::class, 'cover_no'],
            'cover_clauses' => [CoverClause::class, 'cover_no'],
            // 'policy_renewals' => [PolicyRenewal::class, 'policy_number'],
        ];

        foreach ($modelMappings as $key => [$modelClass, $column]) {
            try {
                $count = $modelClass::where($column, $coverNo)->forceDelete();

                if ($count > 0) {
                    $deletedCounts[$key] = $count;
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return $deletedCounts;
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
