<?php

namespace App\Jobs;

use App\Mail\ReinsurerFacultativeMail;
use App\Models\ApprovalSourceLink;
use App\Models\ApprovalsTracker;
use App\Models\Classes;
use App\Models\Company;
use App\Models\CoverClause;
use App\Models\CoverDebit;
use App\Models\CoverInstallments;
use App\Models\CoverPremium;
use App\Models\CoverRegister;
use App\Models\CoverRipart;
use App\Models\CoverRisk;
use App\Models\CoverSlipWording;
use App\Models\PremiumPayTerm;
use App\Models\ReinNote;
use App\Models\SystemProcessAction;
use App\Models\TreatyType;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class SendReinsurerEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $endorsementNo;
    private $partnerNo;
    private $recipientEmail;
    private $include_broking_commission;
    private $emailSubject;
    private $preDebit;
    private $emailCc;
    private $emailContent;

    /**
     * Create a new job instance.
     */
    public function __construct(string $endorsementNo, string $partnerNo, $request)
    {
        $this->endorsementNo = $endorsementNo;
        $this->partnerNo = $partnerNo;
        $this->recipientEmail = $request['emailTo'];
        $this->emailSubject = $request['emailSubject'];
        $this->emailContent = $request['emailContent'];
        $this->include_broking_commission = null;
        $this->preDebit = null;
        $this->emailCc = $request['email_cc'];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $pdf = $this->reinCreditNotes();
            $filename = $pdf['filename'];
            $filepath = $pdf['filepath'];

            $coverSlipPdf = $this->coverSlip();
            $coverSlipFilename = $coverSlipPdf['filename'];
            $coverSlipFilepath = $coverSlipPdf['filepath'];
            $emailCc = $this->emailCc ? implode(';', $this->emailCc) : [];

            $email_to = [];
            $email_to[] = 'reinsurance@acentriagroup.com';
            $email_to[] = 'pknuek@gmail.com';

            Mail::to($email_to)
                ->cc($emailCc)
                ->send(new ReinsurerFacultativeMail(
                    $filepath,
                    $filename,
                    $this->emailSubject,
                    $this->emailContent,
                    $coverSlipPdf,
                    $coverSlipFilename,
                    $coverSlipFilepath
                ));

            // // Clean up temporary file
            // if (file_exists($filepath)) {
            //     unlink($filepath);
            // }
        } catch (\Exception $e) {
            logger($e);
            throw $e;
        }
    }

    private function reinCreditNotes()
    {
        try {
            $endorsement_no = $this->endorsementNo;
            $includeCommission = $this->include_broking_commission;
            $company = Company::first();

            $partner_no = $this->partnerNo;
            if ($partner_no === null) {
                $cover = CoverRegister::where('endorsement_no', $endorsement_no)->first();
                $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)
                    ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                    ->select('coverripart.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->get();
                $credits = ReinNote::where('endorsement_no', $cover->endorsement_no)->get();
            } else {
                $cover = CoverRegister::where('endorsement_no', $endorsement_no)->first();
                $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->where('partner_no', $partner_no)
                    ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                    ->select('coverripart.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->get();
                $credits = ReinNote::where('endorsement_no', $cover->endorsement_no)->where('partner_no', $partner_no)->get();
            }


            if ($cover->class_code == 'TRT') {
                $class_name = 'TREATY';
            } else {
                $class = Classes::where('class_code', $cover->class_code)->first();
                $class_name = $class->class_name;
            }
            $treaty_type = TreatyType::where('treaty_code', $cover->treaty_type)->first();

            if ($includeCommission == 'no') {
                // Filter out entries where entry_type_descr is 'BRC'
                $credits = $credits->reject(function ($credit) {
                    return $credit->entry_type_descr === 'BRC';
                });
            }

            $installmentAmts = CoverInstallments::where('endorsement_no', $cover->endorsement_no)
                ->where('dr_cr', 'CR')
                ->orderBy('installment_no', 'ASC')->get();

            $ppw = PremiumPayTerm::where('pay_term_code', $cover->premium_payment_code)->first();
            $debit = CoverDebit::where('endorsement_no', $cover->endorsement_no)->first();
            $coverpremiums = CoverPremium::join('treaty_types', 'cover_premiums.treaty', '=', 'treaty_types.treaty_code')
                ->where('cover_premiums.endorsement_no', $cover->endorsement_no)
                ->orderBy('cover_premiums.premium_type_order_position', 'asc')
                ->get([
                    'cover_premiums.orig_endorsement_no',
                    'cover_premiums.dr_cr',
                    'cover_premiums.entry_type_descr',
                    'cover_premiums.premium_type_description',
                    'cover_premiums.premtype_name',
                    'cover_premiums.basic_amount',
                    'cover_premiums.apply_rate_flag',
                    'cover_premiums.rate',
                    'cover_premiums.final_amount',
                    'treaty_types.treaty_name',
                    'cover_premiums.layer_no',
                    'cover_premiums.installment_no'
                ]);

            $basicTotalDR = $credits->where('dr_cr', 'DR')
                ->sum('gross');

            $basicTotalCR = $credits->where('dr_cr', 'CR')
                ->sum('gross');

            $finalTotalDR = $credits->where('dr_cr', 'DR')
                ->sum('gross');

            $finalTotalCR = $credits->where('dr_cr', 'CR')
                ->sum('gross');

            $balance = $finalTotalCR - $finalTotalDR;
            $total_cr = ReinNote::where('endorsement_no', $endorsement_no)
                ->where('dr_cr', 'CR')
                ->sum('gross') ?? 0;
            $total_dr = ReinNote::where('endorsement_no', $endorsement_no)
                ->where('dr_cr', 'DR')
                ->where('entry_type_descr', '!=', 'BRC')
                ->sum('gross') ?? 0;

            $net_amnt = $total_cr - $total_dr;
            $is_cover_note = false;

            $shared_data = [
                'company' => $company,
                'cover' => $cover,
                'reinsurers' => $reinsurers,
                'credits' => $credits,
                'debit' => $debit,
                'class_name' => $class_name,
                'treaty_type' => $treaty_type,
                'coverpremiums' => $coverpremiums,
                'basicTotalDR' => $basicTotalDR,
                'basicTotalCR' => $basicTotalCR,
                'finalTotalDR' => $finalTotalDR,
                'finalTotalCR' => $finalTotalCR,
                'balance' => $balance,
                'ppw' => $ppw,
                'net_amnt' => $net_amnt,
                'installmentAmts' => $installmentAmts,
                'is_cover_note' => $is_cover_note
            ];
            $other_data = [];
            $view_name = null;
            $view_path = 'printouts.';

            switch ($cover->type_of_bus) {
                case 'TPR':
                    if ($cover->transaction_type == 'QTR') {
                        $view_name = $view_path . 'tpr_creditnote_qtr';
                    } elseif ($cover->transaction_type == 'PC') {
                        $view_name = $view_path . 'tpr_creditnote_qtr';
                    } elseif ($cover->transaction_type == 'POT' || $cover->transaction_type == 'PIN') {
                        $view_name = $view_path . 'tpr_creditnote_qtr';
                    }
                    break;

                case 'TNP':
                    if ($cover->transaction_type == 'MDP') {
                        $mdpInstallment = CoverInstallments::where('endorsement_no', $coverpremiums[0]->orig_endorsement_no)
                            ->where('dr_cr', 'DR')
                            ->where('installment_no', $coverpremiums[0]->installment_no)
                            ->first();
                        $other_data = [
                            'mdpInstallment' => $mdpInstallment,
                        ];
                        $view_name = $view_path . 'tpr_creditnote_qtr';
                    } elseif ($cover->transaction_type == 'RNS') {
                        $view_name = $view_path . 'tpr_creditnote_qtr';
                    } elseif ($cover->transaction_type == 'ADJ') {
                        $view_name = $view_path . 'tpr_creditnote_qtr';
                    }

                    break;

                case 'FPR':
                case 'FNP':
                    $view_name = $view_path . 'fac_credit_note';
                    break;

                default:
                    break;
            }

            $data = array_merge($shared_data, $other_data);

            $dompdf = Pdf::loadView(
                $view_name,
                $data
            )->setPaper('a4', 'portrait')->setWarnings(false);
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isPhpEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();

            $tempDir = storage_path('app/public/reinsurers');
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            // Generate unique filename
            $filename = 'Credit_Note_' . $this->endorsementNo . '_' . time() . '.pdf';
            $tempFile = $tempDir . '/' . $filename;

            // Save the file
            if (file_put_contents($tempFile, $dompdf->output()) === false) {
                throw new Exception('Failed to save PDF file');
            }

            return ['filename' => $filename, 'filepath' => $tempFile];
        } catch (\Exception $e) {
            logger($e);
        }
    }

    private function coverSlip()
    {
        try {
            $pre_debit = $this->preDebit;
            $has_partner = false;
            $is_cover_note = false;

            if ($this->partnerNo === null) {
                $cover = CoverRegister::where('endorsement_no', $this->endorsementNo)
                    ->join('customers', 'cover_register.customer_id', '=', 'customers.customer_id')
                    ->select('cover_register.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->first();
                $has_partner = false;
            } else {
                $cover = CoverRegister::where('endorsement_no', $this->endorsementNo)
                    ->join('customers', 'customers.customer_id', '=', 'customers.customer_id')
                    ->where('customers.customer_id', $this->partnerNo)
                    ->select('cover_register.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->first();
                $has_partner = true;
            }
            $treaty_type = TreatyType::where('treaty_code', $cover->treaty_type)->first();

            $wordingModel = CoverSlipWording::where('endorsement_no', $cover->endorsement_no);
            $wording = null;
            if ($cover->class_code == 'TRT') {
                $class_name = 'TREATY';
            } else {
                $class = Classes::where('class_code', $cover->class_code)->first();
                $class_name = $class->class_name;
            }
            $ppw = PremiumPayTerm::where('pay_term_code', $cover->premium_payment_code)->first();
            $debit = CoverDebit::where('endorsement_no', $cover->endorsement_no)->first();

            if ($pre_debit !== 'Y' && is_null($debit) && in_array($cover->type_of_bus, ['FPR', 'FNP'])) {
                throw ('This transaction not yet debited');
            }

            if ($wordingModel->exists()) {
                $wording = $wordingModel->first()->wording;
            }

            $clauses = CoverClause::where('endorsement_no', $cover->endorsement_no)->get();
            $schedules = CoverRisk::query()->with('schedule_header')->where('endorsement_no', $cover->endorsement_no)->get();

            $approvalAction = SystemProcessAction::where('nice_name', 'verify_cover')->first();
            $aprovalIds = ApprovalSourceLink::where('process_id', $approvalAction->process_id)
                ->where('process_action', $approvalAction->id)
                ->where('source_table', 'cover_register')
                ->where('source_column_name', 'endorsement_no')
                ->where('source_column_data', $cover->endorsement_no)
                ->pluck('approval_id');
            $query = ApprovalsTracker::query()->whereIn('id', $aprovalIds)->first();
            $approver = User::where('id', $query?->approver)->first();
            $position = Role::where('id', $approver?->role_id)->first();

            $view_name = null;
            $view_path = 'printouts.';

            switch ($cover->type_of_bus) {
                case 'TPR':
                case 'TNP':
                    $view_name = $view_path . 'treaty_coverslip';
                    $company = Company::first();
                    $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->get();
                    break;

                case 'FPR':
                case 'FNP':
                    $view_name = $view_path . 'fac_coverslip';
                    $company = Company::first();
                    if ($this->partnerNo === null) {
                        $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)
                            ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                            ->select('coverripart.*', 'customers.name')
                            ->get();
                    } else {
                        $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)
                            ->where('partner_no', $this->partnerNo)
                            ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                            ->select('coverripart.*', 'customers.name')
                            ->get();
                    }
                    break;

                default:
                    break;
            }

            $data = [
                'company' => $company,
                'cover' => $cover,
                'reinsurers' => $reinsurers,
                'wording' => $wording,
                'schedules' => $schedules,
                'treaty_type' => $treaty_type,
                'debit' => $debit,
                'class_name' => $class_name,
                'ppw' => $ppw,
                'clauses' => $clauses,
                'pre_debit' => $pre_debit,
                'approver' => $approver,
                'position' => $position,
                'has_partner' => $has_partner,
                'is_cover_note' => $is_cover_note
            ];

            $dompdf = Pdf::loadView($view_name, $data)->setPaper('a4', 'portrait')->setWarnings(false);
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isPhpEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();

            $tempDir = storage_path('app/public/coverslip');
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            // Generate unique filename
            $filename = 'Cover_Slip_' . $this->endorsementNo . '_' . time() . '.pdf';
            $tempFile = $tempDir . '/' . $filename;

            // Save the file
            if (file_put_contents($tempFile, $dompdf->output()) === false) {
                throw new Exception('Failed to save PDF file');
            }

            return ['filename' => $filename, 'filepath' => $tempFile];
        } catch (\Exception $e) {
            logger($e);
            throw ($e);
        }
    }


    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        logger($exception->getMessage());
    }
}
