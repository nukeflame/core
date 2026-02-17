<?php

namespace App\Http\Controllers\BdController;

use App\Models\Bd\BdPremtype;
use App\Models\Bd\BdReinclass;
use App\Models\Bd\BdReinlayer;
use App\Models\Bd\BdReinprop;
use App\Models\Bd\Client;
use App\Models\Bd\Leads\Pipeline;
use App\Models\Bd\Occupation;
use App\Models\Bd\Salutation;
use App\Models\Branch;
use App\Models\Broker;
use App\Models\BusinessType;
use App\Models\Classes;
use App\Models\ClassGroup;
use App\Models\Country;
use App\Models\Bd\Intermediary;
use App\Models\Bd\Leads\ActivityAttendees;
use App\Models\Bd\Leads\Leads;
use App\Models\Bd\Leads\LeadsSource;
use App\Models\Bd\Leads\LeadStatus;
use App\Models\Bd\PipelineOpportunity;
use App\Models\CoverRegister;
use App\Models\CoverType;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\CustomerTypes;
use App\Models\PayMethod;
use App\Models\PremiumPayTerm;
use App\Models\ReinclassPremtype;
use App\Models\ReinsClass;
use App\Models\ReinsDivision;
use App\Models\TreatyType;
use App\Models\TypeOfSumInsured;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use App\Repositories\ProspectRepository;
use App\Services\PipelineService;
use Exception;

class LeadsOnboardingController
{
    protected $repository;
    protected $pipelineService;

    public function __construct(ProspectRepository $repository,  PipelineService $pipelineService)
    {
        $this->repository = $repository;
        $this->pipelineService = $pipelineService;
    }

    public function index(Request $request)
    {
        try {
            $currentYear = now()->year;
            $endYear = $currentYear + 10;
            $years = range($currentYear, $endYear);
            $prospectCode = $request->prospect;

            $customer_types = CustomerTypes::select('type_id', 'type_name')->get();
            $salutations = Salutation::all();
            $sources = LeadsSource::all();
            $statuses = LeadStatus::whereIn('id', [2, 4, 5])->get();
            $engage_types = $sources;
            $industries = Occupation::all();
            $divisions = DB::table('divisions')->get();
            $clients = Client::select('full_name', 'global_customer_id', 'client_type', 'salutation_code', 'occupation_code')->get();
            $prospProperties = DB::table('pipeline_opportunities')
                ->where('opportunity_id', $prospectCode)
                ->first();
            $prospect = $prospProperties;
            $users = User::all();

            $currencies = Currency::all();
            $leadsources = DB::table('leadsources')->get();
            $countries = Country::all();
            $underwriters = DB::table('companies')->get();
            $branches = Branch::where('status', 'A')->get(['branch_code', 'branch_name', 'status']);
            $treatytypes = TreatyType::where('status', 'A')->get();
            $types_of_bus = BusinessType::get(['bus_type_id', 'bus_type_name']);
            $brokers = Broker::where('status', 'A')->get(['broker_code', 'broker_name', 'status']);
            $classes = Classes::where('status', 'A')->get(['class_code', 'class_name', 'status']);
            $types_of_sum_insured = TypeOfSumInsured::where('status', 'A')->get(['sum_insured_code', 'sum_insured_name', 'status']);
            $classGroups = ClassGroup::get(['group_code', 'group_name']);
            $paymethods = PayMethod::all();
            $premium_pay_terms = PremiumPayTerm::all();
            $covertypes = CoverType::all();
            $reinsdivisions = ReinsDivision::where('status', 'A')->get();
            $reinsclasses = ReinsClass::where('status', 'A')->get();
            $pipeYear = Pipeline::whereBetween('year', [$currentYear - 4, $currentYear])
                ->orderBy('year', 'asc')
                ->get();

            $contactDetails = DB::table('pipeline_opportunities')
                ->select('contact_name', 'phone', 'email', 'telephone')
                ->where('opportunity_id', $prospectCode)
                ->first();

            $contactNames = json_decode($contactDetails?->contact_name ?? '[]', true) ?: [];
            $emails = json_decode($contactDetails?->email ?? '[]', true) ?: [];
            $phones = json_decode($contactDetails?->phone ?? '[]', true) ?: [];
            $telephones = json_decode($contactDetails?->telephone ?? '[]', true) ?: [];

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
                ->whereIn('customer_types.slug', ['reinsurer', 'cedant'])
                ->distinct('name')
                ->get();

            $insured = DB::table('customers')
                ->join('customer_types', function ($join) {
                    $join->on('customer_types.type_id', '=', DB::raw("ANY (SELECT json_array_elements_text(customers.customer_type)::int)"));
                })
                ->select(
                    DB::raw('CAST(customers.customer_id AS INT) as customer_id'),
                    'customers.name'
                )
                ->where('customer_types.code', 'INSURED')
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
                'currencies' => $currencies,
                'covertypes' => $covertypes,
                'types_of_sum_insured' => $types_of_sum_insured,
                'reinsdivisions' => $reinsdivisions,
                'reinsclasses' => $reinsclasses,
                'treatytypes' => $treatytypes,
                'customers' => $customers,
                'contacts_det' => $contacts,
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
            ];

            return view('business_development.intermediaries.leads_onboarding', array_merge($commonVariables, $otherVariabales));
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to load onboarding form. Please try again.');
        }
    }

    public function treaty_index(Request $request)
    {
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
        $reinsdivisions = ReinsDivision::where('status', 'A')->get();
        $reinsclasses = ReinsClass::where('status', 'A')->get();
        $treatytypes = TreatyType::where('status', 'A')->get();
        $reinPremTypes = ReinclassPremtype::where('status', 'A')->get();
        $pipeYear = Pipeline::whereBetween('year', [$currentYear - 4, $currentYear])
            ->orderBy('year', 'desc')
            ->get();
        $Contact_details = DB::table('pipeline_opportunities')
            ->select('contact_name', 'phone', 'email', 'telephone')
            ->where('opportunity_id', "=", $request->prospect)->first();

        $contactNames = json_decode($Contact_details->contact_name ?? '[]', true);
        $emails = json_decode($Contact_details->email ?? '[]', true);
        $phones = json_decode($Contact_details->phone ?? '[]', true);
        $telephones = json_decode($Contact_details->telephone ?? '[]', true);

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
            ->whereIn('customer_types.slug', ['reinsurer', 'cedant'])
            ->distinct('name')
            ->get();

        $insured = DB::table('customers')
            ->join('customer_types', function ($join) {
                $join->on('customer_types.type_id', '=', DB::raw("ANY (SELECT json_array_elements_text(customers.customer_type)::int)"));
            })
            ->select(
                DB::raw('CAST(customers.customer_id AS INT) as customer_id'),
                'customers.name'
            )
            ->where('customer_types.code', 'INSURED')
            ->get();
        $reins_divisions = ReinsDivision::where('status', 'A')->get();

        $trans_type = $request->trans_type;
        $type_of_bus = $request->type_of_bus;
        $coverreinpropClasses = null;
        $coverreinprops = null;
        $coverReinLayers = null;
        $premtypes = null;
        $renewal_date = null;

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
            'trans_type' => $trans_type,
            'reins_divisions' => $reins_divisions,

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
            'coverreinpropClasses' => $coverreinpropClasses,
            'coverreinprops' => $coverreinprops,
            'premtypes' => $premtypes,
            'reinPremTypes' => $reinPremTypes,
            'coverReinLayers' => $coverReinLayers,
            'renewal_date' => $renewal_date,


        ];
        $allVariables = array_merge($commonVariables, $otherVariabales);

        if ($trans_type == 'NEW') {
            return view('business_development.Treaty.leads_onboarding', $allVariables);
        } else {
            return view('business_development.Treaty.leads_onboarding_update', $allVariables);
        }
    }
    public function customer_data(Request $request)
    {
        $customer_id = $request->customer_id;
        $customerdata = Customer::where('customer_id', $customer_id)->get();
        return response()->json(['status' => 'success', 'data' => $customerdata]);
    }

    public function save(Request $request)
    {
        $rules = [
            // 'source' => 'required',
            // 'industry' => 'required',
            'lead_year' => 'required',
            'email' => 'required|array',
            'email.*' => 'required|email|distinct',
            // 'phone_number' => 'required',
            // 'lead_owner' => 'required',
        ];

        $messages = [
            'required' => ':attribute is required',
            'email.*.distinct' => 'Email Address should be unique for each contact.',
        ];

        $nicenames = [
            'source' => 'Source',
            'industry' => 'Industry',
            'rating' => 'Rating',
            'email' => 'Email',
            'email.*' => 'Email Address',
            'phone_number' => 'Phone Number',
            'lead_owner' => 'Lead Owner',
            'lead_year' => 'Year',
        ];

        $nextCode = Leads::generateNextCode();

        $validator = Validator::make($request->all(), $rules, $messages, $nicenames);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        }

        DB::beginTransaction();

        try {
            $organic_reference = null;




            $result = $this->repository->registerCover($request);

            DB::commit();

            $status = 1;

            // return array('status' => 200, 'qstring' => Crypt::encrypt('pipeline=' . $pipeline->id . '&prospect=' . $nextCode));
            return null;
        } catch (Exception $e) {
            DB::rollback();

            return array('status' => 400);
        }
    }
    public function insertProspectReinProp($data)
    {
        // $CoverRegister = CoverRegister::where('endorsement_no', $this->_endorsement_no)->first();

        // $CoverReinProp = CoverReinProp::where('endorsement_no', $this->_endorsement_no)
        //     ->where('reinclass', $data['treaty_class'])
        //     ->where('item_description', $data['item_description']);

        // if ($CoverReinProp->count() > 0) {
        //     $CoverReinProp = $CoverReinProp->first();
        // } else {

        //     $count = CoverReinProp::where('cover_no', $CoverRegister->cover_no)
        //         ->where('endorsement_no', $this->_endorsement_no)
        //         ->count();
        //     $count = $count + 1;

        //     $CoverReinProp = new CoverReinProp();
        //     $CoverReinProp->cover_no = $CoverRegister->cover_no;
        //     $CoverReinProp->endorsement_no = $CoverRegister->endorsement_no;
        //     $CoverReinProp->item_no = $count;
        //     $CoverReinProp->created_by = Auth::user()->user_name;
        // }

        // $CoverReinProp->reinclass = $data['treaty_class'];
        // $CoverReinProp->item_description = $data['item_description'];
        // $CoverReinProp->retention_rate = $data['retention_per'];
        // $CoverReinProp->treaty_rate = $data['treaty_rate'];
        // $CoverReinProp->retention_amount = $data['retention_amount'];
        // $CoverReinProp->no_of_lines = $data['no_of_lines'];
        // $CoverReinProp->treaty_amount = $data['treaty_amount'];
        // $CoverReinProp->treaty_limit = $data['treaty_limit'];
        // $CoverReinProp->port_prem_rate = 0;
        // $CoverReinProp->port_loss_rate = 0;
        // $CoverReinProp->profit_comm_rate = 0;
        // $CoverReinProp->mgnt_exp_rate = 0;
        // $CoverReinProp->deficit_yrs = 0;
        // $CoverReinProp->estimated_income = $data['estimated_income'];
        // $CoverReinProp->cashloss_limit = $data['cashloss_limit'];
        // $CoverReinProp->updated_by = Auth::user()->user_name;

        // $CoverReinProp->save();
    }

    public function listing()
    {

        $kpis =  $this->pipelineService->getKPIs();
        $statuses = PipelineOpportunity::getStatusOptions();
        $classes =  PipelineOpportunity::getClassOptions();
        $classGroups =  PipelineOpportunity::getClassGroupsOptions();
        $priorities = PipelineOpportunity::getPriorityOptions();

        return view('business_development.intermediaries.leads_listing', compact(
            'kpis',
            'statuses',
            'classGroups',
            'classes',
            'priorities'
        ));
    }

    public function treaty_listing()
    {
        $kpis = $this->getTreatyKPIs();
        $statuses = PipelineOpportunity::getStatusOptions();
        $classes = PipelineOpportunity::getClassOptions();
        $classGroups = PipelineOpportunity::getClassGroupsOptions();
        $priorities = PipelineOpportunity::getPriorityOptions();

        return view('business_development.Treaty.leads_listing', compact(
            'kpis',
            'statuses',
            'classGroups',
            'classes',
            'priorities'
        ));
    }

    /**
     * Get Treaty Pipeline KPIs
     */
    public function getTreatyKPIs()
    {
        $query = DB::table('pipeline_opportunities')
            ->whereIn('type_of_bus', ['TPR', 'TNP']);

        // Total Pipeline Value
        $totalValue = $query->sum('cede_premium') ?? 0;

        // Weighted Value (probability-adjusted)
        $opportunities = $query->get();
        $weightedValue = $opportunities->sum(function ($opp) {
            $probability = $opp->probability ?? 0;
            $premium = $opp->cede_premium ?? 0;
            return ($probability / 100) * $premium;
        });

        // Active Treaties Count
        $activeCount = $query->whereNotNull('status')
            ->where('status', '!=', 'closed')
            ->count();

        // Average Probability
        $avgProbability = $query->avg('probability') ?? 0;

        // Stage Counts
        $stageCounts = [
            'all' => $query->count(),
            'qualification' => $query->where('stage', 1)->count(),
            'proposal' => $query->where('stage', 2)->count(),
            'due_diligence' => $query->where('stage', 3)->count(),
            'negotiation' => $query->where('stage', 4)->count(),
            'approval' => $query->where('stage', 5)->count(),
        ];

        return [
            'total_value' => $totalValue,
            'weighted_value' => $weightedValue,
            'active_count' => $activeCount,
            'avg_probability' => round($avgProbability, 0),
            'stage_counts' => $stageCounts,
        ];
    }

    public function get_leads()
    {

        $leads = DB::table('prospect_handover')->where('cr_processed', 'N')
            ->get();

        return response()->json(['status' => 'success', 'data' => $leads]);
    }

    public function getLeadDetails($leadId)
    {
        try {
            // $lead = Leads::join('pipeline_opportunities', 'leads.code', '=', 'pipeline_opportunities.opportunity_id')
            //         ->where('leads.code',$leadId)
            //         ->first();
            $lead = DB::table('prospect_handover')->where('prospect_id', $leadId)->first();

            return response()->json([
                'status' => 'success',
                'data' => $lead,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lead not found or an error occurred.',
            ], 404);
        }
    }

    // public function leads_get(Request $request)
    // {
    //     $currentYear = Carbon::now()->format('Y');

    //     $data = DB::table('pipeline_opportunities')
    //         // ->where('prequalification', '=', 'N')
    //         // ->where('pip_year', '>=', $currentYear)
    //         // ->where('stage', '<', '1')
    //         ->whereIn('type_of_bus', ['FNP', 'FPR'])
    //         // ->orderBy('created_at', 'desc')
    //         ->get();



    //     return Datatables::of($data)
    //         // ->editColumn('client_type', function ($lead) {
    //         //     if ($lead->client_type == 'C') {
    //         //         return "CORPORATE";
    //         //     } else {
    //         //         return "INDIVIDUAL";
    //         //     }
    //         // })
    //         ->editColumn('client_category', function ($lead) {
    //             if ((string) $lead->client_category === 'O') {
    //                 return 'Organic growth';
    //             } else {
    //                 return "New";
    //             }
    //         })
    //         // ->editColumn('insurance_class', function ($lead) {
    //         //     $class = DB::table('reinsclasses')->where('class_code', $lead->insurance_class)->first();
    //         //    // dd($class);
    //         //     return $class;

    //         // })
    //         ->editColumn('name', function ($lead) {
    //             $div = DB::table('customers')
    //                 ->where('customer_id', (int) $lead->customer_id)
    //                 ->first();
    //             return $div ? $div->name : 'N/A'; // Return customer name or 'N/A' if not found
    //         })

    //         ->editColumn('class', function ($lead) {
    //             $div = DB::table('classes')
    //                 ->where('class_code', (int) $lead->classcode)
    //                 ->first();
    //             return $div ? $div->class_name : 'N/A'; // Return customer name or 'N/A' if not found
    //         })

    //         ->editColumn('divisions', function ($lead) {
    //             $div = DB::table('reins_division')->where('division_code', $lead->divisions)->first();
    //             return $div ? $div->division_name : 'N/A';
    //         })
    //         ->addColumn('action', function ($lead) use ($request) {

    //             $url = route('leads.onboarding', ['prospect' => $lead->opportunity_id]);
    //             $handover = route('lead.handover', ['prospect' => $lead->opportunity_id]);
    //             $btn_handover = '<a href="' . $handover . '"><button class="btn btn-outline-success"><i class="fa fa-arrow->right"></i>handover</button></a>';
    //             $btn_edit = '<a href="' . $url . '"><span class="btn btn-info btn-sm rounded-pill"><i class="bx bx-edit"></i>  Edit prospect</span></a>';
    //             $btn_submited = '<span class="btn btn-dark btn-sm rounded-pill">Submitted To sales</span></a>';
    //             if (is_null($lead->pipeline_id)) {
    //                 return $btn_edit;
    //             } else {
    //                 return $btn_submited;
    //             }
    //         })
    //         ->rawColumns(['action'])
    //         ->make(true);
    // }

    public function leads_get(Request $request)
    {
        $currentYear = Carbon::now()->format('Y');
        $query = DB::table('pipeline_opportunities')
            ->whereIn('type_of_bus', ['FNP', 'FPR']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class')) {
            $query->where('classcode', $request->class);
        }

        if ($request->filled('class_group')) {
            $query->where('class_group', $request->class_group);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('global_search')) {
            $searchTerm = trim((string) $request->global_search);

            $query->where(function ($q) use ($searchTerm) {
                $q->where('opportunity_id', 'ilike', "%{$searchTerm}%")
                    ->orWhere('insured_name', 'ilike', "%{$searchTerm}%")
                    ->orWhere('status', 'ilike', "%{$searchTerm}%")
                    ->orWhere('priority', 'ilike', "%{$searchTerm}%");
            });
        }

        $data = $query->orderBy('created_at', 'desc');

        return Datatables::of($data)
            ->addColumn('opportunity_id', function ($lead) {
                return  '<strong>' . $lead->opportunity_id . '</strong>';
            })
            ->addColumn('cedant', function ($lead) {
                $customer = DB::table('customers')
                    ->where('customer_id', (int) $lead->customer_id)
                    ->first();

                $customerName = $customer ? $customer->name : 'N/A';

                return  '<strong>' . $customerName . '</strong>';
            })
            ->addColumn('insured_name', function ($lead) {
                return  '<strong>' . $lead->insured_name . '</strong>';
            })
            ->addColumn('class_of_business', function ($lead) {
                $class = DB::table('classes')
                    ->where('class_code', (int) $lead->classcode)
                    ->first();
                return $class ? $class->class_name : 'N/A';
            })
            ->addColumn('priority_badge', function ($lead) {
                $priority = $lead->priority ?? 'Normal';
                $badgeClass = match (strtolower($priority)) {
                    'critical' => 'priority-badge priority-critical',
                    'high' => 'priority-badge priority-high',
                    'medium' => 'priority-badge priority-medium',
                    'low' => 'priority-badge priority-low',
                    'normal' => 'priority-badge priority-medium',
                    default => 'priority-badge priority-medium'
                };

                return '<span class="' . $badgeClass . '">' . ucfirst($priority) . '</span>';
            })
            ->addColumn('status_badge', function ($lead) {
                $status = $lead->status ?? 'Active';
                $badgeClass = match (strtolower($status)) {
                    'active' => 'priority-badge priority-low',
                    'pending' => 'priority-badge priority-high',
                    'closed' => 'priority-badge priority-medium',
                    'cancelled' => 'priority-badge priority-high',
                    default => 'priority-badge priority-medium'
                };

                return '<span class="' . $badgeClass . '">' . ucfirst($status) . '</span>';
            })
            ->addColumn('formatted_premium', function ($lead) {
                return $lead->cede_premium ?
                    'KES ' . number_format($lead->cede_premium, 2) :
                    '-';
            })
            ->addColumn('commission_percentage', function ($lead) {
                return $lead->comm_rate ?? null;
            })
            ->addColumn('formatted_expected_premium', function ($lead) {
                // return $lead->expected_premium ?
                //     'KES ' . number_format($lead->expected_premium, 2) :
                //     '-';
                return '-';
            })
            ->addColumn('expiry_date', function ($lead) {
                return $lead->closing_date ?? '-';
            })
            ->addColumn('account_executive', function ($lead) {
                $ae = DB::table('users')
                    ->where('id', $lead->lead_owner ?? 0)
                    ->first();

                return [
                    'name' => $ae ? $ae->name : null
                ];
            })
            // ->addColumn('territory', function ($lead) {
            //     // $territory = DB::table('territories')
            //     //     ->where('territory_id', $lead->territory_id ?? 0)
            //     //     ->first();

            //     // return [
            //     //     'name' => $territory ? $territory->name : null
            //     // ];
            //     return '-';
            // })
            ->addColumn('urgency_class', function ($lead) {
                if (empty($lead->effective_date)) {
                    return null;
                }

                $daysToEffectiveDate = Carbon::now()->startOfDay()
                    ->diffInDays(Carbon::parse($lead->effective_date)->startOfDay(), false);

                if ($daysToEffectiveDate <= 7) {
                    return 'highlight-critical';
                }

                if ($daysToEffectiveDate <= 14) {
                    return 'highlight-urgent';
                }

                if ($daysToEffectiveDate <= 30) {
                    return 'highlight-upcoming';
                }

                return 'highlight-normal';
            })
            ->addColumn('client_category', function ($lead) {
                if ((string) $lead->client_category === 'O') {
                    return 'Organic growth';
                } else {
                    return "New";
                }
            })
            ->addColumn('divisions', function ($lead) {
                $div = DB::table('reins_division')->where('division_code', $lead->divisions)->first();
                return $div ? $div->division_name : 'N/A';
            })
            ->addColumn('action', function ($lead) use ($request) {
                $url = route('leads.onboarding', ['prospect' => $lead->opportunity_id]);
                $prospectId = $lead->opportunity_id;
                // $handover = route('lead.handover', ['prospect' => $lead->opportunity_id]);
                // $btn_handover = '<a href="' . $handover . '" class="btn btn-outline-success btn-sm me-1">
                //         <i class="fa fa-arrow-right"></i> Handover
                //         </a>';
                $btn_edit = '<a href="' . $url . '" class="btn btn-info btn-sm rounded-pill mr-2">
                            <i class="bx bx-edit"></i> Edit Prospect
                        </a>';
                $btn_edit .= '<a href="' . $url . '" class="btn btn-success btn-sm rounded-pill send_to_sales" data-prospect_id="' . $prospectId . '" title="Send to Sales">
                             <i class="bx bx-paper-plane"></i>
                        </a>';
                $btn_submitted = '<span class="btn btn-success btn-sm rounded-pill">
                            Submitted To Sales
                        </span>';

                if (is_null($lead->pipeline_id)) {
                    return $btn_edit;
                } else {
                    return $btn_submitted;
                }
            })

            ->rawColumns(['action', 'priority_badge', 'status_badge', 'opportunity_id', 'insured_name'])
            ->make(true);
    }

    public function treaty_leads_get(Request $request)
    {
        // Base query
        $query = DB::table('pipeline_opportunities')
            ->whereIn('type_of_bus', ['TPR', 'TNP']);

        // Apply stage filter if provided
        if ($request->filled('stage') && $request->stage !== 'all') {
            $stageMap = [
                'qualification' => 1,
                'proposal' => 2,
                'due-diligence' => 3,
                'negotiation' => 4,
                'approval' => 5,
            ];

            if (isset($stageMap[$request->stage])) {
                $query->where('stage', $stageMap[$request->stage]);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('class')) {
            $query->where('classcode', $request->class);
        }

        if ($request->filled('class_group')) {
            $query->where('class_group', $request->class_group);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Apply search filter if provided
        if ($request->filled('global_search') || $request->filled('search_query')) {
            $searchTerm = trim((string) ($request->global_search ?? $request->search_query));
            $query->where(function ($q) use ($searchTerm) {
                $q->where('opportunity_id', 'ilike', "%{$searchTerm}%")
                    ->orWhere('insured_name', 'ilike', "%{$searchTerm}%")
                    ->orWhere('status', 'ilike', "%{$searchTerm}%")
                    ->orWhere('priority', 'ilike', "%{$searchTerm}%");
            });
        }

        $data = $query->orderBy('created_at', 'desc');

        return Datatables::of($data)
            ->addColumn('opportunity_id', function ($lead) {
                return $lead->opportunity_id;
            })
            ->addColumn('insured_name', function ($lead) {
                return $lead->insured_name ?? 'N/A';
            })
            ->addColumn('client_category', function ($lead) {
                if ((string) $lead->client_category === 'O') {
                    return 'Organic growth';
                } else {
                    return "New";
                }
            })
            ->addColumn('client_name', function ($lead) {
                $customer = DB::table('customers')
                    ->where('customer_id', (int) $lead->customer_id)
                    ->first();
                return $customer ? $customer->name : 'N/A';
            })
            ->addColumn('class', function ($lead) {
                $class = DB::table('classes')
                    ->where('class_code', (int) $lead->classcode)
                    ->first();
                return $class ? $class->class_name : 'N/A';
            })
            ->addColumn('divisions', function ($lead) {
                $div = DB::table('reins_division')
                    ->where('division_code', $lead->divisions)
                    ->first();
                return $div ? $div->division_name : 'N/A';
            })
            ->addColumn('type_of_bus', function ($lead) {
                return $lead->type_of_bus;
            })
            ->addColumn('stage', function ($lead) {
                return $lead->stage ?? 1;
            })
            ->addColumn('probability', function ($lead) {
                return $lead->probability ?? 0;
            })
            ->addColumn('priority', function ($lead) {
                return $lead->priority ?? 'medium';
            })
            ->addColumn('next_action', function ($lead) {
                return $lead->next_action ?? 'Follow up';
            })
            ->addColumn('fac_date_offered', function ($lead) {
                return $lead->cede_premium ?? 0;
            })
            ->addColumn('effective_date', function ($lead) {
                return $lead->effective_date ?? '';
            })
            ->addColumn('closing_date', function ($lead) {
                return $lead->closing_date ?? '';
            })
            ->addColumn('action', function ($lead) {
                $url = route('treaty.leads.onboarding', [
                    'prospect' => $lead->opportunity_id,
                    'trans_type' => 'EDIT'
                ]);

                $btn_edit = '<a href="' . $url . '" class="btn btn-info btn-sm rounded-pill">
                            <i class="bx bx-edit"></i> Edit
                        </a>';
                $btn_submitted = '<span class="btn btn-success btn-sm rounded-pill">
                            Submitted To Sales
                        </span>';

                if (is_null($lead->pipeline_id)) {
                    return $btn_edit;
                } else {
                    return $btn_submitted;
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Get Treaty KPIs via API endpoint
     */
    public function getTreatyKPIsApi(Request $request)
    {
        $kpis = $this->getTreatyKPIs();
        return response()->json($kpis);
    }

    public function prequalifications_get(Request $request)
    {

        $currentYear = Carbon::now()->format('Y');
        $data = DB::table('pipeline_opportunities')
            ->where('pip_year', '>=', $currentYear)
            ->orderBy('created_at', 'desc')
            ->where('prequalification', 'Y')
            ->where(function ($query) {

                $query->whereIn('pq_status', ['W', 'L', 'P', 'C']);
            })
            ->orderBy('closing_date', 'asc')
            ->get();

        return Datatables::of($data)
            ->editColumn('client_category', function ($lead) {
                if ($lead->client_category == 'O') {
                    return "Organic growth";
                } else {
                    return "New";
                }
            })
            ->editColumn('insurance_class', function ($lead) {
                $class = DB::table('class_of_insurance')->where('id', $lead->insurance_class)->first()->class_name;
                return $class;
            })
            ->editColumn('division', function ($lead) {
                $div = DB::table('divisions')->where('id', $lead->divisions)->first();
                return $div->name;
            })

            ->addColumn('status', function ($lead) {
                if ($lead->pq_status == 'P') {
                    return 'Proposal';
                } else if ($lead->pq_status == 'W') {
                    return 'Won';
                } else if ($lead->pq_status == 'L') {
                    return 'Lost';
                } else if ($lead->pq_status == 'C') {
                    return 'Won';
                }
            })
            ->addColumn('action', function ($lead) use ($request) {

                if ($currentYear = date('Y') != date('Y', strtotime($lead->effective_date))) {
                    if ($lead->pq_status == 'L' && $lead->prequalification === 'Y') {
                        $btnClosedPq = '<a  class="btn btn-danger btn-sm" href="#"><span><i class="fa fa-thumbs-down"></i>  Edit Next Year</span></a>';
                        return $btnClosedPq;
                    }
                    if ($lead->pq_status == 'W' && $lead->prequalification === 'Y') {
                        $url = route('leads_PQ_Process', ['prospect' => $lead->opportunity_id]);
                        $btnProcess = '<a  class="btn btn-warning btn-sm" href="' . $url . '"><span class="text-blue"><i class="fa fa-file"></i> Process Proposal</span></a>';
                        return $btnProcess;
                    }
                }
                if ($lead->pq_status == 'C' && $lead->prequalification === 'Y') {
                    $btnProcess = '<button class="btn btn-info btn-sm" >
                    <span class="text-blue"><i class="fa fa-thumbs-down"></i> Closed PQ</span>
                    </button>';

                    return $btnProcess;
                }
                if ($lead->pq_status == 'P' && $lead->prequalification === 'Y') {
                    $url = route('leads.onboarding', ['prospect' => $lead->opportunity_id]);
                    $btn_edit = '<a  class="btn btn-primary btn-sm" href="' . $url . '"><span class="text-blue"><i class="fa fa-edit"></i>  Update PQ</span></a>';

                    if (is_null($lead->pipeline_id) && $lead->pq_status != 'W') {
                        return $btn_edit;
                    }
                }
                if ($currentYear = date('Y') === date('Y', strtotime($lead->effective_date))) {
                    if ($lead->pq_status === 'L' && $lead->prequalification === 'Y') {
                        $url = route('leads.onboarding', ['prospect' => $lead->opportunity_id]);
                        $btnOpenPq = '<a  class="btn btn-success btn-sm" href="' . $url . '"><span><i class="fa fa-thumbs-up"></i>  Possible Renewal</span></a>';
                        if ($lead->pq_status = 'L') {
                            return $btnOpenPq;
                        }
                    }
                    if ($lead->pq_status == 'W' && $lead->prequalification === 'Y') {
                        $url = route('leads_PQ_Process', ['prospect' => $lead->opportunity_id]);
                        $btnProcess = '<a  class="btn btn-warning btn-sm" href="' . $url . '"><span class="text-blue"><i class="fa fa-file"></i> Process Proposal</span></a>';
                        return $btnProcess;
                    }
                }
                if ($lead->pq_status == 'C' && $lead->prequalification === 'Y') {
                    $btnProcess = '<button class="btn btn-info btn-sm" >
           <span class="text-blue"><i class="fa fa-thumbs-down"></i> Closed PQ</span>
       </button>';

                    return $btnProcess;
                }
                if ($lead->pq_status == 'P' && $lead->prequalification === 'Y') {
                    $url = route('leads.onboarding', ['prospect' => $lead->opportunity_id]);
                    $btn_edit = '<a  class="btn btn-primary btn-sm" href="' . $url . '"><span class="text-blue"><i class="fa fa-edit"></i>  Update PQ</span></a>';

                    if (is_null($lead->pipeline_id) && $lead->pq_status != 'W') {
                        return $btn_edit;
                    }
                }
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function leads_PQ_Process(Request $request)
    {
        $prospectId = $request->prospect;
        $client = DB::table('pipeline_opportunities')->where('opportunity_id', "=", $prospectId)->first();
        $email = "marketing@acentriagroup.com";
        $client_name = $client->fullname;
        return view('business_development.intermediaries.pq_process_update', compact('prospectId', 'email', 'client_name'));
    }

    public function PQ_proposal_documents(Request $request)
    {

        $uploadsPath = public_path('uploads');
        $attachments = [];

        if (count(DB::table('pipeline_opportunities')->where('opportunity_id', "=", $request->prospectId)->where('pq_status', "=", 'S')->get()) === 0) {
            $request->validate([
                'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
            ]);

            $uploadedFiles = $request->file('files');

            foreach ($uploadedFiles as $file) {

                $originalNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $Filename = mt_rand() . '_' . $originalNameWithoutExtension . '.' . $file->getClientOriginalExtension();
                $mimeType = $file->getMimeType();
                $file->move($uploadsPath, $Filename);
                $attachments[] = $uploadsPath . '/' . $Filename;

                DB::table('bd_docs')->insert([
                    'description' => $originalNameWithoutExtension,
                    'prospect_id' => $request->prospectId,
                    'mimetype' => $mimeType,
                    'file' => $Filename,
                ]);
            }
            // update client to have finished proposal
            DB::table('pipeline_opportunities')->where('opportunity_id', "=", $request->prospectId)->where('pq_status', "=", 'W')->update([
                'pq_status' => 'C',
                'pq_comments' => $request->pqcomment,
            ]);

            //  process and send email to client
            $data = [
                'Email' => env('MAIL_FROM_ADDRESS'),
                'details' => $request->pqcomment,
                'clientsEmail' => trim($request->marketingMail),
                'client_name' => $request->client_name,
                'title' => 'Prequalification Proposal',
                'regards' => 'Acentria Group',
                'Tel' => '+254 705 200 222',
                'BoxNo' => 'P.O BOX 5864 - 00100 Nairobi, Kenya',
                'Location' => 'West Park Towers, 9th Floor, Mpesi Lane, Muthithi Rd',
            ];

            sendPQClientEmail::dispatch($data, $attachments);

            return redirect()->route('leads.listing')->with('success', 'Proposal submited successfully!');
        } else {

            return redirect()->route('leads.listing')->with('error', 'Proposal already submited!');
        }
    }

    public function lead_view(Request $request)
    {
        $string = $request->qstring;
        $request = decryptRequest($string);
        $lead_status = LeadStatus::all();
        $lead = Leads::select('leads.*', 'lead_status.status_name')
            ->where('leads.code', $request->lead)
            ->leftJoin('lead_status', 'leads.status', '=', 'lead_status.status_name')
            ->firstOrFail();
        $agent = Intermediary::where('global_intermediary_id', $lead->lead_owner)->first();
        $lead_owner = $lead->lead_owner == '001' ? 'Direct Lead' : $agent->full_name;
        return view('business_development.intermediaries.lead_view', compact('lead', 'lead_status', 'lead_owner'));
    }

    public function leads_edit(Request $request)
    {
        $string = $request->qstring;
        $request = decryptRequest($string);

        $lead = Leads::where('code', $request->lead)->firstOrFail();

        $statuses = LeadStatus::all();
        $salutations = Salutation::all();

        return view('business_development.intermediaries.leads_edit', compact('lead', 'statuses', 'salutations'));
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

    public function lead_create_activity(Request $request)
    {
        $attendeeEmails = explode(',', $request->email);

        DB::beginTransaction();
        try {
            $activity = DB::table('leads_activity')->insertGetId(
                [
                    'title' => $request->title,
                    'lead_id' => $request->lead_id,
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'activity_location' => $request->location,
                    'notes' => $request->notes,
                    'activity_type' => $request->activity_type,
                    'status' => 'pending',
                    'created_at' => Carbon::today(),
                ]
            );
            foreach ($attendeeEmails as $email) {
                ActivityAttendees::create([
                    'activity_id' => $activity,
                    'attendee_email' => $email,
                    'created_at' => Carbon::today(),
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Activity Added Successfully');
        } catch (Exception $e) {
            // dd($e);
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to create');
        }
    }

    public function lead_activity(Request $request)
    {
        $activitiesWithAttendees = DB::table('leads_activity')
            ->join('activity_attendees', 'leads_activity.id', '=', 'activity_attendees.activity_id')
            ->select('leads_activity.*', 'activity_attendees.attendee_email as email')
            ->where('leads_activity.lead_id', $request->lead_id)
            ->get();

        return Datatables::of($activitiesWithAttendees)
            ->addColumn('from', function ($activity) {
                return Carbon::parse($activity->date_from)->format("Y-m-d");
            })
            ->make(true);
    }

    public function updateLeadStatus(Request $request)
    {
        $leadId = $request->input('lead_id');
        $status = $request->input('status');

        // Retrieve the lead from the database
        $lead = Leads::where('code', $leadId)->first();

        if ($lead) {
            // Update the lead status
            $lead = Leads::where('code', $leadId)->update([
                'status' => $status,
            ]);

            return response()->json(['message' => 'Lead status updated successfully']);
        } else {
            return response()->json(['message' => 'Lead not found'], 404);
        }
    }
    function TypeOfBusCoverDatatable(Request $request)
    {
        $type_of_bus = $request->type_of_bus;
        $Array = implode(',', array_fill(0, count($type_of_bus), '?'));

        $rawQuery = "
        SELECT DISTINCT ON (cr.cover_no)
            cr.cover_no, cr.cover_type, cr.class_code, cr.cover_to, cr.created_at, cr.type_of_bus, c.name
        FROM cover_register cr
        INNER JOIN customers c ON cr.customer_id = c.customer_id
        WHERE cr.type_of_bus IN ($Array)
        ORDER BY cr.cover_no, cr.created_at DESC
    ";

        // Execute the query with the type_of_bus array values
        $results = DB::select($rawQuery, $type_of_bus);

        // Optionally convert the result to a collection of models
        $query = collect($results);
        // dd($query);
        return datatables::of($query)

            ->editColumn('cover_no', function ($fn) {
                return $fn->cover_no;
            })

            ->editColumn('cover_type', function ($fn) {
                $t = CoverType::where('type_id', $fn->cover_type)->first();
                return $t->type_name;
            })

            ->editColumn('class_desc', function ($fn) {
                if ($fn->type_of_bus == 'FPR' || $fn->type_of_bus == 'FNP') {
                    $class_desc = Classes::where('class_code', $fn->class_code)->first();
                    // $class_desc=$class_desc->class_name;
                    if ($class_desc) {
                        $class_desc = 'FACULTATIVE - ' . $class_desc->class_name;
                    } else {
                        $class_desc = 'Unknown Class';
                    }
                } elseif ($fn->type_of_bus == 'TPR') {

                    $class_desc = 'TREATY -  PROPORTIONAL';
                } elseif ($fn->type_of_bus == 'TNP') {
                    $class_desc = 'TREATY  - NON PROPORTIONAL';
                }
                return $class_desc;
            })

            ->editColumn('cover_to', function ($fn) {
                return formatDate($fn->cover_to);
            })
            ->make(true);
    }
}
