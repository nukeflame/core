<?php

namespace App\Http\Controllers;

use App\Models\ApprovalSourceLink;
use App\Models\ApprovalsTracker;
use App\Models\Classes;
use App\Models\Company;
use App\Models\CashBook;
use App\Models\ReinNote;
use App\Models\CoverRisk;
use App\Models\ClaimDebit;
use App\Models\ClaimPeril;
use App\Models\COAListing;
use App\Models\CoverDebit;
use App\Models\TreatyType;
use App\Models\CashBookana;
use App\Models\CBTransType;
use App\Models\CoverRipart;
use App\Models\ClaimAckDocs;
use App\Models\CoverPremium;
use Illuminate\Http\Request;
use App\Models\CBRequisition;
use App\Models\ClaimRegister;
use App\Models\ClaimReinNote;
use App\Models\CoverRegister;
use App\Models\PremiumPayTerm;
use App\Models\CBDeductionMain;
use App\Models\ClaimNtfAckDocs;
use App\Models\ClaimNtfPeril;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ClaimNtfRegister;
use App\Models\ClassGroup;
use App\Models\CoverClause;
use App\Models\CoverSlipWording;
use App\Models\CoverInstallments;
use App\Models\EndorsementNarration;
use App\Models\PolicyRenewal;
use App\Models\SystemProcessAction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Shared\Converter;

class PrintoutController extends Controller
{
    public function coverSlip(Request $request)
    {
        try {
            $pre_debit = $request->pre_debit;
            $has_partner = false;
            $is_cover_note = isset($request->covernote) ? boolval($request->covernote === 'true') : false;
            if ($request->partner_no === null) {
                $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)
                    ->join('customers', 'cover_register.customer_id', '=', 'customers.customer_id')
                    ->select('cover_register.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->first();
                $has_partner = false;
            } else {
                $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)
                    ->join('customers', 'customers.customer_id', '=', 'customers.customer_id')
                    ->where('customers.customer_id', $request->partner_no)
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
                return response()->json([
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'This transaction not yet debited',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
                    if ($request->partner_no === null) {
                        $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)
                            ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                            ->select('coverripart.*', 'customers.name')
                            ->get();
                    } else {
                        $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)
                            ->where('partner_no', $request->partner_no)
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

            return response($dompdf->output(), 200)
                ->header(
                    'Content-Type',
                    'application/pdf'
                )
                ->header('Content-Disposition', 'inline; filename="Cover_Slip_' . time() . '.pdf"');
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An internal server error occurred.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function endorsementNoticeSlip(Request $request)
    {
        try {
            $has_partner = false;
            if ($request->partner_no === null) {
                $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)
                    ->join('customers', 'cover_register.customer_id', '=', 'customers.customer_id')
                    ->select('cover_register.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_country_iso')
                    ->first();
                $has_partner = false;
            } else {
                $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)
                    ->join('customers', 'customers.customer_id', '=', 'customers.customer_id')
                    ->where('customers.customer_id', $request->partner_no)
                    ->select('cover_register.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_country_iso')
                    ->first();
                $has_partner = true;
            }

            $company = Company::first();
            $userName = Auth::user()->name;
            $narration = EndorsementNarration::where(['cover_no' => $cover->cover_no, 'endorsement_no' => $cover->endorsement_no])->first();
            $class = Classes::where('class_code', $cover->class_code)->first();
            $class_group = ClassGroup::where('group_code', $cover?->class_group_code)->first();
            $view_name = null;
            $view_path = 'printouts.';

            $view_name = $view_path . 'fac_endorsement_note';
            $endorsement_document_no = $narration?->document_no;

            $data = [
                'company' => $company,
                'cover' => $cover,
                'class' => $class,
                'class_group' => $class_group,
                'issuedBy' => $userName,
                'endorsement_document_no' => $endorsement_document_no,
                'has_partner' => $has_partner,
                'narration' => $narration
            ];

            $dompdf = Pdf::loadView($view_name, $data)->setPaper('a4', 'portrait')->setWarnings(false);
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isPhpEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();

            return response($dompdf->output(), 200)
                ->header(
                    'Content-Type',
                    'application/pdf'
                )
                ->header('Content-Disposition', 'inline; filename="Endorsement_Notice_Slip_' . time() . '.pdf"');
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An internal server error occurred.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function bdCoverSlip(Request $request)
    {
        try {
            $request->validate([
                'opportunity_id' => 'required',
                'printout_flag' => 'required|boolean',
                'current_stage' => 'required|string',
                'reinsurer_id' => 'nullable',
            ]);

            $opportunityId = $request->opportunity_id;
            $currentStage = 'lead'; //Str::lower($request->current_stage) ?? null;
            $currency = 'KES';
            $formattedActivities = $this->fetchOpportunityData($opportunityId);
            $reinsurerId = $request->reinsurer_id;

            if (!$formattedActivities) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Opportunity not found.',
                ], 404);
            }

            $opportunity = $formattedActivities;
            $referenceNo = 'FAC/' . str_pad($opportunity['customer_id'], 6, '0', STR_PAD_LEFT) . '/' . date('Y');

            $company = Company::first();
            if (!$company) {
                throw new \Exception('Company information not found.');
            }

            $reinsurers = $this->prepareReinData($opportunityId);
            // $requestData = $this->buildRequestData($request, $formattedActivities, $shares);
            // $customers = $this->prepareCustomerCollection($requestData);
            $data = [
                'currentStage' => $currentStage,
                'opportunityId' => $opportunityId,
                'reinsurers' => $reinsurers,
                'company' => $company,
                'opportunity' => $opportunity,
                'currency' => $currency,
                'reference_no' => $referenceNo,
                'reinsurerId' => $reinsurerId
            ];

            return $this->generatePdfDocument($data);
        } catch (ValidationException $e) {

            return response()->json([
                'status' => 422,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while generating the document.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    private function prepareReinData($opportunityId)
    {
        $reinsurers = DB::table('bd_fac_reinsurers as bfr')
            ->leftJoin('customers as c', 'bfr.reinsurer_id', '=', 'c.customer_id')
            ->select([
                'bfr.id',
                'bfr.reinsurer_id',
                'bfr.opportunity_id',
                'bfr.share_amount',
                'bfr.written_share',
                'bfr.email as bfr_email',
                DB::raw("COALESCE(c.name, bfr.reinsurer_name, 'N/A') AS reinsurer_name"),
                'c.email as customer_email',
                'c.postal_address',
                'c.city',
                'c.street',
                'c.country_iso',
                'c.telephone'
            ])
            ->where('bfr.opportunity_id', $opportunityId)
            ->get();

        return $reinsurers->map(function ($q) {
            return [
                'id' => $q->id,
                'reinsurer_id' => $q->reinsurer_id,
                'opportunity_id' => $q->opportunity_id,
                'name' => $q->reinsurer_name,
                'email' => $q->customer_email ?? $q->bfr_email,
                'address' => $q->postal_address,
                'city' => $q->city,
                'location' => null,
                'written_share' => $q->written_share,
                'country' => $q->country_iso,
                'phone' => $q->telephone,
                'share_amount' => $q->share_amount,
            ];
        });
    }

    private function fetchOpportunityData($opportunityId)
    {
        $activity = DB::table('pipeline_opportunities as po')
            ->leftJoin('customers as c', function ($join) {
                $join->on(DB::raw("NULLIF(po.customer_id, '')::INTEGER"), '=', 'c.customer_id');
            })
            ->leftJoin('lead_status as ls', 'po.stage', '=', 'ls.id')
            ->leftJoin('reins_division as rd', 'po.divisions', '=', 'rd.division_code')
            ->leftJoin('classes as cl', 'po.classcode', '=', 'cl.class_code')
            ->leftJoin('business_types as bt', 'po.type_of_bus', '=', 'bt.bus_type_id')
            ->selectRaw('DISTINCT ON (po.opportunity_id) po.*,
                COALESCE(c.name, \'N/A\') AS customer_name,
                ls.status_name as stage,
                rd.division_name as division_name,
                po.cede_premium as cedant_premium,
                po.rein_premium as reinsurer_premium,
                cl.class_name as class_name,
                po.total_sum_insured as sum_insured,
                po.comm_rate as commission_rate,
                bt.bus_type_name as type_of_bus')
            ->where('po.opportunity_id', $opportunityId)
            ->first();

        if (!$activity) {
            return null;
        }

        return [
            'customer_id'       => $activity->customer_id ?? 'N/A',
            'customer_name'     => $activity->customer_name ?? 'N/A',
            'opportunity_id'    => $activity->opportunity_id,
            'effective_date'    => $activity->effective_date,
            'closing_date'      => $activity->closing_date,
            'insured_name'      => $activity->insured_name,
            'commission_rate'   => $activity->commission_rate,
            'type_of_bus'       => $activity->type_of_bus,
            'sum_insured'       => $activity->sum_insured,
            'currency_code'     => $activity->currency_code,
            'contact_name'      => $this->safeJsonDecode($activity->contact_name ?? '[]'),
            'email'             => $this->safeJsonDecode($activity->email ?? '[]'),
            'phone'             => $this->safeJsonDecode($activity->phone ?? '[]'),
            'telephone'         => $this->safeJsonDecode($activity->telephone ?? '[]'),
            'class_name'        => $activity->class_name,
            'stage'             => $activity->stage ?? null,
            'stageType'         => $activity->category_type ?? null,
            'division_name'     => $activity->division_name ?? null,
            'cedant_premium'    => $activity->cedant_premium,
            'reinsurer_premium' => $activity->reinsurer_premium ?? null,
        ];
    }

    private function safeJsonDecode($json, $default = [])
    {
        if (empty($json)) {
            return $default;
        }

        $decoded = json_decode($json, true);
        return $decoded !== null ? $decoded : $default;
    }

    private function resolveNetTaxFactorForCoverNote($cover, $coverpremiums, bool $hasNetOfTaxReinsurer, bool $hasPremiumTax): float
    {
        if (! $hasNetOfTaxReinsurer || ! $hasPremiumTax) {
            return 1.0;
        }

        $premiumTaxRate = (float) ($cover->prem_tax_rate ?? 0);

        if ($premiumTaxRate <= 0) {
            $premiumTaxRate = (float) optional(
                collect($coverpremiums)->first(fn($item) => strtoupper((string) ($item->entry_type_descr ?? '')) === 'PTX')
            )->rate;
        }

        if ($premiumTaxRate <= 0) {
            $premiumTaxRate = 1.0;
        }

        $premiumTaxRate = max(0, min(100, $premiumTaxRate));

        return (100 - $premiumTaxRate) / 100;
    }

    private function isTruthy(mixed $value): bool
    {
        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'y', 'on'], true);
    }

    private function generatePdfDocument($data)
    {
        $viewName = 'printouts.fac_coverslipquote';

        $dompdf = Pdf::loadView($viewName, $data)
            ->setPaper('a4', 'portrait')
            ->setWarnings(false);

        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isPhpEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);

        $dompdf->render();

        $stageName = str_replace(' ', '_', $data['currentStage']);
        $filename = sprintf(
            'Fac_Cover_Slip_%s_Opp_%s.pdf',
            $stageName,
            date('Ymd')
        );

        return $dompdf->stream($filename);
    }

    public function TreatyBdPrintout(Request $request)
    {
        try {
            $user = [
                'firstname' => auth()->user()->firstname ?? '',
                'lastname' => auth()->user()->lastname ?? ''
            ];

            $activities = DB::table('pipeline_opportunities as po')
                ->leftJoin('customers as c', function ($join) {
                    $join->on(DB::raw("NULLIF(po.customer_id, '')::INTEGER"), '=', 'c.customer_id');
                })
                ->leftJoin('lead_status as ls', 'po.stage', '=', 'ls.id')
                // ->leftJoin('reins_division as rd', 'po.divisions', '=', 'rd.division_code')
                // ->leftJoin('classes as cl', 'po.classcode', '=', 'cl.class_code')
                ->leftJoin('business_types as bt', 'po.type_of_bus', '=', 'bt.bus_type_id')
                ->selectRaw('DISTINCT ON (po.opportunity_id) po.*,
                 COALESCE(c.name, \'N/A\') AS customer_name,
                 ls.status_name as stage,



                 bt.bus_type_name as type_of_bus')
                ->where('po.opportunity_id', $request->opp_id)
                ->get();

            $formattedActivities = $activities->map(function ($d) {
                return [
                    'customer_id' => $d->customer_id ?? 'N/A',
                    'customer_name' => $d->customer_name ?? 'N/A',
                    'opportunity_id' => $d->opportunity_id,
                    'effective_date' => $d->effective_date,
                    'closing_date' => $d->closing_date,
                    // 'insured_name' => $d->insured_name,
                    'type_of_bus' => $d->type_of_bus,
                    // 'currency_code' => $d->currency_code,
                    'contact_name' => json_decode($d->contact_name, true) ?? [],
                    'email' => json_decode($d->email, true) ?? [],
                    'phone' => json_decode($d->phone, true) ?? [],
                    'telephone' => json_decode($d->telephone, true) ?? [],
                    // 'class_name' => $d->class_name,
                    'stageType' => $d->category_type
                ];
            });
            // dd($formattedActivities);
            $company = Company::first();
            $data = [
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer ?? $request->customer_name,
                // 'unplaced' => $request->unplaced ?? '',
                // 'updated_written_share_total' => $request->updated_written_share_total ?? '',
                // 'customer_email' => $request->customer_email,
                'contact_name' => $request->contact_name,
                // 'written_share' => isset($request->written_share) ? $request->written_share : ''
            ];
            // dd($data);


            // Transform arrays of stage 25% into a collection of array
            $shares = collect($data['customer_id'])->map(function ($id, $index) use ($data) {
                return [
                    'customer_id' => $id,
                    'customer_name' => $data['customer_name'][$index],
                    // 'customer_email' => $data['customer_email'][$index],
                    // 'written_share' => $data['written_share'][$index] ?? '',
                    'contact_name' => $data['contact_name'][$index] === 'null' ? null : $data['contact_name'][$index]
                ];
            });
            // dd($shares);
            $requestData = [
                'customer_name' => $formattedActivities[0]['customer_name'] ?? '',
                'customer_id' => $formattedActivities[0]['customer_id'] ?? '',
                // 'email' => $request->customer_email,
                'schedule_details' => is_string($request->schedule_details) ? json_decode($request->schedule_details, true) : $request->schedule_details,
                // 'quote_title_intro' => $request->quote_title_intro ?? '',
                'facschedule_details' => is_string($request->facschedule_details) ? json_decode($request->facschedule_details, true) : $request->facschedule_details,
                'shares' => $request->reinsurers ?? $shares,
                // 'unplaced' => $request->unplaced ?? '',
                // 'updated_written_share_total' => $request->updated_written_share_total ?? $request->written_share_total,
                // 'insured_name' => $formattedActivities[0]['insured_name'] ?? '',
                // 'written_share' => $request->written_share ?? '',
                // 'signed_share' => $request->signed_share ?? '',
                'contact_name' => $request->contact_name ?? '',
                'type_of_bus' => $formattedActivities[0]['type_of_bus'] ?? '',
                // 'class_name' => $formattedActivities[0]['class_name'] ?? '',
                'opportunity_id' => $formattedActivities[0]['opportunity_id'] ?? '',
                // 'currency_code' => $formattedActivities[0]['currency_code'] ?? '',
                'effective_date' => $formattedActivities[0]['effective_date'] ?? '',
                'closing_date' => $formattedActivities[0]['closing_date'] ?? '',
                'stage' => $request->stage_cycle_fac ?? $request->stagecycleqt ?? '',
                'stageType' => $formattedActivities[0]['stageType'] ?? '',
            ];

            // dd($requestData);
            // Combine customer data with joined table data
            $customers = collect($requestData['customer_id'])->map(function ($customerId, $index) use ($requestData) {

                // Get the corresponding activity for the customer

                return (object) [
                    'customer_id' => $customerId,
                    'customer_name' => $requestData['customer_name'] ?? 'N/A',
                    'contact_name' => $requestData['contact_name'][$index] ?? null,
                    // 'written_share' => $requestData['written_share'][$index] ?? null,
                    // 'signed_share' => $requestData['signed_share'][$index] ?? null,
                    'schedule_details' => is_string($requestData['schedule_details']) ? json_decode($requestData['schedule_details'], true) : $requestData['schedule_details'],
                    'facschedule_details' => is_string($requestData['facschedule_details']) ? json_decode($requestData['facschedule_details'], true) : $requestData['facschedule_details'],


                    // 'quote_title_intro' => $requestData['quote_title_intro'] ?? null,

                    // 'insured_name' => $requestData['insured_name'] ?? null,
                    'type_of_bus' => $requestData['type_of_bus'] ?? null,
                    'opportunity_id' => $requestData['opportunity_id'] ?? null,
                    'effective_date' => $requestData['effective_date'] ?? null,
                    'closing_date' => $requestData['closing_date'] ?? null,
                    // 'class_name' => $requestData['class_name'] ?? null,
                    // 'currency_code' => $requestData['currency_code'] ?? null,
                    'stageType' => $requestData['stageType'] ?? null

                ];
            });
            // dd($customers);


            $data = [
                'company' => $company,
                'customers' => $customers,
                // 'amount' => $requestData['amount'] ?? '',
                'shares' => $requestData['shares'] ?? '',
                // 'unplaced' => $requestData['unplaced'] ?? '',
                // 'updated_written_share_total' => $requestData['updated_written_share_total'] ?? '',
                'stage' => $requestData['stage'] ?? '',
            ];


            $view_path = 'printouts.';
            $view_name = $view_path . 'treaty_printout';

            if ($request->printout_flag == 1) {
                // Create a new Word document
                $phpWord = new PhpWord();
                $phpWord->setDefaultFontName('Times New Roman');
                $phpWord->setDefaultFontSize(10);

                // Define styles
                $headerFontStyle = ['bold' => true, 'size' => 12];
                $subHeaderFontStyle = ['bold' => true, 'size' => 10];
                $normalFontStyle = ['size' => 10];
                $footerFontStyle = ['size' => 8];
                $centerAlignment = ['alignment' => Jc::CENTER];
                $leftAlignment = ['alignment' => Jc::LEFT];
                $rightAlignment = ['alignment' => Jc::RIGHT];
                $tableStyle = [
                    'borderSize' => 6,
                    'borderColor' => '000000',
                    'cellMargin' => 80,
                ];
                $lineStyle = [
                    'weight' => 1,
                    'width' => 500,
                    'height' => 0,
                    'color' => '000000',
                ];

                // Extract data
                $company = $data['company'];
                $customers = $data['customers'];
                $shares = $data['shares'];
                $unplaced = $data['unplaced'];
                $updated_written_share_total = $data['updated_written_share_total'];
                $stage = $data['stage'];

                // Conditional logic based on stage and stageType
                $isSpecialCase = (($stage == 3 && $customers[0]->stageType == 2) || ($stage == 2 && $customers[0]->stageType == 1));

                foreach ($shares as $index => $sh) {
                    $section = $phpWord->addSection([
                        'marginTop' => Converter::cmToTwip(2),
                        'marginBottom' => Converter::cmToTwip(1),
                        'marginLeft' => Converter::cmToTwip(2),
                        'marginRight' => Converter::cmToTwip(2),
                        'pageNumberingStart' => 1,
                    ]);

                    // Add Header
                    $header = $section->addHeader();
                    $headerTable = $header->addTable();
                    $headerTable->addRow();
                    $logoCell = $headerTable->addCell(5000);
                    $infoCell = $headerTable->addCell(7000);

                    // Add logo
                    $logoPath = public_path('logo.png');
                    if (file_exists($logoPath) && is_readable($logoPath)) {
                        $logoCell->addImage($logoPath, [
                            'width' => 150,
                            'alignment' => Jc::RIGHT,
                        ]);
                    }

                    // Add company info
                    $infoCell->addText($company->company_name, ['bold' => true], $leftAlignment);
                    $infoCell->addText($company->postal_address, [], $leftAlignment);
                    $infoCell->addText('Phone: ' . $company->mobilephone, [], $leftAlignment);
                    $infoCell->addText('Email: ' . $company->email, [], $leftAlignment);

                    foreach ($customers as $customer) {
                        // Add title
                        $section->addText(
                            strtoupper($customer->quote_title_intro . ' - ' . $customer->class_name . ' - ' . ($customer->insured_name ?? 'N/A')),
                            $headerFontStyle,
                            $centerAlignment
                        );
                        $section->addTextBreak(1);

                        // Add horizontal line
                        $section->addLine(array_merge($lineStyle, ['alignment' => Jc::LEFT]));

                        // Basic details
                        $detailsTable = $section->addTable($tableStyle);
                        $details = [
                            'Our Reference' => '',
                            'Cedant Name' => ucfirst($customer->customer_name ?? 'N/A'),
                            'Insured Name' => ucfirst($customer->insured_name ?? 'N/A'),
                            'Insurance Group' => ucfirst($customer->type_of_bus ?? 'N/A'),
                            'Class Of Business' => ucfirst($customer->class_name ?? 'N/A'),
                            'Period Of Cover' => ucfirst(($customer->effective_date ?? '') . ' To ' . ($customer->closing_date ?? '')),
                        ];

                        foreach ($details as $field => $value) {
                            $detailsTable->addRow();
                            $detailsTable->addCell(4000)->addText($field . ':', $subHeaderFontStyle, $leftAlignment);
                            $detailsTable->addCell(8000)->addText($value, $normalFontStyle, $leftAlignment);
                        }

                        $section->addTextBreak(1);

                        // Schedule details
                        $scheduleDetails = isset($customer->facschedule_details) ? $customer->facschedule_details : $customer->schedule_details;
                        if (!empty($scheduleDetails)) {
                            foreach ($scheduleDetails as $detail) {
                                if ((isset($detail['id']) && isset($detail['amount'])) || isset($detail['details'])) {
                                    if (trim(strtolower($detail['name'])) == 'policy wording') {
                                        $section->addText(ucfirst($detail['name'] ?? ''), $subHeaderFontStyle, $leftAlignment);
                                        if (isset($detail['details'])) {
                                            $section->addText(strip_tags(html_entity_decode($detail['details'])), $normalFontStyle, $leftAlignment);
                                        }
                                        $section->addTextBreak(1);
                                    } else {
                                        $scheduleTable = $section->addTable($tableStyle);
                                        $scheduleTable->addRow();
                                        $name = ucfirst($detail['name'] ?? '');
                                        $lowerName = trim(strtolower($detail['name']));
                                        $suffix = '';
                                        if (in_array($lowerName, ['cedant commission rate', 'reinsurer commission rate'])) {
                                            $suffix = ' (%):';
                                        } elseif (
                                            in_array($lowerName, [
                                                'premium',
                                                'total sum insured',
                                                'first loss',
                                                'top location',
                                                'limit of indemnity',
                                                'maximum loss limit',
                                                'limit of liability',
                                                'agreed value'
                                            ])
                                        ) {
                                            $suffix = ' (100%):';
                                        } else {
                                            $suffix = ':';
                                        }
                                        $scheduleTable->addCell(4000)->addText($name . $suffix, $subHeaderFontStyle, $leftAlignment);

                                        $amountText = isset($detail['amount']) && $detail['amount'] !== null ? ($lowerName !== 'reinsurer commission rate' ? $customer->currency_code . ' ' : '') . $detail['amount'] : '';
                                        if ($lowerName == 'reinsurer commission rate') {
                                            $amountText .= '%';
                                        }
                                        $detailsText = isset($detail['details']) ? strip_tags(html_entity_decode($detail['details'])) : '';
                                        $scheduleTable->addCell(8000)->addText(trim($amountText . ' ' . $detailsText), $normalFontStyle, $leftAlignment);
                                    }
                                }
                            }
                        }

                        $section->addTextBreak(1);

                        // Share details
                        if (!$isSpecialCase) {
                            // Case 1: Regular reinsurer pages
                            $section->addText('Placed With:', $subHeaderFontStyle, $leftAlignment);
                            $validNames = [
                                'premium',
                                'total sum insured',
                                'first loss',
                                'top location',
                                'limit of indemnity',
                                'maximum loss limit',
                                'limit of liability',
                                'agreed value'
                            ];
                            $displayValues = [];
                            foreach ($scheduleDetails as $detail) {
                                if (!isset($detail['name']) || !isset($detail['amount']))
                                    continue;
                                $detailName = strtolower(trim($detail['name']));
                                if ($detailName === 'reinsurer commission rate' || !in_array($detailName, $validNames))
                                    continue;
                                $amount = floatval(str_replace(',', '', $detail['amount']));
                                $shareKey = isset($sh['signed_share']) && $sh['signed_share'] != null ? 'signed_share' : 'written_share';
                                $shareValue = isset($sh[$shareKey]) ? floatval($sh[$shareKey]) : 0;
                                $calculatedAmount = $amount * ($shareValue / 100);
                                $displayValues[$detailName] = number_format($calculatedAmount, 2, '.', '');
                            }
                            $selectedNames = array_keys($displayValues);

                            $shareTable = $section->addTable($tableStyle);
                            $shareTable->addRow();
                            $shareTable->addCell(3000)->addText('Reinsurer', $subHeaderFontStyle, $leftAlignment);
                            $shareTable->addCell(2000)->addText(ucfirst(str_replace('_', ' ', $shareKey)) . ' (%)', $subHeaderFontStyle, $centerAlignment);
                            foreach ($selectedNames as $name) {
                                $shareTable->addCell(3000)->addText(ucfirst($name) . " ({$customer->currency_code})", $subHeaderFontStyle, $centerAlignment);
                            }

                            $shareTable->addRow();
                            $reinsurerName = isset($sh['customer_name']) ? ucfirst($sh['customer_name']) : (isset($sh['name']) ? ucfirst($sh['name']) : '');
                            $shareTable->addCell(3000)->addText($reinsurerName, $normalFontStyle, $leftAlignment);
                            $shareTable->addCell(2000)->addText($sh[$shareKey] . '%', $normalFontStyle, $centerAlignment);
                            foreach ($selectedNames as $name) {
                                $shareTable->addCell(3000)->addText(number_format($displayValues[$name] ?? 0, 2), $normalFontStyle, $centerAlignment);
                            }
                        } else {
                            // Case 2: Special case for stage and stageType
                            $section->addText('Offered Share (%):', $subHeaderFontStyle, $leftAlignment);
                            $section->addText($updated_written_share_total . '%', $normalFontStyle, $leftAlignment);
                            $section->addText('Placed With:', $subHeaderFontStyle, $leftAlignment);

                            $validNames = [
                                'premium',
                                'total sum insured',
                                'first loss',
                                'top location',
                                'limit of indemnity',
                                'maximum loss limit',
                                'limit of liability',
                                'agreed value'
                            ];
                            $displayValues = [];
                            foreach ($scheduleDetails as $detail) {
                                if (!isset($detail['name']) || !isset($detail['amount']))
                                    continue;
                                $detailName = strtolower(trim($detail['name']));
                                if ($detailName === 'reinsurer commission rate' || !in_array($detailName, $validNames))
                                    continue;
                                $amount = floatval(str_replace(',', '', $detail['amount']));
                                $displayValues[$detailName] = $amount;
                            }
                            $selectedNames = array_keys($displayValues);

                            $shareTable = $section->addTable($tableStyle);
                            $shareTable->addRow();
                            $shareTable->addCell(3000)->addText('Reinsurer', $subHeaderFontStyle, $leftAlignment);
                            $shareTable->addCell(2000)->addText('Written Share (%)', $subHeaderFontStyle, $centerAlignment);
                            foreach ($selectedNames as $name) {
                                $shareTable->addCell(3000)->addText(ucfirst($name) . " ({$customer->currency_code})", $subHeaderFontStyle, $centerAlignment);
                            }

                            $totalWrittenShare = 0;
                            $columnTotals = array_fill_keys($selectedNames, 0);
                            foreach ($shares as $share) {
                                if (!isset($share['written_share']) || $share['written_share'] == null)
                                    continue;
                                $shareTable->addRow();
                                $reinsurerName = isset($share['customer_name']) ? ucfirst($share['customer_name']) : (isset($share['name']) ? ucfirst($share['name']) : '');
                                $shareTable->addCell(3000)->addText($reinsurerName, $normalFontStyle, $leftAlignment);
                                $shareTable->addCell(2000)->addText($share['written_share'] . '%', $normalFontStyle, $centerAlignment);
                                $writtenShare = floatval($share['written_share']);
                                $totalWrittenShare += $writtenShare;
                                foreach ($selectedNames as $name) {
                                    $calculatedAmount = ($displayValues[$name] * $writtenShare) / 100;
                                    $shareTable->addCell(3000)->addText(number_format($calculatedAmount, 2), $normalFontStyle, $centerAlignment);
                                    $columnTotals[$name] += $calculatedAmount;
                                }
                            }

                            // Add total row
                            $shareTable->addRow();
                            $shareTable->addCell(3000)->addText('Total', $subHeaderFontStyle, $leftAlignment);
                            $shareTable->addCell(2000)->addText(number_format($totalWrittenShare, 2) . '%', $subHeaderFontStyle, $centerAlignment);
                            foreach ($selectedNames as $name) {
                                $shareTable->addCell(3000)->addText(number_format($columnTotals[$name], 2), $subHeaderFontStyle, $centerAlignment);
                            }

                            if ($unplaced > 0) {
                                $section->addTextBreak(1);
                                $unplacedTable = $section->addTable($tableStyle);
                                $unplacedTable->addRow();
                                $unplacedTable->addCell(4000)->addText('Unplaced Share (%)', $subHeaderFontStyle, $leftAlignment);
                                $unplacedTable->addCell(8000)->addText($unplaced . '%', $normalFontStyle, $leftAlignment);
                            }
                        }

                        // Add date generated
                        // $section->addText(
                        //     'Generated on behalf of Acentria International on ' . date('F j, Y'),
                        //     $footerFontStyle,
                        //     $centerAlignment
                        // );

                        // Add page break except for the last share in non-special case
                        if ($index < count($shares) - 1 && !$isSpecialCase) {
                            $section->addPageBreak();
                        }
                    }

                    // Add Footer
                    $footer = $section->addFooter();
                    $footer->addText(
                        '© ' . date('Y') . ' Acentriagroup. All rights reserved. | Page No: ',
                        $footerFontStyle,
                        $rightAlignment
                    );
                    $footer->addPreserveText('{PAGE}', $footerFontStyle, $rightAlignment);
                }

                // Save File
                $fileName = 'Reinsurance_Quote.docx';
                $path = storage_path('app/public/' . $fileName);
                $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($path);

                return response()->download($path)->deleteFileAfterSend(true);
            } else {


                // dd($data);

                // Generate PDF for this specific customer
                $dompdf = Pdf::loadView($view_name, $data)
                    ->setPaper('a4', 'portrait')
                    ->setWarnings(false);

                // Enable necessary options
                $dompdf->set_option('isHtml5ParserEnabled', true);
                $dompdf->set_option('isPhpEnabled', true);
                $dompdf->set_option('isRemoteEnabled', true);

                $dompdf->render();

                // Stream the PDF to the browser for this customer
                return $dompdf->stream('Quotation_Cover_Slip_' . time() . '.pdf');
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while generating the PDF.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function viewRenewalNotice(Request $request)
    {
        try {
            $policy = PolicyRenewal::where('policy_number', $request->policy_number)->first();
            $doc_path = null;
            $doc_name = null;

            if ($policy) {
                foreach ($policy->documents as $document) {
                    if ((string) $request->recipient_type === $document->doc_type) {
                        $doc_path = public_path($document->doc_path);
                        $doc_name = $document->doc_name;
                    }
                }
            }


            return response()->file($doc_path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $doc_name . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An internal server error occurred.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function renewalNotice(Request $request)
    {
        try {
            $policy = PolicyRenewal::where('policy_number', $request->policy_number)->first();
            $doc_path = null;
            $doc_name = null;

            if ($policy) {
                foreach ($policy->documents as $document) {
                    if ((string) $request->recipient_type === $document->doc_type) {
                        $doc_path = public_path($document->doc_path);
                        $doc_name = $document->doc_name;
                    }
                }
            }

            return response()->file($doc_path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $doc_name . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An internal server error occurred.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function downloadRenewalNotice(Request $request)
    {

        try {
            $policy = PolicyRenewal::where('policy_number', $request->policy_number)->with('documents')->first();
            $doc_path = null;
            $doc_name = null;

            if ($policy) {
                foreach ($policy->documents as $document) {
                    if ((string) $request->recipient_type === $document->doc_type) {
                        $doc_path = public_path($document->doc_path);
                        $doc_name = $document->doc_name;
                    }
                }
            }

            return response()->download($doc_path, $doc_name, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An internal server error occurred.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function coverDebitnote(Request $request)
    {
        try {
            $company = Company::first();
            $cover = CoverRegister::with('customer')->where('endorsement_no', $request->endorsement_no)->first();
            if ($cover->class_code == 'TRT') {
                $class_name = 'TREATY';
            } else {
                $class = Classes::where('class_code', $cover->class_code)->first();
                $class_name = $class->class_name;
            }
            $treaty_type = TreatyType::where('treaty_code', $cover->treaty_type)->first();
            $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->get();
            $installmentAmts = CoverInstallments::where('endorsement_no', $cover->endorsement_no)
                ->where('dr_cr', 'DR')
                ->orderBy('installment_no', 'ASC')->get();


            $ppw = PremiumPayTerm::where('pay_term_code', $cover->premium_payment_code)->first();
            $debit = CoverDebit::where('endorsement_no', $cover->endorsement_no)->first();
            $coverpremiums = CoverPremium::join('treaty_types', 'cover_premiums.treaty', '=', 'treaty_types.treaty_code')
                ->where('cover_premiums.endorsement_no', $cover->endorsement_no)
                ->orderBy('cover_premiums.premium_type_order_position', 'asc')
                ->get([
                    'cover_premiums.orig_endorsement_no',
                    'cover_premiums.endorsement_no',
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

            $basicTotalDR = CoverPremium::where('endorsement_no', $cover->endorsement_no)
                ->where('dr_cr', 'DR')
                ->sum('basic_amount');

            $basicTotalCR = CoverPremium::where('endorsement_no', $cover->endorsement_no)
                ->where('dr_cr', 'CR')
                ->sum('basic_amount');

            $finalTotalDR = CoverPremium::where('endorsement_no', $cover->endorsement_no)
                ->where('dr_cr', 'DR')
                ->sum('final_amount');

            $finalTotalCR = CoverPremium::where('endorsement_no', $cover->endorsement_no)
                ->where('dr_cr', 'CR')
                ->sum('final_amount');

            $hasNetOfTaxReinsurer = $reinsurers->contains(fn($reinsurer) => $this->isTruthy($reinsurer->net_of_tax ?? 0));
            $hasPremiumTax = $coverpremiums->contains(function ($item) {
                return strtoupper((string) ($item->entry_type_descr ?? '')) === 'PTX'
                    && (float) ($item->final_amount ?? 0) > 0;
            });
            $netTaxFactor = $this->resolveNetTaxFactorForCoverNote($cover, $coverpremiums, $hasNetOfTaxReinsurer, $hasPremiumTax);

            if ($netTaxFactor !== 1.0) {
                $coverpremiums = $coverpremiums->map(function ($item) use ($netTaxFactor) {
                    $item->basic_amount = (float) ($item->basic_amount ?? 0) * $netTaxFactor;
                    $item->final_amount = (float) ($item->final_amount ?? 0) * $netTaxFactor;

                    return $item;
                });

                $basicTotalDR *= $netTaxFactor;
                $basicTotalCR *= $netTaxFactor;
                $finalTotalDR *= $netTaxFactor;
                $finalTotalCR *= $netTaxFactor;
            }

            $sharePercent = (float) ($cover->share_offered ?? 0) * $netTaxFactor;

            $shared_data = [
                'company' => $company,
                'cover' => $cover,
                'reinsurers' => $reinsurers,
                'debit' => $debit,
                'class_name' => $class_name,
                'treaty_type' => $treaty_type,
                'coverpremiums' => $coverpremiums,
                'basicTotalDR' => $basicTotalDR,
                'basicTotalCR' => $basicTotalCR,
                'finalTotalDR' => $finalTotalDR,
                'finalTotalCR' => $finalTotalCR,
                'share_percent' => $sharePercent,
                'ppw' => $ppw,
                'installmentAmts' => $installmentAmts,
            ];
            $other_data = [];
            $view_name = null;
            $view_path = 'printouts.';

            switch ($cover->type_of_bus) {
                case 'TPR':
                    if ($cover->transaction_type == 'QTR') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    } elseif ($cover->transaction_type == 'PC') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    } elseif ($cover->transaction_type == 'POT' || $cover->transaction_type == 'PIN') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
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
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    } elseif ($cover->transaction_type == 'RNS') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    } elseif ($cover->transaction_type == 'ADJ') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    }

                    break;

                case 'FPR':
                case 'FNP':
                    $view_name = $view_path . 'fac_covernote_all';
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

            return response($dompdf->output(), 200)
                ->header(
                    'Content-Type',
                    'application/pdf'
                )
                ->header('Content-Disposition', 'inline; filename="Debit_Note_' . $cover->endorsement_no . '_' . time() . '.pdf"');
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An internal server error occurred.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function claimCreditnote(Request $request)
    {
        try {
            $company = Company::first();
            $debit = ClaimDebit::where('claim_no', $request->claim_no)->where('id', $request->id)->first();
            $claim = ClaimRegister::where('claim_no', $request->claim_no)->first();
            $claimNotification = ClaimNtfRegister::where('converted_claim_no', $request->claim_no)->first();
            $claimperils = ClaimPeril::where('claim_no', $request->claim_no)->get();
            $cover = CoverRegister::with('customer')->where('endorsement_no', $request->endorsement_no)->first();
            if ($cover->class_code == 'TRT') {
                $class_name = 'TREATY';
            } else {
                $class = Classes::where('class_code', $cover->class_code)->first();
                $class_name = $class->class_name;
            }
            $treaty_type = TreatyType::where('treaty_code', $cover->treaty_type)->first();
            $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->get();
            // TODO: pick correct cover premium
            $ppw = PremiumPayTerm::where('pay_term_code', $cover->premium_payment_code)->first();
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
            $finalTotalDR = 0;
            $finalTotalCR = 0;

            $finalTotalDR = $claimperils->where('dr_cr', 'DR')
                ->sum('basic_amount');

            $finalTotalCR = $claimperils->where('dr_cr', 'CR')
                ->sum('basic_amount');

            $totalClaimAmount = (float) $finalTotalDR - $finalTotalCR;

            $shared_data = [
                'company' => $company,
                'cover' => $cover,
                'claim' => $claim,
                'claimNotification' => $claimNotification,
                'reinsurers' => $reinsurers,
                'debit' => $debit,
                'class_name' => $class_name,
                'treaty_type' => $treaty_type,
                'claimperils' => $claimperils,
                'ppw' => $ppw,
                'coverpremiums' => $coverpremiums,
                'finalTotalDR' => $finalTotalDR,
                'finalTotalCR' => $finalTotalCR,
                'totalClaimAmount' => $totalClaimAmount
            ];

            $other_data = [];
            $view_name = null;
            $view_path = 'printouts.';

            switch ($cover->type_of_bus) {
                case 'TPR':
                    if ($cover->transaction_type == 'QTR') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    } elseif ($cover->transaction_type == 'PC') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    } elseif ($cover->transaction_type == 'POT' || $cover->transaction_type == 'PIN') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    }
                    break;

                case 'TNP':
                    if ($cover->transaction_type == 'MDP') {
                        $mdpInstallment = CoverInstallments::where('endorsement_no', $coverpremiums[0]->orig_endorsement_no)
                            ->where('installment_no', $coverpremiums[0]->installment_no)
                            ->where('dr_cr', 'DR')
                            ->first();
                        $other_data = [
                            'mdpInstallment' => $mdpInstallment,
                        ];
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    } elseif ($cover->transaction_type == 'RNS') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    } elseif ($cover->transaction_type == 'ADJ') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    } elseif ($cover->transaction_type == 'NEW') {
                        $view_name = $view_path . 'tpr_covernote_qtr';
                    }

                    break;

                case 'FPR':
                case 'FNP':
                    $view_name = $view_path . 'fac_clmcreditnote_all';
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

            return $dompdf->stream('Claim_Credit_Note_' . time() . '.pdf');
        } catch (\Exception $e) {
            throw ($e);
        }
    }

    public function reinCreditNotes(Request $request)
    {
        try {
            $endorsement_no = $request->endorsement_no;
            $includeCommission = $request->include_broking_commission;
            $company = Company::first();

            $partner_no = $request->partner_no;
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

            return response($dompdf->output(), 200)
                ->header(
                    'Content-Type',
                    'application/pdf'
                )
                ->header('Content-Disposition', 'inline; filename="Credit_Note_' . time() . '.pdf"');
        } catch (Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An internal server error occurred.',
                'errors' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function reinClmDebitNote(Request $request)
    {
        $endorsement_no = $request->endorsement_no;
        $company = Company::first();

        $partner_no = $request->partner_no;
        if ($partner_no === null) {
            $cover = CoverRegister::where('endorsement_no', $endorsement_no)->first();
            $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)
                ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                ->select('coverripart.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                ->get();
            $rein_notes = ClaimReinNote::where('claim_no', $request->claim_no)->get();
        } else {
            $cover = CoverRegister::where('endorsement_no', $endorsement_no)->first();
            $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->where('partner_no', $partner_no)
                ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                ->select('coverripart.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                ->get();
            $rein_notes = ClaimReinNote::where('claim_no', $request->claim_no)->where('partner_no', $partner_no)->get();
        }

        if ($cover->class_code == 'TRT') {
            $class_name = 'TREATY';
        } else {
            $class = Classes::where('class_code', $cover->class_code)->first();
            $class_name = $class->class_name;
        }
        $treaty_type = TreatyType::where('treaty_code', $cover->treaty_type)->first();
        $claimperils = ClaimPeril::where('claim_no', $request->claim_no)->get();
        if ($request->id == null) {
            $credits = ClaimDebit::where('claim_no', $request->claim_no)->get();
        } else {
            $credits = ClaimDebit::where('claim_no', $request->claim_no)->where('id', $request->id)->get();
        }

        $claim = ClaimRegister::where('claim_no', $request->claim_no)->first();
        $claimNotification = ClaimNtfRegister::where('converted_claim_no', $request->claim_no)->first();
        $basicTotalDR = $rein_notes->where('dr_cr', 'DR')
            ->sum('gross');

        $basicTotalCR = $rein_notes->where('dr_cr', 'CR')
            ->sum('gross');

        $finalTotalDR = $claimperils->where('dr_cr', 'DR')
            ->sum('basic_amount');

        $finalTotalCR = $claimperils->where('dr_cr', 'CR')
            ->sum('basic_amount');

        $totalClaimAmount = (float) $finalTotalDR - $finalTotalCR;

        $balance = $finalTotalCR - $finalTotalDR;

        $debit = ClaimDebit::where('claim_no', $request->claim_no)->where('id', $request->id)->first();

        $shared_data = [
            'company' => $company,
            'cover' => $cover,
            'claimNotification' => $claimNotification,
            'claim' => $claim,
            'reinsurers' => $reinsurers,
            'rein_notes' => $rein_notes,
            'credits' => $credits,
            'class_name' => $class_name,
            'treaty_type' => $treaty_type,
            'basicTotalDR' => $basicTotalDR,
            'basicTotalCR' => $basicTotalCR,
            'finalTotalDR' => $finalTotalDR,
            'finalTotalCR' => $finalTotalCR,
            'balance' => $balance,
            'claimperils' => $claimperils,
            'debit' => $debit,
            'totalClaimAmount' => $totalClaimAmount,
        ];
        $other_data = [];
        $view_name = null;
        $view_path = 'printouts.';

        switch ($cover->type_of_bus) {
            case 'TPR':
            case 'TNP':
            case 'FPR':
            case 'FNP':
                $view_name = $view_path . 'fac_clm_reindebit_note';
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

        return $dompdf->stream('Claim_Debit_Note_' . time() . '.pdf');
    }

    public function claimNtfDocsAckLetter(Request $request)
    {
        $intimation_no = $request->intimation_no;
        $documented = isset($request->documented) ? boolval($request->documented) : false;

        try {
            $company = Company::first();
            $claim = ClaimNtfRegister::where('intimation_no', $intimation_no)->first();
            $docs = ClaimNtfAckDocs::where('intimation_no', $intimation_no)->get();
            $partner_no = $request->partner_no;

            if ($partner_no === null) {
                $cover = CoverRegister::where('endorsement_no', $claim->endorsement_no)
                    ->join('customers', 'cover_register.customer_id', '=', 'customers.customer_id')
                    ->select('cover_register.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->first();
                $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)
                    ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                    ->select('coverripart.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->get();
            } else {
                $cover = CoverRegister::where('endorsement_no', $claim->endorsement_no)
                    ->join('customers', 'cover_register.customer_id', '=', 'customers.customer_id')
                    ->select('cover_register.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->first();
                $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->where('partner_no', $partner_no)
                    ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                    ->select('coverripart.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->get();
            }

            $other_perils = ClaimNtfPeril::where('intimation_no', $intimation_no)
                ->where('dr_cr_note_no', 0)
                ->where('dr_cr', '!=', 'CR')
                ->sum('basic_amount');
            $salvage = ClaimNtfPeril::where('intimation_no', $intimation_no)
                ->where('dr_cr_note_no', 0)
                ->where('dr_cr', 'CR')
                ->sum('basic_amount');
            $total_claim =  $salvage ? (float) $other_perils - (float) $salvage : (float) $other_perils;
            $claim_amount = number_format($total_claim, 2);


            $subject_title = 'Dear Sir/Madam';
            $ref_title = 'RE: ' . $claim->cause_of_loss . ' on ' . Carbon::parse($claim->date_of_losss)->format('M d, Y') . '<br/> Insured: ' . $claim->insured_name;

            $view_path = 'printouts.';
            $view_name = $view_path . 'claimntf_docs_ack';
            $data = [
                'company' => $company,
                'cover' => $cover,
                'claim' => $claim,
                'claim_amount' => $claim_amount,
                'docs' => $docs,
                'ref_title' => $ref_title,
                'reinsurers' => $reinsurers,
                'subject_title' => $subject_title,
                'documented' => $documented
            ];

            $dompdf = Pdf::loadView(
                $view_name,
                $data
            )->setPaper('a4', 'portrait')->setWarnings(false);
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isPhpEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();

            return response($dompdf->output(), 200)
                ->header(
                    'Content-Type',
                    'application/pdf'
                )
                ->header('Content-Disposition', 'inline; filename="Claim_Acknowledgement_Letter_' . time() . '.pdf"');
            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function claimNtfDocsNotcLetter(Request $request)
    {
        $intimation_no = $request->intimation_no;
        try {
            $company = Company::first();
            $claim = ClaimNtfRegister::where('intimation_no', $intimation_no)->first();
            $docs = ClaimNtfAckDocs::where('intimation_no', $intimation_no)->get();
            $partner_no = $request->partner_no;

            if ($partner_no === null) {
                $cover = CoverRegister::where('endorsement_no', $claim->endorsement_no)
                    ->join('customers', 'cover_register.customer_id', '=', 'customers.customer_id')
                    ->select('cover_register.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->first();
                $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)
                    ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                    ->select('coverripart.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->get();
            } else {
                $cover = CoverRegister::where('endorsement_no', $claim->endorsement_no)
                    ->join('customers', 'cover_register.customer_id', '=', 'customers.customer_id')
                    ->select('cover_register.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->first();
                $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->where('partner_no', $partner_no)
                    ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                    ->select('coverripart.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                    ->get();
            }

            $other_perils = ClaimNtfPeril::where('intimation_no', $intimation_no)
                ->where('dr_cr_note_no', 0)
                ->where('dr_cr', '!=', 'CR')
                ->sum('basic_amount');
            $salvage = ClaimNtfPeril::where('intimation_no', $intimation_no)
                ->where('dr_cr_note_no', 0)
                ->where('dr_cr', 'CR')
                ->sum('basic_amount');
            $total_claim =  $salvage ? (float) $other_perils - (float) $salvage : (float) $other_perils;
            $claim_amount = number_format($total_claim, 2);


            $subject_title = 'Dear Sir/Madam';
            $ref_title = 'RE: ' . $claim->cause_of_loss . ' on ' . Carbon::parse($claim->date_of_losss)->format('M d, Y') . '<br/> Insured: ' . $claim->insured_name;

            $view_path = 'printouts.';
            $view_name = $view_path . 'claimntf_docs_notc';
            $data = [
                'company' => $company,
                'cover' => $cover,
                'claim' => $claim,
                'docs' => $docs,
                'reinsurers' => $reinsurers,
                'subject_title' => $subject_title,
                'ref_title' => $ref_title,
                'claim_amount' => $claim_amount
            ];

            $dompdf = Pdf::loadView(
                $view_name,
                $data
            )->setPaper('a4', 'portrait')->setWarnings(false);
            $dompdf->set_option('isHtml5ParserEnabled', true);
            $dompdf->set_option('isPhpEnabled', true);
            $dompdf->set_option('isRemoteEnabled', true);
            $dompdf->render();

            return response($dompdf->output(), 200)
                ->header(
                    'Content-Type',
                    'application/pdf'
                )
                ->header('Content-Disposition', 'inline; filename="Claim_Notice_Letter_' . time() . '.pdf"');
            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function claimDocsAckLetter(Request $request)
    {
        $company = Company::first();
        $claim = ClaimRegister::where('claim_no', $request->claim_no)->first();
        // $cover = CoverRegister::with('customer')->where('endorsement_no',$claim->endorsement_no)->first();
        $docs = ClaimAckDocs::where('claim_no', $request->claim_no)->get();
        $partner_no = $request->partner_no;
        if ($partner_no === null) {
            $reinsurers = CoverRipart::where('endorsement_no', $claim->endorsement_no)
                ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                ->select('coverripart.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                ->get();
            $cover = CoverRegister::query()->where('endorsement_no', $claim->endorsement_no)->with('customer')->first();
        } else {
            $cover = CoverRegister::query()->where('endorsement_no', $claim->endorsement_no)->with('customer')->first();
            $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->where('partner_no', $partner_no)
                ->join('customers', 'coverripart.partner_no', '=', 'customers.customer_id')
                ->select('coverripart.*', 'customers.name as partner_name', 'customers.postal_address as partner_postal_address', 'customers.city as partner_city', 'customers.telephone as partner_telephone', 'customers.street as partner_street', 'customers.country_iso as partner_scountry_iso')
                ->get();
        }
        $view_path = 'printouts.';

        $view_name = $view_path . 'claim_docs_ack';

        $data = [
            'company' => $company,
            'cover' => $cover,
            'claim' => $claim,
            'docs' => $docs,
            'reinsurers' => $reinsurers,
        ];

        $dompdf = Pdf::loadView(
            $view_name,
            $data
        )->setPaper('a4', 'portrait')->setWarnings(false);
        $dompdf->render();

        return $dompdf->stream($filename = "Acknowledgement Letter");
    }

    public function printReceipt(Request $request)
    {
        $cashbook = CashBook::where('offcd', $request->offcd)
            ->where('source_code', $request->source_code)
            ->where('doc_type', $request->doc_type)
            ->where('transaction_no', $request->transaction_no)
            ->where('account_year', $request->account_year)
            ->first();

        $cashbookana = CashBookana::where('offcd', $cashbook->offcd)
            ->where('source_code', $cashbook->source_code)
            ->where('doc_type', $cashbook->doc_type)
            ->where('reference_no', $cashbook->transaction_no)
            ->get();
        $transType = CBTransType::where('doc_type', $cashbook->doc_type)
            ->where('type_code', $cashbook->entry_type_descr)
            ->where('source_code', $cashbook->source_code)
            ->first();

        $dompdf = Pdf::loadView('printouts.receipt_printout', [
            'cashbook' => $cashbook,
            'cashbookana' => $cashbookana,
            'transType' => $transType,
        ])->setPaper('a5', 'landscape')->setWarnings(false);
        $dompdf->render();

        return $dompdf->stream($transType->description . '.pdf');
    }

    function printCOA(Request $request)
    {
        $coa_listing = COAListing::all();

        $dompdf = Pdf::loadView('printouts.coa_printout', [
            'coa_listings' => $coa_listing,
        ])->setPaper('a4', 'portrait')->setWarnings(false);
        $dompdf->render();
        // Output the generated PDF to Browser
        return $dompdf->stream('chartofaccounts.pdf');
    }

    function payRequestPrint(Request $request)
    {
        $requisition_no = $request->requisition_no;
        $requisition = CBRequisition::where('requisition_no', $requisition_no)->first();
        $cbdeductions = CBDeductionMain::where('reference_no', $requisition_no)->get();

        $dompdf = Pdf::loadView('printouts.payreq_printout', [
            'requisition' => $requisition,
            'cbdeductions' => $cbdeductions,
        ])->setPaper('a4', 'portrait')->setWarnings(false);
        $dompdf->render();

        return $dompdf->stream('payreq_printout.pdf');
    }

    function payVoucherPrint(Request $request)
    {
        $requisition_no = $request->requisition_no;
        $cashbook = CashBook::where('pay_request_no', $requisition_no)->first();
        $cashbookanas = CashBookana::where('pay_request_no', $requisition_no)->get();

        $dompdf = Pdf::loadView('printouts.payvoucher_printout', [
            'cashbook' => $cashbook,
            'cashbookanas' => $cashbookanas,
        ])->setPaper('a4', 'portrait')->setWarnings(false);
        $dompdf->render();

        return $dompdf->stream('payvoucher_printout.pdf');
    }

    public function preCoverSlipVerification(Request $request)
    {
        try {
            $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();
            $pending = [];

            $reinsurers = CoverRipart::where(['cover_no' => $cover->cover_no, 'endorsement_no' => $cover->endorsement_no])->get();

            if ($reinsurers->count() == 0) {
                array_push($pending, ' Reinsurer has not yet been placed for this policy.');
            }
            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Verification successful',
                'data' => $pending
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return ['An internal error occured'];
        }
    }

    public function getOppPdfData(Request $request, $opppId)
    {
        $opportunityId = $opppId;
        $stage = (string) $request->input('stage', '');
        $stageValues = $this->resolveProspectStatusesForPreview($stage);

        $prospectDocsQuery = DB::table('prospect_docs')
            ->where('prospect_id', $opppId);

        if (!empty($stageValues)) {
            $prospectDocsQuery->where(function ($query) use ($stageValues) {
                foreach ($stageValues as $value) {
                    $query->orWhere('prospect_status', $value);
                }
            });
        }

        $prospectDocs = $prospectDocsQuery
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($doc) {
                $docUrl = $this->resolveProspectDocumentUrl($doc);
                $docType = strtolower(trim((string) ($doc->type ?? '')));

                if ($docType === '' || $docType === 'genera') {
                    $docType = 'general';
                }

                return [
                    'id' => $doc->id,
                    'name' => $doc->original_name ?: ($doc->file ?: ($doc->description ?: 'Document')),
                    'url' => $docUrl,
                    'mime_type' => $doc->mimetype,
                    'type' => $docType,
                    'description' => $doc->description ?? '',
                    'upload_date' => $doc->created_at,
                    'file_size' => $doc->file_size ?? 0
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'pdfs' => $prospectDocs,
            'opportunity_id' => $opportunityId,
            'stage' => $stage
        ]);
    }

    private function resolveProspectStatusesForPreview(string $stage): array
    {
        $normalizedStage = Str::of($stage)
            ->trim()
            ->lower()
            ->replace('-', '_')
            ->value();

        if ($normalizedStage === '') {
            return [];
        }

        $stageMap = [
            'lead' => ['1', 1, 'lead'],
            'proposal' => ['2', 2, 'proposal'],
            'negotiation' => ['3', 3, 'negotiation'],
            'final' => ['4', 4, 'final', 'final_stage'],
            'final_stage' => ['4', 4, 'final', 'final_stage'],
            'close_won' => ['5', 5, 'won', 'close_won', 'close-won'],
            'won' => ['5', 5, 'won', 'close_won', 'close-won'],
            'lost' => ['6', 6, 'lost'],
        ];

        return $stageMap[$normalizedStage] ?? [$normalizedStage];
    }

    private function resolveProspectDocumentUrl(object $doc): ?string
    {
        $s3Url = trim((string) ($doc->s3_url ?? ''));
        if ($s3Url !== '') {
            return $s3Url;
        }

        $candidatePath = trim((string) ($doc->s3_path ?? ''));
        if ($candidatePath === '') {
            $candidatePath = trim((string) ($doc->file ?? ''));
        }

        if ($candidatePath === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $candidatePath)) {
            return $candidatePath;
        }

        try {
            return Storage::disk('s3')->url($candidatePath);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
