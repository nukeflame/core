<?php

namespace App\Jobs;

use App\Models\ClaimRegister;
use App\Models\CoverAttachment;
use App\Models\CoverClass;
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

class ClearCedantDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customerId;

    /**
     * Create a new job instance.
     *
     * @param int $customerId
     * @return void
     */
    public function __construct($customerId)
    {
        $this->customerId = $customerId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $customer = Customer::find($this->customerId);

            if (!$customer) {
                return;
            }

            $covers = CoverRegister::where('customer_id', $customer->customer_id);

            foreach ($covers->withTrashed()->get() as $cover) {
                $this->deleteCoverData($cover->cover_no);
            }

            $covers->forceDelete();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Delete all related data for a specific cover number
     *
     * @param string $coverNo
     * @return void
     */
    protected function deleteCoverData($coverNo)
    {
        $models = [
            CoverAttachment::class,
            CoverClass::class,
            CoverDebit::class,
            CoverInstallments::class,
            CoverPremium::class,
            CoverPremtype::class,
            CoverReinclass::class,
            CoverRisk::class,
            CoverReinLayer::class,
            CoverReinProp::class,
            CoverRipart::class,
            CoverSlipWording::class,
            ClaimRegister::class,
            CustomerAccDet::class,
            ReinNote::class
        ];

        foreach ($models as $model) {
            $model::where('cover_no', $coverNo)->forceDelete();
        }

        PolicyRenewal::where('policy_number', $coverNo)->forceDelete();
    }
}
