<?php

namespace App\Http\Controllers\BdController;

use App\Jobs\SendQuoteJob;
use App\Jobs\TreatyJob;
use App\Models\Bd\CustomerContact;
use App\Models\Bd\ReinsurersDeclined;
use App\Jobs\SendHandOverApproverEmail;
use App\Models\Bd\Client;
use App\Models\Bd\Quote;
use App\Jobs\sendCrEmailToClient;
use App\Mail\Prospectwonemail;
use App\Models\Bd\Gender;
use App\Models\Bd\Occupation;
use App\Models\Bd\PipelineOpportunity;
use App\Models\Bd\Salutation;
use App\Models\Bd\Status;
use App\Models\ClassGroup;
use App\Models\Country;
use App\Models\Bd\StageComment;
use App\Models\Bd\Leads\Leads;
use App\Models\Bd\Leads\LeadsSource;
use App\Models\Bd\Leads\LeadStatus;
use App\Models\Bd\Leads\Pipeline;
use App\Models\Bd\Prospects;
use App\Models\Classes;
use App\Models\Company;
use App\Models\CoverType;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\PayMethod;
use App\Models\PremiumPayTerm;
use App\Models\QuoteScheduleHeader;
use App\Models\ReinclassPremtype;
use App\Models\ReinsClass;
use App\Models\ReinsDivision;
use App\Models\Bd\QuoteReinsurers;
use App\Models\Bd\QuoteSchedule;
use App\Models\BdScheduleData;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use App\Models\CustomerTypes;
use App\Models\Branch;
use App\Models\TreatyType;
use App\Models\BusinessType;
use App\Models\Broker;
use App\Models\HandoverApproval;
use App\Models\TypeOfSumInsured;
use App\Models\CoverRegister;
use App\Models\Bd\BdPremtype;
use App\Models\Bd\BdReinclass;
use App\Models\Bd\BdReinlayer;
use App\Models\Bd\BdReinprop;
use App\Models\Bd\Tender;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Bd\SalesReportExport;
use App\Exports\Bd\PipelineReportExport;
use App\Exports\Bd\ReinsurersDeclinedExport;
use Illuminate\Support\Str;

class PipelineController
{
    public function index(Request $request)
    {
        $string = $request->qstring;
        $request = decryptRequest($string);

        $statuses = LeadStatus::all()->orderBy('lead_id');
        $salutations = Salutation::all();
        $engage_types = LeadsSource::all();
        $industries = Occupation::all();
        $currencies = Currency::all();
        $divisions = DB::table('divisions')->get();
        $leadsources = DB::table('leadsources')->get();
        $pip_id = $request->pipeline;

        $users = User::leftJoin('user_groups', function ($query) {
            $query->on('users.user_group_id', '=', 'user_groups.id');
        })
            ->where('guard_name', 'underwriter')
            ->get();

        $pipeline = Pipeline::where('id', $request->pipeline)->first();

        $opps = Leads::where('year', $pipeline->year)->get();
        $prospect = Leads::where('code', $request->prospect)->first();

        if (!is_null($prospect)) {
            $client = Client::where('global_customer_id', $prospect->organic_reference)->first();
        } else {
            $client = null;
        }

        return view('Bd_views.intermediaries.pipelinemembers', compact(
            'users',
            'prospect',
            'divisions',
            'pip_id',
            'client',
            'engage_types',
            'currencies',
            'statuses',
            'salutations',
            'industries',
            'opps',
            'leadsources'
        ));
    }
    public function pipeline_report(Request $request)
    {
        $pipelines = Pipeline::orderBy('year', 'asc')->get();

        return view('Bd_views.intermediaries.pipeline_report', compact('pipelines'));
    }

    public function sales_report(Request $request)
    {
        $pipelines = Pipeline::orderBy('year', 'asc')->get();
        return view('Bd_views.intermediaries.sales_report', compact('pipelines'));
    }

    public function decline_report(Request $request)
    {
        $pipelines = Pipeline::orderBy('year', 'asc')->get();
        return view('Bd_views.intermediaries.decline_report', compact('pipelines'));
    }

    public function sales_report_filter(Request $request)
    {
        $category_type = [];
        $lead_status = collect();
        $classes = collect();
        $classGroups = collect();
        $customers = collect();

        $filterType = $request->input('filter_type');
        $lead_status_category = $request->input('lead_status_category');


        switch ($filterType) {
            case 'category_type':
                $category_type = ['1'];
                break;
            case 'lead_status':
                $query = LeadStatus::select('id', 'status_name')->orderBy('id', 'desc');
                if (!empty($lead_status_category)) {
                    $query->where('category_type', $lead_status_category);
                }

                $lead_status = $query->get();

                break;
            case 'class':
                $classes = Classes::where('status', 'A')->get(['class_code as id', 'class_name']);
                break;
            case 'class_group':
                $classGroups = ClassGroup::select('group_code as id', 'group_name')->get();
                break;
            case 'customer':
                $customers = DB::table('customers')
                    ->join('customer_types', function ($join) {
                        $join->on('customer_types.type_id', '=', DB::raw("ANY (SELECT json_array_elements_text(customers.customer_type)::int)"));
                    })
                    ->whereIn('customer_types.type_name', ['INSURANCE', 'REINSURANCE'])
                    ->distinct('name')
                    ->selectRaw('CAST(customers.customer_id AS INT) AS id, customers.name')
                    ->get();
                break;
        }
        if ($request->ajax()) {
            return response()->json([
                'category_type' => $category_type,
                'lead_status' => $lead_status,
                'classes' => $classes,
                'classGroups' => $classGroups,
                'customers' => $customers
            ]);
        }

        return view('Bd_views.intermediaries.sales_report', compact('category_type', 'lead_status', 'classes', 'classGroups', 'customers'));
    }

    public function decline_report_data(Request $request)
    {
        try {
            $year = $request->year;
            $isExport = $request->has('export') && $request->export == 'true';

            $opportunityIds = DB::table('pipeline_opportunities')
                ->where('pip_year', $year)
                ->pluck('opportunity_id');

            $query = DB::table('reinsurers_declined')
                ->whereIn('opportunity_id', $opportunityIds);

            if ($isExport) {
                if ($isExport) {
                    $data = $query->get();
                    $filename = 'Declined_Reinsurers_Report_' . now()->format('YmdHi') . '.xlsx';
                    return Excel::download(new ReinsurersDeclinedExport($data), $filename);
                }
            }

            return DataTables::of($query)

                ->addColumn('customer_name', function ($d) {
                    return optional(DB::table('customers')->where('customer_id', (int) $d->customer_id)->first())->name ?? 'N/A';
                })
                ->rawColumns(['edit', 'action1', 'action'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }


    public function divisionClasses(Request $request)
    {
        try {
            $division = $request->division;

            $classes = DB::table('class_of_insurance')->where('division', $division)->get();
            $res = ['status' => 1, 'classes' => $classes];
        } catch (\Throwable $e) {
            $res = ['status' => 0];
        }
        return $res;
    }

    public function stageDocuments(Request $request)
    {
        try {
            $pipeline = $request->pipeline;
            $prospect = $request->prospect;
            $division = $request->divisions;
            $stage = $request->stage;
            $category_type = $request->category_type;
            $type_of_bus = $request->type_of_business;

            $prosp_doc = DB::table('prospect_docs')
                ->where('prospect_id', $prospect)
                ->get();
            $pros = DB::table('pipeline_opportunities')
                ->where('pipeline_id', $pipeline)
                ->where('divisions', $division)
                ->where('opportunity_id', $prospect)->first();


            $engage = $pros->engage_type;


            if ($pros->stage == 2) {

                $docs = DB::table('stage_documents')
                    ->where('stage_documents.category_type', $category_type)
                    ->join('doc_types', 'stage_documents.doc_type', '=', 'doc_types.id')
                    ->where('stage', $stage)
                    ->whereJsonContains('type_of_bus', $type_of_bus)
                    // ->where('engage_type', $engage)
                    ->select('doc_types.id', 'doc_types.doc_type', 'doc_types.checkbox_doc', 'stage_documents.mandatory')  //added category_type
                    ->get();
            } else if ($pros->stage == 3) {
                $docs = DB::table('stage_documents')
                    ->where('stage_documents.category_type', $category_type)
                    ->join('doc_types', 'stage_documents.doc_type', '=', 'doc_types.id')
                    ->where('stage', $stage)
                    ->whereJsonContains('type_of_bus', $type_of_bus)
                    // ->where('category_type', $category_type)
                    // ->orWhere('stage', ($stage+1))
                    ->select('doc_types.id', 'doc_types.doc_type', 'doc_types.checkbox_doc', 'stage_documents.mandatory', 'stage_documents.stage')
                    ->get();
            } else if ($pros->stage == 4) {
                $docs = DB::table('stage_documents')
                    ->where('stage_documents.category_type', $category_type)
                    ->join('doc_types', 'stage_documents.doc_type', '=', 'doc_types.id')
                    ->where('stage', $stage)
                    ->whereJsonContains('type_of_bus', $type_of_bus)
                    // ->where('category_type', $category_type)
                    // ->orWhere('stage', ($stage+1))
                    ->select('doc_types.id', 'doc_types.doc_type', 'doc_types.checkbox_doc', 'stage_documents.mandatory', 'stage_documents.stage')
                    ->get();
            } else {
                $docs = DB::table('stage_documents')
                    ->where('stage_documents.category_type', $category_type)
                    ->join('doc_types', 'stage_documents.doc_type', '=', 'doc_types.id')
                    ->where('stage', $stage)
                    ->whereJsonContains('type_of_bus', $type_of_bus)

                    // ->where('category_type', $category_type)
                    ->select('doc_types.id', 'doc_types.doc_type', 'doc_types.checkbox_doc', 'doc_types.file_name', 'doc_types.id', 'stage_documents.mandatory')
                    ->get();
            }

            // Get the latest quote reinsurers with unique reinsurer_id
            $latestRecords = DB::table('quote_reinsurers')
                ->select('reinsurer_id', DB::raw('MAX(created_at) as latest_created_at'))
                ->where('opportunity_id', $request->prospect)
                ->where('stage', '>=', $pros->stage)
                ->groupBy('reinsurer_id');
            $quoteReinsurers = DB::table('quote_reinsurers as qr')
                ->joinSub($latestRecords, 'latest', function ($join) {
                    $join->on('qr.reinsurer_id', '=', 'latest.reinsurer_id')
                        ->on('qr.created_at', '=', 'latest.latest_created_at');
                })
                ->leftJoin('customers as c', 'qr.reinsurer_id', '=', 'c.customer_id')
                ->leftJoin('customer_contacts as cc', 'qr.reinsurer_id', '=', 'cc.customer_id')
                ->select(
                    'qr.*',
                    'c.name as reinsurer_name',
                    'c.email',
                    DB::raw("COALESCE(JSON_AGG(DISTINCT JSONB_BUILD_OBJECT('contact_name', cc.contact_name, 'main_contact_person', cc.main_contact_person)) FILTER (WHERE cc.contact_name IS NOT NULL), '[]') AS contacts")
                )
                ->groupBy('qr.id', 'c.name', 'c.email')
                ->orderBy('qr.reinsurer_id')
                ->get();
            $declined = ReinsurersDeclined::where('opportunity_id', $request->prospect)->pluck('reason', 'customer_id');


            // Decode JSONB field to an array
            foreach ($quoteReinsurers as $reinsurer) {
                $reinsurer->contacts = json_decode($reinsurer->contacts);
                $reinsurer->decline_reason = $declined[$reinsurer->reinsurer_id] ?? null;
            }
            $users = DB::table('users')->get();

            $res = ['status' => 1, 'docs' => $docs, 'prosp_doc' => $prosp_doc, 'quoteReinsurers' => $quoteReinsurers, 'users' => $users];
        } catch (\Throwable $e) {
            $res = ['status' => 0];
        }
        return $res;
    }

    public function save(Request $request)
    {

        DB::beginTransaction();

        try {
            $exists = DB::table('pipelines')->where('year', $request->year)->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Pipeline for ' . $request->year . ' already exists');
            }

            DB::table('pipelines')->insert([
                'year' => $request->year,
            ]);
            DB::commit();

            return redirect()->back()->with('success', 'Added successfully');
        } catch (Exception $e) {

            DB::rollback();

            return redirect()->back()->with('error', 'Not successfully');
        }
    }

    public function listing()
    {
        return view('Bd_views.intermediaries.pipeline');
    }

    public function get_leads()
    {
        $leads = Leads::all();
        return response()->json(['status' => 'success', 'data' => $leads]);
    }

    public function getLeadDetails($leadId)
    {
        try {
            $lead = Leads::where('code', $leadId)->first();

            return response()->json([
                'status' => 'success',
                'data' => $lead,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead not found or an error occurred.',
            ], 404);
        }
    }

    public function pipelines_get()
    {
        $data = Pipeline::all();

        return Datatables::of($data)
            ->addColumn('opp_count', function ($d) {
                $count = DB::table('pipeline_opportunities')->where('pipeline_id', $d->id)->count();
                return $count;
            })
            ->addColumn('opp_won', function ($d) {
                $count = DB::table('pipeline_opportunities')->where('pipeline_id', $d->id)->where('stage', 4)->count();
                return $count;
            })
            ->addColumn('opp_lost', function ($d) {
                $count = DB::table('pipeline_opportunities')->where('pipeline_id', $d->id)->where('stage', 5)->count();
                return $count;
            })
            ->addColumn('pipeline_worth', function ($d) {
                $income = DB::table('pipeline_opportunities')->where('pipeline_id', $d->id)->sum('income');
                return number_format($income, 2, '.', ',');
            })
            ->addColumn('action', function ($d) {
                $addUrl = route('pipelines.onboarding', ['qstring' => Crypt::encrypt('pipeline=' . $d->id)]);
                $viewUrl = route('pipeline.view', ['qstring' => Crypt::encrypt('pipeline=' . $d->id)]);
                return '<div style="display:flex"><a href="' . $addUrl . '" class="btn btn-xs btn-primary" title="Add opportunity">Add prospect</a>
                &nbsp;&nbsp;&nbsp;<a href="' . $viewUrl . '" class="btn btn-xs btn-warning" title="View">View</a></div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function returnImportExcelView()
    {
        return view('Bd_views.intermediaries.excelimport');
    }

    public function importExcel(Request $request)
    {
        if (!$request->hasFile('excel_file')) {
            return back()->with('error', 'No file uploaded');
        }

        $file = $request->file('excel_file');

        if (!$file->isValid()) {
            return back()->with('error', 'File upload failed');
        }

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();

            $rows = $worksheet->toArray();

            // Remove header
            array_shift($rows);

            DB::beginTransaction();

            foreach ($rows as $index => $row) {

                if (empty($row)) {
                    continue;
                }

                $referenceValue = $row[0] ?? null;
                if ($referenceValue === null) {
                    continue;
                }

                $referenceModel = Pipeline::where('name', $referenceValue)->first();

                PipelineOpportunity::create([
                    'foreign_key_id' => $referenceModel ? $referenceModel->id : null,
                    'column1' => $row[1] ?? null,
                    'column2' => $row[2] ?? null,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Import successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function pipeline_view(Request $request)
    {
        // try {
        //     if (is_null($request->pipeline)) {
        //         $currentyear = Carbon::now()->year;
        //         $pip_id = DB::table('pipelines')->where('year', $currentyear)->first()->id;
        //     } else {
        //         $pip_id = $request->pipeline;
        //     }

        //     $opportunities = Leads::all();
        //     $statuses = LeadStatus::all();
        //     $underwriters = DB::table('companies')->get();

        //     $q1_pipe_opps = DB::table('pipeline_opportunities')->where('pipeline_id', $pip_id)->where('fiscal_period', 1)->get();
        //     $q2_pipe_opps = DB::table('pipeline_opportunities')->where('pipeline_id', $pip_id)->where('fiscal_period', 2)->get();
        //     $q3_pipe_opps = DB::table('pipeline_opportunities')->where('pipeline_id', $pip_id)->where('fiscal_period', 3)->get();
        //     $q4_pipe_opps = DB::table('pipeline_opportunities')->where('pipeline_id', $pip_id)->where('fiscal_period', 4)->get();
        //     $pip = $pip_id;

        //     $q1_won = collect($q1_pipe_opps)->where('stage', 5)->count();
        //     $q2_won = collect($q2_pipe_opps)->where('stage', 5)->count();
        //     $q3_won = collect($q3_pipe_opps)->where('stage', 5)->count();
        //     $q4_won = collect($q4_pipe_opps)->where('stage', 5)->count();

        //     $q1_lost = collect($q1_pipe_opps)->where('stage', 6)->count();
        //     $q2_lost = collect($q2_pipe_opps)->where('stage', 6)->count();
        //     $q3_lost = collect($q3_pipe_opps)->where('stage', 6)->count();
        //     $q4_lost = collect($q4_pipe_opps)->where('stage', 6)->count();

        //     $q1_lead = collect($q1_pipe_opps)->where('stage', 1)->count();
        //     $q2_lead = collect($q2_pipe_opps)->where('stage', 1)->count();
        //     $q3_lead = collect($q3_pipe_opps)->where('stage', 1)->count();
        //     $q4_lead = collect($q4_pipe_opps)->where('stage', 1)->count();

        //     $q1_proposal = collect($q1_pipe_opps)->where('stage', 2)->count();
        //     $q2_proposal = collect($q2_pipe_opps)->where('stage', 2)->count();
        //     $q3_proposal = collect($q3_pipe_opps)->where('stage', 2)->count();
        //     $q4_proposal = collect($q4_pipe_opps)->where('stage', 2)->count();

        //     $q1_negotiation = collect($q1_pipe_opps)->where('stage', 3)->count();
        //     $q2_negotiation = collect($q2_pipe_opps)->where('stage', 3)->count();
        //     $q3_negotiation = collect($q3_pipe_opps)->where('stage', 3)->count();
        //     $q4_negotiation = collect($q4_pipe_opps)->where('stage', 3)->count();

        //     $q1_final_stage = collect($q1_pipe_opps)->where('stage', 4)->count();
        //     $q2_final_stage = collect($q2_pipe_opps)->where('stage', 4)->count();
        //     $q3_final_stage = collect($q3_pipe_opps)->where('stage', 4)->count();
        //     $q4_final_stage = collect($q4_pipe_opps)->where('stage', 4)->count();

        //     $q1_arr = [$q1_won, $q2_won, $q3_won, $q4_won];
        //     $q2_arr = [$q1_lost, $q2_lost, $q3_lost, $q4_lost];
        //     $q3_arr = [$q1_lead, $q2_lead, $q3_lead, $q4_lead];
        //     $q4_arr = [$q1_proposal, $q2_proposal, $q3_proposal, $q4_proposal];
        //     $q5_arr = [$q1_negotiation, $q2_negotiation, $q3_negotiation, $q4_negotiation];
        //     $q6_arr = [$q1_final_stage, $q2_final_stage, $q3_final_stage, $q4_final_stage];

        //     $data = [$q4_arr, $q5_arr, $q3_arr, $q1_arr, $q2_arr, $q6_arr];

        //     $customers = DB::select("
        //                     SELECT c.*,
        //                         ARRAY_AGG(DISTINCT ct.type_id) AS type_ids,
        //                         (
        //                             SELECT jsonb_agg(
        //                                 jsonb_build_object(
        //                                     'contact_name', cc.contact_name,
        //                                     'contact_email', cc.contact_email,
        //                                     'contact_mobile_no', cc.contact_mobile_no,
        //                                     'contact_position', cc.contact_position,
        //                                     'main_contact_person', cc.main_contact_person
        //                                 )
        //                             )::json
        //                             FROM customer_contacts cc
        //                             WHERE cc.customer_id = c.customer_id
        //                         ) AS contact_persons
        //                     FROM customers c
        //                     LEFT JOIN customer_types ct
        //                         ON c.customer_type::jsonb @> to_jsonb(ct.type_id::text)
        //                     WHERE ct.code IN ('REINCO', 'INSCO', 'REINBROKER')
        //                     AND EXISTS (
        //                         SELECT 1
        //                         FROM customer_contacts cc
        //                         WHERE cc.customer_id = c.customer_id
        //                     )
        //                     GROUP BY c.customer_id
        //                     ORDER BY c.name;
        //                 ");

        //     $users = DB::table('users')->where('is_staff', true)->get();
        //     $pipelines = DB::table('pipelines')->orderBy('year', 'ASC')->get();
        //     $schedule = QuoteScheduleHeader::orderBy('position', 'asc')->get();

        //     return view('Bd_views.intermediaries.pipeline_view', compact(
        //         'pipelines',
        //         'statuses',
        //         'pip',
        //         'q1_pipe_opps',
        //         'q2_pipe_opps',
        //         'q3_pipe_opps',
        //         'q4_pipe_opps',
        //         'opportunities',
        //         'data',
        //         'underwriters',
        //         'customers',
        //         'schedule',
        //         'users'
        //     ));
        // } catch (\Exception $e) {
        //     return redirect()->back()->with('error', 'An error occurred');
        // }

        try {
            $currentyear = Carbon::now()->year;
            $pipelines = DB::table('pipelines')->where('year', $currentyear);

            $pip = $request->get('pipeline', $pipelines->first()->id ?? null);
            $pipelines = $pipelines->get();
        } catch (\Exception $e) {
            logger($e);
            $pipelines = [];
            $pip = null;
        }

        return view('Bd_views.intermediaries.pipeline_view', compact('pipelines', 'pip'));
    }

    public function treaty_pipeline_view(Request $request)
    {
        try {
            if (is_null($request->pipeline)) {
                $currentyear = Carbon::now()->year;
                $pip_id = DB::table('pipelines')->where('year', $currentyear)->first()->id;
            } else {
                $pip_id = $request->pipeline;
            }

            $opportunities = Leads::all();
            $statuses = LeadStatus::all();
            $underwriters = DB::table('companies')->get();

            $q1_pipe_opps = DB::table('pipeline_opportunities')->where('pipeline_id', $pip_id)->where('fiscal_period', 1)->get();
            $q2_pipe_opps = DB::table('pipeline_opportunities')->where('pipeline_id', $pip_id)->where('fiscal_period', 2)->get();
            $q3_pipe_opps = DB::table('pipeline_opportunities')->where('pipeline_id', $pip_id)->where('fiscal_period', 3)->get();
            $q4_pipe_opps = DB::table('pipeline_opportunities')->where('pipeline_id', $pip_id)->where('fiscal_period', 4)->get();
            $pip = $pip_id;

            $q1_won = collect($q1_pipe_opps)->where('stage', 5)->count();
            $q2_won = collect($q2_pipe_opps)->where('stage', 5)->count();
            $q3_won = collect($q3_pipe_opps)->where('stage', 5)->count();
            $q4_won = collect($q4_pipe_opps)->where('stage', 5)->count();

            $q1_lost = collect($q1_pipe_opps)->where('stage', 6)->count();
            $q2_lost = collect($q2_pipe_opps)->where('stage', 6)->count();
            $q3_lost = collect($q3_pipe_opps)->where('stage', 6)->count();
            $q4_lost = collect($q4_pipe_opps)->where('stage', 6)->count();

            $q1_lead = collect($q1_pipe_opps)->where('stage', 1)->count();
            $q2_lead = collect($q2_pipe_opps)->where('stage', 1)->count();
            $q3_lead = collect($q3_pipe_opps)->where('stage', 1)->count();
            $q4_lead = collect($q4_pipe_opps)->where('stage', 1)->count();

            $q1_proposal = collect($q1_pipe_opps)->where('stage', 2)->count();
            $q2_proposal = collect($q2_pipe_opps)->where('stage', 2)->count();
            $q3_proposal = collect($q3_pipe_opps)->where('stage', 2)->count();
            $q4_proposal = collect($q4_pipe_opps)->where('stage', 2)->count();

            $q1_negotiation = collect($q1_pipe_opps)->where('stage', 3)->count();
            $q2_negotiation = collect($q2_pipe_opps)->where('stage', 3)->count();
            $q3_negotiation = collect($q3_pipe_opps)->where('stage', 3)->count();
            $q4_negotiation = collect($q4_pipe_opps)->where('stage', 3)->count();

            $q1_final_stage = collect($q1_pipe_opps)->where('stage', 4)->count();
            $q2_final_stage = collect($q2_pipe_opps)->where('stage', 4)->count();
            $q3_final_stage = collect($q3_pipe_opps)->where('stage', 4)->count();
            $q4_final_stage = collect($q4_pipe_opps)->where('stage', 4)->count();

            $q1_arr = [$q1_won, $q2_won, $q3_won, $q4_won];
            $q2_arr = [$q1_lost, $q2_lost, $q3_lost, $q4_lost];
            $q3_arr = [$q1_lead, $q2_lead, $q3_lead, $q4_lead];
            $q4_arr = [$q1_proposal, $q2_proposal, $q3_proposal, $q4_proposal];
            $q5_arr = [$q1_negotiation, $q2_negotiation, $q3_negotiation, $q4_negotiation];
            $q6_arr = [$q1_final_stage, $q2_final_stage, $q3_final_stage, $q4_final_stage];

            $data = [$q4_arr, $q5_arr, $q3_arr, $q1_arr, $q2_arr, $q6_arr];

            $customers = DB::select("
                SELECT c.*,
                    ARRAY_AGG(DISTINCT ct.type_id) AS type_ids,
                    (
                        SELECT jsonb_agg(
                            jsonb_build_object(
                                'contact_name', cc.contact_name,
                                'contact_email', cc.contact_email,
                                'contact_mobile_no', cc.contact_mobile_no,
                                'contact_position', cc.contact_position,
                                'main_contact_person', cc.main_contact_person
                            )
                        )::json
                        FROM customer_contacts cc
                        WHERE cc.customer_id = c.customer_id
                    ) AS contact_persons
                FROM customers c
                LEFT JOIN customer_types ct
                    ON c.customer_type::jsonb @> to_jsonb(ct.type_id::text)
                WHERE ct.code IN ('REINCO', 'INSCO', 'REINBROKER')
                AND EXISTS (
                    SELECT 1
                    FROM customer_contacts cc
                    WHERE cc.customer_id = c.customer_id
                )
                GROUP BY c.customer_id
                ORDER BY c.name;
            ");

            $types_of_bus = BusinessType::get(['bus_type_id', 'bus_type_name']);
            $branches = Branch::where('status', 'A')->get(['branch_code', 'branch_name', 'status']);
            $brokers = Broker::where('status', 'A')->get(['broker_code', 'broker_name', 'status']);
            $classes = Classes::where('status', 'A')->get(['class_code', 'class_name', 'status']);
            $types_of_sum_insured = TypeOfSumInsured::where('status', 'A')->get(['sum_insured_code', 'sum_insured_name', 'status']);
            $classGroups = ClassGroup::get(['group_code', 'group_name']);
            $paymethods = PayMethod::all();
            $premium_pay_terms = PremiumPayTerm::all();
            $treatytypes = TreatyType::where('status', 'A')->get();
            $covertypes = CoverType::all();
            $reinsdivisions = ReinsDivision::where('status', 'A')->get();
            $reinsclasses = ReinsClass::where('status', 'A')->get();
            // $treatytypes = TreatyType::where('status', 'A')->get();
            $reinPremTypes = ReinclassPremtype::where('status', 'A')->get();
            $pipeYear = Pipeline::orderBy('year', 'asc')->get();
            $contactNames = json_decode($Contact_details->contact_name ?? '[]', true);
            $currencies = Currency::all();
            $prospProperties = DB::table('pipeline_opportunities')->where('opportunity_id', "=", $request->prospect)->first();

            $customers = DB::table('customers')
                ->join('customer_types', function ($join) {
                    $join->on('customer_types.type_id', '=', DB::raw("ANY (SELECT json_array_elements_text(customers.customer_type)::int)"));
                })
                ->select(
                    DB::raw('CAST(customers.customer_id AS INT) as customer_id'),
                    'customers.name'
                )
                ->whereIn('customer_types.type_name', ['INSURANCE', 'REINSURANCE'])
                ->distinct('name')
                ->get();


            $reins_divisions = ReinsDivision::where('status', 'A')->get();
            $trans_type = 'NEW';
            $type_of_bus = $request->type_of_bus;
            // dd($trans_type);
            $coverreinpropClasses = null;
            $coverreinprops = null;
            $coverReinLayers = null;
            $premtypes = null;
            $renewal_date = null;
            $old_endt_trans = null;

            if ($trans_type != 'NEW') {
                $cover_no = $request->cover_no;
                $endorsement_no = $request->endorsement_no;
                $old_endt_trans = CoverRegister::where('endorsement_no', $endorsement_no)->first();



                $coverreinpropClasses = BdReinclass::where('opportunity_id', $request->prospect)
                    ->get();
                $coverreinprops = BdReinprop::where('opportunity_id', $request->prospect)
                    ->get();
                $coverReinLayers = BdReinlayer::query()
                    ->leftJoin('reinsclasses', 'bd_reinlayers.reinclass', '=', 'reinsclasses.class_code')
                    ->where('bd_reinlayers.opportunity_id', $request->prospect)
                    ->select('bd_reinlayers.*', 'reinsclasses.class_name') // Customize as needed
                    ->get();

                $premtypes = BdPremtype::where('opportunity_id', $request->prospect)->get();
                $renewal_date = Carbon::now()->format('Y-m-d');
            }


            $users = DB::table('users')->where('is_staff', true)->get();
            $pipelines = DB::table('pipelines')->orderBy('year', 'ASC')->get();
            $schedule = QuoteScheduleHeader::where('business_type', 'TRT')->orderBy('position', 'asc')->get();
            $schedule = QuoteScheduleHeader::where('business_type', 'TRT')->orderBy('position', 'asc')->get();
            $treaty_operation_checklists = DB::table('treaty_operation_checklists')->get();



            return view('Bd_views.intermediaries.treaty_pipeline_view', compact(
                'pipelines',
                'statuses',
                'pip',
                'q1_pipe_opps',
                'q2_pipe_opps',
                'q3_pipe_opps',
                'q4_pipe_opps',
                'opportunities',
                'data',
                'underwriters',
                'customers',
                'schedule',
                'users',
                'treaty_operation_checklists',
                'treatytypes',
                'types_of_bus',
                'coverreinpropClasses',
                'coverreinprops',
                'premtypes',
                'reinPremTypes',
                'coverReinLayers',
                'renewal_date',
                'trans_type',
                'currencies',
                'reinsclasses',
                'old_endt_trans',
                'prospProperties'



            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred');
        }
    }

    public function bus_type(Request $request)
    {
        $opportunity_id = $request->opportunity_id;

        // Fetch the type of business from the database
        $businessType = PipelineOpportunity::where('opportunity_id', $opportunity_id)->get('type_of_bus');

        if ($businessType) {
            return response()->json(['type_of_bus' => $businessType]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Business type not found'], 404);
        }
    }


    public function leads_edit(Request $request)
    {
        $string = $request->qstring;
        $request = decryptRequest($string);

        $lead = Leads::where('code', $request->lead)->firstOrFail();

        $statuses = LeadStatus::all();
        $salutations = Salutation::all();

        return view('Bd_views.intermediaries.leads_edit', compact('lead', 'statuses', 'salutations'));
    }

    public function edit_lead(Request $request, $code)
    {

        // Validate the form data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'second_name' => 'required',
            'salutation' => 'required',
            'source' => 'required',
            'industry' => 'required',
            'rating' => 'required',
            'status' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'lead_owner' => 'required',
            // Add other validation rules for your other form fields
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();

            return response()->json(['validation_errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {

            // Update the lead record in the database using the 'update' function
            Leads::where('code', $code)->update([
                'first_name' => $request->first_name,
                'second_name' => $request->second_name,
                'full_name' => $request->first_name . ' ' . $request->second_name,
                'salutation' => $request->salutation,
                'source' => $request->source,
                'industry' => $request->industry,
                'rating' => $request->rating,
                'status' => $request->status,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'lead_owner' => $request->lead_owner,
            ]);

            DB::commit();

            return ['status' => 200];
        } catch (\Throwable $e) {

            DB::rollback();

            return ['status' => 400];
        }
    }

    public function pipeline_create_opportunity(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'division' => 'required',
            'prod_cost' => 'nullable|numeric',
            //'cost_currency' => 'required|string',
            'premium' => 'nullable|numeric',
            'postal_address' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'insurance_class' => 'nullable|string',
            'engage_type' => 'nullable|string',
            // 'closing_date' => '',
            // 'effective_date' => '',
            'lead_name' => 'required|string',
            'rating' => 'nullable|string',
            'client_type' => 'required|string',
            'client_category' => 'required',
            'lead_year' => 'required',
            'industry' => 'required',
            'contact_name.*' => 'required|string',
            'email.*' => 'required|email',
            'phone_number.*' => 'required|numeric',
            'full_name' => 'nullable|string',
            'contact_position' => 'nullable|string',
            'country_code' => 'required',
            'town' => 'nullable|string',
            'telephone' => 'nullable',
            'alternative_contact_name' => 'nullable|string',
            'alternative_phone_number' => 'nullable|string',
            'alternative_contact_position' => 'nullable|string',
            'alternative_email' => 'nullable|string',
            'type_of_bus' => 'required',
            'customer_id' => 'nullable|string',
            'branchcode' => 'nullable|string',
            'broker_flag' => 'nullable|string',
            'brokercode' => 'nullable|string',
            'pay_method' => 'nullable|string',
            'no_of_installments' => 'nullable|integer',
            'currency_code' => 'required',
            'today_currency' => 'required',
            'premium_payment_term' => 'nullable|string',
            'class_group' => 'required',
            'classcode' => 'required',
            'insured_name' => 'required',
            'fac_date_offered' => 'required',
            'sum_insured_type' => 'required',
            'total_sum_insured' => 'required',
            'apply_eml' => 'required',
            'eml_rate' => 'nullable|numeric',
            // 'eml_amt' => 'nullable|numeric',
            'effective_sum_insured' => 'required',
            'risk_details' => 'required',
            'cede_premium' => 'required',
            'rein_premium' => 'required',
            'fac_share_offered' => 'required',
            'comm_rate' => 'required|numeric',
            'comm_amt' => 'nullable',
            'reins_comm_type' => 'required',
            'reins_comm_rate' => 'nullable',
            'reins_comm_amt' => 'nullable',
            'brokerage_comm_type' => 'nullable|string',
            'brokerage_comm_amt' => 'nullable',
            'brokerage_comm_rate' => 'nullable',
            'vat_charged' => 'nullable|numeric',
            //  'limit_per_reinclass' => 'nullable|numeric',
            'layer_no' => 'nullable|array',
            'nonprop_reinclass' => 'nullable|array',
            'nonprop_reinclass_desc' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => 'Validation failed', 'errors' => $validator->errors()]);
        }
        $mes = '';

        if (is_null($request->prospect)) {
            $mes = "Prospect created successfully";
        } else {
            $mes = "Prospect updated successfully";
        }
        $nextCode = Prospects::generateNextCode();
        $date = $request->effective_date;
        $carbonDate = Carbon::parse($date);
        $monthNumber = $carbonDate->month;
        $pq_status = 'W';

        if ($request->prequalification === 'Y' && $request->updateState != 'U') {
            $pq_status = 'P';
        } else if ($request->updateState === 'U') {
            $pq_status = $request->pq_status;
        }

        $quarter = null;

        if ($monthNumber < 4) {
            $quarter = 1;
        } else if ($monthNumber >= 4 && $monthNumber < 7) {
            $quarter = 2;
        } else if ($monthNumber >= 7 && $monthNumber < 10) {
            $quarter = 3;
        } else {
            $quarter = 4;
        }

        DB::beginTransaction();

        try {
            if ($request->updateState === 'U') {
                $activity = Prospects::where('opportunity_id', $request->prospect)
                    ->update(
                        [
                            'opportunity_id' => $request->prospect,
                            'stage' => 0,
                            'divisions' => $request->division,
                            // 'currency' => $request->currency,
                            'production_cost' => $request->prod_cost ? str_replace(',', '', $request->prod_cost) : null,
                            'prod_currency' => $request->cost_currency,
                            'premium' => $request->premium ? str_replace(',', '', $request->premium) : null,
                            'postal_address' => $request->postal_address,
                            'postal_code' => $request->postal_code,
                            'insurance_class' => $request->insurance_class,
                            'engage_type' => $request->engage_type,
                            'closing_date' => !empty($request->closing_date) ? $request->closing_date : 'TBA',
                            'effective_date' => !empty($request->effective_date) ? $request->effective_date : 'TBA',
                            'fiscal_period' => $quarter,
                            'lead_name' => $request->lead_name,
                            // 'lead_handler' => $request->lead_handler,
                            // 'lead_source' => $request->lead_source,
                            // 'source_desc' => $request->source_desc,
                            // 'physical_address' => $request->physical_address,
                            'rating' => $request->rating,
                            'contact_name' => json_encode($request->contact_name),
                            'email' => json_encode($request->email),
                            // 'prequalification' => $request->prequalification,
                            'client_type' => $request->client_type,
                            'client_category' => $request->client_category,
                            'pip_year' => $request->lead_year,
                            'industry' => $request->industry,
                            'phone' => json_encode($request->phone_number),
                            'fullname' => $request->full_name,
                            'contact_position' => $request->contact_position,
                            'country_code' => $request->country_code,
                            'town' => $request->town,
                            // 'pq_status' => $pq_status,
                            'telephone' => json_encode($request->telephone),
                            'alternate_contact' => $request->alternative_contact_name,
                            'alternate_phone' => $request->alternative_phone_number,
                            'alternate_position' => $request->alternative_contact_position,
                            'alternate_email' => $request->alternative_email,
                            'type_of_bus' => $request->type_of_bus,
                            'customer_id' => $request->customer_id,
                            'branchcode' => $request->branchcode,
                            'broker_flag' => $request->broker_flag,
                            'brokercode' => $request->brokercode,
                            'pay_method' => $request->pay_method,
                            'no_of_installments' => $request->no_of_installments,
                            'currency_code' => $request->currency_code,
                            'today_currency' => $request->today_currency,
                            'premium_payment_term' => $request->premium_payment_term,
                            'class_group' => $request->class_group,
                            'classcode' => $request->classcode,
                            'insured_name' => $request->insured_name,
                            'fac_date_offered' => $request->fac_date_offered,
                            'sum_insured_type' => $request->sum_insured_type,
                            'total_sum_insured' => $request->total_sum_insured ? str_replace(',', '', $request->total_sum_insured) : null,
                            'apply_eml' => $request->apply_eml,
                            'eml_rate' => $request->eml_rate ? str_replace(',', '', $request->eml_rate) : null,
                            'eml_amt' => $request->eml_amt ? str_replace(',', '', $request->eml_amt) : null,
                            'effective_sum_insured' => $request->effective_sum_insured ? str_replace(',', '', $request->effective_sum_insured) : null,
                            'risk_details' => $request->risk_details,
                            'cede_premium' => $request->cede_premium ? str_replace(',', '', $request->cede_premium) : null,
                            'rein_premium' => $request->rein_premium ? str_replace(',', '', $request->rein_premium) : null,
                            'fac_share_offered' => $request->fac_share_offered ? str_replace(',', '', $request->fac_share_offered) : null,
                            'comm_rate' => $request->comm_rate ? str_replace(',', '', $request->comm_rate) : null,
                            'reins_comm_rate' => $request->reins_comm_rate,
                            'comm_amt' => $request->comm_amt ? str_replace(',', '', $request->comm_amt) : null,
                            'reins_comm_type' => $request->reins_comm_type,
                            'reins_comm_amt' => $request->reins_comm_amt ? str_replace(',', '', $request->reins_comm_amt) : null,
                            'brokerage_comm_type' => $request->brokerage_comm_type,
                            'brokerage_comm_amt' => $request->brokerage_comm_amt ? str_replace(',', '', $request->brokerage_comm_amt) : null,
                            'brokerage_comm_rate' => $request->brokerage_comm_rate ? str_replace(',', '', $request->brokerage_comm_rate) : null,
                            'vat_charged' => $request->vat_charged ? str_replace(',', '', $request->vat_charged) : null,
                            'limit_per_reinclass' => $request->limit_per_reinclass ? str_replace(',', '', $request->limit_per_reinclass) : null,
                            'layer_no' => $request->layer_no ? json_encode($request->layer_no) : null,
                            'nonprop_reinclass' => $request->nonprop_reinclass ? json_encode($request->nonprop_reinclass) : null,
                            'nonprop_reinclass_desc' => $request->nonprop_reinclass_desc ? json_encode($request->nonprop_reinclass_desc) : null,
                            // 'indemnity_treaty_limit' => $request->indemnity_treaty_limit ? json_encode($request->indemnity_treaty_limit) : null,
                            // 'underlying_limit' => $request->underlying_limit ? json_encode($request->underlying_limit) : null,
                            // 'pq_comments' => '', // Restored the commented line

                        ]
                    );
            } else {
                $activity = Prospects::create(
                    [
                        'opportunity_id' => $nextCode,
                        'stage' => 0,
                        'divisions' => $request->division,
                        // 'currency' => $request->currency,
                        'production_cost' => $request->prod_cost ? str_replace(',', '', $request->prod_cost) : null,
                        'prod_currency' => $request->cost_currency,
                        'premium' => $request->premium ? str_replace(',', '', $request->premium) : null,
                        'postal_address' => $request->postal_address,
                        'postal_code' => $request->postal_code,
                        'insurance_class' => $request->insurance_class,
                        'engage_type' => $request->engage_type,
                        'closing_date' => !empty($request->closing_date) ? $request->closing_date : 'TBA',
                        'effective_date' => !empty($request->effective_date) ? $request->effective_date : 'TBA',

                        'fiscal_period' => $quarter,
                        'lead_name' => $request->lead_name,
                        'lead_owner' => $request->lead_owner,
                        // 'lead_handler' => $request->lead_handler,
                        // 'lead_source' => $request->lead_source,
                        // 'source_desc' => $request->source_desc,
                        // 'physical_address' => $request->physical_address,
                        'rating' => $request->rating,
                        'contact_name' => json_encode($request->contact_name),
                        'email' => json_encode($request->email),
                        // 'prequalification' => $request->prequalification,
                        'client_type' => $request->client_type,
                        'client_category' => $request->client_category,
                        'pip_year' => $request->lead_year,
                        'industry' => $request->industry,
                        'phone' => json_encode($request->phone_number),
                        'fullname' => $request->full_name,
                        'contact_position' => $request->contact_position,
                        'country_code' => $request->country_code,
                        'town' => $request->town,
                        // 'pq_status' => $pq_status,
                        'telephone' => json_encode($request->telephone),
                        'alternate_contact' => $request->alternative_contact_name,
                        'alternate_phone' => $request->alternative_phone_number,
                        'alternate_position' => $request->alternative_contact_position,
                        'alternate_email' => $request->alternative_email,
                        'type_of_bus' => $request->type_of_bus,
                        'customer_id' => $request->customer_id,
                        'branchcode' => $request->branchcode,
                        'broker_flag' => $request->broker_flag,
                        'brokercode' => $request->brokercode,
                        'pay_method' => $request->pay_method,
                        'no_of_installments' => $request->no_of_installments,
                        'currency_code' => $request->currency_code,
                        'today_currency' => $request->today_currency,
                        'premium_payment_term' => $request->premium_payment_term,
                        'class_group' => $request->class_group,
                        'classcode' => $request->classcode,
                        'insured_name' => $request->insured_name,
                        'fac_date_offered' => $request->fac_date_offered,
                        'sum_insured_type' => $request->sum_insured_type,
                        'total_sum_insured' => $request->total_sum_insured ? str_replace(',', '', $request->total_sum_insured) : null,
                        'apply_eml' => $request->apply_eml,
                        'eml_rate' => $request->eml_rate ? str_replace(',', '', $request->eml_rate) : null,
                        'eml_amt' => $request->eml_amt ? str_replace(',', '', $request->eml_amt) : null,
                        'effective_sum_insured' => $request->effective_sum_insured ? str_replace(',', '', $request->effective_sum_insured) : null,
                        'risk_details' => $request->risk_details,
                        'reins_comm_rate' => $request->reins_comm_rate,
                        'cede_premium' => $request->cede_premium ? str_replace(',', '', $request->cede_premium) : null,
                        'rein_premium' => $request->rein_premium ? str_replace(',', '', $request->rein_premium) : null,
                        'fac_share_offered' => $request->fac_share_offered ? str_replace(',', '', $request->fac_share_offered) : null,
                        'comm_rate' => $request->comm_rate ? str_replace(',', '', $request->comm_rate) : null,
                        'comm_amt' => $request->comm_amt ? str_replace(',', '', $request->comm_amt) : null,
                        'reins_comm_type' => $request->reins_comm_type,
                        'reins_comm_amt' => $request->reins_comm_amt ? str_replace(',', '', $request->reins_comm_amt) : null,
                        'brokerage_comm_type' => $request->brokerage_comm_type,
                        'brokerage_comm_amt' => $request->brokerage_comm_amt ? str_replace(',', '', $request->brokerage_comm_amt) : null,
                        'brokerage_comm_rate' => $request->brokerage_comm_rate ? str_replace(',', '', $request->brokerage_comm_rate) : null,
                        'vat_charged' => $request->vat_charged ? str_replace(',', '', $request->vat_charged) : null,
                        'limit_per_reinclass' => $request->limit_per_reinclass ? str_replace(',', '', $request->limit_per_reinclass) : null,
                        'layer_no' => $request->layer_no ? json_encode($request->layer_no) : null,
                        'nonprop_reinclass' => $request->nonprop_reinclass ? json_encode($request->nonprop_reinclass) : null,
                        'nonprop_reinclass_desc' => $request->nonprop_reinclass_desc ? json_encode($request->nonprop_reinclass_desc) : null,
                        // 'indemnity_treaty_limit' => $request->indemnity_treaty_limit ? json_encode($request->indemnity_treaty_limit) : null,
                        // 'underlying_limit' => $request->underlying_limit ? json_encode($request->underlying_limit) : null,
                        // 'pq_comments' => '', // Restored the commented line



                    ]
                );
            }

            DB::commit();

            return ['status' => 1, 'message' => $mes];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 0, 'message' => 'An error occurred.'];
        }
    }

    public function treaty_pipeline_create_opportunity(Request $request)
    {
        $mes = '';

        if (is_null($request->prospect)) {
            $mes = "Prospect created successfully";
        } else {
            $mes = "Prospect updated successfully";
        }
        $nextCode = Prospects::generateNextCode();
        $date = $request->effective_date;
        $carbonDate = Carbon::parse($date);
        $monthNumber = $carbonDate->month;
        $pq_status = 'W';

        if ($request->prequalification === 'Y' && $request->updateState != 'U') {
            $pq_status = 'P';
        } else if ($request->updateState === 'U') {
            $pq_status = $request->pq_status;
        }

        $quarter = null;

        if ($monthNumber < 4) {
            $quarter = 1;
        } else if ($monthNumber >= 4 && $monthNumber < 7) {
            $quarter = 2;
        } else if ($monthNumber >= 7 && $monthNumber < 10) {
            $quarter = 3;
        } else {
            $quarter = 4;
        }

        DB::beginTransaction();

        try {
            if ($request->updateState === 'U') {
                $insertedOppId = $request->prospect;
                $activity = Prospects::where('opportunity_id', $request->prospect)
                    ->update(
                        [
                            'stage' => 0,
                            'divisions' => $request->division,
                            // 'currency' => $request->currency,
                            'production_cost' => $request->prod_cost ? str_replace(',', '', $request->prod_cost) : null,
                            'prod_currency' => $request->cost_currency,
                            'premium' => $request->premium ? str_replace(',', '', $request->premium) : null,
                            'postal_address' => $request->postal_address,
                            'postal_code' => $request->postal_code,
                            'insurance_class' => $request->insurance_class,
                            'engage_type' => $request->engage_type,
                            'closing_date' => !empty($request->closing_date) ? $request->closing_date : 'TBA',
                            'effective_date' => !empty($request->effective_date) ? $request->effective_date : 'TBA',
                            'fiscal_period' => $quarter,
                            'lead_name' => $request->lead_name,
                            // 'lead_handler' => $request->lead_handler,
                            // 'lead_source' => $request->lead_source,
                            // 'source_desc' => $request->source_desc,
                            // 'physical_address' => $request->physical_address,
                            'rating' => $request->rating,
                            'contact_name' => json_encode($request->contact_name),
                            'email' => json_encode($request->email),
                            // 'prequalification' => $request->prequalification,
                            'client_type' => $request->client_type,
                            'client_category' => $request->client_category,
                            'pip_year' => $request->lead_year,
                            'industry' => $request->industry,
                            'phone' => json_encode($request->phone_number),
                            'fullname' => $request->full_name,
                            'contact_position' => $request->contact_position,
                            'country_code' => $request->country_code,
                            'town' => $request->town,
                            // 'pq_status' => $pq_status,
                            'telephone' => json_encode($request->telephone),
                            'alternate_contact' => $request->alternative_contact_name,
                            'alternate_phone' => $request->alternative_phone_number,
                            'alternate_position' => $request->alternative_contact_position,
                            'alternate_email' => $request->alternative_email,
                            'type_of_bus' => $request->type_of_bus,
                            'customer_id' => $request->customer_id,
                            'branchcode' => $request->branchcode,
                            'broker_flag' => $request->broker_flag,
                            'brokercode' => $request->brokercode,
                            'pay_method' => $request->pay_method,
                            'no_of_installments' => $request->no_of_installments,
                            'currency_code' => $request->currency_code,
                            'today_currency' => $request->today_currency,
                            'premium_payment_term' => $request->premium_payment_term,
                            'class_group' => $request->class_group,
                            'classcode' => $request->classcode,
                            'insured_name' => $request->insured_name,
                            'tr_date_offered' => $request->date_offered,
                            'sum_insured_type' => $request->sum_insured_type,
                            'total_sum_insured' => $request->total_sum_insured ? str_replace(',', '', $request->total_sum_insured) : null,
                            'apply_eml' => $request->apply_eml,
                            'eml_rate' => $request->eml_rate ? str_replace(',', '', $request->eml_rate) : null,
                            'eml_amt' => $request->eml_amt ? str_replace(',', '', $request->eml_amt) : null,
                            'effective_sum_insured' => $request->effective_sum_insured ? str_replace(',', '', $request->effective_sum_insured) : null,
                            'risk_details' => $request->risk_details,
                            'cede_premium' => $request->cede_premium ? str_replace(',', '', $request->cede_premium) : null,
                            'rein_premium' => $request->rein_premium ? str_replace(',', '', $request->rein_premium) : null,
                            'expected_closure_date' => $request->expected_closure_date,
                            'fac_share_offered' => $request->share_offered ? str_replace(',', '', $request->share_offered) : null,
                            'comm_rate' => $request->comm_rate ? str_replace(',', '', $request->comm_rate) : null,
                            'reins_comm_rate' => $request->reins_comm_rate ?? 0,
                            'comm_amt' => $request->comm_amt ? str_replace(',', '', $request->comm_amt) : null,
                            'reins_comm_type' => $request->reins_comm_type,
                            'reins_comm_amt' => $request->reins_comm_amt ? str_replace(',', '', $request->reins_comm_amt) : null,
                            'brokerage_comm_type' => $request->brokerage_comm_type,
                            'brokerage_comm_amt' => $request->brokerage_comm_amt ? str_replace(',', '', $request->brokerage_comm_amt) : null,
                            'brokerage_comm_rate' => $request->brokerage_comm_rate ? str_replace(',', '', $request->brokerage_comm_rate) : null,
                            'vat_charged' => $request->vat_charged ? str_replace(',', '', $request->vat_charged) : null,
                            'limit_per_reinclass' => $request->limit_per_reinclass ? str_replace(',', '', $request->limit_per_reinclass) : null,
                            'layer_no' => $request->layer_no ? json_encode($request->layer_no) : null,
                            'nonprop_reinclass' => $request->nonprop_reinclass ? json_encode($request->nonprop_reinclass) : null,
                            'nonprop_reinclass_desc' => $request->nonprop_reinclass_desc ? json_encode($request->nonprop_reinclass_desc) : null,
                            'indemnity_treaty_limit' => $request->indemnity_treaty_limit ? json_encode($request->indemnity_treaty_limit) : null,
                            'underlying_limit' => $request->underlying_limit ? json_encode($request->underlying_limit) : null,
                            'ri_tax_rate' => $request->ri_tax_rate,
                            'prem_tax_rate' => $request->prem_tax_rate,
                            'treaty_code' => $request->treatytype,
                            'updated_at' => now(),
                            // 'pq_comments' => '',

                        ]
                    );
            } else {
                $user = Auth::user()->user_name;
                $insertedOppId = DB::table('pipeline_opportunities')->insertGetId(
                    [
                        'opportunity_id' => $nextCode,
                        'stage' => 0,
                        'divisions' => $request->division,
                        // 'currency' => $request->currency,
                        'production_cost' => $request->prod_cost ? str_replace(',', '', $request->prod_cost) : null,
                        'prod_currency' => $request->cost_currency,
                        'premium' => $request->premium ? str_replace(',', '', $request->premium) : null,
                        'postal_address' => $request->postal_address,
                        'postal_code' => $request->postal_code,
                        'insurance_class' => $request->insurance_class,
                        'engage_type' => $request->engage_type,
                        'closing_date' => !empty($request->closing_date) ? $request->closing_date : 'TBA',
                        'effective_date' => !empty($request->effective_date) ? $request->effective_date : 'TBA',

                        'fiscal_period' => $quarter,
                        'lead_name' => $request->lead_name,
                        'lead_owner' => $request->lead_owner,
                        // 'lead_handler' => $request->lead_handler,
                        // 'lead_source' => $request->lead_source,
                        // 'source_desc' => $request->source_desc,
                        // 'physical_address' => $request->physical_address,
                        'rating' => $request->rating,
                        'contact_name' => json_encode($request->contact_name),
                        'email' => json_encode($request->email),
                        // 'prequalification' => $request->prequalification,
                        'client_type' => $request->client_type,
                        'client_category' => $request->client_category,
                        'pip_year' => $request->lead_year,
                        'industry' => $request->industry,
                        'phone' => json_encode($request->phone_number),
                        'fullname' => $request->full_name,
                        'contact_position' => $request->contact_position,
                        'country_code' => $request->country_code,
                        'town' => $request->town,
                        // 'pq_status' => $pq_status,
                        'telephone' => json_encode($request->telephone),
                        'alternate_contact' => $request->alternative_contact_name,
                        'alternate_phone' => $request->alternative_phone_number,
                        'alternate_position' => $request->alternative_contact_position,
                        'alternate_email' => $request->alternative_email,
                        'type_of_bus' => $request->type_of_bus,
                        'customer_id' => $request->customer_id,
                        'branchcode' => $request->branchcode,
                        'broker_flag' => $request->broker_flag,
                        'brokercode' => $request->brokercode,
                        'pay_method' => $request->pay_method,
                        'no_of_installments' => $request->no_of_installments,
                        'currency_code' => $request->currency_code,
                        'today_currency' => $request->today_currency,
                        'premium_payment_term' => $request->premium_payment_term,
                        'class_group' => $request->class_group,
                        'classcode' => $request->classcode,
                        'insured_name' => $request->insured_name,
                        'tr_date_offered' => $request->date_offered,
                        'sum_insured_type' => $request->sum_insured_type,
                        'total_sum_insured' => $request->total_sum_insured ? str_replace(',', '', $request->total_sum_insured) : null,
                        'apply_eml' => $request->apply_eml,
                        'eml_rate' => $request->eml_rate ? str_replace(',', '', $request->eml_rate) : null,
                        'eml_amt' => $request->eml_amt ? str_replace(',', '', $request->eml_amt) : null,
                        'effective_sum_insured' => $request->effective_sum_insured ? str_replace(',', '', $request->effective_sum_insured) : null,
                        'risk_details' => $request->risk_details,
                        'reins_comm_rate' => $request->reins_comm_rate ?? 0,
                        'cede_premium' => $request->cede_premium ? str_replace(',', '', $request->cede_premium) : null,
                        'rein_premium' => $request->rein_premium ? str_replace(',', '', $request->rein_premium) : null,
                        'expected_closure_date' => $request->expected_closure_date,
                        'fac_share_offered' => $request->share_offered ? str_replace(',', '', $request->share_offered) : null,
                        'comm_rate' => $request->comm_rate ? str_replace(',', '', $request->comm_rate) : null,
                        'comm_amt' => $request->comm_amt ? str_replace(',', '', $request->comm_amt) : null,
                        'reins_comm_type' => $request->reins_comm_type,
                        'reins_comm_amt' => $request->reins_comm_amt ? str_replace(',', '', $request->reins_comm_amt) : null,
                        'brokerage_comm_type' => $request->brokerage_comm_type,
                        'brokerage_comm_amt' => $request->brokerage_comm_amt ? str_replace(',', '', $request->brokerage_comm_amt) : null,
                        'brokerage_comm_rate' => $request->brokerage_comm_rate ? str_replace(',', '', $request->brokerage_comm_rate) : null,
                        'vat_charged' => $request->vat_charged ? str_replace(',', '', $request->vat_charged) : null,
                        'limit_per_reinclass' => $request->limit_per_reinclass ? json_encode($request->limit_per_reinclass) : null,
                        'layer_no' => $request->layer_no ? json_encode($request->layer_no) : null,
                        'nonprop_reinclass' => $request->nonprop_reinclass ? json_encode($request->nonprop_reinclass) : null,
                        'nonprop_reinclass_desc' => $request->nonprop_reinclass_desc ? json_encode($request->nonprop_reinclass_desc) : null,
                        'indemnity_treaty_limit' => $request->indemnity_treaty_limit ? json_encode($request->indemnity_treaty_limit) : null,
                        'underlying_limit' => $request->underlying_limit ? json_encode($request->underlying_limit) : null,
                        'ri_tax_rate' => $request->ri_tax_rate,
                        'prem_tax_rate' => $request->prem_tax_rate,
                        'treaty_code' => $request->treatytype,
                        // 'pq_comments' => '',
                        'created_at' => now(),



                    ],
                    'opportunity_id'

                );
                // foreach ($request->treaty_reinclass as $reinclass) {
                //     DB::table('bd_reinclasses')->insert([
                //         'opportunity_id' => $insertedOppId,
                //         'reinclass' => $reinclass,
                //     ]);
                // }
            }
            if ($request->type_of_bus == 'TPR' && !empty($request->treatytype)) {
                logger('inside prop');
                $treaty_reinclass = $request->treaty_reinclass;
                $user = Auth::user()->user_name;

                $existingReinprops = $request->updateState === 'U'
                    ? DB::table('bd_reinprops')
                    ->where('opportunity_id', $insertedOppId)
                    ->get()
                    ->keyBy(function ($item) {
                        return $item->reinclass . '|' . $item->item_no . '|' . $item->item_description;
                    })
                    : collect([]);

                foreach ($treaty_reinclass as $index => $treaty_class) {
                    $this->insertBdReinClass($treaty_class, $request, $insertedOppId);

                    $retention_per = isset($request->retention_per[$index]) ? str_replace(',', '', $request->retention_per[$index]) : 0;
                    $treaty_reice = isset($request->treaty_reice[$index]) ? str_replace(',', '', $request->treaty_reice[$index]) : 0;
                    $surp_retention_amt = isset($request->surp_retention_amt[$index]) ? str_replace(',', '', $request->surp_retention_amt[$index]) : 0;
                    $no_of_lines = isset($request->no_of_lines[$index]) ? str_replace(',', '', $request->no_of_lines[$index]) : 0;
                    $surp_treaty_limit = isset($request->surp_treaty_limit[$index]) ? str_replace(',', '', $request->surp_treaty_limit[$index]) : 0;
                    $quota_retention_amt = isset($request->quota_retention_amt[$index]) ? str_replace(',', '', $request->quota_retention_amt[$index]) : 0;
                    $quota_treaty_limit = isset($request->quota_treaty_limit[$index]) ? str_replace(',', '', $request->quota_treaty_limit[$index]) : 0;
                    $quota_share_total_limit = isset($request->quota_share_total_limit[$index]) ? str_replace(',', '', $request->quota_share_total_limit[$index]) : 0;
                    $estimated_income = isset($request->estimated_income[$index]) ? str_replace(',', '', $request->estimated_income[$index]) : 0;
                    $cashloss_limit = isset($request->cashloss_limit[$index]) ? str_replace(',', '', $request->cashloss_limit[$index]) : 0;

                    $common_data = [
                        'opportunity_id' => $insertedOppId,
                        'reinclass' => $treaty_class,
                        'item_no' => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                        'retention_rate' => $retention_per,
                        'treaty_rate' => $treaty_reice,
                        'no_of_lines' => $no_of_lines,
                        'estimated_income' => $estimated_income,
                        'cashloss_limit' => $cashloss_limit,
                        'port_prem_rate' => $request->port_prem_rate,
                        'port_loss_rate' => $request->port_loss_rate,
                        'profit_comm_rate' => $request->profit_comm_rate,
                        'mgnt_exp_rate' => $request->mgnt_exp_rate,
                        'deficit_yrs' => $request->deficit_yrs,
                        'created_by' => $user,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if ($request->treatytype == 'SURP') {
                        $reinprop_data = array_merge($common_data, [
                            'item_description' => 'SURPLUS',
                            'retention_amount' => $surp_retention_amt,
                            'treaty_amount' => $surp_treaty_limit,
                            'treaty_limit' => $surp_retention_amt + $surp_treaty_limit,
                        ]);
                        $key = $treaty_class . '|' . $common_data['item_no'] . '|SURPLUS';
                        if ($existingReinprops->has($key)) {
                            Log::info('Updating bd_reinprops', ['key' => $key, 'id' => $existingReinprops[$key]->id]);
                            DB::table('bd_reinprops')
                                ->where('id', $existingReinprops[$key]->id)
                                ->update(array_merge($reinprop_data, ['updated_at' => now()]));
                        } else {
                            Log::info('Inserting new bd_reinprops', ['key' => $key]);
                            DB::table('bd_reinprops')->insert($reinprop_data);
                        }
                    } elseif ($request->treatytype == 'QUOT') {
                        $reinprop_data = array_merge($common_data, [
                            'item_description' => 'QUOTA',
                            'retention_amount' => $quota_retention_amt,
                            'treaty_amount' => $quota_treaty_limit,
                            'treaty_limit' => $quota_retention_amt + $quota_treaty_limit,
                        ]);
                        $key = $treaty_class . '|' . $common_data['item_no'] . '|QUOTA';
                        if ($existingReinprops->has($key)) {
                            Log::info('Updating bd_reinprops', ['key' => $key, 'id' => $existingReinprops[$key]->id]);
                            DB::table('bd_reinprops')
                                ->where('id', $existingReinprops[$key]->id)
                                ->update(array_merge($reinprop_data, ['updated_at' => now()]));
                        } else {
                            Log::info('Inserting new bd_reinprops', ['key' => $key]);
                            DB::table('bd_reinprops')->insert($reinprop_data);
                        }
                    } elseif ($request->treatytype == 'SPQT') {
                        if ($quota_share_total_limit > 0) {
                            $reinprop_data = array_merge($common_data, [
                                'item_description' => 'QUOTA',
                                'retention_amount' => $quota_retention_amt,
                                'treaty_amount' => $quota_treaty_limit,
                                'treaty_limit' => $quota_retention_amt + $quota_treaty_limit,
                            ]);
                            $key = $treaty_class . '|' . $common_data['item_no'] . '|QUOTA';
                            if ($existingReinprops->has($key)) {
                                Log::info('Updating bd_reinprops', ['key' => $key, 'id' => $existingReinprops[$key]->id]);
                                DB::table('bd_reinprops')
                                    ->where('id', $existingReinprops[$key]->id)
                                    ->update(array_merge($reinprop_data, ['updated_at' => now()]));
                            } else {
                                Log::info('Inserting new bd_reinprops', ['key' => $key]);
                                DB::table('bd_reinprops')->insert($reinprop_data);
                            }
                        }
                        if ($surp_treaty_limit > 0) {
                            $reinprop_data = array_merge($common_data, [
                                'item_description' => 'SURPLUS',
                                'retention_amount' => $surp_retention_amt,
                                'treaty_amount' => $surp_treaty_limit,
                                'treaty_limit' => $surp_retention_amt + $surp_treaty_limit,
                            ]);
                            $key = $treaty_class . '|' . $common_data['item_no'] . '|SURPLUS';
                            if ($existingReinprops->has($key)) {
                                Log::info('Updating bd_reinprops', ['key' => $key, 'id' => $existingReinprops[$key]->id]);
                                DB::table('bd_reinprops')
                                    ->where('id', $existingReinprops[$key]->id)
                                    ->update(array_merge($reinprop_data, ['updated_at' => now()]));
                            } else {
                                Log::info('Inserting new bd_reinprops', ['key' => $key]);
                                DB::table('bd_reinprops')->insert($reinprop_data);
                            }
                        }
                    }
                }

                $treaty_reinclass = $request->treaty_reinclass;
                $prem_type_reinclass = $request->prem_type_reinclass;
                $prem_type_code = $request->prem_type_code;

                $existingPremtypes = $request->updateState === 'U'
                    ? DB::table('bd_premtypes')
                    ->where('opportunity_id', $insertedOppId)
                    ->get()
                    ->keyBy(function ($item) {
                        return $item->reinclass . '|' . $item->premtype_code;
                    })
                    : collect([]);


                foreach ($treaty_reinclass as $index => $reinclass) {

                    $premtype_reinclass = ReinclassPremtype::where('reinclass', $reinclass)
                        ->where('premtype_code', $prem_type_code[$index])
                        ->first();

                    $premtype_data = [
                        'opportunity_id' => $insertedOppId,
                        'reinclass' => $reinclass,
                        'premtype_code' => $prem_type_code[$index],
                        'premtype_name' => $premtype_reinclass ? $premtype_reinclass->premtype_name : null,
                        'comm_rate' => $request->prem_type_comm_rate[$index],
                        'treaty' => $request->prem_type_treaty[$index]
                    ];


                    $key = $reinclass . '|' . $prem_type_code[$index];
                    if ($existingPremtypes->has($key)) {
                        Log::info('Updating bd_premtypes', ['key' => $key, 'id' => $existingPremtypes[$key]->id]);
                        DB::table('bd_premtypes')
                            ->where('id', $existingPremtypes[$key]->id)
                            ->update(array_merge($premtype_data, ['updated_at' => now()]));
                    } else {
                        Log::info('Inserting new bd_premtypes', ['key' => $key]);
                        DB::table('bd_premtypes')->insert(array_merge($premtype_data, ['created_at' => now()]));
                    }
                }
            } else if ($request->type_of_bus == 'TNP' && !empty($request->treatytype)) {
                $reinclass_code = $request->reinclass_code;
                foreach ($reinclass_code as $index => $reinclass) {
                    $this->insertBdReinClass($reinclass, $request, $insertedOppId);
                }

                $indemnity_limits = $request->indemnity_treaty_limit;
                $underlying_limit = $request->underlying_limit;
                $egnpi = $request->egnpi;
                $method = $request->method;
                $payment_frequency = $request->deposit_frequency ?? 0;
                $min_bc_rate = $request->min_bc_rate;
                $max_bc_rate = $request->max_bc_rate;
                $flat_rate = $request->flat_rate;
                $upper_adj = $request->upper_adj;
                $lower_adj = $request->lower_adj;
                $min_deposit = $request->min_deposit;
                $nonprop_reinclass = $request->nonprop_reinclass;
                $layer_no = $request->layer_no;
                $reinstatement_type = $request->reinstatement_type;
                $reinstatement_value = $request->reinstatement_value;

                // Fetch existing bd_reinlayers records
                $existingReinlayers = $request->updateState === 'U'
                    ? DB::table('bd_reinlayers')
                    ->where('opportunity_id', $insertedOppId)
                    ->get()
                    ->keyBy(function ($item) {
                        return $item->layer_no . '|' . $item->item_no;
                    })
                    : collect([]);

                $item_no = 1;
                foreach ($indemnity_limits as $index => $indemnity_limit) {
                    if ($index > 0 && $layer_no[$index - 1] == $layer_no[$index]) {
                        $item_no += 1;
                    } else {
                        $item_no = 1;
                    }

                    $reinlayer_data = [
                        'opportunity_id' => $insertedOppId,
                        'layer_no' => $layer_no[$index],
                        'reinclass' => $nonprop_reinclass[$index],
                        'indemnity_limit' => (float) str_replace(',', '', $indemnity_limit) ?? 0,
                        'underlying_limit' => (float) str_replace(',', '', $underlying_limit[$index]) ?? 0,
                        'egnpi' => (float) str_replace(',', '', $egnpi[$index]) ?? 0,
                        'method' => $method,
                        'payment_frequency' => $payment_frequency,
                        'reinstatement_type' => $reinstatement_type[$index],
                        'reinstatement_value' => (float) str_replace(',', '', $reinstatement_value[$index]) ?? 0,
                        'item_no' => $item_no,
                        'min_deposit' => (float) str_replace(',', '', $min_deposit[$index]) ?? 0,
                    ];

                    if ($method == 'F') {
                        $flat = (float) str_replace(',', '', $flat_rate[$index]) ?? 0;
                        $reinlayer_data['flat_rate'] = $flat;
                        $reinlayer_data['min_bc_rate'] = 0;
                        $reinlayer_data['max_bc_rate'] = $flat;
                        $reinlayer_data['upper_adj'] = $flat;
                        $reinlayer_data['lower_adj'] = 0;
                    } else {
                        $reinlayer_data['flat_rate'] = 0;
                        $reinlayer_data['min_bc_rate'] = (float) str_replace(',', '', $min_bc_rate[$index]) ?? 0;
                        $reinlayer_data['max_bc_rate'] = (float) str_replace(',', '', $max_bc_rate[$index]) ?? 0;
                        $reinlayer_data['upper_adj'] = (float) str_replace(',', '', $upper_adj[$index]) ?? 0;
                        $reinlayer_data['lower_adj'] = (float) str_replace(',', '', $lower_adj[$index]) ?? 0;
                    }

                    $key = $layer_no[$index] . '|' . $item_no;
                    if ($existingReinlayers->has($key)) {
                        Log::info('Updating bd_reinlayers', ['key' => $key, 'id' => $existingReinlayers[$key]->id]);
                        DB::table('bd_reinlayers')
                            ->where('id', $existingReinlayers[$key]->id)
                            ->update(array_merge($reinlayer_data, ['updated_at' => now()]));
                    } else {
                        Log::info('Inserting new bd_reinlayers', ['key' => $key]);
                        DB::table('bd_reinlayers')->insert(array_merge($reinlayer_data, ['created_at' => now()]));
                    }
                }
            }

            DB::commit();
            return ['status' => 1, 'message' => $mes];
        } catch (Exception $e) {
            DB::rollback();
            return ['status' => 0, 'message' => 'An error occurred.'];
        }
    }

    public function insertBdReinClass($reinclass, $request, $insertedOppId)
    {

        $prospect = $request->prospect;

        DB::table('bd_reinclasses')->updateOrInsert(
            [
                'opportunity_id' => $insertedOppId,
                'reinclass' => $reinclass,
            ],
        );
    }

    public function search_prospect_fullnames(Request $request)
    {

        $searchTerm = $request->input('q');

        $prospProperties = DB::table(DB::raw("(
                    SELECT
                        pipeline_id,
                        jsonb_array_elements_text(contact_name::jsonb) AS matched_name,
                        jsonb_array_elements_text(email::jsonb) AS matched_email,
                        jsonb_array_elements_text(phone::jsonb) AS matched_phone,
                        jsonb_array_elements_text(telephone::jsonb) AS matched_telephone
                    FROM pipeline_opportunities
                    WHERE
                        jsonb_typeof(contact_name::jsonb) = 'array'
                        AND jsonb_typeof(email::jsonb) = 'array'
                        AND jsonb_typeof(phone::jsonb) = 'array'
                        AND jsonb_typeof(telephone::jsonb) = 'array'
                ) AS subquery
            "))->selectRaw("
                DISTINCT ON (matched_name)
                pipeline_id,
                matched_name AS contact_name,
                matched_email AS email,
                matched_phone AS phone,
                matched_telephone AS telephone
            ")
            ->whereRaw("matched_name ILIKE ?", ["%{$searchTerm}%"])
            ->orderByRaw("matched_name, pipeline_id")
            ->limit(10)
            ->get();

        return response()->json($prospProperties);
    }

    public function search_insured_names(Request $request)
    {

        $searchTerm = $request->input('q');

        $prospProperties = DB::table('pipeline_opportunities')
            ->selectRaw('DISTINCT ON (LOWER(insured_name)) pipeline_id, insured_name') // Ensure uniqueness
            ->whereRaw('LOWER(insured_name) LIKE ?', [strtolower($searchTerm) . '%'])
            ->limit(10)
            ->get();

        return response()->json($prospProperties);
    }

    public function search_lead_names(Request $request)
    {

        $searchTerm = $request->input('q');
        $prospProperties = DB::table('pipeline_opportunities')
            ->selectRaw('DISTINCT ON (LOWER(lead_name)) pipeline_id, lead_name') // Ensure uniqueness
            ->whereRaw('LOWER(lead_name) like ?', [strtolower($searchTerm) . '%'])
            ->limit(10)
            ->get(['pipeline_id', 'lead_name']);

        return response()->json($prospProperties);
    }

    public function confirmUserExists(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'required|string',
                'division' => 'required|string',
                'year' => 'required|numeric',
                'insurance_class' => 'required|string',
            ]);

            $userExists = DB::table('pipeline_opportunities')->where('fullname', $request->full_name)
                ->where('divisions', $request->division)
                ->where('pip_year', $request->year)
                ->where('insurance_class', $request->insurance_class)
                ->exists();

            return response()->json(['exists' => $userExists]);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function prospect_handover()
    {
        return view('Bd_views.intermediaries.handover_bd');
    }

    public function handoverToCR(Request $request)
    {
        $pipeid = $request->prospect;

        // begin new code
        $currentYear = date('Y');
        $endYear = $currentYear + 10;
        $years = range($currentYear, $endYear); // Generate an array of years
        $customer_types = CustomerTypes::select('type_id', 'type_name')->get();
        $salutations = Salutation::all();
        $sources = LeadsSource::all();
        $statuses = LeadStatus::wherein('id', [2, 4, 5])->get();
        $engage_types = LeadsSource::all();
        $industries = Occupation::all();
        $divisions = DB::table('divisions')->get();
        $clients = Client::select('full_name', 'global_customer_id', 'client_type', 'salutation_code', 'occupation_code')->get();
        $prospProperties = DB::table('pipeline_opportunities')->where('opportunity_id', "=", $request->prospect)->first();
        $users = User::all();
        // dd($prospProperties);
        $currencies = Currency::all();
        $leadsources = DB::table('leadsources')->get();
        $prospect = $request->prospect;
        $countries = Country::all();
        $underwriters = DB::table('companies')->get();
        $branches = Branch::where('status', 'A')->get(['branch_code', 'branch_name', 'status']);
        $treatytypes = TreatyType::where('status', 'A')->get();
        $types_of_bus = BusinessType::get(['bus_type_id', 'bus_type_name']);
        $branches = Branch::where('status', 'A')->get(['branch_code', 'branch_name', 'status']);
        $brokers = Broker::where('status', 'A')->get(['broker_code', 'broker_name', 'status']);
        $classes = Classes::where('status', 'A')->get(['class_code', 'class_name', 'status']);
        $types_of_sum_insured = TypeOfSumInsured::where('status', 'A')->get(['sum_insured_code', 'sum_insured_name', 'status']);
        $classGroups = ClassGroup::get(['group_code', 'group_name']);
        $paymethods = PayMethod::all();
        $premium_pay_terms = PremiumPayTerm::all();
        $currency = Currency::all();
        $covertypes = CoverType::all();
        $quotes = Quote::where('opportunity_id', $pipeid)->get('quote_number');
        $reinsdivisions = ReinsDivision::where('status', 'A')->get();
        $reinsclasses = ReinsClass::where('status', 'A')->get();
        $treatytypes = TreatyType::where('status', 'A')->get();
        $reinPremTypes = ReinclassPremtype::where('status', 'A')->get();
        $pipeYear = Pipeline::orderBy('year', 'asc')->get();
        $Contact_details = DB::table('pipeline_opportunities')
            ->select('contact_name', 'phone', 'email', 'telephone')
            ->where('opportunity_id', "=", $request->prospect)->first();
        // Convert JSON fields into arrays
        $contactNames = json_decode($Contact_details->contact_name ?? '[]', true);
        $emails = json_decode($Contact_details->email ?? '[]', true);
        $phones = json_decode($Contact_details->phone ?? '[]', true);
        $telephones = json_decode($Contact_details->telephone ?? '[]', true);
        $stage = DB::table('pipeline_opportunities')
            ->select('stage')
            ->where('opportunity_id', "=", $request->prospect)->first();
        $category_type = DB::table('pipeline_opportunities')
            ->select('category_type')
            ->where('opportunity_id', "=", $request->prospect)->first();
        // Ensure they are always arrays and have the same length
        $count = max(count($contactNames), count($emails), count($phones), count($telephones));

        $contacts = [];
        for ($i = 0; $i < $count; $i++) {
            $contacts[] = [
                'contact_name' => $contactNames[$i] ?? '',
                'email' => $emails[$i] ?? '',
                'phone' => $phones[$i] ?? '',
                'telephone' => $telephones[$i] ?? '',
            ];
        }

        $customers = DB::table('customers')
            ->join('customer_types', function ($join) {
                $join->on('customer_types.type_id', '=', DB::raw("ANY (SELECT json_array_elements_text(customers.customer_type)::int)"));
            })
            ->select(
                DB::raw('CAST(customers.customer_id AS INT) as customer_id'),
                'customers.name'
            )
            ->whereIn('customer_types.type_name', ['INSURANCE', 'REINSURANCE'])
            ->distinct('name') // Filtering for 'insurance' or 'reinsurance'
            ->get();



        $insured = DB::table('customers')
            ->join('customer_types', function ($join) {
                $join->on('customer_types.type_id', '=', DB::raw("ANY (SELECT json_array_elements_text(customers.customer_type)::int)"));
            })
            ->select(
                DB::raw('CAST(customers.customer_id AS INT) as customer_id'), // Casting customer_id as an integer
                'customers.name'
            )
            ->where('customer_types.code', 'INSURED')
            ->get();
        $decline_reinsurers = ReinsurersDeclined::with('customer_name')->where('opportunity_id', $pipeid)
            ->get();


        $commonVariables = [
            'insured' => $insured,
            'types_of_bus' => $types_of_bus,
            'branches' => $branches,
            'brokers' => $brokers,
            'classGroups' => $classGroups,
            'class' => $classes,
            'paymethods' => $paymethods,
            'premium_pay_terms' => $premium_pay_terms,
            'currencies' => $currency,
            'covertypes' => $covertypes,
            'types_of_sum_insured' => $types_of_sum_insured,
            'reinsdivisions' => $reinsdivisions,
            'reinsclasses' => $reinsclasses,
            'treatytypes' => $treatytypes,
            'customers' => $customers,
            'contacts_det' => $contacts,
            'decline_reinsurers' => $decline_reinsurers

        ];
        $otherVariabales = [
            'countries' => $countries,
            'prospProperties' => $prospProperties,
            'underwriters' => $underwriters,
            'prospect' => $prospect,
            'engage_types' => $engage_types,
            'divisions' => $divisions,
            'leadsources' => $leadsources,
            'currencies' => $currencies,
            'statuses' => $statuses,
            'salutations' => $salutations,
            'sources' => $sources,
            'industries' => $industries,
            'users' => $users,
            'clients' => $clients,
            'years' => $years,
            'customer_types' => $customer_types,
            'pipeYear' => $pipeYear,
            'quotes' => $quotes,
            'pipeid' => $pipeid

        ];
        $allVariables = array_merge($commonVariables, $otherVariabales);

        $prospect = $request->prospect;
        $occupations = Occupation::all();

        $salutations = Salutation::all();
        $genders = Gender::all();
        $divisions = DB::table('divisions')->get();

        $statuses = Status::all();

        $countries = Country::all();
        $users = User::all();

        $bd_users = User::all();

        // $docs = DB::table('stage_documents')
        //     ->join('doc_types', 'stage_documents.doc_type', '=', 'doc_types.id')
        //     ->where('stage', 4)
        //     ->select('doc_types.id', 'doc_types.doc_type', 'stage_documents.mandatory', 'stage_documents.division')
        //     ->get();

        $category = $category_type->category_type;
        $stage = $stage->stage;
        // dd($category);

        // $docs = DB::table('stage_documents')
        //     ->join('doc_types', 'stage_documents.doc_type', '=', 'doc_types.id')
        //     ->where('stage', $stage )
        //     // ->where('category_type', $category)
        //     ->select('doc_types.id', 'doc_types.doc_type', 'stage_documents.mandatory', 'stage_documents.division')
        //     ->get();

        $docs = DB::table('stage_documents')
            ->join('doc_types', 'stage_documents.doc_type', '=', 'doc_types.id')
            ->where('stage_documents.stage', $stage)
            ->where('stage_documents.category_type', $category)
            ->whereJsonContains('type_of_bus', $prospProperties->type_of_bus)
            ->select(
                'doc_types.id',
                'doc_types.doc_type',
                'stage_documents.mandatory',
                'stage_documents.division'
            )
            ->get();


        $approval = $request->approval;
        $handover_approval = null;
        $prosp_doc = null;
        $reinsurers = null;
        $prosp_doc = DB::table('prospect_docs')
            ->where('prospect_id', $pipeid)
            ->get();

        if ($approval == 1) {
            $handover_approval = HandoverApproval::where('prospect_id', $pipeid)->first();
            $reinsurers = QuoteReinsurers::where('opportunity_id', $pipeid)->where('stage', 4)->get();
        }

        $currencies = Currency::all();
        return view('Bd_views.intermediaries.handover_validate', compact('currencies', 'users', 'bd_users', 'docs', 'divisions', 'prospect', 'countries', 'statuses', 'occupations', 'genders', 'salutations', 'allVariables', 'reinsurers', 'approval', 'prosp_doc', 'handover_approval'));
    }

    public function handoverSave(Request $request)
    {

        try {
            $prospectId = $request->prospect_id;
            $uploadsPath = 'uploads';
            DB::table('pipeline_opportunities')->where('opportunity_id', $prospectId)->update([
                'effective_date' => $request->effective_date,
                'closing_date' => $request->closing_date,
            ]);
            $exist = DB::table('handover_approvals')->where([
                'prospect_id' => $prospectId,
            ])->exists();
            if ($exist) {
                DB::beginTransaction();
                $client = DB::table('handover_approvals')->where('prospect_id', $prospectId)
                    ->update([
                        'excess' => $request->excess,
                        'max/min' => $request->max_min,
                        'quote_number' => $request->quote_number,
                        'range' => $request->range,
                        'handler' => $request->handler,
                        'approver' => json_encode($request->approver),
                        'approval_status' => "",
                        'client_type' => $request->client_type,
                        'excess_type' => $request->excess_type,
                        'inception_date' => $request->effective_date,
                        'updated_at' => now(),
                        'created_by' => auth()->id(),
                        'date_created' => Carbon::today()->toDateString(),
                        'remarks' => $request->remarks,
                    ]);
            } else {

                DB::beginTransaction();
                $client = DB::table('handover_approvals')->insert([
                    'prospect_id' => $prospectId,
                    'excess' => $request->excess,
                    'max/min' => $request->max_min,
                    'quote_number' => $request->quote_number,
                    'range' => $request->range,
                    'handler' => $request->handler,
                    'approver' => json_encode($request->approver),
                    'client_type' => $request->client_type,
                    'excess_type' => $request->excess_type,
                    'inception_date' => $request->effective_date,
                    'created_at' => now(),
                    'created_by' => auth()->id(),
                    'date_created' => Carbon::today()->toDateString(),
                    'remarks' => $request->remarks,
                ]);
            }
            if (!empty($request->document_file)) {
                foreach ($request->document_name as $index => $name) {


                    $file = $request->file('document_file')[$index];

                    if ($file->isValid()) {
                        $originalNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $Filename = mt_rand() . '_' . $originalNameWithoutExtension . '.' . $file->getClientOriginalExtension();
                        $mimeType = $file->getMimeType();

                        $S3FilePath = $uploadsPath . '/' . $Filename;

                        try {
                            // Upload file to S3
                            Storage::disk('s3')->put($S3FilePath, file_get_contents($file), [
                                'visibility' => 'public',
                            ]);

                            // Verify the file was uploaded
                            if (!Storage::disk('s3')->exists($S3FilePath)) {
                                logger("Failed.ConcurrentModificationException: Failed to verify file in S3:  $S3FilePath");
                                return response()->json(['error' => 'Failed to save file to S3.'], 500);
                            }
                        } catch (\Exception $e) {
                            logger("S3 upload error for  $S3FilePath: " . $e->getMessage());
                            return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
                        }
                        $exist = DB::table('prospect_docs')->where([
                            'prospect_id' => $prospectId,
                            'description' => $name,
                            'prospect_status' => 5

                        ])->first();
                        if (!$exist) {

                            DB::table('prospect_docs')->insert([
                                'description' => $name,
                                'prospect_id' => $prospectId,
                                'prospect_status' => 5,
                                'mimetype' => $mimeType,
                                'file' => $Filename
                            ]);
                        } else if ($exist) {
                            DB::table('prospect_docs')->where([
                                'prospect_id' => $prospectId,
                                'description' => $name,
                                'prospect_status' => 5
                            ])->update([
                                'description' => $name,
                                'prospect_id' => $prospectId,
                                'prospect_status' => 5,
                                'mimetype' => $mimeType,
                                'file' => $Filename
                            ]);
                        }
                    }
                }
            }

            $update = DB::table('pipeline_opportunities')->where('opportunity_id', $request->prospect_id)
                ->update([
                    'handed_over' => 'Y',
                    // 'stage' => '6',
                ]);

            $handler_user_id = $request->handler;
            $user_id = $request->approver;

            $handler = User::where('id', $handler_user_id)->first();
            $handlerContactName = $handler->name;
            $handlerMainEmail = $handler->email;
            $handlerCCEmail = [];

            $handlerEmailData = [
                'salutation' => $handlerContactName,
                'email' => $handlerMainEmail,
                'cc' => $handlerCCEmail,
                'title' => 'Notification for account handler',
                'body' => 'Kindly follow up on this prospect ' . $request->prospect_id . ' you are the account handler.'
            ];

            $users = User::whereIn('id', $user_id)->get();

            $contactName = $users->first()->name;
            $mainEmail = $users->first()->email;

            $ccEmails = $users->slice(1)->pluck('email')->toArray();
            $approverEmailData = [
                'salutation' => $contactName,
                'email' => $mainEmail,
                'cc' => $ccEmails ?? [],
                'title' => 'Request for approval of handover',
                'body' => 'Kindly approve the handover of the following prospect ' . $request->prospect_id
            ];
            SendHandOverApproverEmail::dispatch($handlerEmailData);
            SendHandOverApproverEmail::dispatch($approverEmailData);


            if ($client && $update) {
                DB::commit();
                return ['status' => 200];
            } else {
                return ['status' => 400];
            }
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    public function prospectAddToPipeline(Request $request)
    {

        $year = DB::table('pipeline_opportunities')->where('opportunity_id', $request->prospect)->first()->pip_year;

        $pip_id = DB::table('pipelines')->where('id', $year)->first()->id;

        $update = DB::table('pipeline_opportunities')->where('opportunity_id', $request->prospect)
            ->update([
                'pipeline_id' => $pip_id,
                'stage' => 1,
            ]);
        if ($update) {
            return ['status' => 1, 'message' => "Successfully added"];
        } else {
            return ['status' => 0, 'message' => "Failed"];
        }
    }

    public function prospectsVerify(Request $request)
    {
        try {
            DB::beginTransaction();
            $prospectVerificationStatus = 1;

            if (DB::table('prospect_handover')->where('id', "=", $request->prospId)->where('prospect_id', "=", $request->prospect_id)->first()->prospect_verification_status === 1) {
                $prospectVerificationStatus = 2;
            }
            if (DB::table('prospect_handover')->where('id', "=", $request->prospId)->where('prospect_id', "=", $request->prospect_id)->first()->prospect_verification_status === 2) {
                $prospectVerificationStatus = 3;
            }

            DB::table('prospect_handover')->where('id', "=", $request->prospId)->where('prospect_id', "=", $request->prospect_id)->update([
                'full_name' => $request->client_type == 'I' ? $request->fname : $request->corporate_name,
                'client_type' => $request->client_type,
                'client_category' => $request->client_category,
                'division' => $request->division,
                'cr_handler' => $request->lead_handler,
                'bd_handler' => $request->bd_lead,
                'incorporation_cert' => $request->incorporation_cert,
                'class_of_insurance' => $request->insurance_class,
                'nature_of_engagement' => $request->engage_type,
                'id_type' => $request->id_type,
                'id_value' => $request->identity_no,
                'cert_no' => $request->cert_no,
                'date_of_birth_registration' => $request->dob,
                'salutation_code' => $request->salutation_code,
                'pin_no' => $request->pin_no,
                'cr12' => $request->cr12,
                'gender_code' => $request->gender,
                'occupation_code' => $request->occupation_code,
                'email' => $request->email,
                'phone_1' => $request->phone_1,
                'telephone' => $request->telephone,
                'inception_date' => $request->incept_date,
                'address_3' => $request->address_3,
                'country' => $request->country_code,
                'town' => $request->town,
                'postal_address' => $request->postal_address,
                'postal_code' => $request->postal_code,
                'street' => $request->street,
                'created_by' => auth()->id(),
                'date_created' => Carbon::today()->toDateString(),
                'cr_processed' => 'N',
                'quote_currency' => $request->quote_currency,
                'agent_name' => $request->agent_name,
                'agent_comm_rate' => $request->ag_comm_rate,
                'contact_salutation' => $request->contact_salutation,
                'contact_fullname' => $request->contact_name,
                'contact_position' => $request->contact_position,
                'contact_phone' => $request->phone_1,
                'contact_email' => $request->contact_email,
                'alternative_contact_name' => $request->alternative_contact_name,
                'alternative_email' => $request->alternative_email,
                'alternative_phone_number' => $request->alternative_phone_number,
                'alternative_salutation' => $request->alternative_salutation,
                'alternative_contact_position' => $request->alternative_contact_position,
                'final_premium' => $request->final_premium,
                'final_commission' => $request->final_commission,
                'remarks' => $request->remarks,
                'prospect_verification_status' => $prospectVerificationStatus,
            ]);

            DB::commit();

            //  check if CRM continue else if CR redirect to send email to client
            if (count(DB::table('users')->where('id', "=", Auth::user()->id)->where('user_group_id', "=", DB::table('user_groups')->where('group_name', "=", 'underwriter')->first()->id)->get()) > 0) {
                $status = 201;
                return response()->json([
                    'redirect' => route('crSendMailToClient'),
                    'status' => $status,
                    'email' => $request->email,
                    'prospectId' => $request->prospect_id,
                ]);
            } else {
                $status = 200;
                return response()->json([
                    'redirect' => route('viewSchemeAllocate'),
                    'status' => $status,
                    'prospectId' => $request->prospect_id,
                ]);
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with("error", "verification failed");
        }
    }

    public function CRSendMailsToClient(Request $request)
    {

        if ($request->has('isSendNow')) {
            $isSendNow = $request->input('isSendNow');
            $prospectId = Crypt::decrypt($request->input('qstring'));
            $mail = Crypt::decrypt($request->input('email'));

            list($key, $prospectId) = explode('=', $prospectId);
            list($key, $mail) = explode('=', $mail);
        } else {
            $details = $request->query('details');
            $decodedDetails = json_decode($details, true);
            if ($decodedDetails) {
                $prospectId = $decodedDetails['prospectId'];
                $mail = $decodedDetails['mail'];
            }
        }

        return view('Bd_views.intermediaries.showCremailtoclient', compact('mail', 'prospectId'));
    }

    public function sendDraftEmailCrClient(Request $request)
    {
        try {
            $request->validate([
                'prospectId' => 'required|string',
                'recipient' => 'required|email',
                'cc' => 'nullable|string',
                'subject' => 'required|string',
                'content' => 'required|string',
            ]);

            $recipientEmail = $request->input('recipient');
            $ccEmails = $request->input('cc');

            // Convert CC emails to an array if more than one
            $ccEmails = $ccEmails ? explode(',', $ccEmails) : [];

            $emailData = [
                'subject' => $request->input('subject'),
                'content' => $request->input('content'),
            ];

            $data = [
                'recipientEmail' => $recipientEmail,
                'emailData' => $emailData,
                'ccEmails' => $ccEmails,
            ];

            sendCrEmailToClient::dispatch($data);

            // update prospect to status 4 to move to client listing
            DB::table('prospect_handover')->where('prospect_id', "=", $request->input('prospectId'))->update([
                'prospect_verification_status' => 4,
            ]);

            return response()->json(['success' => true, 'message' => 'Email sent successfully.', 'redirect' => route('client_lst')]);
        } catch (\Exception $e) {
            // Handle error sending email
            return response()->json(['success' => false, 'message' => 'Failed to send email: ' . $e->getMessage()]);
        }
    }

    public function viewSchemeAllocateBlade(Request $request)
    {

        $decrypted = Crypt::decrypt($request->input('qstring'));

        parse_str($decrypted, $output);

        $prospectId = $output['prospect'];

        $crProspect = DB::table('prospect_handover')->where('prospect_id', "=", $prospectId)->first();
        $crId = $crProspect->cr_handler;
        $crHandlers = [];
        $crCoHandlers = [];
        $underWritters = [];

        foreach (DB::table('underwriter_account_handlers')->where('divisions_id', "=", $crProspect->division)->where('account_handler', "=", 1)->get() as $cr) {
            $crHandlers[] = DB::table('users')->where('id', "=", $cr->users_id)->first();
        }

        foreach (DB::table('underwriter_account_handlers')->where('divisions_id', "=", $crProspect->division)->where('account_handler', "=", 2)->get() as $crDiv) {
            $crCoHandlers[] = DB::table('users')->where('id', "=", $crDiv->users_id)->first();
        }

        $underWritters = DB::table('users')
            ->join('user_groups', 'users.user_group_id', '=', 'user_groups.id')
            ->whereIn('user_groups.group_name', ['underwriter', 'Head-Underwriter'])
            ->select('users.*')
            ->get();

        $bd_users = User::leftJoin('user_groups', function ($query) {
            $query->on('users.user_group_id', '=', 'user_groups.id');
        })
            ->where('guard_name', 'business-development')
            ->get();

        return view('Bd_intermediaries.scheme_allocate_prospect', compact('prospectId', 'crHandlers', 'crCoHandlers', 'underWritters', 'bd_users'));
    }

    public function prospects_won(Request $request)
    {

        // check if its crm or cr logged in
        if (count(DB::table('users')->where('user_group_id', "=", DB::table('user_groups')->where('group_name', "=", 'underwriter')->first()->id)->where('id', "=", Auth::user()->id)->get()) > 0) {

            $activities = DB::table('prospect_handover')
                ->leftJoin('pipeline_opportunities', 'pipeline_opportunities.opportunity_id', '=', 'prospect_handover.prospect_id')
                ->where('cr_processed', '<>', 'Y')
                ->whereIn('prospect_handover.prospect_verification_status', [2, 3])
                ->select(
                    'pipeline_opportunities.*',
                    'prospect_handover.prospect_verification_status',
                    'prospect_id',
                    'prospect_handover.division',
                )
                ->get();

            return Datatables::of($activities)
                ->editColumn('client_type', function ($d) {
                    $stage = DB::table('lead_status')->where('id', $d->stage)->first();
                    if ($d->client_type == 'C') {
                        return 'Corporate';
                    } else if ($d->client_type == 'I') {
                        return 'Retail';
                    } else if ($d->client_type == 'N') {
                        return 'NGO';
                    } else if ($d->client_type == 'G') {
                        return 'Government';
                    } else if ($d->client_type == 'S') {
                        return "SME's";
                    }
                })
                ->editColumn('division', function ($d) {
                    $division = DB::table('divisions')->where('id', $d->division)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division->name;
                })
                ->editColumn('premium', function ($d) {
                    return number_format($d->premium, 2, '.', ',');
                })
                ->editColumn('income', function ($d) {
                    return number_format($d->income, 2, '.', ',');
                })

                ->addColumn('action', function ($d) {
                    if ($d->prospect_verification_status === 1) {
                        $addUrl = route('viewSchemeAllocate', ['qstring' => Crypt::encrypt('prospect=' . $d->opportunity_id)]);
                        return '<a href="' . $addUrl . '" class="btn btn-primary update_status" title="Allocate Scheme"><i class="fa fa-plus"></i> Allocate Scheme</a>';
                    } elseif ($d->prospect_verification_status === 2) {
                        $addUrl = route('prospect.view', ['qstring' => Crypt::encrypt('prospect=' . $d->opportunity_id)]);
                        return '<a href="' . $addUrl . '" class="btn btn-info update_status" title="View handover"><i class="fa fa-file"></i> View handover</a>';
                    } elseif ($d->prospect_verification_status === 3) {

                        $addUrl = route('crSendMailToClient', [
                            'qstring' => Crypt::encrypt('prospect=' . $d->opportunity_id),
                            'email' => Crypt::encrypt('email=' . $d->email),
                            'isSendNow' => 'N',
                        ]);

                        // Return the HTML anchor tag with the encrypted URL and Font Awesome icon
                        return '<a href="' . $addUrl . '" class="btn btn-info update_status" title="Draft Mail">
                <i class="fa fa-envelope-open"></i> Draft Mail
            </a>';
                    }
                })
                ->make(true);
        } else {
            $activities = DB::table('prospect_handover')
                ->leftJoin('pipeline_opportunities', 'pipeline_opportunities.opportunity_id', '=', 'prospect_handover.prospect_id')
                ->where('cr_processed', '<>', 'Y')
                ->where('prospect_verification_status', "!=", 2)
                ->select(
                    'pipeline_opportunities.*',
                    'prospect_handover.prospect_verification_status',
                    'prospect_handover.division',
                )
                ->get();

            return Datatables::of($activities)
                ->editColumn('client_type', function ($d) {
                    $stage = DB::table('lead_status')->where('id', $d->stage)->first();
                    if ($d->client_type == 'C') {
                        return 'Corporate';
                    } else if ($d->client_type == 'I') {
                        return 'Retail';
                    } else if ($d->client_type == 'N') {
                        return 'NGO';
                    } else if ($d->client_type == 'G') {
                        return 'Government';
                    } else if ($d->client_type == 'S') {
                        return "SME's";
                    }
                })
                ->editColumn('division', function ($d) {
                    $division = DB::table('divisions')->where('id', $d->division)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division->name;
                })
                ->editColumn('premium', function ($d) {
                    return number_format($d->premium, 2, '.', ',');
                })
                ->editColumn('income', function ($d) {
                    return number_format($d->income, 2, '.', ',');
                })

                ->addColumn('action', function ($d) {
                    if ($d->prospect_verification_status === 1) {
                        $addUrl = route('viewSchemeAllocate', ['qstring' => Crypt::encrypt('prospect=' . $d->opportunity_id)]);
                        return '<a href="' . $addUrl . '" class="btn btn-primary update_status" title="Update status"><i class="fa fa-plus"></i> Allocate Scheme</a>';
                    } elseif ($d->prospect_verification_status === 0) {
                        $addUrl = route('prospect.view', ['qstring' => Crypt::encrypt('prospect=' . $d->opportunity_id)]);
                        return '<a href="' . $addUrl . '" class="btn btn-info update_status" title="View handover"><i class="fa fa-file"></i> View handover</a>';
                    } elseif ($d->prospect_verification_status === 3) {
                        $addUrl = route('crSendMailToClient', [
                            'qstring' => Crypt::encrypt('prospect=' . $d->opportunity_id),
                            'email' => Crypt::encrypt('email=' . $d->email),
                            'isSendNow' => 'N',
                        ]);

                        // Return the HTML anchor tag with the encrypted URL and Font Awesome icon
                        return '<a href="' . $addUrl . '" class="btn btn-success update_status" title="Draft Mail">
                 <i class="fa fa-envelope-open"></i> CR to Draft Mail
             </a>';
                    }
                })
                ->make(true);
        }
    }

    public function prospects_won_view(Request $request)
    {
        $string = $request->qstring;
        $request = decryptRequest($string);
        $prospect = $request->prospect;

        if ((int) DB::table('prospect_handover')->where('prospect_id', "=", $request->prospect)->first()->prospect_verification_status === (int) 0 || 2) {
            $occupations = Occupation::all();

            $salutations = Salutation::all();

            $genders = Gender::all();
            $divisions = DB::table('divisions')->get();

            $statuses = Status::all();

            $countries = Country::all();
            $users = User::leftJoin('user_groups', function ($query) {
                $query->on('users.user_group_id', '=', 'user_groups.id');
            })
                ->where('guard_name', 'underwriter')
                ->get();

            $bd_users = User::leftJoin('user_groups', function ($query) {
                $query->on('users.user_group_id', '=', 'user_groups.id');
            })
                ->where('guard_name', 'business-development')
                ->get();

            $docs = DB::table('prospect_docs')->where('prospect_id', "=", $request->prospect)->get();

            $lead = DB::table('prospect_handover')->where('prospect_id', "=", $request->prospect)->first();

            $currencies = Currency::all();

            return view('Bd_views.intermediaries.prospect_view', compact('lead', 'currencies', 'users', 'bd_users', 'docs', 'divisions', 'prospect', 'countries', 'statuses', 'occupations', 'genders', 'salutations'));
        } else {
            return redirect()->back();
        }
    }

    public function cedantDetails(Request $request)
    {
        $opportunity_id = $request->prospect;
        $cedant = DB::table('pipeline_opportunities')->where('opportunity_id', $opportunity_id)->first()->customer_id;
        $cedantDetails = DB::table('customers')->where('customer_id', $cedant)->first()->name;
        $contact_persons = DB::table('customer_contacts')->where('customer_id', $cedant)->get(['customer_id', 'contact_name', 'contact_email']);
        return response()->json(['cedantDetails' => $cedantDetails, 'contact_person' => $contact_persons]);
    }

    public function Report_data(Request $request)
    {
        try {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $closure_date = $request->input('closure_date');
            $from_year = $request->input('from_year');
            $to_year = $request->input('to_year');
            $isExport = $request->has('export') && $request->input('export') === 'true';

            // Validate year input
            if (($from_year && !is_numeric($from_year)) || ($to_year && !is_numeric($to_year))) {
                return response()->json(['error' => 'Invalid year provided'], 400);
            }
            if ($from_year && $to_year && $from_year > $to_year) {
                return response()->json(['error' => 'From Year cannot be greater than To Year'], 400);
            }

            $query = DB::table('pipeline_opportunities')
                ->whereNull('pipeline_id') // Retained from your code
                ->when($from_year && $to_year, function ($query) use ($from_year, $to_year) {
                    return $query->whereBetween('pip_year', [$from_year, $to_year]);
                })
                // ->when($from_year && !$to_year, function ($query) use ($from_year) {
                //     return $query->where('pip_year', '>=', $from_year);
                // })
                // ->when($to_year && !$from_year, function ($query) use ($to_year) {
                //     return $query->where('pip_year', '<=', $to_year);
                // })
                // ->when($client_category, function ($query, $client_category) {
                //     return $query->where('client_category', $client_category);
                // })
                // ->when($lead_status, function ($query, $lead_status) {
                //     return $query->where('lead_status', $lead_status);
                // })
                // ->when($country_code, function ($query, $country_code) {
                //     return $query->where('country_code', $country_code);
                // })
                // ->when($industry, function ($query, $industry) {
                //     return $query->where('industry', $industry);
                // })
                ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                    return $query->whereBetween('effective_date', [$start_date, $end_date]);
                })
                // ->when($start_date && !$end_date, function ($query) use ($start_date) {
                //     return $query->where('effective_date', $start_date);
                // })
                // ->when($end_date && !$start_date, function ($query) use ($end_date) {
                //     return $query->where('effective_date',$end_date);
                // })
                ->when($closure_date, function ($query) use ($closure_date) {
                    return $query->where('fac_date_offered', $closure_date);
                });

            if ($isExport) {
                $data = $query->get();
                $filters = [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'closure_date' => $closure_date,
                    'from_year' => $from_year,
                    'to_year' => $to_year,

                ];
                $filename = 'Pipeline_Report_' . now()->format('Y-m-d_Hi') . '.xlsx';
                return Excel::download(new PipelineReportExport($data, $filters), $filename);
            }

            return DataTables::of($query)
                ->editColumn('client_category', function ($d) {
                    return ($d->client_category === 'O') ? 'Organic growth' : 'New';
                })
                ->editColumn('cedant', function ($d) {
                    return optional(DB::table('customers')
                        ->where('customer_id', $d->customer_id)
                        ->first())->name ?? '';
                })
                ->editColumn('sum_insured_type', function ($d) {
                    return optional(DB::table('type_of_sum_insured')
                        ->where('sum_insured_code', $d->sum_insured_type)
                        ->first())->sum_insured_name ?? '';
                })
                ->addColumn('customer_name', function ($d) {
                    return optional(DB::table('customers')
                        ->where('customer_id', (int) ($d->customer_id ?? 0))
                        ->first())->name ?? 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    return optional(DB::table('lead_status')
                        ->where('id', $d->stage ?? 0)
                        ->where('category_type', $d->category_type ?? '')
                        ->first())->status_name ?? '';
                })
                ->addColumn('division_name', function ($d) {
                    return optional(DB::table('reins_division')
                        ->where('division_code', $d->divisions ?? '')
                        ->first())->division_name ?? '';
                })
                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium ?? 0, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium ?? 0, 2, '.', ',');
                })
                ->addColumn('edit', function ($d) {
                    if ($d->category_type && !in_array($d->stage, [1, 4, 5])) {
                        return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Update proposal"
                            data-stage="' . e($d->stage ?? '') . '"
                            data-division="' . e($d->divisions ?? '') . '"
                            data-opp="' . e($d->opportunity_id ?? '') . '"
                            data-category_type="' . e($d->category_type ?? '') . '"
                            data-status="' . e($d->status ?? '') . '">
                            <i class="bx bx-refresh"></i> Edit</a>';
                    }
                    return '';
                })
                ->addColumn('action1', function ($d) {
                    return ($d->category_type == 1)
                        ? '<button class="text-white btn btn-sm btn-info rounded-pill" disabled> <i class="bx bx-refresh"></i> Quotation</button>'
                        : '<button class="text-white facultative_category btn btn-sm btn-primary rounded-pill"
                            data-stage="' . e($d->stage ?? '') . '"
                            data-division="' . e($d->divisions ?? '') . '"
                            data-opp="' . e($d->opportunity_id ?? '') . '">Facultative Offer</button>';
                })
                ->addColumn('action', function ($d) {
                    if ($d->category_type) {
                        return '<a href="#" class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status"
                            data-stage="' . e($d->stage ?? '') . '"
                            data-division="' . e($d->divisions ?? '') . '"
                            data-opp="' . e($d->opportunity_id ?? '') . '"
                            data-category_type="' . e($d->category_type ?? '') . '"
                            data-status="' . e($d->status ?? '') . '"
                            data-sum-insured-type="' . e($d->sum_insured_type ?? '') . '"
                            data-premium="' . e($d->rein_premium ?? '') . '"
                            data-sum-insured="' . e($d->effective_sum_insured ?? '') . '"
                            data-reins-comm-rate="' . e($d->reins_comm_rate ?? '') . '">
                            <i class="bx bx-refresh"></i> Update status</a>';
                    }
                    return '<a href="#" class="text-white update_category btn btn-sm btn-dark rounded-pill"
                        data-stage="' . e($d->stage ?? '') . '"
                        data-division="' . e($d->divisions ?? '') . '"
                        data-opp="' . e($d->opportunity_id ?? '') . '">
                        <i class="bx bx-edit-alt"></i> Update Category</a>';
                })
                ->rawColumns(['edit', 'action1', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function SalesReportData(Request $request)
    {
        try {
            $year = $request->year;
            $customer_id = $request->cedant;
            $class_group = $request->class_group;

            $classcode = $request->business_class;
            $category_type = $request->category_type;
            $leadStatus = $request->lead_status;
            $lead_status_category = $request->lead_status_category;
            if ($lead_status_category) {
                $category_type = $lead_status_category;
            }
            $start_date = $request->start_date;
            $month = $request->month;
            $isExport = $request->has('export') && $request->export == 'true';

            $query = PipelineOpportunity::query()
                ->where('stage', '>=', 1)
                ->when(!empty($customer_id), function ($q) use ($customer_id) {
                    return $q->where('customer_id', $customer_id);
                })
                ->when(!empty($class_group), function ($q) use ($class_group) {
                    return $q->where('class_group', $class_group);
                })
                ->when(!empty($category_type), function ($q) use ($category_type) {
                    return $q->where('category_type', $category_type);
                })
                ->when(!empty($leadStatus), function ($q) use ($leadStatus) {
                    return $q->where('stage', $leadStatus);
                })
                ->when(!empty($classcode), function ($q) use ($classcode) {
                    return $q->where('classcode', $classcode);
                })
                ->when($start_date, function ($query, $start_date) {
                    return $query->where('sales_entry_date', '>=', $start_date);
                })
                ->when($month, function ($query, $month) {
                    return $query->whereRaw('EXTRACT(MONTH FROM sales_entry_date) = ?', [$month]);
                })
                ->when(!empty($year), function ($q) use ($year) {
                    $q->where(function ($q) use ($year) {
                        $q->where(function ($subQuery) use ($year) {
                            $subQuery->whereNotNull('year_before_revert')
                                ->where('year_before_revert', $year);
                        })->orWhere(function ($subQuery) use ($year) {
                            $subQuery->whereNull('year_before_revert')
                                ->where('pip_year', $year);
                        });
                    });
                });

            if ($isExport) {
                $data = $query->get();
                $filters = [
                    'year' => $year,
                    'cedant' => $customer_id,
                    'class_group' => $class_group,
                    'classcode' => $classcode,
                    'category_type' => $category_type,
                    'lead_status' => $leadStatus,
                    'lead_status_category' => $lead_status_category,
                    'start_date' => $start_date,
                    'month' => $month,
                ];
                $filename = 'Sales_Report_' . now()->format('YmdHi') . '.xlsx';
                return Excel::download(new SalesReportExport($data, $filters), $filename);
            }

            return DataTables::of($query)
                ->addColumn('customer_name', function ($d): mixed {
                    if (empty($d->customer_id)) {
                        return 'N/A';
                    }
                    $lead = DB::table('customers')
                        ->where('customer_id', (int) $d->customer_id)
                        ->first();
                    return $lead ? $lead->name : 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    if ($d->category_type == 1) {
                        $query = DB::table('lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();
                        return $query ? $query->status_name : '';
                    }
                    if ($d->category_type == 2) {
                        $query = DB::table('lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();
                        return $query ? $query->status_name : '';
                    }
                    return '';
                })
                ->addColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    return $division ? $division->division_name : 'N/A';
                })
                ->addColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    return $business_class ? $business_class->class_name : 'N/A';
                })
                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium, 2, '.', ',');
                })
                ->addColumn('turnaround_time', function ($d) {
                    if (!empty($d->sales_entry_date) && !empty($d->won_at)) {
                        $diff = Carbon::parse($d->sales_entry_date)->diff(Carbon::parse($d->won_at));
                        return sprintf('%02d hrs %02d mins %02d secs', $diff->h, $diff->i, $diff->s);
                    }
                    return 'N/A';
                })
                ->addColumn('current_stage_duration', function ($d) {
                    return gmdate('H:i:s', $d->current_stage_duration);
                })
                ->addColumn('action1', function ($d) {
                    if ($d->category_type == 1) {
                        return '<button class="text-white btn btn-sm btn-info rounded-pill" disabled> <i class="bx bx-refresh"></i> Quotation</button>';
                    }
                    if ($d->category_type == 2) {
                        return '<button class="text-white facultative_category btn btn-sm btn-primary rounded-pill" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '">Facultative Offer</button>';
                    }
                    return '';
                })
                ->rawColumns(['action1'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function get_quote_schedules(Request $request)
    {
        $stage = $request->stage_id;
        $opportunity_id = $request->opportunity_id;
        $quote_schedules = QuoteSchedule::selectRaw('DISTINCT ON (schedule_id) schedule_id, name, details, current, proposed, final')
            ->where('opportunity_id', $opportunity_id)
            ->where('stage', '<=', $stage + 1)
            ->orderBy('schedule_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['quote_schedules' => $quote_schedules]);
    }

    public function get_schedules_data(Request $request)
    {
        $classCode = $request->classCode;
        $type_of_bus = $request->type_of_bus;
        $name = $request->name;
        $key = $request->key;
        $ScheduleData = BdScheduleData::where('class_code', $classCode)
            ->where('clause_title', $name)
            ->whereJsonContains('type_of_bus', $type_of_bus)
            ->pluck('clause_wording')
            ->first();
        return response()->json([
            'key' => $key,
            'value' => $ScheduleData
        ]);
    }

    public function getPipelineData(Request $request)
    {
        try {
            $pipelineId = $request->get('pipeline_id');
            $quarter = Str::lower($request->get('quarter'));

            $query = $this->buildOpportunityQuery($pipelineId, $quarter);

            $draw = $request->get('draw');
            $start = $request->get('start', 0);
            $length = $request->get('length', 10);
            $searchValue = $request->get('search')['value'] ?? '';

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('insured_name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('division', 'LIKE', "%{$searchValue}%")
                        ->orWhere('business_class', 'LIKE', "%{$searchValue}%");
                });
            }

            $totalRecords = $query->count();
            $filteredRecords = $totalRecords;

            $opportunities = $query->skip($start)->take($length)->get();

            $data = $opportunities->map(function ($opp) {
                return [
                    'id' => $opp->id,
                    'insured_name' => $opp->insured_name,
                    'division' => $this->getDivision($opp->divisions),
                    'business_class' => $this->getBusinessClass($opp->classcode),
                    'currency' => $opp->currency_code,
                    'sum_insured' => $this->formatNumber($opp->effective_sum_insured),
                    'premium' => $this->formatNumber($opp->cede_premium),
                    'effective_date' => $opp->effective_date ? Carbon::parse($opp->effective_date)->format('Y-m-d') : null,
                    'closing_date' =>  $opp->closing_date ? Carbon::parse($opp->closing_date)->format('Y-m-d') : null,
                    'status' => $this->formatStatus($opp->status),
                    'category' => $this->formatCategory($opp->category_type),
                    'approval_status' => $this->formatApprovalStatus($opp->handed_over),
                    'stage_actions' => $this->formatStageActions($opp),
                    'action' => $this->getActionButtons($opp->id)
                ];
            });

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            logger()->debug($e);
            return response()->json([
                'error' => 'Failed to load pipeline data',
                'message' => $e->getMessage()
            ], 500);
        }

        // $business_types = $request->business_types;

        // $activities = DB::table('pipeline_opportunities')
        //     ->leftJoin('handover_approvals', 'pipeline_opportunities.opportunity_id', '=', 'handover_approvals.prospect_id')
        //     ->where('pipeline_id', $request->pipe_id)
        //     ->where('stage', '>=', 1)
        //     ->whereIn('type_of_bus', $business_types)
        //     ->get();

        // return Datatables::of([])
        //     ->addColumn('customer_name', function ($d): mixed {

        //         if (empty($d->customer_id)) {
        //             return 'N/A';
        //         }

        //         $lead = DB::table('customers')
        //             ->where('customer_id', (int) $d->customer_id)
        //             ->first();

        //         return $lead ? $lead->name : 'N/A';
        //     })
        //     ->editColumn('stage', function ($d) {
        //         if ($d->category_type == 1) {
        //             $query = DB::table('lead_status')
        //                 ->where('id', $d->stage)
        //                 ->where('category_type', $d->category_type)
        //                 ->first();

        //             if (is_null($query)) {
        //                 return '';
        //             }
        //             if ($query->category_type == 1) {
        //                 if ($d->handed_over == 'Y') {
        //                     return $query->status_name . ' (handed ov)';
        //                 }
        //                 return $query->status_name;
        //             }
        //             if ($query->category_type == 2) {
        //                 if ($d->handed_over == 'Y') {
        //                     return $query->status_name . ' (handed ov)';
        //                 }
        //                 return $query->status_name;
        //             }

        //             // return $query->status_name ;
        //         }
        //         if ($d->category_type == 2) {
        //             $query = DB::table('lead_status')
        //                 ->where('id', $d->stage)
        //                 ->where('category_type', $d->category_type)
        //                 ->first();

        //             if (is_null($query)) {
        //                 return '';
        //             }
        //             if ($query->category_type == 2) {
        //                 return $query->status_name;
        //             } else {
        //                 return $query->status_name;
        //             }
        //         }
        //     })
        //     ->addColumn('division_name', function ($d) {
        //         $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
        //         if (is_null($division)) {
        //             return '';
        //         }
        //         return $division ? $division->division_name : 'N/A';
        //     })
        //     ->addColumn('business_class', function ($d) {
        //         $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
        //         if (is_null($business_class)) {
        //             return '';
        //         }
        //         return $business_class ? $business_class->class_name : 'N/A';
        //     })

        //     ->addColumn('cedant_premium', function ($d) {
        //         return number_format($d->cede_premium, 2, '.', ',');
        //     })
        //     ->addColumn('reinsurer_premium', function ($d) {
        //         return number_format($d->rein_premium, 2, '.', ',');
        //     })
        //     ->addColumn('edit', function ($d) {
        //         if ($d->category_type && $d->stage != 1 && $d->stage != 5 && $d->stage != 4) {
        //             return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Udate proposal" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '"> <i class="bx bx-refresh"></i>Edit</a>';
        //         } else {
        //         }
        //     })
        //     ->addColumn('action1', function ($d) {
        //         if ($d->category_type == 1) {
        //             return '<span class="badge bg-success">Quotation</span>';
        //         }
        //         if ($d->category_type == 2) {
        //             return '<span class="badge btn-primary" style="font-size: 10px; padding: 2px 5px;">Facultative offer</span>';
        //         } else {
        //         }
        //     })

        //     ->addColumn('approval_status', function ($d) {
        //         return ($d->handed_over === 'Y')
        //             ? ($d->approval_status === '0'
        //                 ? '<a href="#" title="click" data-rej-text="' . $d->reason_for_rejection . '" class="btn btn-sm btn-danger rounded-pill rej-text">
        //                             <i class="bi bi-x-circle"></i> Rejected
        //                        </a>'
        //                 : ($d->approval_status === '1'
        //                     ? '<a href="#" title="click" data-rej-text="' . $d->approval_comment . '" class="btn btn-sm btn-success rej-text">
        //                                 <i class="bi bi-check-circle"></i> Approved
        //                            </a>'
        //                     : '<span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>'
        //                 )
        //             )
        //             : '';
        //     })


        //     ->addColumn('action', function ($d) {
        //         if ($d->category_type == 1 && $d->stage != 5 || $d->category_type == 2 && $d->stage != 5) {
        //             $cedant = DB::table('customers')->where('customer_id', $d->customer_id)->first();

        //             return '<a href="#"
        //                 class="text-white update_status btn btn-sm btn-success rounded-pill"
        //                 title="Update status"
        //                 data-stage="' . e($d->stage) . '"
        //                 data-division="' . e($d->divisions) . '"
        //                 data-opp="' . e($d->opportunity_id) . '"
        //                 data-category_type="' . e($d->category_type) . '"
        //                 data-status="' . e($d->status) . '"
        //                 data-sum-insured-type="' . e($d->sum_insured_type) . '"
        //                 data-premium="' . e($d->rein_premium) . '"
        //                 data-sum-insured="' . e($d->effective_sum_insured) . '"
        //                 data-reins-comm-rate="' . e($d->reins_comm_rate) . '"
        //                 data-fac-share-offered="' . e($d->fac_share_offered) . '"
        //                 data-cedant-comm-rate="' . e($d->comm_rate) . '"
        //                 data-classcode="' . e($d->classcode) . '"
        //                 data-classgroup="' . e($d->class_group) . '"
        //                 data-insured-name="' . e($d->insured_name) . '"
        //                 data-data-exist-flag="' . e($d->data_exists_flag) . '"
        //                 data-type-of-bus="' . e($d->type_of_bus) . '"
        //                 data-cedant="' . e($cedant->name) . '"


        //                 <i class="bx bx-refresh"></i> Update status
        //             </a>';
        //         } else if (
        //             ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == '') ||
        //             ($d->category_type == 2 && $d->stage == 5 && $d->handed_over == '')
        //         ) {
        //             return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"
        //                 class="text-white update_status btn btn-sm btn-success rounded-pill"
        //                 title="Update status"
        //                 data-stage="' . $d->stage . '"
        //                 data-division="' . $d->divisions . '"
        //                 data-opp="' . $d->opportunity_id . '"
        //                 data-category_type="' . $d->category_type . '"
        //                 data-status="' . $d->status . '"
        //                 data-sum-insured-type="' . $d->sum_insured_type . '">
        //                 <i class="bx bx-refresh"></i>Handover</a>';
        //         } else if (
        //             ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0') ||
        //             ($d->category_type == 2 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0')
        //         ) {
        //             return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"
        //                 class="text-white update_status btn btn-sm btn-success rounded-pill"
        //                 title="Update status"
        //                 data-stage="' . $d->stage . '"
        //                 data-division="' . $d->divisions . '"
        //                 data-opp="' . $d->opportunity_id . '"
        //                 data-category_type="' . $d->category_type . '"
        //                 data-status="' . $d->status . '"
        //                 data-sum-insured-type="' . $d->sum_insured_type . '">
        //                 <i class="bx bx-refresh"></i>Handover</a>';
        //         } else if ($d->category_type == '') {
        //             return '<a href="#"
        //                 class="text-white update_category btn btn-sm btn-dark rounded-pill"
        //                 data-stage="' . $d->stage . '"
        //                 data-division="' . $d->divisions . '"
        //                 data-opp="' . $d->opportunity_id . '">
        //                 <i class="bx bx-edit-alt"></i>Update Category</a>';
        //         }
        //     })
        //     ->rawColumns(['edit', 'action1', 'action', 'approval_status'])
        //     ->make(true);
    }

    //         $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
    //         if (is_null($division)) {
    //             return '';
    //         }
    //         return $division ? $division->division_name : 'N/A';


    private function getDivision($division)
    {
        $division = DB::table('reins_division')->where('division_code', $division)->first();
        return $division ? $division->division_name : 'N/A';
    }

    private function getBusinessClass($classcode)
    {
        $business_class = DB::table('classes')->where('class_code', $classcode)->first();
        return $business_class ? $business_class->class_name : 'N/A';
    }

    private function formatNumber($val, $currency = null)
    {
        $curr = $currency ? $currency . ' ' : '';

        return $val !== null && $val !== ''
            ? "<span class='currency'>{$curr}" . number_format((float) $val, 2) . "</span>"
            : '0.00';
    }

    private function formatCategory($category)
    {
        $statusClasses = [
            '1' => 'quotation',
            '2' => 'facultative offer',
        ];

        $cat = $statusClasses[(string) $category] ?? 'facultative';

        $catText = '';
        $catText =  "<span class='status-badge {$cat}'>" . ucfirst(str_replace('_', ' ', $cat)) . "</span>";
        return $catText;
    }

    private function formatStageActions($opp)
    {

        // logger()->debug($opp);

        // category_type

        $currentStage = 'lead';
        $stageFlow = [
            'lead' => [
                'next' =>  "proposal",
                'button' => "Move to Proposal",
                'class' => "btn-proposal",
                'altNext' => "lost",
                'modalId' => "leadModal",
            ],
            'proposal' => [
                'next' =>  "negotiation",
                'button' => "Move to Negotiation",
                'class' => "btn-negotiation",
                'altNext' => "lost",
                'modalId' => "proposalModal",
            ],
            'negotiation' => [
                'next' =>  "won",
                'button' => "Mark as Won",
                'class' => "btn-won",
                'altNext' => "lost",
                'modalId' => "negotiationModal",
            ],
            'won' => [
                'next' =>  "final",
                'button' => "Move to Final",
                'class' => "btn-final",
                'modalId' => "wonModal",
            ],
            'lost' => [
                'next' =>  null,
                'button' => "Deal Closed",
                'class' => "btn-lost",
                'modalId' => "lostModal",
            ],
            'final' =>  [
                'next' =>  null,
                'button' => "Deal Complete",
                'class' => "btn-final",
                'modalId' => "finalModal",
            ],
        ];

        $stageInfo = $stageFlow[$currentStage];
        $btn = '';
        if (!$stageInfo) {
            return $btn;
        }

        $nextStage = $stageInfo['next'];

        if ($nextStage) {
            $id = $opp->id;
            $displayText = $stageInfo['button'];
            $current_stage = $opp->status;

            $btn = "<button id='stageAction-{$id}' data-deal_id='{$id}' data-current_stage='{$current_stage}' class='stage-btn btn-proposal stage_btn_action' style='opacity: 1; cursor: pointer;'>{$displayText}</button>";
        }
        // logger()->debug($stageInfo);

        return $btn;
    }

    private function formatApprovalStatus($status)
    {
        $action = '<span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>';
        // if ($status === 'Y') {

        // }
        // ? ($d->approval_status === '0'
        //     ? '<a href="#" title="click" data-rej-text="' . $d->reason_for_rejection . '" class="btn btn-sm btn-danger rounded-pill rej-text">
        //                 <i class="bi bi-x-circle"></i> Rejected
        //            </a>'
        //     : ($d->approval_status === '1'
        //         ? '<a href="#" title="click" data-rej-text="' . $d->approval_comment . '" class="btn btn-sm btn-success rej-text">
        //                     <i class="bi bi-check-circle"></i> Approved
        //                </a>'
        //         : '<span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>'
        //     )
        // )
        // : '';
        return $action;
    }

    private function buildOpportunityQuery($pipelineId, $quarter)
    {
        $query = PipelineOpportunity::where('pipeline_id', $pipelineId);

        if ($quarter !== 'all' && is_numeric($quarter)) {
            $query->where('fiscal_period', $quarter);
        }

        return $query;
    }

    private function getAp($status)
    {
        $statusClasses = [
            'proposal' => 'status-proposal',
            'negotiation' => 'status-negotiation',
            'lead' => 'status-lead',
            'won' => 'status-won',
            'lost' => 'status-lost',
            'final_stage' => 'status-final'
        ];

        $class = $statusClasses[$status] ?? 'badge-secondary';
        return "<span class='status-badge {$class}'>" . ucfirst(str_replace('_', ' ', $status)) . "</span>";
    }


    //         return ($d->handed_over === 'Y')
    //             ? ($d->approval_status === '0'
    //                 ? '<a href="#" title="click" data-rej-text="' . $d->reason_for_rejection . '" class="btn btn-sm btn-danger rounded-pill rej-text">
    //                             <i class="bi bi-x-circle"></i> Rejected
    //                        </a>'
    //                 : ($d->approval_status === '1'
    //                     ? '<a href="#" title="click" data-rej-text="' . $d->approval_comment . '" class="btn btn-sm btn-success rej-text">
    //                                 <i class="bi bi-check-circle"></i> Approved
    //                            </a>'
    //                     : '<span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Pending</span>'
    //                 )
    //             )
    //             : '';

    private function formatStatus($status)
    {
        $statusClasses = [
            'proposal' => 'status-proposal',
            'negotiation' => 'status-negotiation',
            'lead' => 'status-lead',
            'won' => 'status-won',
            'lost' => 'status-lost',
            'final_stage' => 'status-final'
        ];

        $class = $statusClasses[$status] ?? 'badge-secondary';
        return "<span class='status-badge {$class}'>" . ucfirst(str_replace('_', ' ', $status)) . "</span>";
    }

    private function getActionButtons($id)
    {
        $showUrl  = ''; //route('opportunity.show', $id);
        $editUrl  =  ''; //route('opportunity.edit', $id);

        return "
        <div class='btn-group'>
            <button class='btn btn-info btn-sm me-1 edit-pipeline' title='Edit Pipeline'><i class='bx bx-edit'></i></button>
            <button class='btn btn-sm btn-danger' onclick='deleteOpportunity({$id})'>
                <i class='bx bx-trash'></i>
            </button>
        </div>
    ";
    }

    public function getPipelineChartData(Request $request)
    {
        try {
            $pipelineId = $request->get('pipeline_id');
            $chartData = $this->buildChartData($pipelineId);

            return response()->json($chartData);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load chart data',
                'data' => [0, 0, 0, 0, 0, 0]
            ], 500);
        }
    }

    private function buildChartData($pipelineId)
    {
        $statusCounts = PipelineOpportunity::where('pipeline_id', $pipelineId)
            ->groupBy('status')
            // ->where('fiscal_period', 3)
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        $statusOrder = ['proposal', 'negotiation', 'lead', 'won', 'lost', 'final_stage'];
        $data = [];

        foreach ($statusOrder as $status) {
            $data[] = $statusCounts[$status] ?? 0;
        }

        // logger()->debug($data);

        return [
            'data' => $data,
            'labels' => ['Proposal', 'Negotiation', 'Lead', 'Won', 'Lost', 'Final Stage'],
            'colors' => ['#d70206', '#f05b4f', '#f4c63d', '#d17905', '#453d3f', '#59922b']
        ];
    }

    public function pipeline_activity_treaty(Request $request)
    {
        try {
            $business_types = $request->business_types;

            $activities = DB::table('pipeline_opportunities')
                ->leftJoin('handover_approvals', 'pipeline_opportunities.opportunity_id', '=', 'handover_approvals.prospect_id')
                ->leftJoin('tenders', 'pipeline_opportunities.opportunity_id', '=', 'tenders.prospect_id')
                ->leftJoin('tender_approvals', 'tenders.tender_no', '=', 'tender_approvals.tender_no')
                ->where('pipeline_id', $request->pipe_id)
                ->where('stage', '>=', 1)
                ->whereIn('type_of_bus', $business_types)
                ->get();

            return Datatables::of($activities)
                ->addColumn('customer_name', function ($d): mixed {

                    if (empty($d->customer_id)) {
                        return 'N/A';
                    }

                    $lead = DB::table('customers')
                        ->where('customer_id', (int) $d->customer_id)
                        ->first();

                    return $lead ? $lead->name : 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    if ($d->category_type == 1) {
                        $query = DB::table('treaty_lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 1) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }
                        if ($query->category_type == 2) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }

                        // return $query->status_name ;
                    }
                    if ($d->category_type == 2) {
                        $query = DB::table('treaty_lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 2) {
                            return $query->status_name;
                        } else {
                            return $query->status_name;
                        }
                    }
                })
                ->addColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division ? $division->division_name : 'N/A';
                })
                ->addColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    if (is_null($business_class)) {
                        return '';
                    }
                    return $business_class ? $business_class->class_name : 'N/A';
                })

                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium, 2, '.', ',');
                })
                ->addColumn('edit', function ($d) {
                    if ($d->category_type && $d->stage != 1 && $d->stage != 5 && $d->stage != 4) {
                        return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Udate proposal" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '"> <i class="bx bx-refresh"></i>Edit</a>';
                    } else {
                    }
                })
                ->addColumn('action1', function ($d) {
                    if ($d->category_type == 1) {
                        return '<span class="badge bg-success">Normal</span>';
                    }
                    if ($d->category_type == 2) {
                        return '<span class="badge btn-primary" style="font-size: 10px; padding: 2px 5px;">Tender</span>';
                    } else {
                    }
                })

                ->addColumn('approval_status', function ($d) {
                    return ($d->stage == 1 && $d->category_type == 2 && $d->status !== null)
                        ? ($d->status === '2'
                            ? '<a href="#" title="click" data-rej-text="' . $d->remarks . '" class="btn btn-sm btn-danger  rounded-pill rej-text">
                                <i class="bi bi-x-circle"></i> Rejected
                            </a>'
                            : '<span class="badge ' .
                            ($d->status === '1' ? 'bg-success' : 'bg-warning text-dark') .
                            '"> ' .
                            ($d->status === '1' ? '<i class="bi bi-check-circle"></i> Approved'
                                : (($d->status === '0') ? '<i class="bi bi-clock"></i> Pending'
                                    : '<i class="bi bi-clock"></i> Pending')) .
                            '</span>'
                        )
                        : '';
                })


                ->addColumn('action', function ($d) {
                    if ($d->category_type == 1 && $d->stage != 5 || $d->category_type == 2 && $d->stage != 9) {
                        $cedant = DB::table('customers')->where('customer_id', $d->customer_id)->first();

                        return '<a href="#"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . e($d->stage) . '"
                        data-division="' . e($d->divisions) . '"
                        data-opp="' . e($d->opportunity_id) . '"
                        data-category_type="' . e($d->category_type) . '"
                        data-status="' . e($d->status) . '"
                        data-sum-insured-type="' . e($d->sum_insured_type) . '"
                        data-premium="' . e($d->rein_premium) . '"
                        data-sum-insured="' . e($d->effective_sum_insured) . '"
                        data-reins-comm-rate="' . e($d->reins_comm_rate) . '"
                        data-fac-share-offered="' . e($d->fac_share_offered) . '"
                        data-cedant-comm-rate="' . e($d->comm_rate) . '"
                        data-classcode="' . e($d->classcode) . '"t
                        data-classgroup="' . e($d->class_group) . '"
                        data-insured-name="' . e($d->insured_name) . '"
                        data-data-exist-flag="' . e($d->data_exists_flag) . '"
                        data-type-of-bus="' . e($d->type_of_bus) . '"
                        data-cedant="' . e($cedant->name) . '"


                        <i class="bx bx-refresh"></i> Update status
                    </a>';
                    } else if (
                        ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == '') ||
                        ($d->category_type == 2 && $d->stage == 9 && $d->handed_over == '')
                    ) {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . $d->stage . '"
                        data-division="' . $d->divisions . '"
                        data-opp="' . $d->opportunity_id . '"
                        data-category_type="' . $d->category_type . '"
                        data-status="' . $d->status . '"
                        data-sum-insured-type="' . $d->sum_insured_type . '">
                        <i class="bx bx-refresh"></i>Handover</a>';
                    } else if (
                        ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0') ||
                        ($d->category_type == 2 && $d->stage == 9 && $d->handed_over == 'Y' && $d->approval_status === '0')
                    ) {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . $d->stage . '"
                        data-division="' . $d->divisions . '"
                        data-opp="' . $d->opportunity_id . '"
                        data-category_type="' . $d->category_type . '"
                        data-status="' . $d->status . '"
                        data-sum-insured-type="' . $d->sum_insured_type . '">
                        <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == '') {
                        return '<a href="#"
                        class="text-white update_category btn btn-sm btn-dark rounded-pill"
                        data-stage="' . $d->stage . '"
                        data-division="' . $d->divisions . '"
                        data-opp="' . $d->opportunity_id . '">
                        <i class="bx bx-edit-alt"></i>Update Category</a>';
                    }
                })
                ->rawColumns(['edit', 'action1', 'action', 'approval_status'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function update_category(Request $request)
    {
        try {
            $opp_id = $request->opportunity_id;
            $category_type = $request->category_type;

            if (!$opp_id || !$category_type) {
                return redirect()->back()->with('error', 'Invalid data provided');
            }
            $opportunity = PipelineOpportunity::where('opportunity_id', $opp_id)
                ->firstOrFail();
            $opportunity->update([
                'category_type' => $category_type,
                'sales_entry_date' => now(),
                // 'stage_updated_at' => Carbon::now(),
            ]);


            if ($opportunity) {
                return redirect()->back()->with('success', 'Category Type updated successfully');
            } else {
                return redirect()->back()->with('error', 'No record was updated');
            }
        } catch (Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function facultativeOffer(Request $request)
    {
        // dd($request->all());
        // dd($request->prospect);-
        $user = [
            'firstname' => auth()->user()->firstname ?? '',
            'lastname' => auth()->user()->lastname ?? ''
        ];



        $activities = DB::table('pipeline_opportunities as po')
            ->leftJoin('customers as c', function ($join) {
                $join->on(DB::raw("NULLIF(po.customer_id, '')::INTEGER"), '=', 'c.customer_id');
            })
            ->leftJoin('lead_status as ls', 'po.stage', '=', 'ls.id')
            ->leftJoin('reins_division as rd', 'po.divisions', '=', 'rd.division_code')
            ->leftJoin('classes as cl', 'po.classcode', '=', 'cl.class_code')
            ->leftJoin('business_types as bt', 'po.type_of_bus', '=', 'bt.bus_type_id')
            ->select(
                'po.*',
                DB::raw("COALESCE(c.name, 'N/A') AS customer_name"), // Ensures null values return 'N/A'
                'ls.status_name as stage',
                'rd.division_name as division_name',
                'po.cede_premium as cedant_premium',
                'po.rein_premium as reinsurer_premium',
                'cl.class_name as class_name',
                'c.name as customer_name  c.',
                'bt.bus_type_name as type_of_bus'
            )
            ->where('po.opportunity_id', $request->prospect)
            ->get();




        $formattedActivities = $activities->map(function ($d) {
            return [
                'customer_name' => $d->customer_name ?? 'N/A',
                'opportunity_id' => $d->opportunity_id,
                'effective_date' => $d->effective_date,
                'closing_date' => $d->closing_date,
                'insured_name' => $d->insured_name,
                'type_of_bus' => $d->type_of_bus,
                'contact_name' => json_decode($d->contact_name, true) ?? [],
                'email' => json_decode($d->email, true) ?? [],
                'phone' => json_decode($d->phone, true) ?? [],
                'telephone' => json_decode($d->telephone, true) ?? [],
                'class_name' => $d->class_name,
            ];
        });


        $facultativeOfferSchedules = DB::table('quote_schedules')
            ->join('quote_schedule_headers', 'quote_schedules.schedule_id', '=', 'quote_schedule_headers.id')
            ->where('quote_schedules.opportunity_id', $request->prospect)
            ->select('quote_schedules.*', 'quote_schedule_headers.name as schedule_header_name')
            ->get();

        $quoteReinsurers = DB::table('quote_reinsurers as qr')
            ->leftJoin('customers as c', 'qr.reinsurer_id', '=', 'c.customer_id')
            ->leftJoin('customer_contacts as cc', 'qr.reinsurer_id', '=', 'cc.customer_id')
            ->select('qr.*', 'c.name', 'c.email', 'cc.contact_name')
            ->where('qr.opportunity_id', $request->prospect)
            ->get();




        // dd($request->facschedule_details);

        $data = [
            'pipeline_data' => $formattedActivities,
            'user' => $user,
            'facDetailInput' => $facultativeOfferSchedules ?? [],
            'quoteReinsurers' => $quoteReinsurers
        ];

        return response()->json([
            'data' => $data,
            'status' => 200
        ]);
    }

    public function editData(Request $request)
    {
        try {
            $pipeline = $request->pipeline;
            $updated = DB::table('pipeline_opportunities')
                ->where('opportunity_id', $pipeline)
                ->update([
                    'stage' => 1
                ]);
            if ($updated) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Edited Successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No record was updated'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function populate_quotefac_view_data()
    {
        try {
            $leadId = request()->L192;
            $pipeline = DB::table('pipeline_opportunities')
                ->where('opportunity_id', $leadId)
                ->get();
            // Fetch the values from the database
            $underwriters = DB::table('prospect_underwriters')->get();
            $quoteSchedules = DB::table('quote_schedules')->join('quote_schedule_headers', 'quote_schedules.schedule_id', '=', 'quote_schedule_headers.id')
                ->select('quote_schedules.*', 'quote_schedule_headers.name as schedule_header_name')
                ->get();
            $quoteReinsurers = DB::table('quote_reinsurers')->join('customers', 'quote_reinsurers.reinsurer_id', '=', 'customers.customer_id')
                ->select('quote_reinsurers.*', 'customers.contact_name as customer_contact_name')
                ->get();
            $quotes = DB::table('quotes')->join('quote_reinsurers', 'quotes.id', '=', 'quote_reinsurers.id')
                ->select('quotes.*', 'quote_reinsurers.reinsurer_name as reinsurer_name')
                ->get();
            $customerContacts = DB::table('customer_contacts')->join('customers', 'customer_contacts.customer_id', '=', 'customers.customer_id')
                ->select('customer_contacts.*', 'customers.contact_name as customer_name')
                ->get();

            // Prepare the response data
            return response()->json([
                'underwriters' => $underwriters,
                'quote_schedules' => $quoteSchedules,
                'quote_reinsurers' => $quoteReinsurers,
                'quotes' => $quotes,
                'customer_contacts' => $customerContacts,
                'pipeline' => $pipeline
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function prospects_showdocs(Request $request)
    {

        $documents = DB::table('prospect_docs')->where('prospect_id', $request->prospect_id)->get();
        $doc_names = DB::table('edms_documents')->get();
        return Datatables::of($documents)
            ->addColumn('view', function ($documents) {
                return '<button class="btn btn-outline-primary btn-sm view-doc"
                    document_path="' . $documents->file . '"
                    doc_mimetype="' . $documents->mimetype . '"
                   ><span class="fa fa-eye"></span> View
                   </button>';
            })
            ->rawColumns(['view'])
            ->make(true);
    }

    public function pipeline_activity_q1(Request $request)
    {
        // dd($request->all());
        $business_types = $request->business_types;
        try {

            $activities = DB::table('pipeline_opportunities')
                ->leftJoin('handover_approvals', 'pipeline_opportunities.opportunity_id', '=', 'handover_approvals.prospect_id')
                ->where('pipeline_id', $request->pipe_id)
                ->where('stage', '>=', 1)
                ->where('fiscal_period', 1)
                ->whereIn('type_of_bus', $business_types)
                ->get();
            return Datatables::of($activities)
                ->addColumn('customer_name', function ($d): mixed {

                    if (empty($d->customer_id)) {
                        return 'N/A';
                    }

                    $lead = DB::table('customers')
                        ->where('customer_id', (int) $d->customer_id)
                        ->first();

                    return $lead ? $lead->name : 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    if ($d->category_type == 1) {
                        $query = DB::table('lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 1) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }
                        if ($query->category_type == 2) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }

                        // return $query->status_name ;
                    }
                    if ($d->category_type == 2) {
                        $query = DB::table('lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 2) {
                            return $query->status_name;
                        } else {
                            return $query->status_name;
                        }
                    }
                })
                ->addColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division ? $division->division_name : 'N/A';
                })
                ->addColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    if (is_null($business_class)) {
                        return '';
                    }
                    return $business_class ? $business_class->class_name : 'N/A';
                })

                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium, 2, '.', ',');
                })
                ->addColumn('edit', function ($d) {
                    if ($d->category_type && $d->stage != 1 && $d->stage != 5 && $d->stage != 4) {
                        return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Udate proposal" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '"> <i class="bx bx-refresh"></i>Edit</a>';
                    } else {
                    }
                })
                ->addColumn('action1', function ($d) {
                    if ($d->category_type == 1) {
                        return '<span class="badge bg-success">Quotation</span>';
                    }
                    if ($d->category_type == 2) {
                        return '<span class="badge btn-primary" style="font-size: 10px; padding: 2px 5px;">Facultative offer</span>';
                    } else {
                    }
                })

                ->addColumn('approval_status', function ($d) {
                    return ($d->handed_over === 'Y')
                        ? ($d->approval_status === '0'
                            ? '<a href="#" title="click" data-rej-text="' . $d->reason_for_rejection . '" class="btn btn-sm btn-danger  rounded-pill rej-text">
                                <i class="bi bi-x-circle"></i> Rejected
                            </a>'
                            : '<span class="badge ' .
                            ($d->approval_status === '1' ? 'bg-success' : 'bg-warning text-dark') .
                            '"> ' .
                            ($d->approval_status === '1' ? '<i class="bi bi-check-circle"></i> Approved'
                                : (($d->approval_status == null) ? '<i class="bi bi-clock"></i> Pending'
                                    : '<i class="bi bi-clock"></i> Pending')) .
                            '</span>'
                        )
                        : '';
                })


                ->addColumn('action', function ($d) {
                    if ($d->category_type == 1 && $d->stage != 5 || $d->category_type == 2 && $d->stage != 5) {
                        // return '<a href="#" class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Update status</a>';

                        return '<a href="#"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . e($d->stage) . '"
                        data-division="' . e($d->divisions) . '"
                        data-opp="' . e($d->opportunity_id) . '"
                        data-category_type="' . e($d->category_type) . '"
                        data-status="' . e($d->status) . '"
                        data-sum-insured-type="' . e($d->sum_insured_type) . '"
                        data-premium="' . e($d->rein_premium) . '"
                        data-sum-insured="' . e($d->effective_sum_insured) . '"
                        data-reins-comm-rate="' . e($d->reins_comm_rate) . '"
                        data-fac-share-offered="' . e($d->fac_share_offered) . '"
                        data-cedant-comm-rate="' . e($d->comm_rate) . '"
                        data-classcode="' . e($d->classcode) . '"
                        data-classgroup="' . e($d->class_group) . '"
                        data-insured-name="' . e($d->insured_name) . '"
                        data-data-exist-flag="' . e($d->data_exists_flag) . '"
                        data-type-of-bus="' . e($d->type_of_bus) . '"


                        <i class="bx bx-refresh"></i> Update status
                    </a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == '' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == '') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == '') {
                        return '<a href="#" class="text-white update_category btn btn-sm btn-dark rounded-pill"  data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '"> <i class="bx bx-edit-alt"></i>Update Category</a>';
                    }
                })
                ->rawColumns(['edit', 'action1', 'action', 'approval_status'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function pipeline_activity_q1_treaty(Request $request)
    {
        // dd($request->all());
        $business_types = $request->business_types;
        try {

            $activities = DB::table('pipeline_opportunities')
                ->leftJoin('handover_approvals', 'pipeline_opportunities.opportunity_id', '=', 'handover_approvals.prospect_id')
                ->leftJoin('tenders', 'pipeline_opportunities.opportunity_id', '=', 'tenders.prospect_id')
                ->leftJoin('tender_approvals', 'tenders.tender_no', '=', 'tender_approvals.tender_no')
                ->where('pipeline_id', $request->pipe_id)
                ->where('stage', '>=', 1)
                ->whereIn('type_of_bus', $business_types)
                ->get();
            return Datatables::of($activities)
                ->addColumn('customer_name', function ($d): mixed {

                    if (empty($d->customer_id)) {
                        return 'N/A';
                    }

                    $lead = DB::table('customers')
                        ->where('customer_id', (int) $d->customer_id)
                        ->first();

                    return $lead ? $lead->name : 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    if ($d->category_type == 1) {
                        $query = DB::table('treaty_lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 1) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }
                        if ($query->category_type == 2) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }

                        // return $query->status_name ;
                    }
                    if ($d->category_type == 2) {
                        $query = DB::table('treaty_lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 2) {
                            return $query->status_name;
                        } else {
                            return $query->status_name;
                        }
                    }
                })
                ->addColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division ? $division->division_name : 'N/A';
                })
                ->addColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    if (is_null($business_class)) {
                        return '';
                    }
                    return $business_class ? $business_class->class_name : 'N/A';
                })

                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium, 2, '.', ',');
                })
                ->addColumn('edit', function ($d) {
                    if ($d->category_type && $d->stage != 1 && $d->stage != 5 && $d->stage != 4) {
                        return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Udate proposal" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '"> <i class="bx bx-refresh"></i>Edit</a>';
                    } else {
                    }
                })
                ->addColumn('action1', function ($d) {
                    if ($d->category_type == 1) {
                        return '<span class="badge bg-success">Normal</span>';
                    }
                    if ($d->category_type == 2) {
                        return '<span class="badge btn-primary" style="font-size: 10px; padding: 2px 5px;">Tender</span>';
                    } else {
                    }
                })

                ->addColumn('approval_status', function ($d) {
                    return ($d->stage == 1 && $d->category_type == 2)
                        ? ($d->status === '2'
                            ? '<a href="#" title="click" data-rej-text="' . $d->remarks . '" class="btn btn-sm btn-danger  rounded-pill rej-text">
                                <i class="bi bi-x-circle"></i> Rejected
                            </a>'
                            : '<span class="badge ' .
                            ($d->status === '1' ? 'bg-success' : 'bg-warning text-dark') .
                            '"> ' .
                            ($d->status === '1' ? '<i class="bi bi-check-circle"></i> Approved'
                                : (($d->status == 0) ? '<i class="bi bi-clock"></i> Pending'
                                    : '<i class="bi bi-clock"></i> Pending')) .
                            '</span>'
                        )
                        : '';
                })


                ->addColumn('action', function ($d) {
                    if ($d->category_type == 1 && $d->stage != 5 || $d->category_type == 2 && $d->stage != 5) {
                        // return '<a href="#" class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Update status</a>';

                        return '<a href="#"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . e($d->stage) . '"
                        data-division="' . e($d->divisions) . '"
                        data-opp="' . e($d->opportunity_id) . '"
                        data-category_type="' . e($d->category_type) . '"
                        data-status="' . e($d->status) . '"
                        data-sum-insured-type="' . e($d->sum_insured_type) . '"
                        data-premium="' . e($d->rein_premium) . '"
                        data-sum-insured="' . e($d->effective_sum_insured) . '"
                        data-reins-comm-rate="' . e($d->reins_comm_rate) . '"
                        data-fac-share-offered="' . e($d->fac_share_offered) . '"
                        data-cedant-comm-rate="' . e($d->comm_rate) . '"
                        data-classcode="' . e($d->classcode) . '"
                        data-classgroup="' . e($d->class_group) . '"
                        data-insured-name="' . e($d->insured_name) . '"
                        data-data-exist-flag="' . e($d->data_exists_flag) . '"
                        data-type-of-bus="' . e($d->type_of_bus) . '"


                        <i class="bx bx-refresh"></i> Update status
                    </a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == '' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == '') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == '') {
                        return '<a href="#" class="text-white update_category btn btn-sm btn-dark rounded-pill"  data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '"> <i class="bx bx-edit-alt"></i>Update Category</a>';
                    }
                })
                ->rawColumns(['edit', 'action1', 'action', 'approval_status'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function pipeline_activity_q2(Request $request)
    {
        try {
            $business_types = $request->business_types;
            $activities = DB::table('pipeline_opportunities')
                ->leftJoin('handover_approvals', 'pipeline_opportunities.opportunity_id', '=', 'handover_approvals.prospect_id')
                ->where('pipeline_id', $request->pipe_id)
                ->where('stage', '>=', 1)
                ->where('fiscal_period', 2)
                ->whereIn('type_of_bus', $business_types)
                ->get();
            return Datatables::of($activities)
                ->addColumn('customer_name', function ($d): mixed {

                    if (empty($d->customer_id)) {
                        return 'N/A';
                    }

                    $lead = DB::table('customers')
                        ->where('customer_id', (int) $d->customer_id)
                        ->first();

                    return $lead ? $lead->name : 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    if ($d->category_type == 1) {
                        $query = DB::table('lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 1) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }
                        if ($query->category_type == 2) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }

                        // return $query->status_name ;
                    }
                    if ($d->category_type == 2) {
                        $query = DB::table('lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 2) {
                            return $query->status_name;
                        } else {
                            return $query->status_name;
                        }
                    }
                })
                ->addColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division ? $division->division_name : 'N/A';
                })
                ->addColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    if (is_null($business_class)) {
                        return '';
                    }
                    return $business_class ? $business_class->class_name : 'N/A';
                })

                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium, 2, '.', ',');
                })
                ->addColumn('edit', function ($d) {
                    if ($d->category_type && $d->stage != 1 && $d->stage != 5 && $d->stage != 4) {
                        return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Udate proposal" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '"> <i class="bx bx-refresh"></i>Edit</a>';
                    } else {
                    }
                })
                ->addColumn('action1', function ($d) {
                    if ($d->category_type == 1) {
                        return '<span class="badge bg-success">Quotation</span>';
                    }
                    if ($d->category_type == 2) {
                        return '<span class="badge btn-primary" style="font-size: 10px; padding: 2px 5px;">Facultative offer</span>';
                    } else {
                    }
                })

                ->addColumn('approval_status', function ($d) {
                    return ($d->handed_over === 'Y')
                        ? ($d->approval_status === '0'
                            ? '<a href="#" title="click" data-rej-text="' . $d->reason_for_rejection . '" class="btn btn-sm btn-danger  rounded-pill rej-text">
                                <i class="bi bi-x-circle"></i> Rejected
                            </a>'
                            : '<span class="badge ' .
                            ($d->approval_status === '1' ? 'bg-success' : 'bg-warning text-dark') .
                            '"> ' .
                            ($d->approval_status === '1' ? '<i class="bi bi-check-circle"></i> Approved'
                                : (($d->approval_status == null) ? '<i class="bi bi-clock"></i> Pending'
                                    : '<i class="bi bi-clock"></i> Pending')) .
                            '</span>'
                        )
                        : '';
                })


                ->addColumn('action', function ($d) {
                    if ($d->category_type == 1 && $d->stage != 5 || $d->category_type == 2 && $d->stage != 5) {
                        // return '<a href="#" class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Update status</a>';

                        return '<a href="#"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . e($d->stage) . '"
                        data-division="' . e($d->divisions) . '"
                        data-opp="' . e($d->opportunity_id) . '"
                        data-category_type="' . e($d->category_type) . '"
                        data-status="' . e($d->status) . '"
                        data-sum-insured-type="' . e($d->sum_insured_type) . '"
                        data-premium="' . e($d->rein_premium) . '"
                        data-sum-insured="' . e($d->effective_sum_insured) . '"
                        data-reins-comm-rate="' . e($d->reins_comm_rate) . '"
                        data-fac-share-offered="' . e($d->fac_share_offered) . '"
                        data-cedant-comm-rate="' . e($d->comm_rate) . '"
                        data-classcode="' . e($d->classcode) . '"
                        data-classgroup="' . e($d->class_group) . '"
                        data-insured-name="' . e($d->insured_name) . '"
                        data-data-exist-flag="' . e($d->data_exists_flag) . '"
                        data-type-of-bus="' . e($d->type_of_bus) . '"


                        <i class="bx bx-refresh"></i> Update status
                    </a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == '' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == '') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == '') {
                        return '<a href="#" class="text-white update_category btn btn-sm btn-dark rounded-pill"  data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '"> <i class="bx bx-edit-alt"></i>Update Category</a>';
                    }
                })
                ->rawColumns(['edit', 'action1', 'action', 'approval_status'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function pipeline_activity_q2_treaty(Request $request)
    {
        try {
            $business_types = $request->business_types;
            $activities = DB::table('pipeline_opportunities')
                ->leftJoin('handover_approvals', 'pipeline_opportunities.opportunity_id', '=', 'handover_approvals.prospect_id')
                ->leftJoin('tenders', 'pipeline_opportunities.opportunity_id', '=', 'tenders.prospect_id')
                ->leftJoin('tender_approvals', 'tenders.tender_no', '=', 'tender_approvals.tender_no')
                ->where('pipeline_id', $request->pipe_id)
                ->where('stage', '>=', 1)
                ->where('fiscal_period', 2)
                ->whereIn('type_of_bus', $business_types)
                ->get();
            return Datatables::of($activities)
                ->addColumn('customer_name', function ($d): mixed {

                    if (empty($d->customer_id)) {
                        return 'N/A';
                    }

                    $lead = DB::table('customers')
                        ->where('customer_id', (int) $d->customer_id)
                        ->first();

                    return $lead ? $lead->name : 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    if ($d->category_type == 1) {
                        $query = DB::table('treaty_lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 1) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }
                        if ($query->category_type == 2) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }

                        // return $query->status_name ;
                    }
                    if ($d->category_type == 2) {
                        $query = DB::table('treaty_lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 2) {
                            return $query->status_name;
                        } else {
                            return $query->status_name;
                        }
                    }
                })
                ->addColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division ? $division->division_name : 'N/A';
                })
                ->addColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    if (is_null($business_class)) {
                        return '';
                    }
                    return $business_class ? $business_class->class_name : 'N/A';
                })

                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium, 2, '.', ',');
                })
                ->addColumn('edit', function ($d) {
                    if ($d->category_type && $d->stage != 1 && $d->stage != 5 && $d->stage != 4) {
                        return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Udate proposal" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '"> <i class="bx bx-refresh"></i>Edit</a>';
                    } else {
                    }
                })
                ->addColumn('action1', function ($d) {
                    if ($d->category_type == 1) {
                        return '<span class="badge bg-success">Normal</span>';
                    }
                    if ($d->category_type == 2) {
                        return '<span class="badge btn-primary" style="font-size: 10px; padding: 2px 5px;">Tender</span>';
                    } else {
                    }
                })

                ->addColumn('approval_status', function ($d) {
                    return ($d->stage == 1 && $d->category_type == 2)
                        ? ($d->status === '2'
                            ? '<a href="#" title="click" data-rej-text="' . $d->remarks . '" class="btn btn-sm btn-danger  rounded-pill rej-text">
                                <i class="bi bi-x-circle"></i> Rejected
                            </a>'
                            : '<span class="badge ' .
                            ($d->status === '1' ? 'bg-success' : 'bg-warning text-dark') .
                            '"> ' .
                            ($d->status === '1' ? '<i class="bi bi-check-circle"></i> Approved'
                                : (($d->status == 0) ? '<i class="bi bi-clock"></i> Pending'
                                    : '<i class="bi bi-clock"></i> Pending')) .
                            '</span>'
                        )
                        : '';
                })


                ->addColumn('action', function ($d) {
                    if ($d->category_type == 1 && $d->stage != 5 || $d->category_type == 2 && $d->stage != 5) {
                        // return '<a href="#" class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Update status</a>';

                        return '<a href="#"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . e($d->stage) . '"
                        data-division="' . e($d->divisions) . '"
                        data-opp="' . e($d->opportunity_id) . '"
                        data-category_type="' . e($d->category_type) . '"
                        data-status="' . e($d->status) . '"
                        data-sum-insured-type="' . e($d->sum_insured_type) . '"
                        data-premium="' . e($d->rein_premium) . '"
                        data-sum-insured="' . e($d->effective_sum_insured) . '"
                        data-reins-comm-rate="' . e($d->reins_comm_rate) . '"
                        data-fac-share-offered="' . e($d->fac_share_offered) . '"
                        data-cedant-comm-rate="' . e($d->comm_rate) . '"
                        data-classcode="' . e($d->classcode) . '"
                        data-classgroup="' . e($d->class_group) . '"
                        data-insured-name="' . e($d->insured_name) . '"
                        data-data-exist-flag="' . e($d->data_exists_flag) . '"
                        data-type-of-bus="' . e($d->type_of_bus) . '"


                        <i class="bx bx-refresh"></i> Update status
                    </a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == '' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == '') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == '') {
                        return '<a href="#" class="text-white update_category btn btn-sm btn-dark rounded-pill"  data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '"> <i class="bx bx-edit-alt"></i>Update Category</a>';
                    }
                })
                ->rawColumns(['edit', 'action1', 'action', 'approval_status'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function pipeline_activity_q3(Request $request)
    {
        try {
            $business_types = $request->business_types;

            $activities = DB::table('pipeline_opportunities')
                ->leftJoin('handover_approvals', 'pipeline_opportunities.opportunity_id', '=', 'handover_approvals.prospect_id')
                ->where('pipeline_id', $request->pipe_id)
                ->where('stage', '>=', 1)
                ->where('fiscal_period', operator: 3)
                ->whereIn('type_of_bus', $request->business_types)
                ->get();

            return Datatables::of($activities)
                ->addColumn('customer_name', function ($d): mixed {

                    if (empty($d->customer_id)) {
                        return 'N/A';
                    }

                    $lead = DB::table('customers')
                        ->where('customer_id', (int) $d->customer_id)
                        ->first();

                    return $lead ? $lead->name : 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    if ($d->category_type == 1) {
                        $query = DB::table('lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 1) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }
                        if ($query->category_type == 2) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }

                        // return $query->status_name ;
                    }
                    if ($d->category_type == 2) {
                        $query = DB::table('lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 2) {
                            return $query->status_name;
                        } else {
                            return $query->status_name;
                        }
                    }
                })
                ->addColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division ? $division->division_name : 'N/A';
                })
                ->addColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    if (is_null($business_class)) {
                        return '';
                    }
                    return $business_class ? $business_class->class_name : 'N/A';
                })

                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium, 2, '.', ',');
                })
                ->addColumn('edit', function ($d) {
                    if ($d->category_type && $d->stage != 1 && $d->stage != 5 && $d->stage != 4) {
                        return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Udate proposal" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '"> <i class="bx bx-refresh"></i>Edit</a>';
                    } else {
                    }
                })
                ->addColumn('action1', function ($d) {
                    if ($d->category_type == 1) {
                        return '<span class="badge bg-success">Quotation</span>';
                    }
                    if ($d->category_type == 2) {
                        return '<span class="badge btn-primary" style="font-size: 10px; padding: 2px 5px;">Facultative offer</span>';
                    } else {
                    }
                })

                ->addColumn('approval_status', function ($d) {
                    return ($d->handed_over === 'Y')
                        ? ($d->approval_status === '0'
                            ? '<a href="#" title="click" data-rej-text="' . $d->reason_for_rejection . '" class="btn btn-sm btn-danger  rounded-pill rej-text">
                                <i class="bi bi-x-circle"></i> Rejected
                            </a>'
                            : '<span class="badge ' .
                            ($d->approval_status === '1' ? 'bg-success' : 'bg-warning text-dark') .
                            '"> ' .
                            ($d->approval_status === '1' ? '<i class="bi bi-check-circle"></i> Approved'
                                : (($d->approval_status == null) ? '<i class="bi bi-clock"></i> Pending'
                                    : '<i class="bi bi-clock"></i> Pending')) .
                            '</span>'
                        )
                        : '';
                })


                ->addColumn('action', function ($d) {
                    if ($d->category_type == 1 && $d->stage != 5 || $d->category_type == 2 && $d->stage != 5) {
                        // return '<a href="#" class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Update status</a>';

                        return '<a href="#"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . e($d->stage) . '"
                        data-division="' . e($d->divisions) . '"
                        data-opp="' . e($d->opportunity_id) . '"
                        data-category_type="' . e($d->category_type) . '"
                        data-status="' . e($d->status) . '"
                        data-sum-insured-type="' . e($d->sum_insured_type) . '"
                        data-premium="' . e($d->rein_premium) . '"
                        data-sum-insured="' . e($d->effective_sum_insured) . '"
                        data-reins-comm-rate="' . e($d->reins_comm_rate) . '"
                        data-fac-share-offered="' . e($d->fac_share_offered) . '"
                        data-cedant-comm-rate="' . e($d->comm_rate) . '"
                        data-classcode="' . e($d->classcode) . '"
                        data-classgroup="' . e($d->class_group) . '"
                        data-insured-name="' . e($d->insured_name) . '"
                        data-data-exist-flag="' . e($d->data_exists_flag) . '"
                        data-type-of-bus="' . e($d->type_of_bus) . '"


                        <i class="bx bx-refresh"></i> Update status
                    </a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == '' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == '') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == '') {
                        return '<a href="#" class="text-white update_category btn btn-sm btn-dark rounded-pill"  data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '"> <i class="bx bx-edit-alt"></i>Update Category</a>';
                    }
                })
                ->rawColumns(['edit', 'action1', 'action', 'approval_status'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function pipeline_activity_q3_treaty(Request $request)
    {
        try {
            $business_types = $request->business_types;

            $activities = DB::table('pipeline_opportunities')
                ->leftJoin('handover_approvals', 'pipeline_opportunities.opportunity_id', '=', 'handover_approvals.prospect_id')
                ->leftJoin('tenders', 'pipeline_opportunities.opportunity_id', '=', 'tenders.prospect_id')
                ->leftJoin('tender_approvals', 'tenders.tender_no', '=', 'tender_approvals.tender_no')
                ->where('pipeline_id', $request->pipe_id)
                ->where('stage', '>=', 1)
                ->where('fiscal_period', 3)
                ->whereIn('type_of_bus', $business_types)
                ->get();

            return Datatables::of($activities)
                ->addColumn('customer_name', function ($d): mixed {

                    if (empty($d->customer_id)) {
                        return 'N/A';
                    }

                    $lead = DB::table('customers')
                        ->where('customer_id', (int) $d->customer_id)
                        ->first();

                    return $lead ? $lead->name : 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    if ($d->category_type == 1) {
                        $query = DB::table('treaty_lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 1) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }
                        if ($query->category_type == 2) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }

                        // return $query->status_name ;
                    }
                    if ($d->category_type == 2) {
                        $query = DB::table('treaty_lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 2) {
                            return $query->status_name;
                        } else {
                            return $query->status_name;
                        }
                    }
                })
                ->addColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division ? $division->division_name : 'N/A';
                })
                ->addColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    if (is_null($business_class)) {
                        return '';
                    }
                    return $business_class ? $business_class->class_name : 'N/A';
                })

                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium, 2, '.', ',');
                })
                ->addColumn('edit', function ($d) {
                    if ($d->category_type && $d->stage != 1 && $d->stage != 5 && $d->stage != 4) {
                        return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Udate proposal" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '"> <i class="bx bx-refresh"></i>Edit</a>';
                    } else {
                    }
                })
                ->addColumn('action1', function ($d) {
                    if ($d->category_type == 1) {
                        return '<span class="badge bg-success">Normal</span>';
                    }
                    if ($d->category_type == 2) {
                        return '<span class="badge btn-primary" style="font-size: 10px; padding: 2px 5px;">Tender</span>';
                    } else {
                    }
                })

                ->addColumn('approval_status', function ($d) {
                    return ($d->stage == 1 && $d->category_type == 2)
                        ? ($d->status === '2'
                            ? '<a href="#" title="click" data-rej-text="' . $d->remarks . '" class="btn btn-sm btn-danger  rounded-pill rej-text">
                                <i class="bi bi-x-circle"></i> Rejected
                            </a>'
                            : '<span class="badge ' .
                            ($d->status === '1' ? 'bg-success' : 'bg-warning text-dark') .
                            '"> ' .
                            ($d->status === '1' ? '<i class="bi bi-check-circle"></i> Approved'
                                : (($d->status == 0) ? '<i class="bi bi-clock"></i> Pending'
                                    : '<i class="bi bi-clock"></i> Pending')) .
                            '</span>'
                        )
                        : '';
                })


                ->addColumn('action', function ($d) {
                    if ($d->category_type == 1 && $d->stage != 5 || $d->category_type == 2 && $d->stage != 5) {
                        // return '<a href="#" class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Update status</a>';

                        return '<a href="#"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . e($d->stage) . '"
                        data-division="' . e($d->divisions) . '"
                        data-opp="' . e($d->opportunity_id) . '"
                        data-category_type="' . e($d->category_type) . '"
                        data-status="' . e($d->status) . '"
                        data-sum-insured-type="' . e($d->sum_insured_type) . '"
                        data-premium="' . e($d->rein_premium) . '"
                        data-sum-insured="' . e($d->effective_sum_insured) . '"
                        data-reins-comm-rate="' . e($d->reins_comm_rate) . '"
                        data-fac-share-offered="' . e($d->fac_share_offered) . '"
                        data-cedant-comm-rate="' . e($d->comm_rate) . '"
                        data-classcode="' . e($d->classcode) . '"
                        data-classgroup="' . e($d->class_group) . '"
                        data-insured-name="' . e($d->insured_name) . '"
                        data-data-exist-flag="' . e($d->data_exists_flag) . '"
                        data-type-of-bus="' . e($d->type_of_bus) . '"


                        <i class="bx bx-refresh"></i> Update status
                    </a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == '' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == '') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == '') {
                        return '<a href="#" class="text-white update_category btn btn-sm btn-dark rounded-pill"  data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '"> <i class="bx bx-edit-alt"></i>Update Category</a>';
                    }
                })
                ->rawColumns(['edit', 'action1', 'action', 'approval_status'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function pipeline_activity_q4(Request $request)
    {
        try {
            $business_types = $request->business_types;

            $activities = DB::table('pipeline_opportunities')
                ->leftJoin('handover_approvals', 'pipeline_opportunities.opportunity_id', '=', 'handover_approvals.prospect_id')
                ->where('pipeline_id', $request->pipe_id)
                ->where('stage', '>=', 1)
                ->where('fiscal_period', 4)
                ->whereIn('type_of_bus', $business_types)
                ->get();

            return Datatables::of($activities)
                ->addColumn('customer_name', function ($d): mixed {

                    if (empty($d->customer_id)) {
                        return 'N/A';
                    }

                    $lead = DB::table('customers')
                        ->where('customer_id', (int) $d->customer_id)
                        ->first();

                    return $lead ? $lead->name : 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    if ($d->category_type == 1) {
                        $query = DB::table('lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 1) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }
                        if ($query->category_type == 2) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }

                        // return $query->status_name ;
                    }
                    if ($d->category_type == 2) {
                        $query = DB::table('lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 2) {
                            return $query->status_name;
                        } else {
                            return $query->status_name;
                        }
                    }
                })
                ->addColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division ? $division->division_name : 'N/A';
                })
                ->addColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    if (is_null($business_class)) {
                        return '';
                    }
                    return $business_class ? $business_class->class_name : 'N/A';
                })

                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium, 2, '.', ',');
                })
                ->addColumn('edit', function ($d) {
                    if ($d->category_type && $d->stage != 1 && $d->stage != 5 && $d->stage != 4) {
                        return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Udate proposal" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '"> <i class="bx bx-refresh"></i>Edit</a>';
                    } else {
                    }
                })
                ->addColumn('action1', function ($d) {
                    if ($d->category_type == 1) {
                        return '<span class="badge bg-success">Quotation</span>';
                    }
                    if ($d->category_type == 2) {
                        return '<span class="badge btn-primary" style="font-size: 10px; padding: 2px 5px;">Facultative offer</span>';
                    } else {
                    }
                })

                ->addColumn('approval_status', function ($d) {
                    return ($d->handed_over === 'Y')
                        ? ($d->approval_status === '0'
                            ? '<a href="#" title="click" data-rej-text="' . $d->reason_for_rejection . '" class="btn btn-sm btn-danger  rounded-pill rej-text">
                                <i class="bi bi-x-circle"></i> Rejected
                            </a>'
                            : '<span class="badge ' .
                            ($d->approval_status === '1' ? 'bg-success' : 'bg-warning text-dark') .
                            '"> ' .
                            ($d->approval_status === '1' ? '<i class="bi bi-check-circle"></i> Approved'
                                : (($d->approval_status == null) ? '<i class="bi bi-clock"></i> Pending'
                                    : '<i class="bi bi-clock"></i> Pending')) .
                            '</span>'
                        )
                        : '';
                })


                ->addColumn('action', function ($d) {
                    if ($d->category_type == 1 && $d->stage != 5 || $d->category_type == 2 && $d->stage != 5) {
                        // return '<a href="#" class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Update status</a>';

                        return '<a href="#"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . e($d->stage) . '"
                        data-division="' . e($d->divisions) . '"
                        data-opp="' . e($d->opportunity_id) . '"
                        data-category_type="' . e($d->category_type) . '"
                        data-status="' . e($d->status) . '"
                        data-sum-insured-type="' . e($d->sum_insured_type) . '"
                        data-premium="' . e($d->rein_premium) . '"
                        data-sum-insured="' . e($d->effective_sum_insured) . '"
                        data-reins-comm-rate="' . e($d->reins_comm_rate) . '"
                        data-fac-share-offered="' . e($d->fac_share_offered) . '"
                        data-cedant-comm-rate="' . e($d->comm_rate) . '"
                        data-classcode="' . e($d->classcode) . '"
                        data-classgroup="' . e($d->class_group) . '"
                        data-insured-name="' . e($d->insured_name) . '"
                        data-data-exist-flag="' . e($d->data_exists_flag) . '"
                        data-type-of-bus="' . e($d->type_of_bus) . '"


                        <i class="bx bx-refresh"></i> Update status
                    </a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == '' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == '') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == '') {
                        return '<a href="#" class="text-white update_category btn btn-sm btn-dark rounded-pill"  data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '"> <i class="bx bx-edit-alt"></i>Update Category</a>';
                    }
                })
                ->rawColumns(['edit', 'action1', 'action', 'approval_status'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function pipeline_activity_q4_treaty(Request $request)
    {
        try {
            $business_types = $request->business_types;

            $activities = DB::table('pipeline_opportunities')
                ->leftJoin('handover_approvals', 'pipeline_opportunities.opportunity_id', '=', 'handover_approvals.prospect_id')
                ->leftJoin('tenders', 'pipeline_opportunities.opportunity_id', '=', 'tenders.prospect_id')
                ->leftJoin('tender_approvals', 'tenders.tender_no', '=', 'tender_approvals.tender_no')
                ->where('pipeline_id', $request->pipe_id)
                ->where('stage', '>=', 1)
                ->where('fiscal_period', 4)
                ->whereIn('type_of_bus', $business_types)
                ->get();
            return Datatables::of($activities)
                ->addColumn('customer_name', function ($d): mixed {

                    if (empty($d->customer_id)) {
                        return 'N/A';
                    }

                    $lead = DB::table('customers')
                        ->where('customer_id', (int) $d->customer_id)
                        ->first();

                    return $lead ? $lead->name : 'N/A';
                })
                ->editColumn('stage', function ($d) {
                    if ($d->category_type == 1) {
                        $query = DB::table('treaty_lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 1) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }
                        if ($query->category_type == 2) {
                            if ($d->handed_over == 'Y') {
                                return $query->status_name . ' (handed ov)';
                            }
                            return $query->status_name;
                        }

                        // return $query->status_name ;
                    }
                    if ($d->category_type == 2) {
                        $query = DB::table('treaty_lead_status')
                            ->where('id', $d->stage)
                            ->where('category_type', $d->category_type)
                            ->first();

                        if (is_null($query)) {
                            return '';
                        }
                        if ($query->category_type == 2) {
                            return $query->status_name;
                        } else {
                            return $query->status_name;
                        }
                    }
                })
                ->addColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division ? $division->division_name : 'N/A';
                })
                ->addColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    if (is_null($business_class)) {
                        return '';
                    }
                    return $business_class ? $business_class->class_name : 'N/A';
                })

                ->addColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->addColumn('reinsurer_premium', function ($d) {
                    return number_format($d->rein_premium, 2, '.', ',');
                })
                ->addColumn('edit', function ($d) {
                    if ($d->category_type && $d->stage != 1 && $d->stage != 5 && $d->stage != 4) {
                        return '<a href="#" class="text-white update_proposal btn btn-sm btn-success rounded-pill" title="Udate proposal" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '"> <i class="bx bx-refresh"></i>Edit</a>';
                    } else {
                    }
                })
                ->addColumn('action1', function ($d) {
                    if ($d->category_type == 1) {
                        return '<span class="badge bg-success">Normal</span>';
                    }
                    if ($d->category_type == 2) {
                        return '<span class="badge btn-primary" style="font-size: 10px; padding: 2px 5px;">Tender</span>';
                    } else {
                    }
                })

                ->addColumn('approval_status', function ($d) {
                    return ($d->stage == 1 && $d->category_type == 2)
                        ? ($d->status === '2'
                            ? '<a href="#" title="click" data-rej-text="' . $d->remarks . '" class="btn btn-sm btn-danger  rounded-pill rej-text">
                                <i class="bi bi-x-circle"></i> Rejected
                            </a>'
                            : '<span class="badge ' .
                            ($d->status === '1' ? 'bg-success' : 'bg-warning text-dark') .
                            '"> ' .
                            ($d->status === '1' ? '<i class="bi bi-check-circle"></i> Approved'
                                : (($d->status == 0) ? '<i class="bi bi-clock"></i> Pending'
                                    : '<i class="bi bi-clock"></i> Pending')) .
                            '</span>'
                        )
                        : '';
                })


                ->addColumn('action', function ($d) {
                    if ($d->category_type == 1 && $d->stage != 5 || $d->category_type == 2 && $d->stage != 5) {
                        // return '<a href="#" class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Update status</a>';

                        return '<a href="#"
                        class="text-white update_status btn btn-sm btn-success rounded-pill"
                        title="Update status"
                        data-stage="' . e($d->stage) . '"
                        data-division="' . e($d->divisions) . '"
                        data-opp="' . e($d->opportunity_id) . '"
                        data-category_type="' . e($d->category_type) . '"
                        data-status="' . e($d->status) . '"
                        data-sum-insured-type="' . e($d->sum_insured_type) . '"
                        data-premium="' . e($d->rein_premium) . '"
                        data-sum-insured="' . e($d->effective_sum_insured) . '"
                        data-reins-comm-rate="' . e($d->reins_comm_rate) . '"
                        data-fac-share-offered="' . e($d->fac_share_offered) . '"
                        data-cedant-comm-rate="' . e($d->comm_rate) . '"
                        data-classcode="' . e($d->classcode) . '"
                        data-classgroup="' . e($d->class_group) . '"
                        data-insured-name="' . e($d->insured_name) . '"
                        data-data-exist-flag="' . e($d->data_exists_flag) . '"
                        data-type-of-bus="' . e($d->type_of_bus) . '"


                        <i class="bx bx-refresh"></i> Update status
                    </a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == '' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == '') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == 1 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0' || $d->category_type == 2 && $d->stage == 5 && $d->handed_over == 'Y' && $d->approval_status === '0') {
                        return '<a href="' . route('lead.handover', ['prospect' => $d->opportunity_id, 'approval' => 0]) . '"  class="text-white update_status btn btn-sm btn-success rounded-pill" title="Update status" data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '" data-category_type="' . $d->category_type . '" " data-status="' . $d->status . '" data-sum-insured-type="' . $d->sum_insured_type . '"> <i class="bx bx-refresh"></i>Handover</a>';
                    } else if ($d->category_type == '') {
                        return '<a href="#" class="text-white update_category btn btn-sm btn-dark rounded-pill"  data-stage="' . $d->stage . '" data-division="' . $d->divisions . '" data-opp="' . $d->opportunity_id . '"> <i class="bx bx-edit-alt"></i>Update Category</a>';
                    }
                })
                ->rawColumns(['edit', 'action1', 'action', 'approval_status'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function updateLeadStatus(Request $request)
    {
        // dd($request->all());
        $uploadsPath = 'uploads';

        try {

            DB::beginTransaction();
            $leadId = $request->opp_id;
            $pipeline = $request->pip_id;
            $stage_cycle = $request->stage_cycle;
            $stage_cycle_fac = $request->stage_cycle_fac;
            $division = $request->division;
            $underwriters = $request->underwriters;
            $customer_id = $request->customer_id;
            // dd($customer_id);
            $customer_name = $request->customer_name;
            $customer_email = $request->customer_email;
            $selected_contact_person_main = $request->selected_contact_person_main;
            $contact_name = $request->contact_name;
            $schedule_details = !empty($request->schedule_details) ? ($request->schedule_details) : [];
            $facschedule_details = !empty($request->facschedule_details) ? ($request->facschedule_details) : [];



            $declineUncheckedCount = $request->declineUncheckedCount ?? null;
            $document_name = $request->document_name;
            $ced_checkbox_docs = $request->cedant_checkbox_docs ?? [];
            $our_checkbox_docs = $request->our_checkbox_docs ?? [];
            $received_docs_checkboxes = $request->received_docs_checkboxes ?? [];

            $checkbox_docs = [];
            // Ensure both arrays are defined and are arrays before merging

            if ($request->bus_type == 'TRT') {
                $newRequest = new Request($request->all());
                $this->treaty_pipeline_create_opportunity($newRequest);
                $checkbox_docs = array_merge(
                    array_map(function ($name) {
                        return ['name' => $name, 'source' => 1];
                    }, $ced_checkbox_docs), //document required by cedant
                    array_map(function ($name) {
                        return ['name' => $name, 'source' => 2];
                    }, $our_checkbox_docs), //document requred by us
                    array_map(function ($name) {
                        return ['name' => $name, 'source' => 3];
                    }, $received_docs_checkboxes) //document received
                );
            }

            $send_email_flag = $request->send_email_flag;

            $pq_status = $request->pq_status;
            $status = $request->status;
            $query_text = $request->query_text;
            $decline_negotiation_text = $request->decline_negotiation_text;

            if ($schedule_details !== null) {
                foreach ($schedule_details as &$item) {
                    if (isset($item['amount'])) {
                        $item['details'] = $item['amount'];
                        unset($item['amount']);
                    }
                }
            }
            unset($item);
            $manipulated_schedule_details = $schedule_details ?? [];

            if ($facschedule_details !== null) {
                foreach ($facschedule_details as &$item2) {
                    if (isset($item2['amount'])) {
                        $item2['details'] = $item2['amount'];
                        unset($item2['amount']);
                    }
                }
            }
            unset($item2);
            $manipulated_facschedule_details = $facschedule_details ?? [];


            //*********update pipeline_opportunities***********
            $all_schedule_details = array_merge($manipulated_schedule_details ?? [], $manipulated_facschedule_details ?? []);
            $validNames = [
                'FIRST LOSS',
                'LIMIT OF INDEMNITY',
                'MAXIMUM LOSS LIMIT',
                'LIMIT OF LIABILITY',
                'Agreed Value',
                'Total Sum insured',
                'TOP LOCATION'
            ];
            // dd($all_schedule_details);

            $effective_sum_insured = null;
            $rein_premium = null;
            $reins_comm_rate = null;
            $comm_rate = null;
            $unplaced_share = $request->unplaced ?? null;
            foreach ($all_schedule_details as $detail) {
                if (!empty($detail['name']) && in_array($detail['name'], $validNames) && !empty($detail['details'])) {
                    $effective_sum_insured = $detail['details'];
                }
                if ($detail['name'] === 'Premium') {
                    $rein_premium = $detail['details'];
                }
                if ($detail['name'] === 'Reinsurer Commission Rate') {
                    $reins_comm_rate = $detail['details'];
                }
                if ($detail['name'] === 'Cedant Commission Rate') {
                    $comm_rate = $detail['details'];
                }
            }

            // only update if the value is not empty
            $updateData = [];
            switch ($request->bus_type) {
                case 'FAC':
                    if ($rein_premium)
                        $updateData['rein_premium'] = str_replace(',', '', $rein_premium);
                    if ($reins_comm_rate)
                        $updateData['reins_comm_rate'] = str_replace(',', '', $reins_comm_rate);
                    if ($comm_rate)
                        $updateData['comm_rate'] = str_replace(',', '', $comm_rate);
                    if ($effective_sum_insured)
                        $updateData['effective_sum_insured'] = str_replace(',', '', $effective_sum_insured);
                    if (isset($unplaced_share))
                        $updateData['unplaced_share'] = $unplaced_share;
                    if (isset($request->written_share[0]))
                        $updateData['fac_share_offered'] = str_replace(',', '', $request->written_share[0]);
                    if (!empty($updateData)) {
                        DB::table('pipeline_opportunities')->where('opportunity_id', $request->opp_id)
                            ->update($updateData);
                    }

                    break;
            }



            //********end update of pipeline_opportunities********
            if (!is_null($underwriters)) {
                foreach ($underwriters as $uws) {
                    DB::table('prospect_underwriters')->insert([
                        'company_id' => $uws,
                        'prospect_id' => $leadId
                    ]);
                }
            }


            DB::table('stage_comments')->insert([
                'prospect_id' => $leadId,
                'stage_id' => $stage_cycle_fac ?? $stage_cycle,
                'quote_title_intro' => $request->quote_title_intro
            ]);




            $quoteId = $this->generateQuoteNo($request->category_type, $request->opp_id);

            // if($request->bus_type == 'TRT') {
            //     $quote_reinsurer_Ids = ['TRT'];
            // }
            $quote_reinsurer_Ids = [];

            // dd($stage_cycle);

            if (($stage_cycle == 5 || $stage_cycle_fac == 5) && $request->bus_type == 'FAC') {
                DB::table('pipeline_opportunities')
                    ->where('opportunity_id', $leadId)->update([
                        'won_at' => now(),
                    ]);
            }
            if (($stage_cycle == 6 || $stage_cycle_fac == 6) && $request->bus_type == 'FAC') {
                DB::table('pipeline_opportunities')
                    ->where('opportunity_id', $leadId)->update([
                        'pipeline_id' => null,
                        'year_before_revert' => DB::raw('pip_year'),
                        'pip_year' => DB::raw('pip_year + 1'),
                        'reverted_to_pipeline' => 'YES',
                        'stage' => 6
                    ]);
            }

            $reinsurerId = [];

            if (isset($stage_cycle_fac)) {
                if ($stage_cycle_fac == 2 && $request->bus_type == 'FAC') {

                    if (is_array($customer_id) && is_array($customer_name)) {

                        foreach ($customer_id as $index => $id) {

                            $contactName = $selected_contact_person_main['contact_name'][$index] ?? null;
                            $email = $selected_contact_person_main['contact_email'][$index] ?? null;
                            $mainContactPerson = $selected_contact_person_main['main_contact_person'][$index] ?? null;
                            $qt_re_id = '';
                            try {
                                $exist = DB::table('quote_reinsurers')->where([
                                    'reinsurer_id' => $id,
                                    'opportunity_id' => $leadId,
                                    'quote_id' => $quoteId,
                                    'stage' => $stage_cycle_fac
                                ])->exists();
                                if ($exist) {
                                    $reinsurerName = DB::table('customers')->where('customer_id', $id)->value('name');
                                    return redirect()->back()
                                        ->withInput()
                                        ->withErrors(['error' => $reinsurerName . "  " . 'data already exists']);
                                } else {

                                    $qt_re_id = DB::table('quote_reinsurers')->insertGetId([
                                        'reinsurer_id' => $id,
                                        'reinsurer_name' => $customer_name[$index],
                                        'email' => $email,
                                        'contact_name' => $contactName,
                                        'main_contact_person' => $mainContactPerson,
                                        'written_share' => $request->written_share[$index] ?? null,
                                        'opportunity_id' => $leadId,
                                        'quote_id' => $quoteId,
                                        'stage' => $stage_cycle_fac,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);
                                    $reinsurerId[] = $id;
                                }
                                if ($qt_re_id) {
                                    $quote_reinsurer_Ids[] = $qt_re_id;
                                } else {
                                }
                            } catch (\Exception $e) {
                                DB::rollback();
                                return redirect()->back()
                                    ->withInput()
                                    ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
                            }
                        }
                    }
                } elseif (($stage_cycle_fac == 3 || $stage_cycle_fac == 4) && $request->bus_type == 'FAC') {


                    $reinsurers = $request->reinsurers;
                    $written_share = array_column($reinsurers, 'written_share');
                    $signed_share = array_column($reinsurers, 'signed_share');

                    if (is_array($reinsurers)) {
                        $blockInsertions = false;
                        foreach ($reinsurers as $idx => $re) {
                            if (!empty($re['decline']) && empty($re['decline_inserted']) && $unplaced_share > 0) {
                                $blockInsertions = true;
                                break;
                            }
                        }

                        foreach ($reinsurers as $index => $re_details) {
                            $id = $re_details['customer_id'] ?? null;
                            $name = $re_details['name'] ?? null;
                            if (!isset($selected_contact_person_main) || !is_array($selected_contact_person_main)) {
                                $selected_contact_person_main = [];
                            }

                            if ($stage_cycle_fac == 4) {
                                $contactName = $selected_contact_person_main['contact_name'][$index] ?? 'not provided';
                                $email = $selected_contact_person_main['contact_email'][$index] ?? 'not_provided@example.com';

                                $mainContactPerson = $selected_contact_person_main['main_contact_person'][$index] ?? null;
                            } else if ($stage_cycle_fac == 3) {
                                $contactName = $selected_contact_person_main['contact_name'][0] ?? 'not provided';
                                $email = $selected_contact_person_main['contact_email'][0] ?? 'not_provided@example.com';
                                $mainContactPerson = $selected_contact_person_main['main_contact_person'][0] ?? null;
                            }

                            $declineCustomer = $re_details['decline'] ?? null;
                            $declineInserted = $re_details['decline_inserted'] ?? null;
                            $declineReason = $re_details['comments'] ?? null;
                            $qt_re_id = "";

                            try {
                                if ($declineCustomer !== null && $declineCustomer !== '' && $declineInserted == null) {

                                    $data = [
                                        'customer_id' => $declineCustomer,
                                        'opportunity_id' => $leadId
                                    ];

                                    $rules = [
                                        'customer_id' => 'required|integer',
                                        'opportunity_id' => 'required'
                                    ];

                                    $validator = Validator::make($data, $rules);

                                    if ($validator->fails()) {
                                        return redirect()->back()
                                            ->withInput()
                                            ->withErrors($validator->errors());
                                    }

                                    try {
                                        ReinsurersDeclined::updateOrCreate(
                                            [
                                                'customer_id' => $data['customer_id'],
                                                'opportunity_id' => $data['opportunity_id'],
                                            ],
                                            [
                                                'reason' => $declineReason,
                                            ]


                                        );
                                    } catch (\Exception $e) {
                                        DB::rollback();
                                        return redirect()->back()
                                            ->withInput()
                                            ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
                                    }
                                    continue;
                                }
                                if (!$blockInsertions && (empty($declineCustomer))) {
                                    $exist = DB::table('quote_reinsurers')->where([
                                        'reinsurer_id' => $id,
                                        'opportunity_id' => $leadId,
                                        'quote_id' => $quoteId,
                                        'stage' => $stage_cycle_fac
                                    ])->exists();
                                    if ($exist) {
                                        $reinsurerName = DB::table('customers')->where('customer_id', $id)->value('name');
                                        return redirect()->back()
                                            ->withInput()
                                            ->withErrors(['error' => $reinsurerName . "  " . 'data already exists']);
                                    } else {
                                        $qt_re_id = DB::table('quote_reinsurers')->insertGetId([
                                            'reinsurer_id' => $id,
                                            'reinsurer_name' => $name,
                                            'email' => $email,
                                            'contact_name' => $contactName,
                                            'main_contact_person' => $mainContactPerson,
                                            'written_share' => $written_share[$index],
                                            'signed_share' => $signed_share[$index] ?? null,
                                            'opportunity_id' => $leadId,
                                            'quote_id' => $quoteId,
                                            'stage' => $stage_cycle_fac,
                                            'created_at' => now(),
                                            'updated_at' => now()

                                        ]);
                                        $reinsurerId[] = $id;
                                    }
                                    if ($qt_re_id) {
                                        $quote_reinsurer_Ids[] = $qt_re_id;
                                    }
                                }
                            } catch (\Exception $e) {
                                DB::rollback();
                                return redirect()->back()
                                    ->withInput()
                                    ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
                            }
                        }
                    } else {
                    }
                }
            }
            $quote_reverted_to_lead = 'N';

            if (isset($request->stage_cycle)) {
                if ($request->stage_cycle == 2 && $request->bus_type == 'FAC') {


                    if (is_array($customer_id) && is_array($customer_name)) {
                        foreach ($customer_id as $index => $id) {
                            if (!isset($selected_contact_person_main) || !is_array($selected_contact_person_main)) {
                                continue;
                            }

                            $contactName = $selected_contact_person_main['contact_name'][$index] ?? 'not provided';
                            $email = $selected_contact_person_main['contact_email'][$index] ?? 'not_provided@example.com';
                            $mainContactPerson = $selected_contact_person_main['main_contact_person'][$index] ?? null;

                            if (!isset($customer_name[$index])) {
                                continue;
                            }
                            $qt_re_id = "";
                            if (isset($request->written_share[$index])) {
                                $quote_reverted_to_lead = 'Y';
                            }


                            try {
                                $exist = DB::table('quote_reinsurers')->where([
                                    'reinsurer_id' => $id,
                                    'opportunity_id' => $leadId,
                                    'quote_id' => $quoteId,
                                    'stage' => $stage_cycle
                                ])->exists();
                                if ($exist) {
                                    $reinsurerName = DB::table('customers')->where('customer_id', $id)->value('name');
                                    return redirect()->back()
                                        ->withInput()
                                        ->withErrors(['error' => $reinsurerName . "  " . 'data already exists']);
                                } else {
                                    $qt_re_id = DB::table('quote_reinsurers')->insertGetId([
                                        'reinsurer_id' => $id,
                                        'reinsurer_name' => $customer_name[$index],
                                        'email' => $email,
                                        'contact_name' => $contactName,
                                        'main_contact_person' => $mainContactPerson,
                                        'written_share' => $request->written_share[$index] ?? null,
                                        'opportunity_id' => $leadId,
                                        'quote_id' => $quoteId,
                                        'stage' => $stage_cycle,
                                        'quote_reverted_to_lead' => $quote_reverted_to_lead,
                                        'created_at' => now(),
                                        'updated_at' => now()

                                    ]);
                                    $reinsurerId[] = $id;
                                }
                                if ($qt_re_id) {
                                    $quote_reinsurer_Ids[] = $qt_re_id;
                                }
                            } catch (\Exception $e) {
                                DB::rollback();
                                return redirect()->back()
                                    ->withInput()
                                    ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
                            }
                        }
                    } else {
                    }
                } elseif (($stage_cycle == 3 || $stage_cycle == 4) && $request->bus_type == 'FAC') {


                    $reinsurers = $request->reinsurers;
                    $written_share = array_column($reinsurers, 'written_share');
                    $signed_share = !empty($reinsurers) ? array_column($reinsurers, 'signed_share') : null;




                    if (is_array($reinsurers) && !is_null($reinsurers)) {

                        $blockInsertions = false;
                        foreach ($reinsurers as $idx => $re) {
                            if (!empty($re['decline']) && empty($re['decline_inserted']) && $unplaced_share > 0) {
                                $blockInsertions = true;
                                break;
                            }
                        }

                        foreach ($reinsurers as $index => $re_details) {
                            $id = $re_details['customer_id'] ?? null;
                            $name = $re_details['name'] ?? null;

                            if (!isset($selected_contact_person_main) || !is_array($selected_contact_person_main)) {
                                $selected_contact_person_main = [];
                            }
                            if ($stage_cycle == 4) {
                                $contactName = $selected_contact_person_main['contact_name'][$index] ?? 'not provided';
                                $email = $selected_contact_person_main['contact_email'][$index] ?? 'not_provided@example.com';
                                $mainContactPerson = $selected_contact_person_main['main_contact_person'][$index] ?? null;
                            } else if ($stage_cycle == 3) {
                                $contactName = $selected_contact_person_main['contact_name'][0] ?? 'not provided';
                                $email = $selected_contact_person_main['contact_email'][0] ?? 'not_provided@example.com';

                                $mainContactPerson = $selected_contact_person_main['main_contact_person'][0] ?? null;
                            }

                            $declineCustomer = $re_details['decline'] ?? null;
                            $declineInserted = $re_details['decline_inserted'] ?? null;
                            $declineReason = $re_details['comments'] ?? null;
                            $qt_re_id = "";

                            try {

                                if ($declineCustomer !== null && $declineCustomer !== '' && $declineInserted == null) {

                                    $data = [
                                        'customer_id' => $declineCustomer,
                                        'opportunity_id' => $leadId
                                    ];

                                    $rules = [
                                        'customer_id' => 'required|integer',
                                        'opportunity_id' => 'required'
                                    ];

                                    $validator = Validator::make($data, $rules);

                                    if ($validator->fails()) {
                                        return redirect()->back()
                                            ->withInput()
                                            ->withErrors($validator->errors());
                                    }
                                    if ($declineUncheckedCount !== "") {
                                        pipelineOpportunity::updateOrCreate(['opportunity_id' => $leadId], ['decline_unchecked_count' => $declineUncheckedCount]);
                                    }

                                    try {
                                        ReinsurersDeclined::updateOrCreate(
                                            [
                                                'customer_id' => $data['customer_id'],
                                                'opportunity_id' => $data['opportunity_id'],
                                            ],
                                            [
                                                'reason' => $declineReason,
                                            ]


                                        );
                                    } catch (\Exception $e) {
                                        DB::rollback();
                                        return redirect()->back()
                                            ->withInput()
                                            ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
                                    }
                                    continue;
                                }

                                if (!$blockInsertions && (empty($declineCustomer))) {
                                    $exist = DB::table('quote_reinsurers')->where([
                                        'reinsurer_id' => $id,
                                        'opportunity_id' => $leadId,
                                        'quote_id' => $quoteId,
                                        'stage' => $stage_cycle
                                    ])->exists();
                                    if ($exist) {
                                        $reinsurerName = DB::table('customers')->where('customer_id', $id)->value('name');
                                        return redirect()->back()
                                            ->withInput()
                                            ->withErrors(['error' => $reinsurerName . "  " . 'data already exists']);
                                    } else {
                                        $qt_re_id = DB::table('quote_reinsurers')->insertGetId([
                                            'reinsurer_id' => $id,
                                            'reinsurer_name' => $name,
                                            'email' => $email,
                                            'contact_name' => $contactName,
                                            'main_contact_person' => $mainContactPerson,
                                            'written_share' => $written_share[$index],
                                            'signed_share' => $signed_share[$index] ?? null,
                                            'opportunity_id' => $leadId,
                                            'quote_id' => $quoteId,
                                            'stage' => $stage_cycle,
                                            'created_at' => now(),
                                            'updated_at' => now()

                                        ]);
                                        $reinsurerId[] = $id;
                                    }
                                    if ($qt_re_id) {

                                        $quote_reinsurer_Ids[] = $qt_re_id;
                                    }
                                }
                            } catch (\Exception $e) {
                                DB::rollback();
                                return redirect()->back()
                                    ->withInput()
                                    ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
                            }
                        }
                    } else {
                    }
                }
            }
            if (isset($request->stage_cycle) && $request->bus_type == 'TRT') {
                if ($request->stage_cycle == 3 || $request->stage_cycle == 4) {
                    $customer_id = $request->customer_id;
                    // dd($request->all());




                    if (!is_null($customer_id)) {





                        $id = $customer_id;
                        $name = $request->customer;

                        if (!isset($selected_contact_person_main) || !is_array($selected_contact_person_main)) {
                            $selected_contact_person_main = [];
                        }


                        $contactName = $selected_contact_person_main['contact_name'][0] ?? 'not provided';
                        $email = $selected_contact_person_main['contact_email'][0] ?? 'not_provided@example.com';
                        $mainContactPerson = $selected_contact_person_main['main_contact_person'][0] ?? null;


                        $declineCustomer = $re_details['decline'] ?? null;
                        $declineInserted = $re_details['decline_inserted'] ?? null;
                        $declineReason = $re_details['comments'] ?? null;
                        $qt_re_id = "";

                        try {


                            $exist = DB::table('quote_reinsurers')->where([
                                'reinsurer_id' => $id,
                                'opportunity_id' => $leadId,
                                'quote_id' => $quoteId,
                                'stage' => $stage_cycle
                            ])->exists();
                            if ($exist) {
                                $reinsurerName = DB::table('customers')->where('customer_id', $id)->value('name');
                                return redirect()->back()
                                    ->withInput()
                                    ->withErrors(['error' => $reinsurerName . "  " . 'data already exists']);
                            } else {

                                $qt_re_id = DB::table('quote_reinsurers')->insertGetId([
                                    'reinsurer_id' => $id,
                                    'reinsurer_name' => $name,
                                    'email' => $email,
                                    'contact_name' => $contactName,
                                    'main_contact_person' => $mainContactPerson,
                                    'opportunity_id' => $leadId,
                                    'quote_id' => $quoteId,
                                    'stage' => $stage_cycle,
                                    'created_at' => now(),
                                    'updated_at' => now()

                                ]);
                                $reinsurerId[] = $id;
                            }
                            if ($qt_re_id) {
                                $quote_reinsurer_Ids[] = $qt_re_id;
                            }
                        } catch (\Exception $e) {
                            DB::rollback();
                            return redirect()->back()
                                ->withInput()
                                ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
                        }
                    } else {
                    }
                } else if ($request->stage_cycle == 5) {
                    if (is_array($customer_id) && is_array($customer_name)) {
                        foreach ($customer_id as $index => $id) {
                            if (!isset($selected_contact_person_main) || !is_array($selected_contact_person_main)) {
                                continue;
                            }

                            $contactName = $selected_contact_person_main['contact_name'][$index] ?? 'not provided';
                            $email = $selected_contact_person_main['contact_email'][$index] ?? 'not_provided@example.com';
                            $mainContactPerson = $selected_contact_person_main['main_contact_person'][$index] ?? null;

                            if (!isset($customer_name[$index])) {
                                continue;
                            }
                            $qt_re_id = "";
                            if (isset($request->written_share[$index])) {
                                $quote_reverted_to_lead = 'Y';
                            }


                            try {
                                $exist = DB::table('quote_reinsurers')->where([
                                    'reinsurer_id' => $id,
                                    'opportunity_id' => $leadId,
                                    'quote_id' => $quoteId,
                                    'stage' => $stage_cycle
                                ])->exists();
                                if ($exist) {
                                    $reinsurerName = DB::table('customers')->where('customer_id', $id)->value('name');
                                    return redirect()->back()
                                        ->withInput()
                                        ->withErrors(['error' => $reinsurerName . "  " . 'data already exists']);
                                } else {
                                    $qt_re_id = DB::table('quote_reinsurers')->insertGetId([
                                        'reinsurer_id' => $id,
                                        'reinsurer_name' => $customer_name[$index],
                                        'email' => $email,
                                        'contact_name' => $contactName,
                                        'main_contact_person' => $mainContactPerson,
                                        'opportunity_id' => $leadId,
                                        'quote_id' => $quoteId,
                                        'stage' => $stage_cycle,
                                        'quote_reverted_to_lead' => $quote_reverted_to_lead,
                                        'created_at' => now(),
                                        'updated_at' => now()

                                    ]);
                                    $reinsurerId[] = $id;
                                }
                                if ($qt_re_id) {
                                    $quote_reinsurer_Ids[] = $qt_re_id;
                                }
                            } catch (\Exception $e) {
                                DB::rollback();
                                return redirect()->back()
                                    ->withInput()
                                    ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
                            }
                        }
                    }
                }
            }

            if (isset($request->stage_cycle_fac) && $request->bus_type == 'TRT') {
                if ($request->stage_cycle_fac == 4) {
                    $customer_id = $request->customer_id;
                    // dd($request->all());




                    if (!is_null($customer_id)) {





                        $id = $customer_id;
                        $name = $request->customer;

                        if (!isset($selected_contact_person_main) || !is_array($selected_contact_person_main)) {
                            $selected_contact_person_main = [];
                        }


                        $contactName = $selected_contact_person_main['contact_name'][0] ?? 'not provided';
                        $email = $selected_contact_person_main['contact_email'][0] ?? 'not_provided@example.com';
                        $mainContactPerson = $selected_contact_person_main['main_contact_person'][0] ?? null;


                        $declineCustomer = $re_details['decline'] ?? null;
                        $declineInserted = $re_details['decline_inserted'] ?? null;
                        $declineReason = $re_details['comments'] ?? null;
                        $qt_re_id = "";

                        try {


                            $exist = DB::table('quote_reinsurers')->where([
                                'reinsurer_id' => $id,
                                'opportunity_id' => $leadId,
                                'quote_id' => $quoteId,
                                'stage' => $stage_cycle_fac
                            ])->exists();
                            if ($exist) {
                                $reinsurerName = DB::table('customers')->where('customer_id', $id)->value('name');
                                return redirect()->back()
                                    ->withInput()
                                    ->withErrors(['error' => $reinsurerName . "  " . 'data already exists']);
                            } else {

                                $qt_re_id = DB::table('quote_reinsurers')->insertGetId([
                                    'reinsurer_id' => $id,
                                    'reinsurer_name' => $name,
                                    'email' => $email,
                                    'contact_name' => $contactName,
                                    'main_contact_person' => $mainContactPerson,
                                    'opportunity_id' => $leadId,
                                    'quote_id' => $quoteId,
                                    'stage' => $stage_cycle_fac,
                                    'created_at' => now(),
                                    'updated_at' => now()

                                ]);
                                $reinsurerId[] = $id;
                            }
                            if ($qt_re_id) {
                                $quote_reinsurer_Ids[] = $qt_re_id;
                            }
                        } catch (\Exception $e) {
                            DB::rollback();
                            return redirect()->back()
                                ->withInput()
                                ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
                        }
                    } else {
                    }
                } else if ($request->stage_cycle_fac == 4) {
                    if (is_array($customer_id) && is_array($customer_name)) {
                        foreach ($customer_id as $index => $id) {
                            if (!isset($selected_contact_person_main) || !is_array($selected_contact_person_main)) {
                                continue;
                            }

                            $contactName = $selected_contact_person_main['contact_name'][$index] ?? 'not provided';
                            $email = $selected_contact_person_main['contact_email'][$index] ?? 'not_provided@example.com';
                            $mainContactPerson = $selected_contact_person_main['main_contact_person'][$index] ?? null;

                            if (!isset($customer_name[$index])) {
                                continue;
                            }
                            $qt_re_id = "";
                            if (isset($request->written_share[$index])) {
                                $quote_reverted_to_lead = 'Y';
                            }


                            try {
                                $exist = DB::table('quote_reinsurers')->where([
                                    'reinsurer_id' => $id,
                                    'opportunity_id' => $leadId,
                                    'quote_id' => $quoteId,
                                    'stage' => $stage_cycle
                                ])->exists();
                                if ($exist) {
                                    $reinsurerName = DB::table('customers')->where('customer_id', $id)->value('name');
                                    return redirect()->back()
                                        ->withInput()
                                        ->withErrors(['error' => $reinsurerName . "  " . 'data already exists']);
                                } else {
                                    $qt_re_id = DB::table('quote_reinsurers')->insertGetId([
                                        'reinsurer_id' => $id,
                                        'reinsurer_name' => $customer_name[$index],
                                        'email' => $email,
                                        'contact_name' => $contactName,
                                        'main_contact_person' => $mainContactPerson,
                                        'opportunity_id' => $leadId,
                                        'quote_id' => $quoteId,
                                        'stage' => $stage_cycle,
                                        'quote_reverted_to_lead' => $quote_reverted_to_lead,
                                        'created_at' => now(),
                                        'updated_at' => now()

                                    ]);
                                    $reinsurerId[] = $id;
                                }
                                if ($qt_re_id) {
                                    $quote_reinsurer_Ids[] = $qt_re_id;
                                }
                            } catch (\Exception $e) {
                                DB::rollback();
                                return redirect()->back()
                                    ->withInput()
                                    ->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
                            }
                        }
                    }
                }
            }

            if (!empty($manipulated_schedule_details) || !empty($manipulated_facschedule_details)) {
                $all_schedule_details = array_merge($manipulated_schedule_details ?? [], $manipulated_facschedule_details ?? []);
                switch ($request->bus_type) {
                    case 'FAC':
                        foreach ($all_schedule_details as $item) {
                            if (!empty($item['details'])) {
                                DB::table('quote_schedules')->insert([
                                    'schedule_id' => $item['id'],
                                    'name' => $item['name'],
                                    'details' => $item['details'],
                                    'opportunity_id' => $leadId,
                                    'stage' => $request->stage_cycle ?? $request->stage_cycle_fac,
                                    'quote_reverted_to_lead' => $quote_reverted_to_lead,
                                    'created_at' => now(),
                                ]);
                            }
                        }
                        break;
                    case 'TRT':
                        foreach ($all_schedule_details as $item) {
                            $details = json_decode($item['details'], true);
                            $has_current_amount = isset($item['current_amount']) && !empty($item['current_amount']);
                            $has_proposed_amount = isset($item['proposed_amount']) && !empty($item['proposed_amount']);
                            $has_final_amount = isset($item['final_amount']) && !empty($item['final_amount']);


                            if (
                                !empty($details) &&
                                ((isset($item['current_amount']) && !empty($item['current_amount'])) && (isset($item['proposed_amount']) && !empty($item['proposed_amount']))) ||
                                (!empty($details['current']) || !empty($details['proposed']))
                            ) {
                                if (!empty($item['details'])) {
                                    DB::table('quote_schedules')->insert([
                                        'schedule_id' => $item['id'],
                                        'name' => $item['name'],
                                        // 'details' => $item['details'],

                                        'current' => $has_current_amount ? ($item['current_amount'] ?? null) : ($details['current'] ?? null),
                                        'proposed' => $has_proposed_amount ? ($item['proposed_amount'] ?? null) : ($details['proposed'] ?? null),
                                        'final' => $has_final_amount ? ($item['final_amount'] ?? null) : ($details['final'] ?? null),
                                        'opportunity_id' => $leadId,
                                        'stage' => $request->stage_cycle ?? $request->stage_cycle_fac,
                                        'quote_reverted_to_lead' => $quote_reverted_to_lead,
                                        'created_at' => now(),
                                    ]);
                                }
                            }
                        }
                        break;
                }
            }


            // if ($request->stage_cycle == 4) {
            //     if (!empty($manipulated_schedule_details)) {
            //         $schedule_details = $manipulated_schedule_details ?? [];
            //         foreach ($schedule_details as $item) {
            //             if (is_array($customer_id) && is_array($customer_name)) {
            //                 foreach ($customer_id as $index => $id) {
            //                     DB::table('quote_schedules')->insert([
            //                         'reinsurer_id' => $id,
            //                         'reinsurer_name' => $customer_name[$index],
            //                         'contact_name' => $contact_name[$index] ?? null,
            //                         'details' => $item['details'],
            //                         'opportunity_id' => $leadId
            //                     ]);
            //                 }
            //             }
            //         }
            //     }
            // }


            // if ($request->stage_cycle == 4) {
            //     if (!empty($manipulated_schedule_details)) {
            //         $schedule_details = $manipulated_schedule_details ?? [];
            //         foreach ($schedule_details as $item) {
            //             if (is_array($customer_id) && is_array($customer_name)) {
            //                 foreach ($customer_id as $index => $id) {
            //                     DB::table('quote_schedules')->insert([
            //                         'reinsurer_id' => $id,
            //                         'reinsurer_name' => $customer_name[$index],
            //                         'contact_name' => $contact_name[$index] ?? null,
            //                         'details' => $item['details'],
            //                         'opportunity_id' => $leadId
            //                     ]);
            //                 }
            //             }
            //         }
            //     }
            // }



            if ($send_email_flag == 1 && !empty(array_filter($quote_reinsurer_Ids))) {

                $customer_id = $reinsurerId ?? [];
                $stage = $request->stage_cycle ?? $request->stage_cycle_fac;
                $stageType = isset($request->stage_cycle) ? 1 : 2;

                switch ($request->bus_type) {
                    case 'FAC':
                        $this->sendEmailFacSlipPerReinsurer($request->opp_id, $customer_id, $stage, $stageType, $request);
                        break;
                    case 'TRT':
                        // logger('inside email treaty');
                        // dd($checkbox_docs);
                        $this->sendTreatyEmail($request->opp_id, $customer_id, $stage, $stageType, $request);
                        break;
                }
            }


            //insert file for quotation
            if (isset($request->document_file, $stage_cycle)) {
                if ($request->stage_cycle != 3 && $request->stage_cycle != 4) {
                    $this->stageNotThreeOrFour($request, $uploadsPath, $document_name, $checkbox_docs, $quote_reinsurer_Ids, $leadId, $stage_cycle);
                }
                if ($request->stage_cycle == 3 || $request->stage_cycle == 4) {
                    $this->stageEqualThreeOrFour($request, $uploadsPath, $document_name, $quote_reinsurer_Ids, $leadId, $stage_cycle);
                }
            }

            //insert file for  fac logic
            if (isset($request->document_file, $stage_cycle_fac) || $request->bus_type == 'TRT' && isset($stage_cycle_fac)) {
                if ($request->stage_cycle_fac == 5 && $request->bus_type == 'TRT') {

                    $this->stageNotThree($request, $uploadsPath, $document_name, $checkbox_docs, $quote_reinsurer_Ids, $leadId, $stage_cycle_fac);
                }
                if ($request->stage_cycle_fac == 4 && $request->bus_type == 'TRT') {
                    $this->stageEqualThree($request, $uploadsPath, $document_name, $quote_reinsurer_Ids, $leadId, $stage_cycle_fac);
                }
                if ($request->stage_cycle_fac == 2 && $request->bus_type == 'FAC') {
                    $this->stageNotThree($request, $uploadsPath, $document_name, $checkbox_docs, $quote_reinsurer_Ids, $leadId, $stage_cycle_fac);
                }
            }

            if ($stage_cycle) {
                if ($stage_cycle != 5 && $request->pq != 'Y') {

                    $this->stageCycleNotEqualFive($leadId, $stage_cycle, $pipeline, $division, $request);
                } else if ($stage_cycle == 5) {

                    // $this->stageCycleEqualFive($leadId, $stage_cycle, $pipeline, $division);
                    $opportunity = PipelineOpportunity::where('opportunity_id', $leadId)
                        ->where('pipeline_id', $pipeline)
                        ->where('divisions', $division)->firstOrFail();

                    $opportunity->update([
                        'stage' => $stage_cycle,
                    ]);
                    DB::commit();
                    return redirect()->route('lead.handover', ['prospect' => $leadId]);
                } else {
                    $this->stageCycle($leadId, $stage_cycle);
                }
                if ($stage_cycle == 'W') {
                    $this->stageCycleWon($leadId);
                }
            }

            if ($stage_cycle_fac && $request->bus_type == "FAC") {
                if ($stage_cycle_fac != 5 && $request->pq != 'Y') {

                    $this->stageCycleFacNotEqualFour($leadId, $stage_cycle_fac, $pipeline, $division, $request);
                } else if ($stage_cycle_fac == 5) {

                    // $this->stageCycleFacEqualFour($leadId, $stage_cycle_fac, $pipeline, $division);
                    $opportunity = PipelineOpportunity::where('opportunity_id', $leadId)
                        ->where('pipeline_id', $pipeline)
                        ->where('divisions', $division)->firstOrFail();

                    $opportunity->update([
                        'stage' => $stage_cycle,
                    ]);
                    DB::commit();
                    return redirect()->route('lead.handover', ['prospect' => $leadId]);
                } else {
                    $this->stageCycleFac($leadId, $stage_cycle_fac);
                }
                if ($stage_cycle_fac == 'W') {
                    $this->stageCycleFacWon($leadId);
                }
            }
            if ($stage_cycle_fac && $request->bus_type == "TRT") {
                if ($stage_cycle_fac != 9 && $request->pq != 'Y') {

                    $this->stageCycleFacNotEqualNine($leadId, $stage_cycle_fac, $pipeline, $division, $request);
                } else if ($stage_cycle_fac == 9) {

                    // $this->stageCycleFacEqualFour($leadId, $stage_cycle_fac, $pipeline, $division);
                    DB::table('pipeline_opportunities')
                        ->where('opportunity_id', $leadId)
                        ->where('pipeline_id', $pipeline)
                        ->where('divisions', $division)
                        ->update([
                            'stage' => $stage_cycle_fac,
                        ]);
                    DB::commit();
                    return redirect()->route('lead.handover', ['prospect' => $leadId]);
                } else {
                    $this->stageCycleFac($leadId, $stage_cycle_fac);
                }
                if ($stage_cycle_fac == 'W') {
                    $this->stageCycleFacWon($leadId);
                }
            }



            DB::commit();
            // if ($stage == 0) {
            //     return redirect()->route('pipelines.onboarding', ['qstring'=>Crypt::encrypt('pipeline='.$pipeline.'&prospect='.$leadId)])->with('success','Status updated successfully');
            // }
            return redirect()->back()->with('success', 'Status updated successfully');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Something went wrong', [
                'message' => $th->getMessage(),
                'trace' => $th->getTraceAsString(),
            ]);

            // dd([
            //     'message' => $th->getMessage(),
            //     'file' => $th->getFile(),
            //     'line' => $th->getLine(),
            //     'trace' => $th->getTraceAsString(),
            // ]);
        }
    }

    public function stageNotThreeOrFour($request, $uploadsPath, $document_name, $checkbox_docs, $quote_reinsurer_Ids, $leadId, $stage_cycle)
    {
        // dd($request->document_file);


        switch ($request->bus_type) {
            case 'TRT':
                if (is_array($checkbox_docs) && !empty($checkbox_docs)) {
                    foreach ($checkbox_docs as $doc) {
                        $prospect_doc_id = DB::table('prospect_docs')->insertGetId([
                            'description' => $doc['name'],
                            'prospect_id' => $leadId,
                            'prospect_status' => $stage_cycle,
                            'type_of_treaty_doc' => $doc['source'],
                            'bus_type' => 'TRT',
                            'created_at' => now(),
                        ]);
                    }
                }
                break;
        }

        if (!is_null($document_name)) {
            foreach ($document_name as $index => $name) {

                if (!isset($name) || !isset($request->document_file[$index])) {
                    continue;
                }

                $file = $request->document_file[$index];

                if (!$file->isValid()) {
                    continue; // Skip invalid files
                }

                $mimetype = $file->getClientMimeType();
                $fileContent = file_get_contents($file);
                $encodedFileContent = base64_encode($fileContent);

                $originalNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $Filename = mt_rand() . '_' . $originalNameWithoutExtension . '.' . $file->getClientOriginalExtension();

                $S3FilePath = $uploadsPath . '/' . $Filename;

                try {
                    Storage::disk('s3')->put($S3FilePath, $fileContent, [
                        'visibility' => 'public',
                    ]);

                    if (!Storage::disk('s3')->exists($S3FilePath)) {
                        logger("Failed.ConcurrentModificationException: Failed to verify file in S3: $S3FilePath");
                        return response()->json(['error' => 'Failed to save file to S3.'], 500);
                    }

                    logger("File uploaded successfully to S3: $S3FilePath");
                } catch (Exception $e) {
                    logger("S3 upload error for $S3FilePath: " . $e->getMessage());
                    return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
                }
                $prospect_doc_id = DB::table('prospect_docs')->insertGetId([
                    'description' => $name,
                    'prospect_id' => $leadId,
                    'prospect_status' => $stage_cycle,
                    'mimetype' => $mimetype,
                    'file' => $Filename,
                    'bus_type' => $request->bus_type,
                    'created_at' => now(),
                ]);
            }
        }
    }

    public function stageEqualThreeOrFour($request, $uploadsPath, $document_name, $quote_reinsurer_Ids, $leadId, $stage_cycle)
    {

        if (!is_null($request->reinsurers)) {
            foreach ($request->reinsurers as $reinsurerIndex => $reinsurer) {
                if (!empty($reinsurer['documents'])) {
                    foreach ($reinsurer['documents'] as $docIndex => $doc) {
                        if (!isset($doc['title']) || !isset($doc['file'])) {
                            continue;
                        }
                        $name = $doc['title'];
                        $file = $doc['file'];



                        // Skip if file is null or invalid
                        if (is_null($file) || !$file->isValid()) {
                            continue;
                        }

                        $mimetype = $file->getClientMimeType();
                        $fileContent = file_get_contents($file);
                        $encodedFileContent = base64_encode($fileContent);

                        $originalNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $Filename = mt_rand() . '_' . $originalNameWithoutExtension . '.' . $file->getClientOriginalExtension();

                        $S3FilePath = $uploadsPath . '/' . $Filename;

                        try {
                            // Upload file to S3
                            Storage::disk('s3')->put($S3FilePath, $fileContent, [
                                'visibility' => 'public',
                            ]);

                            // Verify the file was uploaded
                            if (!Storage::disk('s3')->exists($S3FilePath)) {
                                logger("Failed.ConcurrentModificationException: Failed to verify file in S3: $S3FilePath");
                                return response()->json(['error' => 'Failed to save file to S3.'], 500);
                            }

                            logger("File uploaded successfully to S3: $S3FilePath");
                        } catch (\Exception $e) {
                            logger("S3 upload error for $S3FilePath: " . $e->getMessage());
                            return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
                        }


                        $prospect_doc_id = DB::table('prospect_docs')->insertGetId([
                            'description' => $name,
                            'prospect_id' => $leadId,
                            'prospect_status' => $stage_cycle,
                            'mimetype' => $mimetype,
                            'file' => $Filename,
                            'bus_type' => $request->bus_type,
                            'created_at' => now(),

                        ]);
                    }
                }
            }
        }
    }

    public function stageNotThree($request, $uploadsPath, $document_name, $checkbox_docs, $quote_reinsurer_Ids, $leadId, $stage_cycle)
    {
        // dd($request->all());
        //  dd($request->document_file);
        switch ($request->bus_type) {
            case 'TRT':
                if (is_array($checkbox_docs) && !empty($checkbox_docs)) {
                    foreach ($checkbox_docs as $doc) {
                        $prospect_doc_id = DB::table('prospect_docs')->insertGetId([
                            'description' => $doc['name'],
                            'prospect_id' => $leadId,
                            'prospect_status' => $stage_cycle,
                            'type_of_treaty_doc' => $doc['source'],
                            'bus_type' => 'TRT',
                            'required_doc_from_cedant' => true,
                            'created_at' => now(),
                        ]);
                    }
                } else {
                    if (!is_null($document_name)) {
                        foreach ($document_name as $index => $name) {
                            if (!isset($name) || !isset($request->document_file[$index])) {
                                continue;
                            }

                            $file = $request->document_file[$index];

                            if (!$file->isValid()) {
                                continue; // Skip invalid files
                            }

                            $mimetype = $file->getClientMimeType();
                            $fileContent = file_get_contents($file);
                            $encodedFileContent = base64_encode($fileContent);

                            $originalNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                            $Filename = mt_rand() . '_' . $originalNameWithoutExtension . '.' . $file->getClientOriginalExtension();

                            $S3FilePath = $uploadsPath . '/' . $Filename;

                            try {
                                // Upload file to S3
                                Storage::disk('s3')->put($S3FilePath, $fileContent, [
                                    'visibility' => 'public',
                                ]);

                                // Verify the file was uploaded
                                if (!Storage::disk('s3')->exists($S3FilePath)) {
                                    logger("Failed.ConcurrentModificationException: Failed to verify file in S3: $S3FilePath");
                                    return response()->json(['error' => 'Failed to save file to S3.'], 500);
                                }

                                // logger("File uploaded successfully to S3: $S3FilePath");
                            } catch (\Exception $e) {
                                logger("S3 upload error for $S3FilePath: " . $e->getMessage());
                                return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
                            }
                            // logger("File uploaded successfully to S3: $S3FilePath");

                            $prospect_doc_id = DB::table('prospect_docs')->insertGetId([
                                'description' => $name,
                                'prospect_id' => $leadId,
                                'prospect_status' => $stage_cycle,
                                'mimetype' => $mimetype,
                                'file' => $Filename,
                                'bus_type' => $request->bus_type,
                                'created_at' => now(),
                            ]);
                        }
                    }
                }


                break;
            case 'FAC':
                if (!is_null($document_name)) {
                    foreach ($document_name as $index => $name) {
                        if (!isset($name) || !isset($request->document_file[$index])) {
                            continue;
                        }

                        $file = $request->document_file[$index];

                        if (!$file->isValid()) {
                            continue; // Skip invalid files
                        }

                        $mimetype = $file->getClientMimeType();
                        $fileContent = file_get_contents($file);
                        $encodedFileContent = base64_encode($fileContent);

                        $originalNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                        $Filename = mt_rand() . '_' . $originalNameWithoutExtension . '.' . $file->getClientOriginalExtension();

                        $S3FilePath = $uploadsPath . '/' . $Filename;

                        try {
                            // Upload file to S3
                            Storage::disk('s3')->put($S3FilePath, $fileContent, [
                                'visibility' => 'public',
                            ]);

                            // Verify the file was uploaded
                            if (!Storage::disk('s3')->exists($S3FilePath)) {
                                logger("Failed.ConcurrentModificationException: Failed to verify file in S3: $S3FilePath");
                                return response()->json(['error' => 'Failed to save file to S3.'], 500);
                            }

                            logger("File uploaded successfully to S3: $S3FilePath");
                        } catch (\Exception $e) {
                            logger("S3 upload error for $S3FilePath: " . $e->getMessage());
                            return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
                        }
                        logger("File uploaded successfully to S3: $S3FilePath");

                        $prospect_doc_id = DB::table('prospect_docs')->insertGetId([
                            'description' => $name,
                            'prospect_id' => $leadId,
                            'prospect_status' => $stage_cycle,
                            'mimetype' => $mimetype,
                            'file' => $Filename,
                            'bus_type' => $request->bus_type,
                            'created_at' => now(),
                        ]);
                    }
                }
                break;
        }
    }

    public function stageEqualThree($request, $uploadsPath, $document_name, $quote_reinsurer_Ids, $leadId, $stage_cycle)
    {

        if (!is_null($request->reinsurers)) {
            foreach ($request->reinsurers as $reinsurerIndex => $reinsurer) {
                $reinsurer_id = $reinsurer['reinsurer_id'] ?? null;

                if (!empty($reinsurer['documents'])) {
                    foreach ($reinsurer['documents'] as $docIndex => $doc) {
                        if (!isset($doc['title']) || !isset($doc['file'])) {
                            continue;
                        }
                        $name = $doc['title'];
                        $file = $doc['file'];

                        // Skip if file is null or invalid
                        if (is_null($file) || !$file->isValid()) {
                            continue;
                        }

                        $mimetype = $file->getClientMimeType();
                        $fileContent = file_get_contents($file);
                        $encodedFileContent = base64_encode($fileContent);

                        $originalNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $Filename = mt_rand() . '_' . $originalNameWithoutExtension . '.' . $file->getClientOriginalExtension();

                        // Move file to the desired location
                        $S3FilePath = $uploadsPath . '/' . $Filename;

                        try {
                            // Upload file to S3
                            Storage::disk('s3')->put($S3FilePath, $fileContent, [
                                'visibility' => 'public',
                            ]);

                            // Verify the file was uploaded
                            if (!Storage::disk('s3')->exists($S3FilePath)) {
                                logger("Failed.ConcurrentModificationException: Failed to verify file in S3: $S3FilePath");
                                return response()->json(['error' => 'Failed to save file to S3.'], 500);
                            }

                            logger("File uploaded successfully to S3: $S3FilePath");
                        } catch (\Exception $e) {
                            logger("S3 upload error for $S3FilePath: " . $e->getMessage());
                            return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
                        }


                        $prospect_doc_id = DB::table('prospect_docs')->insertGetId([
                            'description' => $name,
                            'prospect_id' => $leadId,
                            'prospect_status' => $stage_cycle,
                            'mimetype' => $mimetype,
                            'file' => $Filename,
                            'quote_reinsurer_id' => $reinsurer_id,
                        ]);
                    }
                }
            }
        }
    }

    public function stageCycleNotEqualFive($leadId, $stage_cycle, $pipeline, $division, $request)
    {
        $reinsurers = $request->reinsurers;
        $decline = [];
        $unplaced_share = $request->unplaced;

        if (isset($reinsurers)) {
            if (is_array($reinsurers)) {
                // If it's a single associative array (one reinsurer), wrap it
                if (array_key_exists('decline', $reinsurers)) {
                    $reinsurers = [$reinsurers];
                }

                $decline = array_column($reinsurers, 'decline');
            }
        }

        $reset = false;
        if (isset($reinsurers)) {
            if (is_array($reinsurers)) {
                foreach ($reinsurers as $idx => $re) {
                    if (!empty($re['decline']) && empty($re['decline_inserted']) && $unplaced_share > 0) {
                        $reset = true;
                        break;
                    }
                }
            }
        }
        if ($reset && (isset($decline) && !empty($decline))) {
            $opportunity = PipelineOpportunity::where('opportunity_id', $leadId)
                ->where('pipeline_id', $pipeline)
                ->where('divisions', $division)
                ->firstOrFail();
            $opportunity->update([
                'stage' => 1,
                'data_exists_flag' => 'Y',
                // 'stage_updated_at' => Carbon::now(),
            ]);
        } else {
            $opportunity = PipelineOpportunity::where('opportunity_id', $leadId)
                ->where('pipeline_id', $pipeline)
                ->where('divisions', $division)
                ->firstOrFail();
            $opportunity->update([
                'stage' => $stage_cycle,
                'data_exists_flag' => 'Y',
                // 'stage_updated_at' => Carbon::now(),
            ]);
        }
    }

    public function stageCycleEqualFive($leadId, $stage_cycle, $pipeline, $division)
    {
        DB::table('pipeline_opportunities')
            ->where('opportunity_id', $leadId)
            ->where('pipeline_id', $pipeline)
            ->where('divisions', $division)
            ->update([
                'stage' => $stage_cycle,
            ]);
        // DB::commit();
        return redirect()->route('lead.handover', ['prospect' => $leadId]);
    }

    public function stageCycle($leadId, $stage_cycle)
    {
        DB::table('pipeline_opportunities')
            ->where('opportunity_id', $leadId)
            ->update([
                'pq_status' => $stage_cycle,
            ]);
    }

    public function stageCycleWon($leadId)
    {
        $pip = DB::table('pipeline_opportunities')
            ->where('opportunity_id', $leadId)->first();
        $data = [
            'name' => $pip->fullname,
            'contact' => $pip->contact_name,
            'email' => $pip->email,
            'phone' => $pip->phone,
            'start_date' => $pip->effective_date,
        ];
        DB::commit();
        return redirect()->route('lead.handover', ['prospect' => $leadId]);
        // Mail::to('mutuaian176@gmail.com')->send(new Prospectwonemail($data));
        Mail::to('marketing@accentriagroup.com')->send(new Prospectwonemail($data));
    }

    public function stageCycleFacNotEqualFour($leadId, $stage_cycle_fac, $pipeline, $division, $request)
    {
        $reinsurers = $request->reinsurers;
        $unplaced_share = $request->unplaced;
        $decline = [];

        if (isset($reinsurers)) {
            if (is_array($reinsurers)) {
                // If it's a single associative array (one reinsurer), wrap it
                if (array_key_exists('decline', $reinsurers)) {
                    $reinsurers = [$reinsurers];
                }

                $decline = array_column($reinsurers, 'decline');
            }
        }

        $reset = false;
        if (isset($reinsurers)) {
            if (is_array($reinsurers)) {
                foreach ($reinsurers as $idx => $re) {
                    if (!empty($re['decline']) && empty($re['decline_inserted']) && $unplaced_share > 0) {
                        $reset = true;
                        break;
                    }
                }
            }
        }
        if ($reset && (isset($decline) && !empty($decline))) {
            DB::table('pipeline_opportunities')
                ->where('opportunity_id', $leadId)
                ->where('pipeline_id', $pipeline)
                ->where('divisions', $division)
                ->update([
                    'stage' => 1,
                    'data_exists_flag' => 'Y',
                ]);
        } else {
            DB::table('pipeline_opportunities')
                ->where('opportunity_id', $leadId)
                ->where('pipeline_id', $pipeline)
                ->where('divisions', $division)
                ->update([
                    'stage' => $stage_cycle_fac,
                    'data_exists_flag' => 'Y',
                ]);
        }
    }

    public function stageCycleFacNotEqualNine($leadId, $stage_cycle_fac, $pipeline, $division, $request)
    {


        if ($stage_cycle_fac == 3) {
            if ($request->tender_status == 3) {
                DB::table('pipeline_opportunities')
                    ->where('opportunity_id', $leadId)->update([
                        'stage' => 3
                    ]);
            } else {
                DB::table('pipeline_opportunities')
                    ->where('opportunity_id', $leadId)->update([
                        'pipeline_id' => null,
                        'year_before_revert' => DB::raw('pip_year'),
                        'pip_year' => DB::raw('pip_year + 1'),
                        'reverted_to_pipeline' => 'YES',
                        'stage' => 10
                    ]);
            }
        } else {
            DB::table('pipeline_opportunities')
                ->where('opportunity_id', $leadId)
                ->where('pipeline_id', $pipeline)
                ->where('divisions', $division)
                ->update([
                    'stage' => $stage_cycle_fac,
                    'data_exists_flag' => 'Y',
                ]);
        }
    }

    public function stageCycleFacEqualFour($leadId, $stage_cycle_fac, $pipeline, $division)
    {
        DB::table('pipeline_opportunities')
            ->where('opportunity_id', $leadId)
            ->where('pipeline_id', $pipeline)
            ->where('divisions', $division)
            ->update([
                'stage' => $stage_cycle_fac,
            ]);
        // DB::commit();
        return redirect()->route('lead.handover', ['prospect' => $leadId]);
    }

    public function stageCycleFac($leadId, $stage_cycle_fac)
    {
        DB::table('pipeline_opportunities')
            ->where('opportunity_id', $leadId)
            ->update([
                'pq_status' => $stage_cycle_fac,
            ]);
    }

    public function stageCycleFacWon($leadId)
    {
        $pip = DB::table('pipeline_opportunities')
            ->where('opportunity_id', $leadId)->first();
        $data = [
            'name' => $pip->fullname,
            'contact' => $pip->contact_name,
            'email' => $pip->email,
            'phone' => $pip->phone,
            'start_date' => $pip->effective_date,
        ];

        // Mail::to('mutuaian176@gmail.com')->send(new Prospectwonemail($data));
        Mail::to('marketing@accentriagroup.com')->send(new Prospectwonemail($data));
    }

    public function sendEmailFacSlipPerReinsurer($opportunityID, $customer_id, $stage, $stageType, $request)
    {

        $allQuotes = QuoteReinsurers::where('opportunity_id', $opportunityID)
            ->whereIn('reinsurer_id', $customer_id)
            ->where('stage', $stage)
            ->with('quote')->get();

        $quoteSchedules = QuoteSchedule::selectRaw('DISTINCT ON (schedule_id) *')
            ->where('opportunity_id', $opportunityID)
            ->where('stage', $stage)
            ->orderBy('schedule_id')
            ->orderBy('stage', 'desc')
            ->get();


        $uncheckedCount = PipelineOpportunity::where('opportunity_id', $opportunityID)
            ->value('decline_unchecked_count');

        $edit_contact_name = $request->edit_contact_name ?? null;
        $emailCC = $request->emailCC ?? null;
        $reinsurerCCEmail = $request->selected_contact_person;
        $cedant_main_email = isset($request->selected_contact_person_main) ? $request->selected_contact_person_main['contact_email'][0] : null;
        $cedant_cc = isset($request->cedant_contact_person_cc) ? $request->cedant_contact_person_cc['cedant_cc'] : null;
        $cedant_contact_name = isset($request->selected_contact_person_main) ? $request->selected_contact_person_main['contact_name'][0] : null;
        // $emailBody = isset($request->emailBody) ? $request->emailBody : null;




        $columns = [
            'customers.name as customer_name',
            'customers.email as customer_email',
            'customers.email as customer_email',
            'reins_division.division_name as divison_name',
            'classes.class_name as class_name',
            'business_types.bus_type_name as bus_type_name',
            'pipeline_opportunities.effective_date as effective_date',
            'pipeline_opportunities.closing_date as closing_date',
            'pipeline_opportunities.insured_name as insured_name',
            'pipeline_opportunities.classcode as classcode',
            'pipeline_opportunities.currency_code as currency_code',
            'pipeline_opportunities.unplaced_share as unplaced_share',
            'pipeline_opportunities.fac_share_offered as fac_share_offered',
        ];

        $opportunity = PipelineOpportunity::query()
            ->select($columns)
            ->join('reins_division', function ($join) {
                $join->on('pipeline_opportunities.divisions', '=', 'reins_division.division_code');
            })
            ->join('customers', function ($q) {
                $q->on(DB::raw('CAST(pipeline_opportunities.customer_id AS INTEGER)'), '=', 'customers.customer_id');
            })
            ->join('classes', function ($join) {
                $join->on('pipeline_opportunities.classcode', '=', 'classes.class_code');
            })
            ->join('business_types', function ($join) {
                $join->on('pipeline_opportunities.type_of_bus', '=', 'business_types.bus_type_id');
            })
            ->where('pipeline_opportunities.opportunity_id', $opportunityID)
            ->first();





        $class = Classes::where('class_code', $opportunity->classcode)->first();


        $company = Company::first();
        $stgcomentData = StageComment::where('prospect_id', $opportunityID)
            ->orderByDesc('id')
            ->first();


        if (($stage == 3 && $stageType == 2) || ($stage == 3 && $stageType == 1)) {
            $view_path = 'printouts.';
            $view_name = $view_path . 'fac_coverslipquote_new';
            $unplaced_share = $opportunity->unplaced_share;
            $fac_share_offered = $opportunity->fac_share_offered;

            $data = [
                'quotes' => $allQuotes,
                'quoteSchedules' => $quoteSchedules,
                'opportunity' => $opportunity,
                'company' => $company,
                'class' => $class,
                'stcomentData' => $stgcomentData,
                'stage' => $stage,
                'stageType' => $stageType,
                'unplaced_share' => $unplaced_share,
                'fac_share_offered' => $fac_share_offered
            ];

            $pdfFolderPath = 'uploads';
            if (!file_exists($pdfFolderPath)) {
                @mkdir($pdfFolderPath, 0777, true); // Suppress warning if directory exists
            }

            if ($stageType == 1) {
                $pdfFilename = $class->class_name . ' ' . 'Quotation Slip' . '_' . mt_rand() . '.pdf';
            } else {
                $pdfFilename = $class->class_name . ' ' . 'Offer Slip' . '_' . mt_rand() . '.pdf';
            }
            $pdfPath = $pdfFolderPath . '/' . $pdfFilename;

            $pdf = Pdf::loadView($view_name, $data)
                ->setPaper('a4', 'portrait')
                ->setWarnings(false);

            $pdf->set_option('isHtml5ParserEnabled', true);
            $pdf->set_option('isPhpEnabled', true);
            $pdf->set_option('isRemoteEnabled', true);
            $pdf->render();

            // Save the PDF file
            try {
                Storage::disk('s3')->put($pdfPath, $pdf->output());

                // Check if the PDF was saved in S3
                if (!Storage::disk('s3')->exists($pdfPath)) {
                    return response()->json(['error' => 'Failed to save PDF to S3.'], 500);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
            }

            $filePath = [];
            $fileName = [];

            if ($request->hasFile('document_file_email_attachment')) {

                $pdfFolderPath = 'uploads';

                foreach ($request->document_file_email_attachment as $index => $file) {

                    $uploadedFile = $request->file('document_file_email_attachment')[$index];
                    $document_file_email_attachment_name = $request->document_name_email_attachment[$index] ?? 'unknown';

                    $generatedFileName = $class->class_name . ' ' . $document_file_email_attachment_name . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();
                    $generatedFilePath = $pdfFolderPath . '/' . $generatedFileName;

                    try {
                        // Upload file to S3
                        Storage::disk('s3')->put($generatedFilePath, file_get_contents($uploadedFile), ['visibility' => 'public']);

                        // Verify if the file was uploaded
                        if (!Storage::disk('s3')->exists($generatedFilePath)) {
                            return response()->json(['error' => "File upload failed at index $index. File not found in S3."], 500);
                        }

                        $fileName[] = $generatedFileName;
                        $filePath[] = $generatedFilePath;
                    } catch (\Exception $e) {
                        return response()->json(['error' => "S3 upload error at index $index: " . $e->getMessage()], 500);
                    }
                }
            }


            $CedantCCEmails = [];
            if (!empty($reinsurerCCEmail) && is_array($reinsurerCCEmail)) {
                foreach ($reinsurerCCEmail['contact_email'] as $index => $ced_cc) {
                    if (!empty($ced_cc)) {
                        $CedantCCEmails[] = $reinsurerCCEmail['contact_email'][$index];
                    }
                }
            }
            if (
                !empty($request->selected_dept_user_email) &&
                !empty($request->selected_dept_user_email['dept_user_email']) &&
                is_array($request->selected_dept_user_email['dept_user_email'])
            ) {

                foreach ($request->selected_dept_user_email['dept_user_email'] as $deptEmail) {
                    $CedantCCEmails[] = $deptEmail;
                }
            }

            $contactName = $edit_contact_name ?? 'Valued Customer';
            $qtEmailBody = '
                        <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                          <tr>
                            <td>Greetings,</td>
                        </tr>
                            <tr>
                                <td style="padding-top:1px;">Kindly find attached terms for your review and confirm if we can proceed to bind cover.</td>
                            </tr>
                        </table>';

            $facEmailBody = '
                    <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                      <tr>
                            <td>Greetings,</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 1px;">We are pleased to present the subject facultative placement for your consideration on the attached placement slip.</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 1px;">Kindly review the placement and confirm we can proceed to bind cover.</td>
                        </tr>
                        <tr>
                            <td>Looking forward to your feedback.</td>
                        </tr>
                    </table>';

            $data = [

                'salutation' => $cedant_contact_name,
                'email' => $cedant_main_email,
                'cc' => $CedantCCEmails ?? [],
                'title' => firstUpper($stgcomentData->quote_title_intro) . " - " . firstUpper($opportunity->insured_name) . " - " . " (" . now()->year . ")" . " " . firstUpper($class->class_name) . "-" . firstUpper($opportunity->customer_name) . " - " . $allQuotes[0]->quote->quote_number,
                'body' => ($stageType == 1) ? $qtEmailBody : $facEmailBody
            ];


            // Dispatch the job
            SendQuoteJob::dispatch($data, $pdfPath, $filePath, $pdfFilename, $fileName);
        } else {
            if ($stage == 4) {
                $allQuotesData = [];
                $pdfFolderPath = 'uploads';

                if (!file_exists($pdfFolderPath)) {
                    @mkdir($pdfFolderPath, 0777, true); // Suppress warning if directory exists
                }

                // Generate a unique filename for the single PDF containing all reinsurers' quotes
                $documentType = ($stageType == 1) ? 'Quotation Slip' : 'Offer Slip';
                $pdfFilename = $class->class_name . ' ' . $documentType . '_' . mt_rand() . '_' . time() . '.pdf';
                $pdfPath = $pdfFolderPath . '/' . $pdfFilename;

                // Collect data for all quotes, each associated with a reinsurer
                foreach ($allQuotes as $index => $quote) {
                    // Fetch reinsurer data for the quote
                    $reinsurer = Customer::where('customer_id', $quote->reinsurer_id)->get();

                    $allQuotesData[] = [
                        'quote' => $quote,
                        'customers' => $reinsurer,
                        'quoteSchedules' => $quoteSchedules,
                        'opportunity' => $opportunity,
                        'company' => $company,
                        'class' => $class,
                        'stcomentData' => $stgcomentData,
                        'stage' => $stage,
                        'stageType' => $stageType,
                    ];
                }

                // Generate one PDF containing all reinsurers' quotes
                $view_path = 'printouts.';
                $view_name = $view_path . 'fac_coverslipquote_combined';
                $data = [
                    'allQuotesData' => $allQuotesData,
                    'stcomentData' => $stgcomentData,
                    'opportunity' => $opportunity,
                    'class' => $class,
                    'stage' => $stage,
                    'stageType' => $stageType,
                ];

                $pdf = Pdf::loadView($view_name, $data)
                    ->setPaper('a4', 'portrait')
                    ->setWarnings(false)
                    ->setOption('isHtml5ParserEnabled', true)
                    ->setOption('isPhpEnabled', true)
                    ->setOption('isRemoteEnabled', true);

                // Save the single PDF file with all reinsurers' quotes
                $pdf->render();
                try {
                    Storage::disk('s3')->put($pdfPath, $pdf->output(), ['visibility' => 'public']);

                    // Check if the PDF was saved in S3
                    if (!Storage::disk('s3')->exists($pdfPath)) {
                        return response()->json(['error' => 'Failed to save PDF to S3.'], 500);
                    }
                } catch (\Exception $e) {
                    return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
                }

                // Save document record if stage is 4 (one record for the single PDF)
                $mimetype = 'application/pdf';
                $prospect_doc_id = DB::table('prospect_docs')->insertGetId([
                    'description' => $documentType,
                    'prospect_id' => $opportunityID,
                    'prospect_status' => $stage,
                    'mimetype' => $mimetype,
                    'file' => $pdfFilename,
                ]);
            }
            foreach ($allQuotes as $index => $quote) {
                $reinsurer = Customer::where('customer_id', $quote->reinsurer_id)->get();

                $view_path = 'printouts.';
                $view_name = $view_path . 'fac_coverslipquote_new';
                $data = [
                    'quote' => $quote,
                    'customers' => $reinsurer,
                    'quoteSchedules' => $quoteSchedules,
                    'opportunity' => $opportunity,
                    'company' => $company,
                    'class' => $class,
                    'stcomentData' => $stgcomentData,
                    'stage' => $stage,
                    'stageType' => $stageType
                ];


                $pdfFolderPath = 'Uploads';
                if (!file_exists($pdfFolderPath)) {
                    @mkdir($pdfFolderPath, 0777, true); // Suppress warning if directory exists
                }
                // Generate a unique filename
                if ($stageType == 1) {
                    $pdfFilename = $class->class_name . ' ' . 'Quotation Slip' . '_' . mt_rand() . $quote->id . '_' . $quote->reinsurer_id . '.pdf';
                } else {
                    $pdfFilename = $class->class_name . ' ' . 'Offer Slip' . '_' . mt_rand() . $quote->id . '_' . $quote->reinsurer_id . '.pdf';
                }
                $pdfPath = $pdfFolderPath . '/' . $pdfFilename;

                $pdf = Pdf::loadView($view_name, $data)
                    ->setPaper('a4', 'portrait')
                    ->setWarnings(false);

                $pdf->set_option('isHtml5ParserEnabled', true);
                $pdf->set_option('isPhpEnabled', true);
                $pdf->set_option('isRemoteEnabled', true);
                $pdf->render();

                // Save the PDF file
                try {
                    Storage::disk('s3')->put($pdfPath, $pdf->output(), ['visibility' => 'public']);

                    // Check if the PDF was saved in S3
                    if (!Storage::disk('s3')->exists($pdfPath)) {
                        return response()->json(['error' => 'Failed to save PDF to S3.'], 500);
                    }
                } catch (\Exception $e) {
                    return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
                }
                $filePath = [];
                $fileName = [];

                if ($request->hasFile('document_file_email_attachment')) {

                    $pdfFolderPath = 'uploads';

                    foreach ($request->document_file_email_attachment as $index => $file) {

                        $uploadedFile = $request->file('document_file_email_attachment')[$index];
                        $document_file_email_attachment_name = $request->document_name_email_attachment[$index] ?? 'unknown';

                        // Generate unique file name
                        $generatedFileName = $class->class_name . ' ' . $document_file_email_attachment_name . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();
                        $generatedFilePath = $pdfFolderPath . '/' . $generatedFileName;

                        try {
                            // Upload file to S3
                            Storage::disk('s3')->put($generatedFilePath, file_get_contents($uploadedFile), ['visibility' => 'public']);

                            // Verify if the file was uploaded
                            if (!Storage::disk('s3')->exists($generatedFilePath)) {
                                return response()->json(['error' => "File upload failed at index $index. File not found in S3."], 500);
                            }

                            $fileName[] = $generatedFileName;
                            $filePath[] = $generatedFilePath; // Store S3 path
                        } catch (\Exception $e) {
                            return response()->json(['error' => "S3 upload error at index $index: " . $e->getMessage()], 500);
                        }
                    }
                }


                $mainEmail = null;
                if ($quote->main_contact_person === 'Y') {

                    $mainEmail = $quote->email;
                }
                // dd($mainEmail);
                if (!$mainEmail) {
                    $mainEmail = 'derrickriziki7@gmail.com';
                }

                $reinsurerCCEmails = [];
                $contactName = $edit_contact_name ?? $quote->contact_name ?? 'Valued Customer';
                if (!empty($reinsurerCCEmail['reinsurer_id']) && is_array($reinsurerCCEmail['reinsurer_id'])) {
                    foreach ($reinsurerCCEmail['reinsurer_id'] as $index => $reinsurerId) {
                        if ($quote->reinsurer_id == $reinsurerId) {
                            if (!empty($reinsurerCCEmail['contact_email'][$index])) {
                                $reinsurerCCEmails[] = $reinsurerCCEmail['contact_email'][$index];
                            }
                        }

                        $reinsurer = Customer::where('customer_id', $quote->reinsurer_id)->first();

                        if (!file_exists($pdfPath)) {
                            return; // Stop execution if the file does not exist
                        }
                    }
                }



                if (
                    !empty($request->selected_dept_user_email) &&
                    !empty($request->selected_dept_user_email['dept_user_email']) &&
                    is_array($request->selected_dept_user_email['dept_user_email'])
                ) {

                    foreach ($request->selected_dept_user_email['dept_user_email'] as $deptEmail) {
                        $reinsurerCCEmails[] = $deptEmail;
                    }
                }

                if ($stage == 2 && $uncheckedCount > 0) {
                    $qtEmailBody = '
                <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                  <tr>
                            <td>Greetings,</td>
                        </tr>
                    <tr>
                        <td>We wish to offer you quotation with the given terms. We look forward to your positive feedback.</td>
                    </tr>
                </table>';
                } else if ($stage == 2 && $uncheckedCount == 0) {
                    $qtEmailBody = '
                <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                  <tr>
                            <td>Greetings,</td>
                        </tr>
                    <tr>
                        <td style="padding-bottom: 1px;">Kindly favour us with terms as per the attached supporting documents.</td>
                    </tr>
                    <tr>
                        <td>We look forward to your positive feedback.</td>
                    </tr>
                </table>
                ';
                } else {
                    $qtEmailBody = '
                <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                  <tr>
                            <td>Greetings,</td>
                        </tr>
                    <tr>
                        <td>We  Confirm your support with the terms given as per the attached quotation slip.</td>
                    </tr>
                </table>';
                }


                $facEmailBody = '
                    <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                      <tr>
                            <td>Greetings,</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 1px;">We are pleased to present the subject facultative offer for your consideration. Please find attached the placement slip and supporting documents outlining the risk details.</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 1px;">Kindly review the offer and confirm your maximum line of support.</td>
                        </tr>
                        <tr>
                            <td>Looking forward to your feedback.</td>
                        </tr>
                    </table>';

                $data = [
                    'salutation' => $contactName,
                    'email' => $mainEmail,
                    'cc' => $reinsurerCCEmails ?? [],
                    'title' => firstUpper($stgcomentData->quote_title_intro) . "-" . firstUpper($opportunity->insured_name) . "- (" . now()->year . ")" . " " . firstUpper($class->class_name) . "-" . firstUpper($opportunity->customer_name) . " - " . $quote->quote->quote_number,
                    'body' => ($stageType == 1) ? $qtEmailBody : $facEmailBody
                ];
                SendQuoteJob::dispatch($data, $pdfPath, $filePath, $pdfFilename, $fileName);
            }
        }
    }

    public function sendTreatyEmail($opportunityID, $customer_id, $stage, $stageType, $request)
    {

        $allQuotes = QuoteReinsurers::where('opportunity_id', $opportunityID)
            ->whereIn('reinsurer_id', $customer_id)
            ->where('stage', $stage)
            ->with('quote')->get();
        // dd($allQuotes);

        // $quoteSchedules = QuoteSchedule::selectRaw('DISTINCT ON (schedule_id) *')
        //     ->where('opportunity_id', operator: $opportunityID)
        //     ->where('stage', $stage)
        //     ->orderBy('schedule_id')
        //     ->orderBy('stage', 'desc')
        //     ->get();

        // dd($quoteSchedules);

        $edit_contact_name = $request->edit_contact_name ?? null;
        $emailCC = $request->emailCC ?? null;
        $reinsurerCCEmail = $request->selected_contact_person;
        $cedant_main_email = isset($request->selected_contact_person_main) ? $request->selected_contact_person_main['contact_email'][0] : null;
        $cedant_cc = isset($request->cedant_contact_person_cc) ? $request->cedant_contact_person_cc['cedant_cc'] : null;
        $cedant_contact_name = isset($request->selected_contact_person_main) ? $request->selected_contact_person_main['contact_name'][0] : null;
        // $emailBody = isset($request->emailBody) ? $request->emailBody : null;




        $columns = [
            'customers.name as customer_name',
            'customers.email as customer_email',
            'customers.email as customer_email',


            'business_types.bus_type_name as bus_type_name',
            'pipeline_opportunities.effective_date as effective_date',
            'pipeline_opportunities.closing_date as closing_date',
            'pipeline_opportunities.insured_name as insured_name',




        ];

        $opportunity = PipelineOpportunity::query()
            ->select($columns)

            ->join('customers', function ($q) {
                $q->on(DB::raw('CAST(pipeline_opportunities.customer_id AS INTEGER)'), '=', 'customers.customer_id');
            })

            ->join('business_types', function ($join) {
                $join->on('pipeline_opportunities.type_of_bus', '=', 'business_types.bus_type_id');
            })
            ->where('pipeline_opportunities.opportunity_id', $opportunityID)
            ->first();





        // $class = Classes::where('class_code', $opportunity->classcode)->first();


        $company = Company::first();
        $stgcomentData = StageComment::where('prospect_id', $opportunityID)->first();






        //  if(($stage == 3 $$stageType == 2 ) ||  ($stage == 4 $$stageType == 1 ) )
        if (($stage == 4 && $stageType == 1) || ($stage == 3 && $stageType == 1)) {
            // $allQuotes = Quote::where('quote_id', $quoteId)->get();
            $view_path = 'printouts.';
            $view_name = $view_path . 'treaty_printout_email';
            $unplaced_share = $opportunity->unplaced_share;
            $comm_rate = $opportunity->comm_rate;

            $data = [
                'quotes' => $allQuotes,
                'quoteSchedules' => $quoteSchedules,
                'opportunity' => $opportunity,
                'company' => $company,
                // 'class' => $class,
                'stcomentData' => $stgcomentData,
                'stage' => $stage,
                'stageType' => $stageType,
                // 'unplaced_share' => $unplaced_share,
                // 'comm_rate' => $comm_rate
            ];

            $pdfFolderPath = 'uploads';
            if (!file_exists($pdfFolderPath)) {
                mkdir($pdfFolderPath, 0777, true);
            }

            if ($stageType == 1) {
                $pdfFilename = 'Quotation Slip' . mt_rand() . '.pdf';
            } else {
                $pdfFilename = 'Offer Slip' . mt_rand() . '.pdf';
            }
            $pdfPath = $pdfFolderPath . '/' . $pdfFilename;

            $pdf = Pdf::loadView($view_name, $data)
                ->setPaper('a4', 'portrait')
                ->setWarnings(false);

            $pdf->set_option('isHtml5ParserEnabled', true);
            $pdf->set_option('isPhpEnabled', true);
            $pdf->set_option('isRemoteEnabled', true);
            $pdf->render();

            // Save the PDF file
            file_put_contents($pdfPath, $pdf->output());

            // Check if the PDF was saved
            if (!file_exists($pdfPath)) {
                return;
            }

            if (!file_exists($pdfFolderPath)) {
                if (!mkdir($pdfFolderPath, 0777, true) && !is_dir($pdfFolderPath)) {
                    return response()->json(['error' => 'Failed to create uploads directory.'], 500);
                }
            }


            $filePath = [];
            $fileName = [];

            if ($request->hasFile('document_file_email_attachment')) {

                $pdfFolderPath = 'uploads';

                foreach ($request->document_file_email_attachment as $index => $file) {

                    $uploadedFile = $request->file('document_file_email_attachment')[$index];

                    $document_file_email_attachment_name = $request->document_name_email_attachment[$index] ?? 'unknown';

                    $generatedFileName = $document_file_email_attachment_name . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();
                    $generatedFilePath = $pdfFolderPath . '/' . $generatedFileName;


                    try {
                        $uploadedFile->move($pdfFolderPath, $generatedFileName);
                        $fileName[] = $generatedFileName;
                        $filePath[] = $generatedFilePath;
                    } catch (\Exception $e) {
                        return response()->json(['error' => "File move error at index $index: " . $e->getMessage()], 500);
                    }


                    if (!file_exists($generatedFilePath)) {
                        return response()->json(['error' => "File move failed at index $index. File not found in directory."], 500);
                    }
                }
            }




            $CedantCCEmails = [];
            if (!empty($reinsurerCCEmail) && is_array($reinsurerCCEmail)) {
                foreach ($reinsurerCCEmail['contact_email'] as $index => $ced_cc) {
                    if (!empty($ced_cc)) {
                        $CedantCCEmails[] = $reinsurerCCEmail['contact_email'][$index];
                    }
                }
            }
            if (
                !empty($request->selected_dept_user_email) &&
                !empty($request->selected_dept_user_email['dept_user_email']) &&
                is_array($request->selected_dept_user_email['dept_user_email'])
            ) {

                foreach ($request->selected_dept_user_email['dept_user_email'] as $deptEmail) {
                    $CedantCCEmails[] = $deptEmail;
                }
            }

            // if ($request->hasFile('document_file_email_attachment')) {
            $cedant_doc_present = false;
            $pdfFolderPath = 'uploads';
            if (isset($request->cedant_checkbox_docs_id)) {
                $cedant_doc_present = true;
                foreach ($request->cedant_checkbox_docs_id as $id) {
                    $doc_type = DB::table('doc_types')->where('id', $id)->first();
                    $file_name = $doc_type->file_name;
                    $filePath[] = $pdfFolderPath . '/' . $file_name;
                    $fileName[] = $file_name;
                }
            }

            $contactName = $edit_contact_name ?? 'Valued Customer';
            $qtEmailBody = '
                        <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                            <tr>
                                <td>Greetings,</td>
                            </tr>
                            <tr>
                                <td style="padding-top: 10px;">Kindly favour us with terms as per the attached supporting documents. We look forward to your positive feedback.</td>
                            </tr>
                        </table>';

            $facEmailBody = '
                    <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                        <tr>
                            <td>Greetings,</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 10px;">We are pleased to present the subject facultative placement for your consideration on the attached placement slip.</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 10px;">Kindly review the placement and confirm we can proceed to bind cover.</td>
                        </tr>
                        <tr>
                            <td style="padding-top: 10px;">Looking forward to your feedback.</td>
                        </tr>
                    </table>';

            $data = [

                'salutation' => $cedant_contact_name,
                'email' => $cedant_main_email,
                'cc' => $CedantCCEmails ?? [],
                'title' => 'Response to document you requested',
                'body' => ($stageType == 1) ? $qtEmailBody : $facEmailBody,
                'cedant_doc_present' => $cedant_doc_present,
                'docs_we_require' => $request->our_checkbox_docs ?? [],
                'received_docs' => $request->received_docs_checkboxes ?? [],
            ];

            TreatyJob::dispatch($data, $fileName, $cedant_doc_name = [], $pdfFilename, $stage);
            // TreatyJob::dispatch($data, $fileName);


            // Dispatch the job
        } else {
            foreach ($allQuotes as $index => $quote) {

                $reinsurer = Customer::where('customer_id', $quote->reinsurer_id)->get();






                $customer = Customer::where('customer_id', $quote->reinsurer_id)
                    ->first();
                $contact_person = CustomerContact::where('customer_id', $quote->reinsurer_id)->value('contact_name');



                $company = Company::first();




                $tender = Tender::where('prospect_id', $opportunityID)
                    // ->select('tender_no', 'commence_year', 'email_dated')
                    ->first();


                $view_path = 'printouts.';
                $view_name = $view_path . 'reply_cedant_document_requested';
                $data = [
                    'tender' => $tender,
                    'customer' => $customer,
                    'contact_person' => $contact_person,
                    'email_dated' => $request->email_dated,
                    'commence_year' => $request->commence_year,
                    'our_checkbox' => $request->our_checkbox_docs,
                    'received_docs_checkboxes' => $request->received_docs_checkboxes,
                    //     'quote' => $quote,
                    //     'customers' => $reinsurer,
                    //     'quoteSchedules' => $quoteSchedules,
                    //     'opportunity' => $opportunity,
                    'company' => $company,
                    //     'class' => $class,
                    //     'stcomentData' => $stgcomentData,
                    'stage' => $stage,
                    'stageType' => $stageType
                ];






                $pdfFolderPath = 'uploads/tender_letters';
                if (!file_exists($pdfFolderPath)) {
                    mkdir($pdfFolderPath, 0777, true); // Create folder if it does not exist
                }

                // // Generate a unique filename
                if ($stageType == 1) {
                    $pdfFilename = 'Quotation' . mt_rand() . $quote->id . '_' . $quote->reinsurer_id . '.pdf';
                } else {
                    $pdfFilename = $tender->tender_no . ' ' . 'Letter Requesting Documents' . '_' . mt_rand() . '.pdf';
                }
                $pdfPath = $pdfFolderPath . '/' . $pdfFilename;

                $pdf = Pdf::loadView($view_name, $data)
                    ->setPaper('a4', 'portrait')
                    ->setWarnings(false);

                $pdf->set_option('isHtml5ParserEnabled', true);
                $pdf->set_option('isPhpEnabled', true);
                $pdf->set_option('isRemoteEnabled', true);
                $pdf->render();
                try {
                    Storage::disk('s3')->put($pdfPath, $pdf->output());


                    // Check if the PDF was saved in S3
                    if (!Storage::disk('s3')->exists($pdfPath)) {
                        return response()->json(['error' => 'Failed to save PDF to S3.'], 500);
                    }
                } catch (\Exception $e) {
                    return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
                }
                $filePath = [];
                $fileName = [];
                $cedant_doc_name = [];


                if ($request->hasFile('document_file_email_attachment')) {

                    $pdfFolderPath = 'uploads';

                    foreach ($request->document_file_email_attachment as $index => $file) {

                        $uploadedFile = $request->file('document_file_email_attachment')[$index];

                        $document_file_email_attachment_name = $request->document_name_email_attachment[$index] ?? 'unknown';

                        $generatedFileName = $document_file_email_attachment_name . '_' . time() . '.' . $uploadedFile->getClientOriginalExtension();
                        $generatedFilePath = $pdfFolderPath . '/' . $generatedFileName;


                        try {
                            $uploadedFile->move($pdfFolderPath, $generatedFileName);
                            $fileName[] = $generatedFileName;
                            $filePath[] = $generatedFilePath;
                        } catch (\Exception $e) {
                            return response()->json(['error' => "File move error at index $index: " . $e->getMessage()], 500);
                        }


                        if (!file_exists($generatedFilePath)) {
                            return response()->json(['error' => "File move failed at index $index. File not found in directory."], 500);
                        }
                    }
                }


                // if ($request->hasFile('document_file_email_attachment')) {
                $cedant_doc_present = false;
                // $pdfFolderPath = 'uploads';
                if (isset($request->cedant_checkbox_docs_id)) {
                    $cedant_doc_present = true;
                    foreach ($request->cedant_checkbox_docs_id as $id) {
                        $doc_type = DB::table('doc_types')->where('id', $id)->first();
                        $file_name = $doc_type->file_name;
                        $filePath[] = $pdfFolderPath . '/' . $file_name;
                        $cedant_doc_name[] = $file_name;
                    }
                }

                // }



                $mainEmail = null;
                if ($quote->main_contact_person === 'Y') {

                    $mainEmail = $quote->email;
                }


                $reinsurerCCEmails = [];
                $contactName = $edit_contact_name ?? $quote->contact_name ?? 'Valued Customer';
                if (!empty($reinsurerCCEmail['reinsurer_id']) && is_array($reinsurerCCEmail['reinsurer_id'])) {
                    foreach ($reinsurerCCEmail['reinsurer_id'] as $index => $reinsurerId) {
                        if ($quote->reinsurer_id == $reinsurerId) {
                            if (!empty($reinsurerCCEmail['contact_email'][$index])) {
                                $reinsurerCCEmails[] = $reinsurerCCEmail['contact_email'][$index];
                            }
                        }

                        $reinsurer = Customer::where('customer_id', $quote->reinsurer_id)->first();

                        // if (!file_exists($pdfPath)) {
                        //     return; // Stop execution if the file does not exist
                        // }
                    }
                }



                if (
                    !empty($request->selected_dept_user_email) &&
                    !empty($request->selected_dept_user_email['dept_user_email']) &&
                    is_array($request->selected_dept_user_email['dept_user_email'])
                ) {

                    foreach ($request->selected_dept_user_email['dept_user_email'] as $deptEmail) {
                        $reinsurerCCEmails[] = $deptEmail;
                    }
                }
                $qtEmailBody = '
                <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                    <tr>
                        <td style="padding-bottom: 10px;">Greetings,</td>
                    </tr>
                    <tr>
                        <td>Response to document you requested.</td>
                    </tr>
                </table>';

                $facEmailBody = '
                    <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                        <tr>
                            <td style="padding-bottom: 10px;">Greetings,</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px;">We are pleased to present the subject facultative offer for your consideration. Please find attached the placement slip and supporting documents outlining the risk details.</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 10px;">Kindly review the offer and confirm your maximum line of support.</td>
                        </tr>
                        <tr>
                            <td>Looking forward to your feedback.</td>
                        </tr>
                    </table>';


                $data = [
                    'salutation' => $contactName,
                    'email' => $mainEmail,
                    'cc' => $reinsurerCCEmails ?? [],
                    'title' => 'Request To Submit Treaty Doocuments.',
                    'body' => ($stageType == 1) ? $qtEmailBody : $facEmailBody,
                    'cedant_doc_present' => $cedant_doc_present,
                    'docs_we_require' => $request->our_checkbox_docs ?? [],
                    'received_docs' => $request->received_docs_checkboxes ?? [],
                ];
                // SendQuoteJob::dispatch($data, $pdfPath, $filePath, $pdfFilename, $fileName);
                TreatyJob::dispatch($data, $fileName, $cedant_doc_name, $pdfFilename, $stage, $stageType, $pdfPath);
            }
        }
    }

    public function generateQuoteNo($category_type, $opportunity_id)
    {

        $existingQuote = Quote::where('opportunity_id', $opportunity_id)->first();
        if ($existingQuote) {
            return $existingQuote->id;
        }

        $prefix = ($category_type == 1) ? 'FQ' : (($category_type == 2) ? 'FO' : '');

        if (!$prefix) {
            return null;
        }
        $lastQuote = Quote::where('quote_number', 'LIKE', "$prefix%")
            ->latest('quote_number')
            ->first();
        $newNumber = $lastQuote
            ? (intval(substr($lastQuote->quote_number, 2)) + 1)
            : 1;
        $quoteNumber = $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);

        $quoteId = Quote::insertGetId([
            'quote_number' => $quoteNumber,
            'opportunity_id' => $opportunity_id
        ]);

        return $quoteId;
    }

    public function bd_handovers(Request $request)
    {
        return view('pipeline.bd_handovers');
    }

    public function bd_handovers_datatable(Request $request)
    {
        $pipelines = PipelineOpportunity::where('handed_over', 'Y')->has('handovers')->with('handovers')->get();

        return Datatables::of($pipelines)
            ->addColumn('customer_name', function ($d): mixed {
                if (empty($d->customer_id)) {
                    return 'N/A';
                }
                $lead = DB::table('customers')
                    ->where('customer_id', (int) $d->customer_id)
                    ->first();
                return $lead ? $lead->name : 'N/A';
            })
            ->addColumn('cedant', function ($d) {
                $customer = Customer::where('customer_id', $d->customer_id)->first(['customer_id', 'name']);
                return $customer->name ?? '--';
            })
            ->addColumn('division_name', function ($d) {
                $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                if (is_null($division)) {
                    return '';
                }
                return $division ? $division->division_name : 'N/A';
            })
            ->addColumn('business_class', function ($d) {
                $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                if (is_null($business_class)) {
                    return '';
                }
                return $business_class ? $business_class->class_name : 'N/A';
            })
            ->addColumn('cedant_premium', function ($d) {
                return number_format($d->cede_premium, 2, '.', ',');
            })
            ->addColumn('effective_sum_insured', function ($d) {
                return number_format($d->effective_sum_insured, 2, '.', ',');
            })
            ->addColumn('action', function ($fn) {
                $approvals = HandoverApproval::where('prospect_id', $fn->opportunity_id)
                    ->select('approval_status', 'intergrate')
                    ->get();

                $pendingApproval = $approvals->contains(function ($approval) {
                    return is_null($approval->approval_status) || $approval->approval_status === '';
                });

                $isApprovedNotIntegrated = $approvals->contains(function ($approval) {
                    return $approval->approval_status === '1' && $approval->intergrate === false;
                });

                $isRejected = $approvals->contains(function ($approval) {
                    return $approval->approval_status === '0';
                });

                $actionButtons = '';

                if ($pendingApproval) {
                    $actionButtons .= sprintf(
                        '<button type="button" class="btn btn-sm btn-success btn-sm-action approve-btn" data-id="%s">Approve <i class="bx bx-check"></i></button> ',
                        $fn->id
                    );
                }

                if ($isApprovedNotIntegrated) {
                    $actionButtons .= sprintf(
                        '<button type="button" class="btn btn-sm btn-dark btn-sm-action rotate-fill integrate-btn" data-id="%s">Integrate <i class="bx bx-analyse"></i></button> ',
                        $fn->id
                    );
                }

                $actionButtons .= sprintf(
                    '<div id="dropdown-%1$s" class="btn-group my-0">
                        <button type="button" id="dropdown-btn-%1$s" class="btn btn-icon btn-sm btn-light p-0" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul id="dropdown-menu-%1$s" class="dropdown-menu">
                          <li><a id="action-item-%1$s" class="dropdown-item review-btn" href="javascript:void(0);" data-url="%2$s">Review</a></li>',
                    $fn->id,
                    route('lead.handover', ['prospect' => $fn->opportunity_id, 'approval' => 1, 'action' => 'review'])
                );

                if ($pendingApproval) {
                    $actionButtons .= sprintf(
                        '<li><a id="reject-action-%s" class="dropdown-item reject-bd-btn" href="javascript:void(0);" data-id="%s">Reject</a></li>',
                        $fn->id,
                        $fn->id
                    );
                }

                if ($isRejected) {
                    $h = $fn->handovers->where('prospect_id', $fn->opportunity_id)->select('reason_for_rejection')->first();
                    if ($h) {
                        $reason_for_rejection = $h['reason_for_rejection'];
                    } else {
                        $reason_for_rejection = 'No reason provided';
                    }
                    $actionButtons .= sprintf(
                        '<li><a id="rejected-comment-action-%s" class="dropdown-item rejected-bd-comment" href="javascript:void(0);" data-reason="%s">Rejected comment</a></li>',
                        $fn->id,
                        $reason_for_rejection
                    );
                }

                $actionButtons .= '</ul></div>';

                return $actionButtons;
            })
            ->addColumn('bd_status', function ($fn) {
                $approvals = HandoverApproval::where('prospect_id', $fn->opportunity_id)
                    ->select('approval_status', 'intergrate')
                    ->get();
                $status_label = '<span class="badge bg-warning rounded-pill">Pending <i class="bx bx-time"></span>';

                $pendingApproval = $approvals->contains(function ($approval) {
                    return is_null($approval->approval_status) || $approval->approval_status === '';
                });

                $isApproved = $approvals->contains(function ($approval) {
                    return $approval->approval_status === 1;
                });

                $isApprovedAndIntergrated = $approvals->contains(function ($approval) {
                    return $approval->approval_status === '1' && $approval->intergrate === true;
                });

                $isApprovedNotIntegrated = $approvals->contains(function ($approval) {
                    return $approval->approval_status === '1' && $approval->intergrate === false;
                });

                $isRejected = $approvals->contains(function ($approval) {
                    return $approval->approval_status === '0';
                });

                if ($pendingApproval) {
                    $status_label = '<span class="badge bg-warning rounded-pill">Pending <i class="bx bx-time"></span>';
                } else if ($isRejected) {
                    $status_label = '<span class="badge bg-danger rounded-pill">Rejected <i class="bx bx-x"></span>';
                } else if ($isApproved) {
                    $status_label = '<span class="badge bg-success rounded-pill">Approved <i class="bx bx-check-circle"></i></span>';
                } else if ($isApprovedNotIntegrated) {
                    $status_label = '<span class="badge bg-warning rounded-pill">Waiting <i class="bx bx-time"></i></span>';
                } else if ($isApprovedAndIntergrated) {
                    $status_label = '<span class="badge bg-dark rounded-pill">Integrated <i class="bx bx-check"></i></span>';
                }

                return $status_label;
            })
            ->rawColumns(['action', 'bd_status'])
            ->make(true);
    }

    public function reinsurers_declined(Request $request)
    {
        $opportunity_id = $request->prospect;
        $declined_reinsurers = ReinsurersDeclined::with('customer_name')->where('opportunity_id', $opportunity_id)->get();
        $decline_unchecked_count = PipelineOpportunity::where('opportunity_id', $opportunity_id)
            ->pluck('decline_unchecked_count');

        return response()->json([
            'declined_reinsurers' => $declined_reinsurers,
            'decline_unchecked_count' => $decline_unchecked_count
        ]);
    }

    public function filterReinsurers(Request $request)
    {
        $stage = $request->stage;
        $opportunity_id = $request->opportunity_id;

        $reinsurers = QuoteReinsurers::where('stage', $stage)
            ->where('opportunity_id', $opportunity_id)
            ->get();
        return Response()->Json(['reinsurers' => $reinsurers]);
    }

    public function saveTenderDocs(Request $request)
    {
        $validated = $request->validate([
            'prospect_id' => 'required',
            'prospect_status' => 'required|integer',
            'reinsurer_id' => 'required',
            'our_checkbox_docs' => 'sometimes|array|min:1',
            'document_file_email_attachment.*' => 'sometimes|file|mimes:pdf,doc,docx,png,jpg|max:20480',
            'reinsurer_emails' => 'required|array|min:1',
            'reinsurer_emails.*' => 'email',
            'selected_dept_user_email.dept_user_email' => 'sometimes|array',
            'selected_dept_user_email.dept_user_email.*' => 'email',
            // 'date_received.*' => 'sometimes|date',
        ]);

        $uploadsPath = 'uploads/cedant_docs';
        $leadId = $request->prospect_id;
        $stage_cycle = $request->prospect_status;
        $stage = 5; // From your context (stage_id == 5)
        $stageType = 2; // From your context (category_type == 2)


        DB::beginTransaction();

        // Fetch existing documents
        $existingDocs = DB::table('prospect_docs')
            ->where('prospect_id', $leadId)
            ->where('prospect_status', $stage_cycle)
            ->get()
            ->keyBy('description');

        // Save documents to prospect_docs
        $receivedDocuments = $request->received_docs_checkboxes ?? [];
        $missingDocuments = $request->missing_docs ?? [];
        $savedDocuments = [];
        $required_docs = [];
        if (!is_null($request->reinsurers)) {
            foreach ($request->reinsurers as $reinsurerIndex => $reinsurer) {
                $reinsurer_id = $reinsurer['reinsurer_id'] ?? null;

                if (!empty($reinsurer['documents'])) {
                    foreach ($reinsurer['documents'] as $docIndex => $doc) {
                        $name = $doc['title'];
                        $file = $doc['file'] ?? null;
                        $received_date = $doc['received_date'] ?? null;
                        $required_docs[] = $name;

                        $data = [
                            'description' => $name,
                            'prospect_id' => $leadId,
                            'prospect_status' => $stage_cycle,
                            'received_date' => $received_date,
                            'received_document' => false,
                            'updated_at' => now(),
                        ];

                        if (!is_null($file) && $file->isValid()) {
                            $mimetype = $file->getClientMimeType();
                            $fileContent = file_get_contents($file);
                            $originalNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                            $filename = mt_rand() . '_' . $originalNameWithoutExtension . '.' . $file->getClientOriginalExtension();
                            $s3FilePath = $uploadsPath . '/' . $filename;

                            try {
                                $result = Storage::disk('s3')->put($s3FilePath, $fileContent, [
                                    'visibility' => 'public',
                                ]);
                                logger("S3 upload result for $s3FilePath: " . json_encode($result));

                                if (!Storage::disk('s3')->exists($s3FilePath)) {
                                    logger("Failed.ConcurrentModificationException: Failed to verify file in S3: $s3FilePath");
                                    throw new \Exception('Failed to save file to S3.');
                                }

                                $data['file'] = $filename;
                                $data['mimetype'] = $mimetype;
                                $data['received_document'] = true;
                                $savedDocuments[] = [
                                    'name' => $name,
                                    'filename' => $filename,
                                    'path' => $s3FilePath
                                ];
                            } catch (\Exception $e) {
                                logger("S3 upload error for $s3FilePath: " . $e->getMessage());
                                throw new \Exception('S3 upload error: ' . $e->getMessage());
                            }
                        }

                        if (isset($existingDocs[$name])) {
                            // Update existing document
                            DB::table('prospect_docs')
                                ->where('id', $existingDocs[$name]->id)
                                ->update($data);
                        } else {
                            // Create new document
                            $data['created_at'] = now();
                            DB::table('prospect_docs')->insert($data);
                        }
                    }
                }
            }
        }
        $status = 201;
        if (empty($missingDocuments)) {
            $status = 301;
            DB::table('pipeline_opportunities')
                ->where('opportunity_id', $leadId)->update([
                    'stage' => 4
                ]);
        }

        // Email sending logic
        $customer = Customer::where('customer_id', $reinsurer_id)->first();
        $contact_person = CustomerContact::where('customer_id', $reinsurer_id)->value('contact_name') ?? 'Valued Customer';
        $company = Company::first();
        $tender = Tender::where('prospect_id', $leadId)->first();

        if (!$tender) {
            throw new \Exception('Tender not found for the provided prospect ID.');
        }

        $view_path = 'printouts.';
        $view_name = $view_path . 'reply_cedant_document_requested';
        $data = [
            'tender' => $tender,
            'customer' => $customer,
            'contact_person' => $contact_person,
            'email_dated' => $request->email_dated ?? now()->format('Y-m-d'),
            'commence_year' => $request->commence_year ?? now()->year,
            'our_checkbox' => $missingDocuments ?? [],
            'received_docs_checkboxes' => $receivedDocuments ?? [],
            'company' => $company,
            'stage' => 4,
            'stageType' => $stageType
        ];

        $pdfFolderPath = 'Uploads/tender_letters';
        $pdfFilename = $tender->tender_no . ' Letter Requesting Documents_' . mt_rand() . '.pdf';
        $pdfPath = $pdfFolderPath . '/' . $pdfFilename;

        try {
            $pdf = Pdf::loadView($view_name, $data)
                ->setPaper('a4', 'portrait')
                ->setWarnings(false)
                ->set_option('isHtml5ParserEnabled', true)
                ->set_option('isPhpEnabled', true)
                ->set_option('isRemoteEnabled', true);
            $pdfContent = $pdf->output();
            Storage::disk('s3')->put($pdfPath, $pdfContent, ['visibility' => 'public']);

            if (!Storage::disk('s3')->exists($pdfPath)) {
                logger("Failed to save PDF to S3: $pdfPath");
                return response()->json(['error' => 'Failed to save PDF to S3.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'S3 upload error: ' . $e->getMessage()], 500);
        }

        $filePath = [];
        $fileName = [];
        $cedant_doc_name = [];

        // Handle uploaded files
        if ($request->hasFile('document_file_email_attachment')) {
            foreach ($request->file('document_file_email_attachment') as $index => $file) {
                if ($file->isValid()) {
                    $document_name = $request->document_name_email_attachment[$index] ?? 'unknown';
                    $generatedFileName = $document_name . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $generatedFilePath = $uploadsPath . '/' . $generatedFileName;

                    try {
                        Storage::disk('s3')->put($generatedFilePath, file_get_contents($file), ['visibility' => 'public']);
                        if (!Storage::disk('s3')->exists($generatedFilePath)) {
                            logger("Failed to save file to S3: $generatedFilePath");
                            return response()->json(['error' => "Failed to save file to S3: $generatedFilePath"], 500);
                        }
                        $fileName[] = $generatedFileName;
                        $filePath[] = $generatedFilePath;
                    } catch (\Exception $e) {
                        logger("S3 upload error for $generatedFilePath: " . $e->getMessage());
                        return response()->json(['error' => "S3 upload error: " . $e->getMessage()], 500);
                    }
                }
            }
        }


        $cedant_doc_present = false;
        if ($request->has('cedant_checkbox_docs_id')) {
            $cedant_doc_present = true;
            foreach ($request->cedant_checkbox_docs_id as $id) {
                $doc_type = DB::table('doc_types')->where('id', $id)->first();
                if ($doc_type && $doc_type->file_name) {
                    $filePath[] = $uploadsPath . '/' . $doc_type->file_name;
                    $cedant_doc_name[] = $doc_type->file_name;
                }
            }
        }

        // Email setup
        $mainEmail = $request->reinsurer_emails[0];
        $reinsurerCCEmails = [];
        $ccEmails = array_slice($request->reinsurer_emails, 1);
        $reinsurerCCEmails = array_merge($reinsurerCCEmails, $ccEmails);
        if ($request->has('selected_dept_user_email.dept_user_email')) {
            foreach ($request->selected_dept_user_email['dept_user_email'] as $deptEmail) {
                $reinsurerCCEmails[] = $deptEmail;
            }
        }

        $emailBody = '
            <table cellspacing="0" cellpadding="0" border="0" width="100%" style="font-family: Arial, sans-serif; font-size: 14px; color: #000;">
                <tr>
                    <td style="padding-bottom: 10px;">Greetings,</td>
                </tr>
                <tr>
                    <td>Response to document you requested.</td>
                </tr>
            </table>';

        $emailData = [
            'salutation' => $contact_person,
            'email' => $mainEmail,
            'cc' => $reinsurerCCEmails,
            'title' => 'Request To Submit Treaty Documents.',
            'body' => $emailBody,
            'cedant_doc_present' => $cedant_doc_present,
            'docs_we_require' => $request->our_checkbox_docs ?? [],
            'received_docs' => $request->received_docs_checkboxes ?? [],
        ];

        // Dispatch email job
        TreatyJob::dispatch($emailData, $fileName, $cedant_doc_name, $pdfFilename, $stage, $stageType, $pdfPath);


        DB::commit();




        return response()->json(['status' => $status, 'message' => 'Document saved successfully']);
    }

    public function TenderCedantContactPerson(Request $request)
    {
        $customerId = $request->customer_id;

        $customers = DB::select("
                        SELECT
                            c.*,
                            ARRAY_AGG(DISTINCT ct.type_id) AS type_ids,
                            (
                                SELECT json_agg(
                                    json_build_object(
                                        'contact_name', cc.contact_name,
                                        'contact_email', cc.contact_email,
                                        'contact_mobile_no', cc.contact_mobile_no,
                                        'contact_position', cc.contact_position,
                                        'main_contact_person', cc.main_contact_person
                                    )
                                )
                                FROM customer_contacts cc
                                WHERE cc.customer_id = c.customer_id
                            ) AS contact_persons
                        FROM customers c
                        LEFT JOIN customer_types ct
                            ON c.customer_type::jsonb @> to_jsonb(ct.type_id::text)
                        WHERE ct.code IN ('REINCO', 'INSCO', 'REINBROKER')
                        AND c.customer_id = ?
                        GROUP BY c.customer_id
                        ORDER BY c.name
                    ", [$customerId]);

        foreach ($customers as $customer) {
            $customer->contact_persons = json_decode($customer->contact_persons, true);
            $customer->type_ids = json_decode($customer->type_ids, true);
        }

        return response()->json(['customers' => $customers]);
    }
    public function TenderDocAttachement(Request $request)
    {
        $opportunityID = $request->opportunity_id;
        $data = PipelineOpportunity::where('opportunity_id', $opportunityID)->get();
        return view('Bd_views.tenders.doc_attachment', ['opportunities' => $data]);
    }
}
