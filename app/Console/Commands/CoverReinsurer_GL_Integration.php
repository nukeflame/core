<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Customer;
use App\Models\CoverRipart;
use App\Models\ReinNote;
use App\Models\CoverDebit;
use App\Models\Classes;
use App\Models\CoverRegister;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CoverReinsurer_GL_Integration extends Command
{
    protected $signature = 'coverReinsurer:glupdate';
    protected $description = 'Push all Debitted Reinsurer transactions to GLTransactions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            // $datas = CoverDebit::where('glReinsurer_updated', '!=', 'Y')
            //     ->orWhereNull('glReinsurer_updated')
            //     ->get();

            $datas = DB::table('cover_debit as db')
                ->where('db.gl_updated', 'Y')
                ->join('coverripart as ri', 'ri.endorsement_no', '=', 'db.endorsement_no')
                ->where('ri.glReinsurer_updated', '!=', 'Y')
                ->select('db.*')
                ->distinct('db.endorsement_no')
                ->get();
            // $datas = CoverDebit::where('endorsement_no', 'CNEW0000142025')->get();
            if ($datas->isNotEmpty()) {
                foreach ($datas as $data) {
                    $coverdebit = CoverDebit::where('endorsement_no', $data?->endorsement_no)->first();
                    $coverReg = CoverRegister::where('endorsement_no', $data?->endorsement_no)->first();
                    $class = Classes::where('class_code', $coverReg?->class_code)->first();

                    if ($coverdebit) {
                        // logger($coverdebit));

                        $participants = CoverRipart::where('endorsement_no', $coverdebit->endorsement_no)
                            ->where('glReinsurer_updated', '!=', 'Y')
                            ->get();
                        foreach ($participants as $participant) {
                            $countItem = 0;
                            $coverItems = [];
                            $customer = Customer::where('customer_id', $participant->partner_no)->first();
                            $skipTypes = ['PRM', 'COM'];

                            //Premium
                            $partnerDebit = ReinNote::where('endorsement_no', $coverdebit->endorsement_no)
                                ->where('partner_no', $participant->partner_no)
                                ->whereIn('entry_type_descr', $skipTypes)
                                ->where('dr_cr', 'DR')
                                ->sum('net_amt') ?? 0;

                            $partnerCredit = ReinNote::where('endorsement_no', $coverdebit->endorsement_no)
                                ->where('partner_no', $participant->partner_no)
                                ->whereIn('entry_type_descr', $skipTypes)
                                ->where('dr_cr', 'CR')
                                ->sum('net_amt') ?? 0;

                            $BrokerComm = ReinNote::where('endorsement_no', $coverdebit->endorsement_no)
                                ->where('partner_no', $participant->partner_no)
                                ->where('entry_type_descr', 'BRC')
                                ->sum('net_amt') ?? 0;

                            $premiumAmt = (float) number_format(abs($partnerCredit - $partnerDebit + $BrokerComm), 2, '.', '');

                            $countItem++;
                            $premiumItem = [
                                'item_number' => (string)$countItem,
                                'item_name' => 'PRM',
                                'item_description' => 'PRM',
                                'foreign_unit_cost' => $premiumAmt,
                                'local_unit_cost' => ($premiumAmt * ($coverReg->currency_rate ?: 1)),
                                'foreign_gross_total_cost' => $premiumAmt,
                                'local_gross_total_cost' => ($premiumAmt * ($coverReg->currency_rate ?: 1)),
                                'discount_type' => null,
                                'foreign_discount_amount' => 0,
                                'local_discount_amount' => 0,
                                'foreign_nett_total_amount' => $premiumAmt,
                                'local_nett_total_amount' => ($premiumAmt * ($coverReg->currency_rate ?: 1)),
                                'quantity' => 1,
                                'status' => 'A',
                                'dr_cr' =>  $coverdebit->document === 'DRN' ? 'D' : 'C'
                            ];
                            $coverItems[] = $premiumItem;

                            // With-holding Tax & Broking Commission Computations
                            $partnerItems = ReinNote::where('endorsement_no', $coverdebit->endorsement_no)
                                ->where('partner_no', $participant->partner_no)
                                ->whereNotIn('entry_type_descr', $skipTypes)
                                ->get()
                                ->groupBy('entry_type_descr')
                                ->map(function ($itemsByPremiumType, $premiumTypeDescription) use ($coverReg, &$countItem) {
                                    $countItem++;
                                    $net_amt = (float) $itemsByPremiumType->sum('gross');
                                    return [
                                        'item_number' => (string)$countItem,
                                        'item_name' => $itemsByPremiumType->first()->entry_type_descr,
                                        'item_description' => $premiumTypeDescription,
                                        'foreign_unit_cost' => $net_amt,
                                        'local_unit_cost' => ($net_amt  * ($coverReg->currency_rate ?: 1)),
                                        'foreign_gross_total_cost' => $net_amt,
                                        'local_gross_total_cost' => ($net_amt * ($coverReg->currency_rate ?: 1)),
                                        'discount_type' => null,
                                        'foreign_discount_amount' => 0,
                                        'local_discount_amount' => 0,
                                        'foreign_nett_total_amount' => $net_amt,
                                        'local_nett_total_amount' => (float)($net_amt * ($coverReg->currency_rate ?: 1)),
                                        'quantity' => $itemsByPremiumType->count(),
                                        'status' => 'A',
                                        'dr_cr' => $itemsByPremiumType->first()->dr_cr === 'DR' ? 'C' : 'D',
                                    ];
                                })->values()->toArray();

                            $coverItems = array_merge($coverItems, $partnerItems);
                            $countItem++;

                            //Net Payable Amount Computations
                            $totalDebit = ReinNote::where('endorsement_no', $coverdebit->endorsement_no)
                                ->where('partner_no', $participant->partner_no)
                                ->where('entry_type_descr', '!=', 'BRC')
                                ->where('dr_cr', 'DR')
                                ->sum('gross') ?? 0;
                            $totalCredit = ReinNote::where('endorsement_no', $coverdebit->endorsement_no)
                                ->where('partner_no', $participant->partner_no)
                                ->where('entry_type_descr', '!=', 'BRC')
                                ->where('dr_cr', 'CR')
                                ->sum('gross') ?? 0;
                            $netAmt = (float) number_format(abs($totalCredit - $totalDebit), 2, '.', '');
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
                                'local_nett_total_amount' => $netAmt * ($coverReg->currency_rate ?: 1),
                                'quantity' => 1,
                                'status' => 'A',
                                'dr_cr' =>  $coverdebit->document === 'DRN' ? 'C' : 'D'
                            ];
                            $countItem++;

                            //Other Income where Cedant has difference with Reinsurer Premiums
                            $otherIncome = 0;
                            if ($coverReg->cedant_premium != $coverReg->rein_premium && in_array($coverReg->type_of_bus, ['FNR', 'FPR'])) {
                                $otherIncome = ($coverReg->cedant_premium ?? 0) - ($coverReg->rein_premium ?? 0);
                            }
                            $otherIncome = (float) $otherIncome * ($participant->share / 100);
                            $otherItem = [
                                'item_number' => (string)$countItem,
                                'item_name' => 'OTH',
                                'item_description' => 'OTH',
                                'foreign_unit_cost' => $otherIncome,
                                'local_unit_cost' => ($otherIncome * ($coverReg->currency_rate ?: 1)),
                                'foreign_gross_total_cost' => $otherIncome,
                                'local_gross_total_cost' => ($otherIncome * ($coverReg->currency_rate ?: 1)),
                                'discount_type' => null,
                                'foreign_discount_amount' => 0,
                                'local_discount_amount' => 0,
                                'foreign_nett_total_amount' => $otherIncome,
                                'local_nett_total_amount' => (float) ($otherIncome * ($coverReg->currency_rate ?: 1)),
                                'quantity' => 1,
                                'status' => 'A',
                                'dr_cr' =>  $coverdebit->document === 'DRN' ? 'C' : 'D'

                            ];

                            $coverItems[] = $netItem;
                            $coverItems[] = $otherItem;

                            $approval = DB::table('approvals_tracker')
                                ->whereRaw("CAST(data::json->>'endorsement_no' AS TEXT) = ?", [$coverdebit->endorsement_no])
                                ->where('status', 'A')
                                ->first();

                            $document = $coverdebit->document === 'DRN' ? 'CRN' : 'DRN';
                            // Prepare the API request payload
                            $requestPayload = [
                                'order' => [
                                    'partner_number' => $customer->partner_number,
                                    'order_type' => $coverReg->type_of_bus,
                                    'currency_code' => $coverReg->currency_code,
                                    'currency_rate' => $coverReg->currency_rate,
                                    'dr_cr' => substr($document, 0, 1),
                                    'order_due_date' => $coverdebit->premium_payment_due_date,
                                    'local_amount' => $coverdebit->net_amt * $coverReg->currency_rate ? $coverReg->currency_rate : 1,
                                    'notes' => $document . ' document',
                                    'terms_conditions' => null,
                                    'created_by' => $coverdebit->created_by,
                                    'updated_by' => $coverdebit->updated_by ?? 'system',
                                    'created_at' => $coverdebit->created_at,
                                    'updated_at' => $coverdebit->updated_at,
                                    'approved_by' => $approval->updated_by ?? 'system',
                                    'approved_at' => $approval->updated_at,
                                    'type_of_bus' => $coverReg->type_of_bus,
                                    'transaction_type' => $document,
                                    'order_status' => 'A',
                                    'pay_after_earn' => 'Y',
                                    'subsidiary_code' => 'REINSURANCE',
                                    'foreign_amount' => $coverdebit->net_amt,
                                    'lob_reference_1' => $coverdebit->cover_no,
                                    'lob_reference_2' => $coverdebit->endorsement_no,
                                    'lob_reference_3' => str_pad($participant->tran_no, 5, '0', STR_PAD_LEFT) . $coverdebit->period_year,
                                    'lob_order_reference' => str_pad($participant->tran_no, 5, '0', STR_PAD_LEFT) . $coverdebit->period_year,
                                    'lob_invoice_reference' => str_pad($coverdebit->dr_no, 5, '0', STR_PAD_LEFT) . $coverdebit->period_year,
                                    'order_title' => (in_array($coverReg->type_of_bus, ['FPR', 'FNP'])
                                        ? $coverReg->insured_name . '-'
                                        : '') .
                                        ($coverReg->class_code === 'TRT'
                                            ? 'Treaty'
                                            : $class->class_name) . '-' .
                                        $customer->name . '-' . $coverdebit->period_year,
                                    'branch' => $coverReg->branch_code,
                                    'department_code' => null,
                                    'channel_type_code' => 'REINCO',
                                    'channel_code' => 'REINCO',
                                    'channel_is_customer' => 'N',
                                ],
                                'items' => $coverItems,
                                'attachments' => [],
                            ];


                            $response = Http::withOptions([
                                'cert' => [storage_path('certs/acfinance.local-cert.pem'), 'UzMkgwdHseFXvKjB'], // Certificate with password
                                'ssl_key' => storage_path('certs/acfinance.local-key.pem'), // Private key
                                // 'verify' => storage_path('certs/client-ca.pem'), // Optional: CA validation
                            ])->withHeaders([
                                'Reference-ID' => '4fjaZ70kuZTCgTBpCu3aGbCVBNLkja5T',
                            ])->post(env('FINANCE_URL') . '/api/postAPOrder', $requestPayload);

                            if ($response->successful()) {
                                $gl_invoice_reference = $response->json()['invoice_reference'];
                                CoverRipart::where('endorsement_no', $coverdebit->endorsement_no)
                                    ->where('tran_no', $participant->tran_no)
                                    ->update([
                                        'glReinsurer_updated' => 'Y',
                                        'glReinsurer_updated_retries' => $coverdebit->gl_updated_retries + 1,
                                        'glReinsurer_updated_at' => Carbon::now(),
                                        'glReinsurer_updated_by' => 'system',
                                        'glReinsurer_updated_errors' => '',
                                        'glReinsurer_updated_order_reference' => $gl_invoice_reference
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
                                CoverDebit::where('endorsement_no', $data->endorsement_no)->update([
                                    'gl_updated_errors' => $formattedErrors,
                                ]);
                                $this->error("Error for Participant No {$participant->partner_no}: $formattedErrors");
                            }
                        }
                        // CoverDebit::where('endorsement_no', $coverdebit->endorsement_no)->update([
                        //     'glReinsurer_updated' => 'Y',
                        //     'glReinsurer_updated_retries' => $coverdebit->gl_updated_retries + 1,
                        //     'glReinsurer_updated_at' => Carbon::now(),
                        //     'glReinsurer_updated_by' => 'system',
                        //     'glReinsurer_updated_errors' => '',
                        // ]);
                    }
                }
            } else {
                $this->info("No debits to process");
            }
            $this->info("All debits have been processed");
        } catch (\Exception $e) {
            logger($e);
            $this->error("Error processing debits: " . $e->getMessage());
        }
    }
}
