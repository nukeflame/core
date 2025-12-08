<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Customer;
use App\Services\SequenceService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    private SequenceService $sequenceService;

    public function __construct(SequenceService $sequenceService)
    {
        $this->sequenceService = $sequenceService;
    }


    public function index(Request $request)
    {
        return view('quote.quote_home', []);
    }

    public function quotationCoverSlip(Request $request)
    {
        try {
            $validated = $request->validate([
                'opportunity_id' => 'required',
                'printout_flag' => 'sometimes|boolean',
                'reinsurers_data' => 'required'
            ]);
            logger()->debug($request->all());

            $activity = $this->fetchOpportunityData($validated['opportunity_id']);

            if (!$activity) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Opportunity not found.',
                ], 404);
            }

            $formattedActivity = $this->formatActivityData($activity, $request);
            $shares = $this->prepareReinsurers($request, $formattedActivity);
            $requestData = $this->buildRequestData($request, $formattedActivity, $shares);

            $reinsurers = $this->prepareCustomerObjects($requestData);

            $company = Company::firstOrFail();

            $ref = $this->sequenceService->generateDocumentNumber();
            $reference_no = $ref->doc_no;

            $data = [
                'company' => $company,
                'reinsurers' => $reinsurers,
                'shares' => $requestData['shares'],
                'unplaced' => $requestData['unplaced'],
                'updated_written_share_total' => $requestData['updated_written_share_total'],
                'stage' => $requestData['stage'],
                'reference_no' => $reference_no,
                'opportunity' => $formattedActivity
            ];

            $view = 'printouts.fac_coverslipquote';

            // logger()->debug(json_encode($data, JSON_PRETTY_PRINT));

            $dompdf = Pdf::loadView($view, $data)
                ->setPaper('a4', 'portrait')
                ->setWarnings(false)
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

            return $dompdf->stream('Quotation_Cover_Slip_' . time() . '.pdf');
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 422,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {

            logger($e);

            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while generating the document.',
            ], 500);
        }
    }

    private function fetchOpportunityData(string $opportunityId)
    {
        return DB::table('pipeline_opportunities as po')
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
                bt.bus_type_name as type_of_bus')
            ->where('po.opportunity_id', $opportunityId)
            ->first();
    }

    private function formatActivityData($activity, $request)
    {
        return [
            'customer_id' => $activity->customer_id ?? 'N/A',
            'customer_name' => $activity->customer_name ?? 'N/A',
            'opportunity_id' => $activity->opportunity_id,
            'effective_date' => $activity->effective_date,
            'closing_date' => $activity->closing_date,
            'insured_name' => $activity->insured_name,
            'type_of_bus' => $activity->type_of_bus,
            'currency_code' => $activity->currency_code,
            // 'contact_name' => $this->decodeJsonField($activity->contact_name),
            // 'email' => $this->decodeJsonField($activity->email),
            // 'phone' => $this->decodeJsonField($activity->phone),
            // 'telephone' => $this->decodeJsonField($activity->telephone),
            'class_name' => $activity->class_name,
            'stageType' => $activity->category_type ?? null,
            'sum_insured' => $activity->total_sum_insured ?? $request->total_sum_insured
        ];
    }

    private function prepareReinsurers(Request $request, array $formattedActivity)
    {
        if ($request->has('reinsurers_data')) {

            $reinsurers_data = $request->reinsurers_data;
            return json_decode($reinsurers_data, true);
        }

        $customerIds = $request->input('customer_id', []);

        if (empty($customerIds)) {
            return [];
        }

        return collect($customerIds)->map(function ($id, $index) use ($request) {
            return [
                'customer_id' => $id,
                'customer_name' => $request->customer_name[$index] ?? null,
                'customer_email' => $request->customer_email[$index] ?? null,
                'written_share' => $request->written_share[$index] ?? null,
                'contact_name' => ($request->contact_name[$index] ?? null) === 'null' ? null : ($request->contact_name[$index] ?? null),
            ];
        })->toArray();
    }

    private function buildRequestData(Request $request, array $formattedActivity, $shares)
    {
        $current_stage = $request->current_stage;
        return [
            'customer_name' => $formattedActivity['customer_name'],
            'customer_id' => $formattedActivity['customer_id'],
            'schedule_details' => $this->decodeScheduleDetails($request->schedule_details),
            'quote_title_intro' => $request->quote_title_intro ?? '',
            'facschedule_details' => $this->decodeScheduleDetails($request->facschedule_details),
            'shares' => $shares,
            'unplaced' => $request->unplaced ?? '',
            'updated_written_share_total' => $request->updated_written_share_total ?? $request->written_share_total ?? '',
            'insured_name' => $formattedActivity['insured_name'],
            'written_share' => $request->written_share ?? '',
            'signed_share' => $request->signed_share ?? '',
            'contact_name' => $request->contact_name ?? '',
            'type_of_bus' => $formattedActivity['type_of_bus'],
            'class_name' => $formattedActivity['class_name'],
            'opportunity_id' => $formattedActivity['opportunity_id'],
            'currency_code' => $formattedActivity['currency_code'],
            'effective_date' => $formattedActivity['effective_date'],
            'closing_date' => $formattedActivity['closing_date'],
            'stage' => $current_stage,
            'stageType' => $formattedActivity['stageType'],
        ];
    }

    private function prepareCustomerObjects(array $requestData)
    {

        // $reinsurers_data = $requestData['reinsurers_data'];
        // $reinsurers = json_decode($reinsurers_data, true);


        // if (!is_array($requestData['customer_id'])) {
        //     return collect([(object) $this->createCustomerObject($requestData, 0)]);
        // }

        return collect($requestData['shares'])->map(function ($reinsurer, $index) use ($requestData) {
            return (object) $this->createCustomerObject($requestData, $reinsurer);
        });
    }

    private function createCustomerObject(array $requestData, $reinsurer)
    {
        $rein = Customer::where('customer_id', $reinsurer['id'])->first();

        // logger()->debug(json_encode($opportunity, JSON_PRETTY_PRINT));

        return [
            'customer_id' => $rein->customer_id,
            'customer_name' => $rein->name,
            'address' => $rein->address,
            'city' => $rein->city,
            'country' => $rein->country_iso ? 'Kenya' : '',
            'contact_name' => '',
            'written_share' => 0,
            'signed_share' => 0,
            'schedule_details' => $requestData['schedule_details'],
            'facschedule_details' => $requestData['facschedule_details'],
            'quote_title_intro' => $requestData['quote_title_intro'],
            'insured_name' => $requestData['insured_name'],
            'type_of_bus' => $requestData['type_of_bus'],
            'opportunity_id' => $requestData['opportunity_id'],
            'effective_date' => $requestData['effective_date'],
            'closing_date' => $requestData['closing_date'],
            'class_name' => $requestData['class_name'],
            'currency_code' => $requestData['currency_code'],
            'stageType' => $requestData['stageType'],
        ];

        // return [
        //     'customer_id' => is_array($requestData['customer_id']) ? $requestData['customer_id'][$index] : $requestData['customer_id'],
        //     'customer_name' => $requestData['customer_name'],
        //     'contact_name' => is_array($requestData['contact_name']) ? ($requestData['contact_name'][$index] ?? null) : $requestData['contact_name'],
        //     'written_share' => is_array($requestData['written_share']) ? ($requestData['written_share'][$index] ?? null) : $requestData['written_share'],
        //     'signed_share' => is_array($requestData['signed_share']) ? ($requestData['signed_share'][$index] ?? null) : $requestData['signed_share'],
        //     'schedule_details' => $requestData['schedule_details'],
        //     'facschedule_details' => $requestData['facschedule_details'],
        //     'quote_title_intro' => $requestData['quote_title_intro'],
        //     'insured_name' => $requestData['insured_name'],
        //     'type_of_bus' => $requestData['type_of_bus'],
        //     'opportunity_id' => $requestData['opportunity_id'],
        //     'effective_date' => $requestData['effective_date'],
        //     'closing_date' => $requestData['closing_date'],
        //     'class_name' => $requestData['class_name'],
        //     'currency_code' => $requestData['currency_code'],
        //     'stageType' => $requestData['stageType'],
        // ];
    }

    private function decodeScheduleDetails($details)
    {
        if (empty($details)) {
            return [];
        }

        if (is_string($details)) {
            $decoded = json_decode($details, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($details) ? $details : [];
    }


    // public function QuotationCoverSlip(Request $request)
    // {
    //     // dd($request->all());
    //     try {
    //         $user = [
    //             'firstname' => auth()->user()->firstname ?? '',
    //             'lastname' => auth()->user()->lastname ?? ''
    //         ];



    //         $activities = DB::table('pipeline_opportunities as po')
    //             ->leftJoin('customers as c', function ($join) {
    //                 $join->on(DB::raw("NULLIF(po.customer_id, '')::INTEGER"), '=', 'c.customer_id');
    //             })
    //             ->leftJoin('lead_status as ls', 'po.stage', '=', 'ls.id')
    //             ->leftJoin('reins_division as rd', 'po.divisions', '=', 'rd.division_code')
    //             ->leftJoin('classes as cl', 'po.classcode', '=', 'cl.class_code')
    //             ->leftJoin('business_types as bt', 'po.type_of_bus', '=', 'bt.bus_type_id')
    //             ->selectRaw('DISTINCT ON (po.opportunity_id) po.*,
    //              COALESCE(c.name, \'N/A\') AS customer_name,
    //              ls.status_name as stage,
    //              rd.division_name as division_name,
    //              po.cede_premium as cedant_premium,
    //              po.rein_premium as reinsurer_premium,
    //              cl.class_name as class_name,
    //              bt.bus_type_name as type_of_bus')
    //             ->where('po.opportunity_id', $request->opp_id)
    //             ->get();

    //         // dd($activities);


    //         $formattedActivities = $activities->map(function ($d) {
    //             return [
    //                 'customer_id' => $d->customer_id ?? 'N/A',
    //                 'customer_name' => $d->customer_name ?? 'N/A',
    //                 'opportunity_id' => $d->opportunity_id,
    //                 'effective_date' => $d->effective_date,
    //                 'closing_date' => $d->closing_date,
    //                 'insured_name' => $d->insured_name,
    //                 'type_of_bus' => $d->type_of_bus,
    //                 'currency_code' => $d->currency_code,
    //                 'contact_name' => json_decode($d->contact_name, true) ?? [],
    //                 'email' => json_decode($d->email, true) ?? [],
    //                 'phone' => json_decode($d->phone, true) ?? [],
    //                 'telephone' => json_decode($d->telephone, true) ?? [],
    //                 'class_name' => $d->class_name,
    //                 'stageType' => $d->category_type
    //             ];
    //         });
    //         // dd($formattedActivities);
    //         $company = Company::first();
    //         $data = [
    //             'customer_id' => $request->customer_id,
    //             'customer_name' => $request->customer_name,
    //             'unplaced' => $request->unplaced ?? '',
    //             'updated_written_share_total' => $request->updated_written_share_total ?? '',
    //             'customer_email' => $request->customer_email,
    //             'contact_name' => $request->contact_name,
    //             'written_share' => isset($request->written_share) ? $request->written_share : ''
    //         ];
    //         // dd($data);


    //         // Transform arrays of stage 25% into a collection of array
    //         $shares = collect($data['customer_id'])->map(function ($id, $index) use ($data) {
    //             return [
    //                 'customer_id' => $id,
    //                 'customer_name' => $data['customer_name'][$index],
    //                 'customer_email' => $data['customer_email'][$index],
    //                 'written_share' => $data['written_share'][$index] ?? '',
    //                 'contact_name' => $data['contact_name'][$index] === 'null' ? null : $data['contact_name'][$index]
    //             ];
    //         });
    //         $requestData = [
    //             'customer_name' => $formattedActivities[0]['customer_name'] ?? '',
    //             'customer_id' => $formattedActivities[0]['customer_id'] ?? '',
    //             // 'email' => $request->customer_email,
    //             'schedule_details' => is_string($request->schedule_details) ? json_decode($request->schedule_details, true) : $request->schedule_details,
    //             'quote_title_intro' => $request->quote_title_intro ?? '',
    //             'facschedule_details' => is_string($request->facschedule_details) ? json_decode($request->facschedule_details, true) : $request->facschedule_details,
    //             'shares' => $request->reinsurers ?? $shares,
    //             'unplaced' => $request->unplaced ?? '',
    //             'updated_written_share_total' => $request->updated_written_share_total ?? $request->written_share_total,
    //             'insured_name' => $formattedActivities[0]['insured_name'] ?? '',
    //             'written_share' => $request->written_share ?? '',
    //             'signed_share' => $request->signed_share ?? '',
    //             'contact_name' => $request->contact_name ?? '',
    //             'type_of_bus' => $formattedActivities[0]['type_of_bus'] ?? '',
    //             'class_name' => $formattedActivities[0]['class_name'] ?? '',
    //             'opportunity_id' => $formattedActivities[0]['opportunity_id'] ?? '',
    //             'currency_code' => $formattedActivities[0]['currency_code'] ?? '',
    //             'effective_date' => $formattedActivities[0]['effective_date'] ?? '',
    //             'closing_date' => $formattedActivities[0]['closing_date'] ?? '',
    //             'stage' => $request->stage_cycle_fac ?? $request->stagecycleqt ?? '',
    //             'stageType' => $formattedActivities[0]['stageType'] ?? '',
    //         ];

    //         // dd($requestData);
    //         // Combine customer data with joined table data
    //         $customers = collect($requestData['customer_id'])->map(function ($customerId, $index) use ($requestData) {

    //             // Get the corresponding activity for the customer

    //             return (object) [
    //                 'customer_id' => $customerId,
    //                 'customer_name' => $requestData['customer_name'] ?? 'N/A',
    //                 'contact_name' => $requestData['contact_name'][$index] ?? null,
    //                 'written_share' => $requestData['written_share'][$index] ?? null,
    //                 'signed_share' => $requestData['signed_share'][$index] ?? null,
    //                 'schedule_details' => is_string($requestData['schedule_details']) ? json_decode($requestData['schedule_details'], true) : $requestData['schedule_details'],
    //                 'facschedule_details' => is_string($requestData['facschedule_details']) ? json_decode($requestData['facschedule_details'], true) : $requestData['facschedule_details'],


    //                 'quote_title_intro' => $requestData['quote_title_intro'] ?? null,

    //                 'insured_name' => $requestData['insured_name'] ?? null,
    //                 'type_of_bus' => $requestData['type_of_bus'] ?? null,
    //                 'opportunity_id' => $requestData['opportunity_id'] ?? null,
    //                 'effective_date' => $requestData['effective_date'] ?? null,
    //                 'closing_date' => $requestData['closing_date'] ?? null,
    //                 'class_name' => $requestData['class_name'] ?? null,
    //                 'currency_code' => $requestData['currency_code'] ?? null,
    //                 'stageType' => $requestData['stageType'] ?? null

    //             ];
    //         });
    //         // dd($customers);


    //         $data = [
    //             'company' => $company,
    //             'customers' => $customers,
    //             'amount' => $requestData['amount'] ?? '',
    //             'shares' => $requestData['shares'] ?? '',
    //             'unplaced' => $requestData['unplaced'] ?? '',
    //             'updated_written_share_total' => $requestData['updated_written_share_total'] ?? '',
    //             'stage' => $requestData['stage'] ?? '',
    //         ];


    //         $view_path = 'printouts.';
    //         $view_name = $view_path . 'fac_coverslipquote';

    //         // dd($customers);
    //         // dd($QuoteDetailInput);





    //         if ($request->printout_flag == 1) {
    //             // Create a new Word document
    //             $phpWord = new PhpWord();
    //             $phpWord->setDefaultFontName('Times New Roman');
    //             $phpWord->setDefaultFontSize(10);

    //             // Define styles
    //             $headerFontStyle = ['bold' => true, 'size' => 12];
    //             $subHeaderFontStyle = ['bold' => true, 'size' => 10];
    //             $normalFontStyle = ['size' => 10];
    //             $footerFontStyle = ['size' => 8];
    //             $centerAlignment = ['alignment' => Jc::CENTER];
    //             $leftAlignment = ['alignment' => Jc::LEFT];
    //             $rightAlignment = ['alignment' => Jc::RIGHT];
    //             $tableStyle = [
    //                 'borderSize' => 6,
    //                 'borderColor' => '000000',
    //                 'cellMargin' => 80,
    //             ];
    //             $lineStyle = [
    //                 'weight' => 1,
    //                 'width' => 500,
    //                 'height' => 0,
    //                 'color' => '000000',
    //             ];

    //             // Extract data
    //             $company = $data['company'];
    //             $customers = $data['customers'];
    //             $shares = $data['shares'];
    //             $unplaced = $data['unplaced'];
    //             $updated_written_share_total = $data['updated_written_share_total'];
    //             $stage = $data['stage'];

    //             // Conditional logic based on stage and stageType
    //             $isSpecialCase = (($stage == 3 && $customers[0]->stageType == 2) || ($stage == 2 && $customers[0]->stageType == 1));

    //             foreach ($shares as $index => $sh) {
    //                 $section = $phpWord->addSection([
    //                     'marginTop' => Converter::cmToTwip(2),
    //                     'marginBottom' => Converter::cmToTwip(1),
    //                     'marginLeft' => Converter::cmToTwip(2),
    //                     'marginRight' => Converter::cmToTwip(2),
    //                     'pageNumberingStart' => 1,
    //                 ]);

    //                 // Add Header
    //                 $header = $section->addHeader();
    //                 $headerTable = $header->addTable();
    //                 $headerTable->addRow();
    //                 $logoCell = $headerTable->addCell(5000);
    //                 $infoCell = $headerTable->addCell(7000);

    //                 // Add logo
    //                 $logoPath = public_path('logo.png');
    //                 if (file_exists($logoPath) && is_readable($logoPath)) {
    //                     $logoCell->addImage($logoPath, [
    //                         'width' => 150,
    //                         'alignment' => Jc::RIGHT,
    //                     ]);
    //                 }

    //                 // Add company info
    //                 $infoCell->addText($company->company_name, ['bold' => true], $leftAlignment);
    //                 $infoCell->addText($company->postal_address, [], $leftAlignment);
    //                 $infoCell->addText('Phone: ' . $company->mobilephone, [], $leftAlignment);
    //                 $infoCell->addText('Email: ' . $company->email, [], $leftAlignment);

    //                 foreach ($customers as $customer) {
    //                     // Add title
    //                     $section->addText(
    //                         strtoupper($customer->quote_title_intro . ' - ' . $customer->class_name . ' - ' . ($customer->insured_name ?? 'N/A')),
    //                         $headerFontStyle,
    //                         $centerAlignment
    //                     );
    //                     $section->addTextBreak(1);

    //                     // Add horizontal line
    //                     $section->addLine(array_merge($lineStyle, ['alignment' => Jc::LEFT]));

    //                     // Basic details
    //                     $detailsTable = $section->addTable($tableStyle);
    //                     $details = [
    //                         'Our Reference' => '',
    //                         'Cedant Name' => ucfirst($customer->customer_name ?? 'N/A'),
    //                         'Insured Name' => ucfirst($customer->insured_name ?? 'N/A'),
    //                         'Insurance Group' => ucfirst($customer->type_of_bus ?? 'N/A'),
    //                         'Class Of Business' => ucfirst($customer->class_name ?? 'N/A'),
    //                         'Period Of Cover' => ucfirst(($customer->effective_date ?? '') . ' To ' . ($customer->closing_date ?? '')),
    //                     ];

    //                     foreach ($details as $field => $value) {
    //                         $detailsTable->addRow();
    //                         $detailsTable->addCell(4000)->addText($field . ':', $subHeaderFontStyle, $leftAlignment);
    //                         $detailsTable->addCell(8000)->addText($value, $normalFontStyle, $leftAlignment);
    //                     }

    //                     $section->addTextBreak(1);

    //                     // Schedule details
    //                     $scheduleDetails = isset($customer->facschedule_details) ? $customer->facschedule_details : $customer->schedule_details;
    //                     if (!empty($scheduleDetails)) {
    //                         foreach ($scheduleDetails as $detail) {
    //                             if ((isset($detail['id']) && isset($detail['amount'])) || isset($detail['details'])) {
    //                                 if (trim(strtolower($detail['name'])) == 'policy wording') {
    //                                     $section->addText(ucfirst($detail['name'] ?? ''), $subHeaderFontStyle, $leftAlignment);
    //                                     if (isset($detail['details'])) {
    //                                         $section->addText(strip_tags(html_entity_decode($detail['details'])), $normalFontStyle, $leftAlignment);
    //                                     }
    //                                     $section->addTextBreak(1);
    //                                 } else {
    //                                     $scheduleTable = $section->addTable($tableStyle);
    //                                     $scheduleTable->addRow();
    //                                     $name = ucfirst($detail['name'] ?? '');
    //                                     $lowerName = trim(strtolower($detail['name']));
    //                                     $suffix = '';
    //                                     if (in_array($lowerName, ['cedant commission rate', 'reinsurer commission rate'])) {
    //                                         $suffix = ' (%):';
    //                                     } elseif (
    //                                         in_array($lowerName, [
    //                                             'premium',
    //                                             'total sum insured',
    //                                             'first loss',
    //                                             'top location',
    //                                             'limit of indemnity',
    //                                             'maximum loss limit',
    //                                             'limit of liability',
    //                                             'agreed value'
    //                                         ])
    //                                     ) {
    //                                         $suffix = ' (100%):';
    //                                     } else {
    //                                         $suffix = ':';
    //                                     }
    //                                     $scheduleTable->addCell(4000)->addText($name . $suffix, $subHeaderFontStyle, $leftAlignment);

    //                                     $amountText = isset($detail['amount']) && $detail['amount'] !== null ? ($lowerName !== 'reinsurer commission rate' ? $customer->currency_code . ' ' : '') . $detail['amount'] : '';
    //                                     if ($lowerName == 'reinsurer commission rate') {
    //                                         $amountText .= '%';
    //                                     }
    //                                     $detailsText = isset($detail['details']) ? strip_tags(html_entity_decode($detail['details'])) : '';
    //                                     $scheduleTable->addCell(8000)->addText(trim($amountText . ' ' . $detailsText), $normalFontStyle, $leftAlignment);
    //                                 }
    //                             }
    //                         }
    //                     }

    //                     $section->addTextBreak(1);

    //                     // Share details
    //                     if (!$isSpecialCase) {
    //                         // Case 1: Regular reinsurer pages
    //                         $section->addText('Placed With:', $subHeaderFontStyle, $leftAlignment);
    //                         $validNames = [
    //                             'premium',
    //                             'total sum insured',
    //                             'first loss',
    //                             'top location',
    //                             'limit of indemnity',
    //                             'maximum loss limit',
    //                             'limit of liability',
    //                             'agreed value'
    //                         ];
    //                         $displayValues = [];
    //                         foreach ($scheduleDetails as $detail) {
    //                             if (!isset($detail['name']) || !isset($detail['amount']))
    //                                 continue;
    //                             $detailName = strtolower(trim($detail['name']));
    //                             if ($detailName === 'reinsurer commission rate' || !in_array($detailName, $validNames))
    //                                 continue;
    //                             $amount = floatval(str_replace(',', '', $detail['amount']));
    //                             $shareKey = isset($sh['signed_share']) && $sh['signed_share'] != null ? 'signed_share' : 'written_share';
    //                             $shareValue = isset($sh[$shareKey]) ? floatval($sh[$shareKey]) : 0;
    //                             $calculatedAmount = $amount * ($shareValue / 100);
    //                             $displayValues[$detailName] = number_format($calculatedAmount, 2, '.', '');
    //                         }
    //                         $selectedNames = array_keys($displayValues);

    //                         $shareTable = $section->addTable($tableStyle);
    //                         $shareTable->addRow();
    //                         $shareTable->addCell(3000)->addText('Reinsurer', $subHeaderFontStyle, $leftAlignment);
    //                         $shareTable->addCell(2000)->addText(ucfirst(str_replace('_', ' ', $shareKey)) . ' (%)', $subHeaderFontStyle, $centerAlignment);
    //                         foreach ($selectedNames as $name) {
    //                             $shareTable->addCell(3000)->addText(ucfirst($name) . " ({$customer->currency_code})", $subHeaderFontStyle, $centerAlignment);
    //                         }

    //                         $shareTable->addRow();
    //                         $reinsurerName = isset($sh['customer_name']) ? ucfirst($sh['customer_name']) : (isset($sh['name']) ? ucfirst($sh['name']) : '');
    //                         $shareTable->addCell(3000)->addText($reinsurerName, $normalFontStyle, $leftAlignment);
    //                         $shareTable->addCell(2000)->addText($sh[$shareKey] . '%', $normalFontStyle, $centerAlignment);
    //                         foreach ($selectedNames as $name) {
    //                             $shareTable->addCell(3000)->addText(number_format($displayValues[$name] ?? 0, 2), $normalFontStyle, $centerAlignment);
    //                         }
    //                     } else {
    //                         // Case 2: Special case for stage and stageType
    //                         $section->addText('Offered Share (%):', $subHeaderFontStyle, $leftAlignment);
    //                         $section->addText($updated_written_share_total . '%', $normalFontStyle, $leftAlignment);
    //                         $section->addText('Placed With:', $subHeaderFontStyle, $leftAlignment);

    //                         $validNames = [
    //                             'premium',
    //                             'total sum insured',
    //                             'first loss',
    //                             'top location',
    //                             'limit of indemnity',
    //                             'maximum loss limit',
    //                             'limit of liability',
    //                             'agreed value'
    //                         ];
    //                         $displayValues = [];
    //                         foreach ($scheduleDetails as $detail) {
    //                             if (!isset($detail['name']) || !isset($detail['amount']))
    //                                 continue;
    //                             $detailName = strtolower(trim($detail['name']));
    //                             if ($detailName === 'reinsurer commission rate' || !in_array($detailName, $validNames))
    //                                 continue;
    //                             $amount = floatval(str_replace(',', '', $detail['amount']));
    //                             $displayValues[$detailName] = $amount;
    //                         }
    //                         $selectedNames = array_keys($displayValues);

    //                         $shareTable = $section->addTable($tableStyle);
    //                         $shareTable->addRow();
    //                         $shareTable->addCell(3000)->addText('Reinsurer', $subHeaderFontStyle, $leftAlignment);
    //                         $shareTable->addCell(2000)->addText('Written Share (%)', $subHeaderFontStyle, $centerAlignment);
    //                         foreach ($selectedNames as $name) {
    //                             $shareTable->addCell(3000)->addText(ucfirst($name) . " ({$customer->currency_code})", $subHeaderFontStyle, $centerAlignment);
    //                         }

    //                         $totalWrittenShare = 0;
    //                         $columnTotals = array_fill_keys($selectedNames, 0);
    //                         foreach ($shares as $share) {
    //                             if (!isset($share['written_share']) || $share['written_share'] == null)
    //                                 continue;
    //                             $shareTable->addRow();
    //                             $reinsurerName = isset($share['customer_name']) ? ucfirst($share['customer_name']) : (isset($share['name']) ? ucfirst($share['name']) : '');
    //                             $shareTable->addCell(3000)->addText($reinsurerName, $normalFontStyle, $leftAlignment);
    //                             $shareTable->addCell(2000)->addText($share['written_share'] . '%', $normalFontStyle, $centerAlignment);
    //                             $writtenShare = floatval($share['written_share']);
    //                             $totalWrittenShare += $writtenShare;
    //                             foreach ($selectedNames as $name) {
    //                                 $calculatedAmount = ($displayValues[$name] * $writtenShare) / 100;
    //                                 $shareTable->addCell(3000)->addText(number_format($calculatedAmount, 2), $normalFontStyle, $centerAlignment);
    //                                 $columnTotals[$name] += $calculatedAmount;
    //                             }
    //                         }

    //                         // Add total row
    //                         $shareTable->addRow();
    //                         $shareTable->addCell(3000)->addText('Total', $subHeaderFontStyle, $leftAlignment);
    //                         $shareTable->addCell(2000)->addText(number_format($totalWrittenShare, 2) . '%', $subHeaderFontStyle, $centerAlignment);
    //                         foreach ($selectedNames as $name) {
    //                             $shareTable->addCell(3000)->addText(number_format($columnTotals[$name], 2), $subHeaderFontStyle, $centerAlignment);
    //                         }

    //                         if ($unplaced > 0) {
    //                             $section->addTextBreak(1);
    //                             $unplacedTable = $section->addTable($tableStyle);
    //                             $unplacedTable->addRow();
    //                             $unplacedTable->addCell(4000)->addText('Unplaced Share (%)', $subHeaderFontStyle, $leftAlignment);
    //                             $unplacedTable->addCell(8000)->addText($unplaced . '%', $normalFontStyle, $leftAlignment);
    //                         }
    //                     }

    //                     // Add date generated
    //                     $section->addText(
    //                         'Generated on behalf of Acentria International on ' . date('F j, Y'),
    //                         $footerFontStyle,
    //                         $centerAlignment
    //                     );

    //                     // Add page break except for the last share in non-special case
    //                     if ($index < count($shares) - 1 && !$isSpecialCase) {
    //                         $section->addPageBreak();
    //                     }
    //                 }

    //                 // Add Footer
    //                 $footer = $section->addFooter();
    //                 $footer->addText(
    //                     '© ' . date('Y') . ' Acentriagroup. All rights reserved. | Page No: ',
    //                     $footerFontStyle,
    //                     $rightAlignment
    //                 );
    //                 $footer->addPreserveText('{PAGE}', $footerFontStyle, $rightAlignment);
    //             }

    //             // Save File
    //             $fileName = 'Reinsurance_Quote.docx';
    //             $path = storage_path('app/public/' . $fileName);
    //             $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    //             $objWriter->save($path);

    //             return response()->download($path)->deleteFileAfterSend(true);
    //         } else {


    //             // dd($data);

    //             // Generate PDF for this specific customer
    //             $dompdf = Pdf::loadView($view_name, $data)
    //                 ->setPaper('a4', 'portrait')
    //                 ->setWarnings(false);

    //             // Enable necessary options
    //             $dompdf->set_option('isHtml5ParserEnabled', true);
    //             $dompdf->set_option('isPhpEnabled', true);
    //             $dompdf->set_option('isRemoteEnabled', true);

    //             $dompdf->render();

    //             // Stream the PDF to the browser for this customer
    //             return $dompdf->stream('Quotation_Cover_Slip_' . time() . '.pdf');
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 500,
    //             'message' => 'An error occurred while generating the PDF.',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
