<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Customer;
use App\Models\ClaimDebit;
use App\Models\Classes;
use App\Models\ClaimReinNote;
use App\Models\CoverPremium;
use App\Models\ClaimRegister;
use App\Models\CoverRegister;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class Claim_GL_Integration extends Command
{
    protected $signature = 'claim-credits:glupdate';
    protected $description = 'Push all claim credits transactions to GLTransactions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $datas = ClaimDebit::where('gl_updated', '!=', 'Y')
                ->orWhereNull('gl_updated')
                ->get();

            if ($datas->isNotEmpty()) {
                foreach ($datas as $data) {
                    $claimdebit = ClaimDebit::where('claim_no', $data->claim_no)->first();
                    $coverReg = CoverRegister::where('endorsement_no', $data->endorsement_no)->first();
                    $claimReg = ClaimRegister::where('claim_no', $data->claim_no)->first();
                    $customer = Customer::where('customer_id', $coverReg->customer_id)->first();
                    $class = Classes::where('class_code', $coverReg->class_code)->first();

                    if ($claimdebit && $coverReg && $claimReg && $customer && $class) {
                        $countItem = 0;
                        $claimItems = [];
                        $dr_cr_note_no = str_pad($claimdebit->dr_no, 6, '0', STR_PAD_LEFT) . $claimdebit->period_year;
                        $partnerItems = DB::table('claim_perils')->where('claim_no', $claimdebit->claim_no)
                            ->where('dr_cr_note_no', $dr_cr_note_no)
                            ->get()
                            ->groupBy('peril_name')
                            ->map(function ($itemsByPeril, $perilDescription) use ($coverReg, &$countItem) {
                                $countItem++;
                                $net_amt = $itemsByPeril->sum('final_amount');

                                return [
                                    'item_number' => (string)$countItem,
                                    'item_name' => $itemsByPeril->first()->entry_type_descr,
                                    'item_description' => $perilDescription,
                                    'foreign_unit_cost' => $net_amt,
                                    'local_unit_cost' => ($net_amt * ($coverReg->currency_rate ?: 1)),
                                    'foreign_gross_total_cost' => $net_amt,
                                    'local_gross_total_cost' => ($net_amt * ($coverReg->currency_rate ?: 1)),
                                    'discount_type' => null,
                                    'foreign_discount_amount' => 0,
                                    'local_discount_amount' => 0,
                                    'foreign_nett_total_amount' => $net_amt,
                                    'local_nett_total_amount' => ($net_amt * ($coverReg->currency_rate ?: 1)),
                                    'quantity' => $itemsByPeril->count(),
                                    'status' => 'A',
                                    'dr_cr' => $itemsByPeril->first()->dr_cr === 'DR' ? 'C' : 'D',
                                ];
                            })->values()->toArray();

                        $claimItems = array_merge($claimItems, $partnerItems);
                        $countItem++;

                        //Net Payable Amount Computations
                        $totalDebit = DB::table('claim_perils')->where('claim_no', $claimdebit->claim_no)
                            ->where('dr_cr_note_no', $dr_cr_note_no)
                            ->where('dr_cr', 'DR')
                            ->sum('final_amount') ?? 0;
                        $totalCredit = DB::table('claim_perils')->where('claim_no', $claimdebit->claim_no)
                            ->where('dr_cr_note_no', $dr_cr_note_no)
                            ->where('dr_cr', 'CR')
                            ->sum('final_amount') ?? 0;
                        $netAmt = abs($totalCredit - $totalDebit);

                        $netItem = [
                            'item_number' => (string)$countItem,
                            'item_name' => 'NET',
                            'item_description' => 'NET',
                            'foreign_unit_cost' => $netAmt,
                            'local_unit_cost' => ($netAmt * ($coverReg->currency_rate ?: 1)),
                            'foreign_gross_total_cost' => $netAmt,
                            'local_gross_total_cost' => ($netAmt * ($coverReg->currency_rate ?: 1)),
                            'discount_type' => null,
                            'foreign_discount_amount' => 0,
                            'local_discount_amount' => 0,
                            'foreign_nett_total_amount' => $netAmt,
                            'local_nett_total_amount' => ($netAmt * ($coverReg->currency_rate ?: 1)),
                            'quantity' => 1,
                            'status' => 'A',
                            'dr_cr' => $claimdebit->document === 'DRN' ? 'C' : 'D'
                        ];

                        $claimItems[] = $netItem;

                        $approval = DB::table('approvals_tracker')
                            ->whereRaw("CAST(data::json->>'claim_no' AS TEXT) = ?", [$claimdebit->claim_no])
                            ->whereRaw("CAST(data::json->>'dr_no' AS TEXT) = ?", [$dr_cr_note_no])
                            ->where('status', 'A')
                            ->first();

                        $document = $claimdebit->document === 'DRN' ? 'DRN' : 'CRN';
                        if ($claimReg->date_notified_insurer) {
                            $dueDateCarbon = Carbon::parse($claimReg->date_notified_insurer);
                            $due_date = $dueDateCarbon->addDays((int) $coverReg->premium_payment_days);
                        } else {
                            $due_date = null;
                        }
                        // Prepare the API request payload
                        $requestPayload = [
                            'order' => [
                                'partner_number' => $customer->partner_number,
                                'order_type' => $coverReg->type_of_bus,
                                'currency_code' => $claimReg->currency_code,
                                'currency_rate' => $claimReg->currency_rate,
                                'dr_cr' => substr($document, 0, 1),
                                'order_due_date' => $due_date,
                                'local_amount' => $claimdebit->net_amt * $claimReg->currency_rate ? $claimReg->currency_rate : 1,
                                'notes' => $document . ' document',
                                'terms_conditions' => null,
                                'created_by' => $claimdebit->created_by,
                                'updated_by' => $claimdebit->updated_by ?? $claimdebit->created_by,
                                'created_at' => $claimdebit->created_at,
                                'updated_at' => $claimdebit->updated_at,
                                'approved_by' => $approval ? $approval->updated_by : $claimdebit->created_by,
                                'approved_at' => $approval ? $approval->updated_at : $claimdebit->created_at,
                                'type_of_bus' => 'CLM', //$claimReg->type_of_bus,
                                'transaction_type' => $document,
                                'order_status' => 'A',
                                'subsidiary_code' => 'REINSURANCE',
                                'foreign_amount' => $claimdebit->net_amt,
                                'lob_reference_1' => $claimdebit->cover_no,
                                'lob_reference_2' => $claimdebit->endorsement_no,
                                'lob_reference_3' => str_pad($claimdebit->dr_no, 5, '0', STR_PAD_LEFT) . $claimdebit->period_year,
                                'lob_order_reference' => str_pad($claimdebit->dr_no, 5, '0', STR_PAD_LEFT) . $claimdebit->period_year,
                                'lob_invoice_reference' => str_pad($claimdebit->dr_no, 5, '0', STR_PAD_LEFT) . $claimdebit->period_year,
                                'order_title' => (in_array($claimReg->type_of_bus, ['FPR', 'FNP'])
                                    ? $claimReg->insured_name . '-'
                                    : '') .
                                    ($claimReg->class_code === 'TRT'
                                        ? 'Treaty'
                                        : $class->class_name) . '-' .
                                    $customer->name,
                                'branch' => $claimReg->branch_code,
                                'department_code' => null,
                                'channel_type_code' => 'INSCO',
                                'channel_code' => 'INSCO',
                                'channel_is_customer' => 'N',
                            ],
                            'items' => $claimItems,
                            'attachments' => [],
                        ];

                        // dd('requestPayload', $requestPayload);

                        // Send the API request
                        $response = Http::withOptions([
                            'cert' => [storage_path('certs/acfinance.local-cert.pem'), 'UzMkgwdHseFXvKjB'], // Certificate with password
                            'ssl_key' => storage_path('certs/acfinance.local-key.pem'), // Private key
                            // 'verify' => storage_path('certs/client-ca.pem'), // Optional: CA validation
                        ])->withHeaders([
                            'Reference-ID' => '4fjaZ70kuZTCgTBpCu3aGbCVBNLkja5T',
                        ])->post(env('FINANCE_URL') . '/api/postAPOrder', $requestPayload);

                        if ($response->successful()) {
                            $gl_invoice_reference = $response->json()['invoice_reference'];
                            ClaimDebit::where('endorsement_no', $data->endorsement_no)->update([
                                'gl_updated' => 'Y',
                                'gl_updated_retries' => $claimdebit->gl_updated_retries + 1,
                                'gl_updated_at' => Carbon::now(),
                                'gl_updated_by' => 'system',
                                'gl_updated_errors' => null,
                                'gl_updated_invoice_reference' => $gl_invoice_reference
                            ]);
                            $this->info("Debit ID {$data->id} processed successfully.");
                        } else {
                            $errors = $response->json('errors');

                            if (is_array($errors)) {
                                $formattedErrors = collect($errors)
                                    ->map(function ($messages, $field) {
                                        $msg = is_array($messages) ? $messages : [$messages];
                                        return "$field: " . implode(', ', $msg);
                                    })
                                    ->implode('; ');
                            } else {
                                // dd( $response);
                                $formattedErrors = 'Unexpected error structure';
                            }

                            ClaimDebit::where('claim_no', $data->claim_no)->update([
                                'gl_updated_errors' => $formattedErrors,
                            ]);

                            $this->error("Error for Claim No {$data->claim_no}: $formattedErrors");
                        }
                    } else {
                        $this->error("Error: Missing required related data for Claim Debit ID: {$data->id}");
                    }
                }
            } else {
                $this->info("No transaction to process");
            }
            $this->info("All transactions have been processed");
        } catch (\Exception $e) {
            $this->error("Error processing transaction : " . $e->getMessage());
        }
    }
}
