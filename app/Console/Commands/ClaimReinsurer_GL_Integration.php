<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Customer;
use App\Models\CoverRipart;
use App\Models\ClaimReinNote;
use App\Models\ClaimDebit;
use App\Models\Classes;
use App\Models\ClaimRegister;
use App\Models\CoverRegister;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ClaimReinsurer_GL_Integration extends Command
{
    protected $signature = 'claimReinsurer:glupdate';
    protected $description = 'Push all Debitted Reinsurer transactions to GLTransactions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $datas = DB::table('claim_debit as db')
                ->where('db.gl_updated', 'Y')
                ->join('claim_rein_notes as ri', 'ri.claim_no', '=', 'db.claim_no')
                ->whereColumn('ri.dr_no', '=', 'db.dr_no') // Corrected join condition
                ->where('ri.gl_updated', '!=', 'Y')
                ->select('db.*')
                ->distinct('db.id')
                ->get();

            if ($datas->isNotEmpty()) {
                foreach ($datas as $data) {
                    $claimdebit = ClaimDebit::where('claim_no', $data->claim_no)->first();
                    $coverReg = CoverRegister::where('endorsement_no', $data->endorsement_no)->first();
                    $claimReg = ClaimRegister::where('endorsement_no', $data->endorsement_no)->first();
                    $class = Classes::where('class_code', $coverReg->class_code)->first();
                    $dr_cr_note_no = str_pad($claimdebit->dr_no, 6, '0', STR_PAD_LEFT) . $claimdebit->period_year;

                    if ($claimdebit) {
                        $participants = ClaimReinNote::select('claim_no', 'partner_no', 'dr_no')
                            ->where('claim_no', $data->claim_no)
                            ->where('dr_no', $data->dr_no)
                            ->where('gl_updated', '!=', 'Y')
                            ->groupBy('claim_no', 'dr_no', 'partner_no')
                            ->get();

                        foreach ($participants as $participant) {
                            $countItem = 0;
                            $claimItems = [];
                            $customer = Customer::where('customer_id', $participant->partner_no)->first();

                            // Computations
                            $partnerItems = ClaimReinNote::where('claim_no', $participant->claim_no)
                                ->where('dr_no', $participant->dr_no)
                                ->where('partner_no', $participant->partner_no)
                                ->get()
                                ->groupBy('item_title')
                                ->map(function ($itemsByPeril, $premiumTypeDescription) use ($claimReg, &$countItem) {
                                    $countItem++;
                                    $net_amt = (float) $itemsByPeril->sum('net_amt');
                                    return [
                                        'item_number' => (string)$countItem,
                                        'item_name' => $itemsByPeril->first()->entry_type_descr,
                                        'item_description' => $premiumTypeDescription,
                                        'foreign_unit_cost' => $net_amt,
                                        'local_unit_cost' => ($net_amt * ($claimReg->currency_rate ?: 1)),
                                        'foreign_gross_total_cost' => $net_amt,
                                        'local_gross_total_cost' => ($net_amt * ($claimReg->currency_rate ?: 1)),
                                        'discount_type' => null,
                                        'foreign_discount_amount' => 0,
                                        'local_discount_amount' => 0,
                                        'foreign_nett_total_amount' => $net_amt,
                                        'local_nett_total_amount' => (float)($net_amt * ($claimReg->currency_rate ?: 1)),
                                        'quantity' => $itemsByPeril->count(),
                                        'status' => 'A',
                                        'dr_cr' => $itemsByPeril->first()->dr_cr === 'DR' ? 'C' : 'D',
                                    ];
                                })->values()->toArray();

                            $claimItems = array_merge($claimItems, $partnerItems);
                            $countItem++;

                            //Net Payable Amount Computations
                            $totalDebit = ClaimReinNote::where('claim_no', $participant->claim_no)
                                ->where('dr_no', $participant->dr_no)
                                ->where('partner_no', $participant->partner_no)
                                ->where('entry_type_descr', '!=', 'BRC')
                                ->where('dr_cr', 'DR')
                                ->sum('net_amt') ?? 0;
                            $totalCredit = ClaimReinNote::where('claim_no', $participant->claim_no)
                                ->where('dr_no', $participant->dr_no)
                                ->where('partner_no', $participant->partner_no)
                                ->where('entry_type_descr', '!=', 'BRC')
                                ->where('dr_cr', 'CR')
                                ->sum('net_amt') ?? 0;
                            $netAmt = (float) number_format(abs($totalCredit - $totalDebit), 2, '.', '');
                            $netItem = [
                                'item_number' => (string)$countItem,
                                'item_name' => 'NET',
                                'item_description' => 'NET',
                                'foreign_unit_cost' => $netAmt,
                                'local_unit_cost' => ($netAmt * ($claimReg->currency_rate ?: 1)),
                                'foreign_gross_total_cost' => $netAmt,
                                'local_gross_total_cost' => ($netAmt * ($claimReg->currency_rate ?: 1)),
                                'discount_type' => null,
                                'foreign_discount_amount' => 0,
                                'local_discount_amount' => 0,
                                'foreign_nett_total_amount' => $netAmt,
                                'local_nett_total_amount' => $netAmt * ($claimReg->currency_rate ?: 1),
                                'quantity' => 1,
                                'status' => 'A',
                                'dr_cr' =>  $claimdebit->document === 'DRN' ? 'C' : 'D'
                            ];
                            $countItem++;

                            $claimItems[] = $netItem;

                            $approval = DB::table('approvals_tracker')
                                ->whereRaw("CAST(data::json->>'claim_no' AS TEXT) = ?", [$claimdebit->claim_no])
                                ->whereRaw("CAST(data::json->>'dr_no' AS TEXT) = ?", [$dr_cr_note_no])
                                ->where('status', 'A')
                                ->first();

                            $document = $claimdebit->document === 'DRN' ? 'CRN' : 'DRN';
                            if ($claimReg->date_notified_reinsurer) {
                                $dueDateCarbon = Carbon::parse($claimReg->date_notified_reinsurer);
                                $due_date = $dueDateCarbon->addDays((int) $coverReg->premium_payment_days);
                            } else {
                                $due_date = null;
                            }
                            // Prepare the API request payload
                            $requestPayload = [
                                'invoice' => [
                                    'partner_number' => $customer->partner_number,
                                    'invoice_type' => $coverReg->type_of_bus,
                                    'currency_code' => $claimReg->currency_code,
                                    'currency_rate' => $claimReg->currency_rate,
                                    'dr_cr' => substr($claimdebit->document, 0, 1),
                                    'invoice_due_date' => $due_date,
                                    'local_amount' => $netAmt * $claimReg->currency_rate,
                                    'notes' => $document . ' document',
                                    'terms_conditions' => null,
                                    'created_by' => $claimdebit->created_by ?? 'system',
                                    'updated_by' => $claimdebit->updated_by ?? 'system',
                                    'created_at' => $claimdebit->created_at ?? now(),
                                    'updated_at' => $claimdebit->updated_at ?? now(),
                                    'approved_by' =>  $approval->updated_by ?? 'system',
                                    'approved_at' => $approval->updated_at ?? now(),
                                    'type_of_bus' => $claimReg->type_of_bus,
                                    'transaction_type' => $claimdebit->document,
                                    'invoice_status' => 'A',
                                    'subsidiary_code' => 'REINSURANCE',
                                    'foreign_amount' => $netAmt,
                                    'lob_reference_1' => $claimdebit->cover_no,
                                    'lob_reference_2' => $claimdebit->endorsement_no,
                                    'lob_reference_3' => null,
                                    'lob_invoice_reference' => str_pad($claimdebit->dr_no, 5, '0', STR_PAD_LEFT) . $claimdebit->period_year,
                                    'invoice_title' => (in_array($claimReg->type_of_bus, ['FPR', 'FNP'])
                                        ? $claimReg->insured_name . '-'
                                        : '') .
                                        ($claimReg->class_code === 'TRT'
                                            ? 'Treaty'
                                            : $class->class_name) . '-' .
                                        $customer->name,
                                    'branch' => $claimReg?->branch_code,
                                    'department_code' => null,
                                    'channel_type_code' => 'REINCO',
                                    'channel_code' => 'REINCO',
                                    'channel_is_customer' => 'N',
                                ],
                                'items' => $claimItems,
                                'attachments' => [], // Optional attachments
                            ];

                            // dd(json_encode($requestPayload, JSON_PRETTY_PRINT));

                            $response = Http::withOptions([
                                'cert' => [storage_path('certs/acfinance.local-cert.pem'), 'UzMkgwdHseFXvKjB'], // Certificate with password
                                'ssl_key' => storage_path('certs/acfinance.local-key.pem'), // Private key
                                // 'verify' => storage_path('certs/client-ca.pem'), // Optional: CA validation
                            ])->withHeaders([
                                'Reference-ID' => '4fjaZ70kuZTCgTBpCu3aGbCVBNLkja5T',
                            ])->post(env('FINANCE_URL') . '/api/postARInvoice', $requestPayload);
                            // dd($response);
                            if ($response->successful()) {
                                $gl_invoice_reference = $response->json()['invoice_reference'];
                                ClaimReinNote::where('claim_no', $claimdebit->claim_no)
                                    ->where('dr_no', $participant->dr_no)
                                    ->where('partner_no', $participant->partner_no)
                                    ->update([
                                        'gl_updated' => 'Y',
                                        'gl_updated_retries' => $claimdebit->gl_updated_retries + 1,
                                        'gl_updated_at' => Carbon::now(),
                                        'gl_updated_by' => 'system',
                                        'gl_updated_errors' => '',
                                        'gl_updated_order_reference' => $gl_invoice_reference
                                    ]);
                                $this->info("Participant No {$participant->partner_no} processed successfully.");
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
                                    $formattedErrors = 'Unexpected error structure';
                                }
                                ClaimDebit::where('claim_no', $data->claim_no)->where('dr_no', $participant->dr_no)->update([
                                    'gl_updated_errors' => $formattedErrors,
                                ]);
                                $this->error("Error for Participant No {$participant->partner_no}: $formattedErrors");
                            }
                        }
                    }
                }
            } else {
                $this->info("No transaction to process");
            }
            $this->info("All transactions have been processed");
        } catch (\Exception $e) {
            $this->error("Error processing transactions: " . $e->getMessage());
        }
    }
}
