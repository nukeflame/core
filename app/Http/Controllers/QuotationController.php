<?php

namespace App\Http\Controllers;

use App\Enums\Stage;
use App\Models\Company;
use App\Models\Customer;
use App\Services\SequenceService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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
            $cedant = $this->prepareCedantObject($requestData);

            $company = Company::firstOrFail();

            $ref = $this->sequenceService->generateDocumentNumber();
            $reference_no = $ref->doc_no;
            $stage = $requestData['stage'];
            $title = '';
            $year = Carbon::parse($formattedActivity['effective_date'])->year;
            $updated_written_share_total = 0;

            if ($stage === Stage::LEAD) {
                $title = 'FACULTATIVE PLACEMENT - ' . strtoupper($formattedActivity['class_name'] ?? 'N/A') . ' - ' . strtoupper($formattedActivity['insured_name'] ?? 'N/A');
                $updated_written_share_total = 100;
            } else {
                $title = 'QUOTATION PLACEMENT - ' . strtoupper($formattedActivity['class_name'] ?? 'N/A') . ' (' . $year . ') - ' . strtoupper($formattedActivity['insured_name'] ?? 'N/A');
                $updated_written_share_total =  100;
            }

            $data = [
                'company' => $company,
                'reinsurers' => $reinsurers,
                'cedant' => $cedant,
                'shares' => $requestData['shares'],
                'unplaced' => $requestData['unplaced'],
                'stage' => $stage,
                'title' => $title,
                'reference_no' => $reference_no,
                'opportunity' => $formattedActivity,
                'updated_written_share_total' => $updated_written_share_total,
            ];

            $view = 'printouts.fac_coverslipquote';

            logger()->debug($request->all());


            logger()->debug(json_encode($data, JSON_PRETTY_PRINT));

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
            'class_name' => $activity->class_name,
            'stageType' => $activity->category_type ?? null,
            'sum_insured' => $activity->total_sum_insured ?? $request->total_sum_insured,
            'premium' => $activity->cede_premium ?? $request->premium,
            'commission_rate' => $activity->comm_rate ?? 0
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

        $sel_rein = $request->selected_reinsurers;
        $selected_reinsurers = $sel_rein ? json_decode($sel_rein, true) : [];

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
            'selected_reinsurers' =>  $selected_reinsurers ?? []
        ];
    }

    private function prepareCustomerObjects(array $requestData)
    {
        $reinsurers = [];

        if (!empty($requestData['selected_reinsurers'])) {
            $reinsurers = $requestData['selected_reinsurers'];
        } else {
            $reinsurers = collect($requestData['shares'])->map(function ($reinsurer, $index) use ($requestData) {
                return (object) $this->createCustomerObject($requestData, $reinsurer);
            });
        }

        return collect($reinsurers)->map(function ($reinsurer, $index) use ($requestData) {
            return (object) $this->createCustomerObject($requestData, $reinsurer);
        });
    }

    private function prepareCedantObject(array $requestData)
    {
        if ($requestData['customer_id']) {
            $reinsurer['id'] = $requestData['customer_id'];

            return (object) $this->createCustomerObject($requestData, $reinsurer);
        }
    }


    private function createCustomerObject(array $requestData, $reinsurer)
    {
        $rein = Customer::where('customer_id', $reinsurer['id'])->first();

        return [
            'customer_id' => $rein->customer_id,
            'customer_name' => $rein->name,
            'address' => $rein->address,
            'city' => $rein->city,
            'country' => $rein->country_iso ? 'Kenya' : '',
            'contact_name' => '',
            'written_share' => $reinsurer['written_share'] ?? 0,
            'signed_share' => $reinsurer['signed_share'] ?? 0,
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
}
