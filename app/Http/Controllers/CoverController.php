<?php

namespace App\Http\Controllers;

use App\Enums\PermissionsLevel;
use App\Exceptions\BusinessRuleException;
use App\Http\Requests\CoverRegistrationRequest;
use App\Jobs\SendReinsurerEmailJob;
use App\Jobs\SendRenewalNoticeJob;
use App\Models\ApprovalSourceLink;
use App\Models\ApprovalsTracker;
use App\Models\Bd\PipelineOpportunity;
use App\Models\BdFacReinsurer;
use App\Models\BinderCover;
use App\Models\Branch;
use App\Models\Broker;
use App\Models\BusinessType;
use App\Models\Classes;
use App\Models\ClassGroup;
use App\Models\ClaimDebit;
use App\Models\ClaimRegister;
use App\Models\ClauseParam;
use App\Models\Company;
use App\Models\Country;
use App\Models\CoverAttachment;
use App\Models\CoverClass;
use App\Models\CoverClause;
use App\Models\CoverDebit;
use App\Models\CoverInstallments;
use App\Models\CoverPremium;
use App\Models\CoverPremtype;
use App\Models\CoverRegister;
use App\Models\CoverReinLayer;
use App\Models\CoverReinProp;
use App\Models\CoverRipart;
use App\Models\CoverRisk;
use App\Models\CoverType;
use App\Models\Currency;
use App\Models\CurrencyRate;
use App\Models\Customer;
use App\Models\CustomerAccDet;
use App\Models\DebitNote;
use App\Models\EndorsementNarration;
use App\Models\EndorsementType;
use App\Models\PayMethod;
use App\Models\PolicyRenewal;
use App\Models\PolicyRenewalDocument;
use App\Models\PremiumPayTerm;
use App\Models\ReinclassPremtype;
use App\Models\ReinNote;
use App\Models\ReinsClass;
use App\Models\ReinsDivision;
use App\Models\SystemProcessAction;
use App\Models\SystemSerials;
use App\Models\TreatyType;
use App\Models\TypeOfSumInsured;
use App\Models\User;
use App\Repositories\CoverRepository;
use App\Services\CoverService;
use App\Services\CreditNoteService;
use App\Services\TaxCalculationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use App\Services\DebitNoteService;

class CoverController extends Controller
{
    private $_year;

    private $_month;

    private $_quarter;

    private $_endorsement_no;

    protected $coverRepository;

    protected $coverService;

    protected $prospectDataService;

    protected $debitNoteService;

    protected $creditNoteService;

    protected $taxService;

    public function __construct(
        CoverService $coverService,
        CoverRepository $coverRepository,
        DebitNoteService $debitNoteService,
        TaxCalculationService $taxService,
        CreditNoteService $creditNoteService,
    ) {
        $this->_year = Carbon::now()->year;
        $this->_month = Carbon::now()->month;
        $this->_quarter = Carbon::now()->quarter;

        $this->coverRepository = $coverRepository;
        $this->coverService = $coverService;

        $this->debitNoteService = $debitNoteService;
        $this->taxService = $taxService;

        $this->creditNoteService = $creditNoteService;
    }

    public function getCustomers(Request $request)
    {
        $customers = DB::table('customers')
            ->join('customer_types', function ($join) {
                $join->on('customer_types.type_id', '=', DB::raw('ANY (SELECT json_array_elements_text(customers.customer_type)::int)'));
            })
            ->select('customers.customer_id', 'customers.name')
            ->whereIn('customer_types.code', ['INSCO', 'REINCO'])
            ->get();

        return response()->json($customers);
    }

    public function CoverForm(Request $request)
    {
        try {
            $trans_type = $request->trans_type;
            $type_of_bus = $request->type_of_bus;
            $prospect_id = $request->has('prospect_id') ? $request->prospect_id : null;

            if (! $request->customer_id) {
                return back()->with('error', 'Customer ID is required');
            }

            if ($trans_type != 'NEW') {
                $cover_no = $request->cover_no;
                $endorsement_no = $request->endorsement_no;

                if (! $cover_no || ! $endorsement_no) {
                    return back()->with('error', 'Cover number and endorsement number are required for non-NEW transactions');
                }

                $old_endt_trans = CoverRegister::where('endorseu8ment_no', $endorsement_no)->first();
                if ($old_endt_trans) {
                    if (! in_array($old_endt_trans?->transaction_type, ['NEW', 'REN']) && $trans_type == 'EDIT') {
                        return back()->with('error', 'You can only edit New covers or Renewals');
                    }

                    $coverreinpropClasses = CoverReinProp::where('cover_no', $cover_no)
                        ->select('reinclass')
                        ->where('endorsement_no', $old_endt_trans->endorsement_no)
                        ->groupBy('reinclass')
                        ->get();

                    $coverreinprops = CoverReinProp::where('cover_no', $cover_no)
                        ->where('endorsement_no', $old_endt_trans->endorsement_no)
                        ->get();

                    $coverReinLayers = CoverReinLayer::where('endorsement_no', $old_endt_trans->endorsement_no)->get();
                    $premtypes = CoverPremtype::with('premiumType')->where('endorsement_no', $old_endt_trans->endorsement_no)->get();
                    $renewal_date = Carbon::parse($old_endt_trans->cover_to)->addDay()->format('Y-m-d');
                } else {
                    $renewal_date = '';
                    $coverreinprops = collect();
                    $premtypes = collect();
                    $coverreinpropClasses = collect();
                    $coverReinLayers = collect();
                }
            } else {
                $old_endt_trans = null;
                $renewal_date = '';
                $coverreinprops = collect();
                $premtypes = collect();
                $coverreinpropClasses = collect();
                $coverReinLayers = collect();
            }

            $customer = Customer::where('customer_id', $request->customer_id)
                ->select(['customer_id', 'name', 'postal_address', 'postal_town', 'city', 'email', 'telephone', 'country_iso', 'customer_type'])
                ->first();

            if (! $customer) {
                return back()->with('error', 'Customer not found');
            }

            $insured = DB::table('customers')
                ->join('customer_types', function ($join) {
                    $join->on('customer_types.type_id', '=', DB::raw('ANY (SELECT json_array_elements_text(customers.customer_type)::int)'));
                })
                ->select('customers.customer_id', 'customers.name')
                ->where('customer_types.code', 'INSURED')
                ->get();

            $countries = Country::where('country_iso', $customer->country_iso)
                ->select(['country_iso', 'country_name'])
                ->first();

            if (! $countries) {
                $countries = (object) ['country_iso' => $customer->country_iso, 'country_name' => 'Unknown'];
            }

            $customerTypes = [];

            $branches = Branch::where('status', 'A')->get(['branch_code', 'branch_name', 'status']);
            $brokers = Broker::where('status', 'A')->get(['broker_code', 'broker_name', 'status']);
            $classes = Classes::where('status', 'A')->get(['class_code', 'class_name', 'status']);
            $types_of_sum_insured = TypeOfSumInsured::where('status', 'A')->get(['sum_insured_code', 'sum_insured_name', 'status']);
            $classGroups = ClassGroup::get(['group_code', 'group_name']);

            $types_of_busCount = BusinessType::where('bus_type_id', $type_of_bus)->count();
            if ($types_of_busCount > 0) {
                $types_of_bus = BusinessType::where('bus_type_id', $type_of_bus)->get(['bus_type_id', 'bus_type_name']);
            } else {
                $types_of_bus = BusinessType::get(['bus_type_id', 'bus_type_name']);
            }

            $paymethods = PayMethod::all();
            $premium_pay_terms = PremiumPayTerm::all();
            $currency = Currency::all();
            $covertypes = CoverType::all();
            $reinsdivisions = ReinsDivision::where('status', 'A')->get();
            $reinsclasses = ReinsClass::where('status', 'A')->get();
            $treatytypes = TreatyType::where('status', 'A')->get();
            $reinPremTypes = ReinclassPremtype::where('status', 'A')->get();

            $endorsement_no = $request->endorsement_no ?? '';
            $coverInstallments = CoverInstallments::where(['endorsement_no' => $endorsement_no, 'dr_cr' => 'DR'])->get();

            $selected_pay_method = null;
            if ($old_endt_trans) {
                $selected_pay_method = collect($paymethods)->first(
                    fn($item) => $item->pay_method_code == $old_endt_trans->pay_method_code,
                );
            }

            $allActiveStaff = User::where('status', 'A')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            $prospProperties = DB::table('pipeline_opportunities')
                ->selectRaw('DISTINCT ON (LOWER(insured_name)) pipeline_id, insured_name')
                ->get()
                ->map(function ($item, $index) {
                    return (object) [
                        'customer_id' => 4,
                        'name' => $item->insured_name,
                    ];
                });

            return view('cover.cover_form', [
                'type_of_cust' => $customerTypes,
                'country' => $countries,
                'customer' => $customer,
                'branches' => $branches,
                'brokers' => $brokers,
                'trans_type' => $trans_type,
                'types_of_bus' => $types_of_bus,
                'classGroups' => $classGroups,
                'class' => $classes,
                'paymethods' => $paymethods,
                'premium_pay_terms' => $premium_pay_terms,
                'currencies' => $currency,
                'covertypes' => $covertypes,
                'types_of_sum_insured' => $types_of_sum_insured,
                'old_endt_trans' => $old_endt_trans,
                'renewal_date' => $renewal_date,
                'reinsdivisions' => $reinsdivisions,
                'reinsclasses' => $reinsclasses,
                'treatytypes' => $treatytypes,
                'insured' => $prospProperties,
                'coverreinpropClasses' => $coverreinpropClasses,
                'coverreinprops' => $coverreinprops,
                'premtypes' => $premtypes,
                'reinPremTypes' => $reinPremTypes,
                'coverReinLayers' => $coverReinLayers,
                'coverInstallments' => $coverInstallments,
                'selected_pay_method' => $selected_pay_method,
                'prospectId' => $prospect_id,
                'staff' => $allActiveStaff,
            ]);
        } catch (\Exception $e) {
            return back()->withErros(['An error occurred while loading the cover form']);
        }
    }

    public function getTreatyPerBusType(Request $request)
    {
        $type_of_bus = $request->type_of_bus;
        $result = TreatyType::where('type_of_bus', $type_of_bus)->where('status', 'A')->get();

        return response()->json($result);
    }

    public function CoverRegister(CoverRegistrationRequest $request)
    {
        try {
            $transType = $request->get('trans_type');

            if ($request->validated()) {
                $cover = match ($transType) {
                    'NEW' => $this->coverService->registerNewCover($request->toArray()),
                    // 'REN' => $this->coverService->renewCover($request->validated()),
                    // 'EXT' => $this->coverService->processEndorsement($request->validated(), 'EXTRA'),
                    // 'CNC' => $this->coverService->processEndorsement($request->validated(), 'CANCEL'),
                    // 'RFN' => $this->coverService->processEndorsement($request->validated(), 'REFUND'),
                    // 'NIL' => $this->coverService->processEndorsement($request->validated(), 'NIL'),
                    // 'INS' => $this->coverService->processInstallment($request->validated()),
                    default => throw new Exception('Invalid transaction type')
                };

                $redirectUrl = route('cover.CoverHome', ['endorsement_no' => $cover['endorsement_no']]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cover registration processed successfully',
                    'data' => [
                        'trans_type' => $transType,
                        'redirectUrl' => $redirectUrl,
                    ],
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the cover registration',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error',
            ], 500);
        }
    }

    public function processCoverEndorsement(Request $request)
    {
        DB::beginTransaction();
        try {
            $result = $this->coverRepository->saveCoverEndorsement($request);

            DB::commit();

            return redirect()->route('cover.CoverHome', ['endorsement_no' => $result['endorsement_no']])->with('success', 'Cover Endorsement information updated successfully');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function editCoverRegister(Request $request)
    {
        return view('cover.edit_cover_form', []);

        // $validator = Validator::make($request->all(), [
        //     'cover_no' => 'required',
        //     'endorsement_no' => 'required',
        //     'covertype' => 'required',
        //     'branchcode' => 'required',
        //     'customer_id' => 'required',
        //     'classcode' => 'required',
        //     'coverfrom' => 'required',
        //     'coverto' => 'required',
        //     'pay_method' => 'required',
        //     'type_of_bus' => 'required',
        //     'class_group' => 'required',
        // ]);

        // if (!$validator) {
        //     return redirect()->route('cover.editCoverForm', [
        //         'cover_no' => $request->cover_no,
        //         'endorsement_no' => $request->endorsement_no,
        //         'customer_id' => $request->customer_id,
        //         'trans_type' => $request->trans_type,
        //     ])->with('errors', $validator->errors());
        // }

        // DB::beginTransaction();
        // try {
        //     $result = $this->coverRepository->editCoverRegister($request);
        //     DB::commit();
        //     return redirect()->route('cover.CoverHome', ['endorsement_no' => $result->endorsement_no])->with('success', 'Cover Register information updated successfully');
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return redirect()->route('cover.CoverHome', ['endorsement_no' => $request->endorsement_no])->with('error', 'Failed to update Cover information');
        // }
    }

    public function insertCoverReinProp($data)
    {
        $CoverRegister = CoverRegister::where('endorsement_no', $this->_endorsement_no)->first();
        $CoverReinProp = CoverReinProp::where('endorsement_no', $this->_endorsement_no)
            ->where('reinclass', $data['treaty_class'])
            ->where('item_description', $data['item_description']);

        if ($CoverReinProp->count() > 0) {
            $CoverReinProp = $CoverReinProp->first();
        } else {

            $count = CoverReinProp::where('cover_no', $CoverRegister->cover_no)
                ->where('endorsement_no', $this->_endorsement_no)
                ->count();
            $count = $count + 1;

            $CoverReinProp = new CoverReinProp;
            $CoverReinProp->cover_no = $CoverRegister->cover_no;
            $CoverReinProp->endorsement_no = $CoverRegister->endorsement_no;
            $CoverReinProp->item_no = $count;
            $CoverReinProp->created_by = Auth::user()->user_name;
        }

        $CoverReinProp->reinclass = $data['treaty_class'];
        $CoverReinProp->item_description = $data['item_description'];
        $CoverReinProp->retention_rate = $data['retention_per'];
        $CoverReinProp->treaty_rate = $data['treaty_rate'];
        $CoverReinProp->retention_amount = $data['retention_amount'];
        $CoverReinProp->no_of_lines = $data['no_of_lines'];
        $CoverReinProp->treaty_amount = $data['treaty_amount'];
        $CoverReinProp->treaty_limit = $data['treaty_limit'];
        $CoverReinProp->port_prem_rate = 0;
        $CoverReinProp->port_loss_rate = 0;
        $CoverReinProp->profit_comm_rate = 0;
        $CoverReinProp->mgnt_exp_rate = 0;
        $CoverReinProp->deficit_yrs = 0;
        $CoverReinProp->estimated_income = $data['estimated_income'];
        $CoverReinProp->cashloss_limit = $data['cashloss_limit'];
        $CoverReinProp->updated_by = Auth::user()->user_name;

        $CoverReinProp->save();
    }

    public function CoverDatatable(Request $request)
    {
        $customer_id = $request->get('customer_id');
        if (! $customer_id) {
            return response()->json(['error' => 'Customer ID is required'], 400);
        }

        $results = [];
        try {
            $query = DB::table('cover_register')
                ->select('cover_no', 'cover_type', 'class_code', 'cover_to', 'created_at', 'type_of_bus', 'verified')
                ->where('customer_id', $customer_id)
                ->whereNull('deleted_at')
                ->orderBy('cover_no')
                ->orderBy('created_at', 'desc')
                ->distinct('cover_no');

            if ((int) auth()->user()->role->permission_level < PermissionsLevel::MODERATOR) {
                $query->where('created_by', auth()->user()->user_name);
            }

            $results = collect($query->get())->sortByDesc('created_at')->values();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database query failed: ' . $e->getMessage()], 500);
        }

        return datatables::of($results)
            ->editColumn('cover_no', function ($fn) {
                return $fn->cover_no;
            })
            ->editColumn('cover_type', function ($fn) {
                $coverType = CoverType::where('type_id', $fn->cover_type)->first();

                return $coverType ? $coverType->type_name : 'Unknown Type';
            })
            ->editColumn('class_desc', function ($fn) {
                if (in_array($fn->type_of_bus, ['FPR', 'FNP'])) {
                    $classDesc = Classes::where('class_code', $fn->class_code)->first();

                    return $classDesc ? 'FACULTATIVE - ' . $classDesc->class_name : 'Unknown Class';
                } elseif ($fn->type_of_bus == 'TPR') {
                    return 'TREATY - PROPORTIONAL';
                } elseif ($fn->type_of_bus == 'TNP') {
                    return 'TREATY - NON-PROPORTIONAL';
                } else {
                    return ' ';
                }
            })
            ->editColumn('cover_to', function ($fn) {
                return $fn->cover_to ? formatDate($fn->cover_to) : 'N/A';
            })
            ->editColumn('status', function ($fn) {
                $badge = '';
                switch ($fn->verified) {
                    case 'P':
                        $badge = '<span class="badge bg-danger-gradient badge-sm-action"> Inactive</span>';
                        break;
                    case 'A':
                        $badge = '<span class="badge bg-success-gradient badge-sm-action"> Active</span>';
                        break;
                    case 'R':
                        $badge = '<span class="badge bg-danger-gradient badge-sm-action"> Rejected</span>';
                        break;
                    default:
                        $badge = '<span class="badge bg-danger-gradient badge-sm-action"> Inactive</span>';
                        break;
                }

                return $badge;
            })
            ->editColumn('actions', function ($fn) {
                $viewUrl = '#';

                return '<a href="' . $viewUrl . '" class="btn btn-sm btn-primary btn-sm-action"  id="view-coverlist-table">View <i class="bx bx-send"></i></a>';
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    public function EndorseDatatable(Request $request)
    {
        $customer_id = $request->customer_id;
        $cover_no = $request->cover_no;
        $query = CoverRegister::query()->where('customer_id', $customer_id)->where('cover_no', $cover_no);

        return datatables::of($query)
            ->editColumn('id_no', function ($fn) {
                return $fn->endorsement_no;
            })
            ->editColumn('endorsement_no', function ($fn) {
                return $fn->endorsement_no;
            })
            ->editColumn('transaction_type', function ($fn) {
                $trans_type = '';
                switch ($fn->transaction_type) {
                    case 'REN':
                        $trans_type = 'RENEWAL';
                        break;
                    case 'NEW':
                        $trans_type = 'NEW';
                        break;
                    case 'EXT':
                        $trans_type = 'ENDORSEMENT';
                        break;
                }

                return $trans_type;
            })
            ->editColumn('cover_from', function ($fn) {
                return formatDate($fn->cover_from);
            })
            ->editColumn('cover_to', function ($fn) {
                return formatDate($fn->cover_to);
            })
            ->editColumn('status_verification', function ($fn) {
                $trans_type = '';
                switch ($fn->verified) {
                    case 'A':
                        $trans_type = '<span class="badge bg-success-gradient">Approved</span>';
                        break;
                    case 'P':
                        $trans_type = '<span class="badge bg-danger-gradient">Pending</span>';
                        break;
                    case 'R':
                        $trans_type = '<span class="badge bg-danger-gradient">Rejected</span>';
                        break;
                    default:
                        $trans_type = '<span class="badge bg-danger-gradient">Pending</span>';
                        break;
                }

                return $trans_type;
            })
            ->editColumn('actions', function ($fn) {
                $btn = '<div class="btn-group btn-group-sm" role="group" aria-label="Endorsement actions">';
                $btn .= "<button type='button' class='btn btn-outline-primary view-endorsement-table' data-customer_id='{$fn->customer_id}' data-cover_no='{$fn->cover_no}' data-endorsement_no='{$fn->endorsement_no}' title='View details'><i class='bi bi-eye'></i></button>";
                $btn .= "<button type='button' class='btn btn-outline-danger remove-endorsement-table' data-customer_id='{$fn->customer_id}' data-cover_no='{$fn->cover_no}' data-endorsement_no='{$fn->endorsement_no}' title='Remove'><i class='bi bi-trash'></i></button>";
                $btn .= '</div>';

                return $btn;
            })
            ->rawColumns(['actions', 'id_no', 'status_verification'])
            ->make(true);
    }

    public function GetSpecificClasses(Request $request)
    {
        $class = Classes::where('class_group_code', $request->class_group)->where('status', 'A')->get();
        echo json_encode($class);
    }

    public function GetBinderCovers(Request $request)
    {
        $binders = BinderCover::all();
        echo json_encode($binders);
    }

    public function coverHome(Request $request)
    {
        $cover = $this->coverRepository->processCoverHome($request);

        $summaryData = ['summaryData' => []];
        $data = array_merge($summaryData, $cover);

        $isTreaty = in_array($cover['type_of_bus']['bus_type_id'], ['TPR', 'TNP']);
        if ($isTreaty) {
            if (! $cover['actionable']) {
                return redirect()->route('cover.transactions.index', ['coverNo' => $cover['coverNo']]);
            } else {
                return view('cover.cover_home', $data);
            }
        }

        return view('cover.cover_home', $data);
    }

    public function get_todays_rate(Request $request)
    {
        $selected_currency = $request->get('currency_code');
        $date = Carbon::today();

        $currency = Currency::where('currency_code', $selected_currency)->get(['currency_code', 'base_currency']);
        $currency = $currency[0];

        if ($currency->base_currency == 'Y') {

            $result = ['valid' => 2, 'rate' => 1];
            echo json_encode($result);
        } else {
            $count_curr = CurrencyRate::where('currency_code', $selected_currency)
                ->where('currency_date', $date)
                ->count();

            if ($count_curr > 0) {
                $rate = CurrencyRate::where('currency_code', $selected_currency)
                    ->where('currency_date', $date)
                    ->get();
                $result = ['valid' => 1, 'rate' => $rate[0]->currency_rate];
                echo json_encode($result);
            } else {
                $result = ['valid' => 0, 'short_descr' => $currency->currency_code];
                echo json_encode($result);
            }
        }
    }

    public function yesterdayRate(Request $request)
    {
        $jana = Carbon::yesterday()->format('Y-m-d H:i:s');
        $query = CurrencyRate::where('currency_date', $jana)->where('currency_code', $request->currency_code);
        $check = $query->count();

        if ($check == 0) {
            return $check;
        } else {
            $rate = $query->first();

            return $rate;
        }
    }

    public function endorse_functions(Request $request)
    {
        $cover_no = trim($request->cover_no);

        $latest_endorsement = CoverRegister::where('cover_no', $cover_no)
            ->where('cancelled', '<>', 'Y')
            ->whereIn('transaction_type', ['NEW', 'REN'])
            ->where('verified', 'A')
            ->orWhere(function ($query) use ($cover_no) {
                $query->where('cancelled', '<>', 'Y')
                    ->whereExists(function ($query) use ($cover_no) {
                        $query->select(DB::raw(1))
                            ->from('cover_debit')
                            ->whereColumn('cover_debit.endorsement_no', 'cover_register.endorsement_no')
                            ->where('cover_debit.cover_no', $cover_no);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->first();

        $all_endorsements = CoverRegister::where('cover_no', $cover_no)->where('cancelled', '<>', 'Y')->get();
        if (empty($latest_endorsement)) {
            $latest_endorsement = CoverRegister::where('cover_no', $cover_no)
                ->orderBy('dola', 'desc')
                ->first();
        }
        $customer = Customer::where('customer_id', $latest_endorsement->customer_id)->first();
        $type_of_bus = BusinessType::where('bus_type_id', $latest_endorsement->type_of_bus)->first();
        $class = Classes::where('class_code', $latest_endorsement->class_code)->first();
        $treaty_years = CoverRegister::where('cover_no', $cover_no)
            ->where('verified', 'A')
            ->where('commited', 'Y')
            ->distinct('account_year')
            ->get(['account_year']);
        $year = $latest_endorsement->cover_from->year;
        $month = $latest_endorsement->cover_from->month;
        $coverpremtypes = CoverPremtype::where('endorsement_no', $latest_endorsement->endorsement_no)
            ->join('reinsclasses', 'cover_premtypes.reinclass', '=', 'reinsclasses.class_code')
            ->get();
        $reinLayersCount = CoverReinLayer::where('endorsement_no', $latest_endorsement->endorsement_no)->count();
        $mdpAmount = CoverReinLayer::where('endorsement_no', $latest_endorsement->endorsement_no)->sum('min_deposit');
        $endorsments = CoverRegister::where('orig_endorsement_no', $latest_endorsement->orig_endorsement_no)->pluck('endorsement_no');
        $debitedmdpInst = CoverPremium::wherein('endorsement_no', $endorsments)
            ->whereNotNull('installment_no')
            ->pluck('installment_no');

        $mdpInstallments = DB::table('cover_installments')
            ->select('endorsement_no', 'installment_no', 'installment_date', DB::raw('SUM("installment_amt") as installment_amt'))
            ->groupBy('endorsement_no', 'installment_no', 'installment_date')
            ->whereNotIn('installment_no', $debitedmdpInst)
            ->where('endorsement_no', $latest_endorsement->orig_endorsement_no)
            ->get();
        $mdpInsLayerwise = CoverInstallments::where('endorsement_no', $latest_endorsement->orig_endorsement_no)
            ->whereNotIn('installment_no', $debitedmdpInst)
            ->where('trans_type', 'MDP')
            ->where('entry_type', 'MDP')
            ->where('dr_cr', 'DR')
            ->get();

        $EndorsementTypes = EndorsementType::where('type_of_bus', $latest_endorsement->type_of_bus)->get();

        $cover_installments = CoverInstallments::where('endorsement_no', $latest_endorsement->orig_endorsement_no)
            ->where('trans_type', 'FPR')
            ->where('dr_cr', 'DR')
            ->first();

        $premium_due_date = Carbon::parse($cover_installments?->installment_date)->format('Y-m-d');

        // Fetch claims for this cover
        $claims = ClaimRegister::where('cover_no', $cover_no)
            ->where('endorsement_no', $latest_endorsement->endorsement_no)
            ->orderByDesc('claim_serial_no')
            ->get();

        // Fetch claim debits (claim statements) for this cover
        $claimDebits = ClaimDebit::where('cover_no', $cover_no)
            ->where('endorsement_no', $latest_endorsement->endorsement_no)
            ->orderByDesc('created_at')
            ->get();

        // Fetch reinsurer statements (premium statements) for this cover
        $reinsurerStatements = CoverRipart::where('cover_no', $cover_no)
            ->where('endorsement_no', $latest_endorsement->endorsement_no)
            ->with(['partner'])
            ->orderBy('tran_no')
            ->get();

        return view('cover.endorsement_dtl', [
            'latest_endorsement' => $latest_endorsement,
            'all_endorsements' => $all_endorsements,
            'customer' => $customer,
            'cover_no' => $cover_no,
            'type_of_bus' => $type_of_bus,
            'class' => $class,
            'year' => $year,
            'month' => $month,
            'coverpremtypes' => $coverpremtypes,
            'treaty_years' => $treaty_years,
            'mdpInstallments' => $mdpInstallments,
            'mdpInsLayerwise' => $mdpInsLayerwise,
            'reinLayersCount' => $reinLayersCount,
            'mdpAmount' => $mdpAmount,
            'EndorsementTypes' => $EndorsementTypes,
            'premium_due_date' => $premium_due_date,
            'claims' => $claims,
            'claimDebits' => $claimDebits,
            'reinsurerStatements' => $reinsurerStatements,
        ]);
    }

    public function saveReinsurerData(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'endorsement_no' => 'required|exists:cover_register,endorsement_no',
                'treaty' => 'required|array|min:1',
                'treaty.*.treaty' => 'nullable|string',
                'treaty.*.reinsurers' => 'required|array|min:1',
                'treaty.*.reinsurers.*.reinsurer' => 'required|exists:customers,customer_id',
                'treaty.*.reinsurers.*.share' => 'required|numeric|min:0|max:100',
                'treaty.*.reinsurers.*.written_share' => 'required|numeric|min:0|max:100',
                'treaty.*.reinsurers.*.comm_rate' => 'nullable|numeric|min:0',
                'treaty.*.reinsurers.*.amount_type' => 'nullable|string',
                'treaty.*.reinsurers.*.compulsory_acceptance' => 'nullable|numeric|min:0',
                'treaty.*.reinsurers.*.optional_acceptance' => 'nullable|numeric|min:0',
                // 'treaty.*.reinsurers.*.amount_type' => 'nullable|string',
                'treaty.*.reinsurers.*.wht_rate' => 'nullable|numeric|min:0|max:100',
                'treaty.*.reinsurers.*.pay_method' => 'required|exists:pay_method,pay_method_code',
                'treaty.*.reinsurers.*.no_of_installments' => 'nullable|integer|min:1|max:12',
                'treaty.*.reinsurers.*.installments' => 'nullable|array',
                'treaty.*.reinsurers.*.installments.*.number' => 'required_with:treaty.*.reinsurers.*.installments|integer',
                'treaty.*.reinsurers.*.installments.*.due_date' => 'required_with:treaty.*.reinsurers.*.installments|date',
                'treaty.*.reinsurers.*.installments.*.amount' => 'required_with:treaty.*.reinsurers.*.installments|numeric',
            ];

            $messages = [
                'treaty.*.reinsurers.*.reinsurer.required' => 'Reinsurer is required for all entries',
                'treaty.*.reinsurers.*.reinsurer.exists' => 'Selected reinsurer does not exist',
                'treaty.*.reinsurers.*.share.required' => 'Share is required for all reinsurers',
                'treaty.*.reinsurers.*.share.max' => 'Share cannot exceed 100%',
                'treaty.*.reinsurers.*.written_share.required' => 'Written share is required',
                'treaty.*.reinsurers.*.pay_method.required' => 'Payment method is required',
            ];

            $validated = $request->validate($rules, $messages);

            $coverRegister = CoverRegister::where('endorsement_no', $validated['endorsement_no'])->firstOrFail();

            $this->_endorsement_no = $coverRegister->endorsement_no;
            $this->_year = now()->year;
            $this->_month = now()->month;

            if (in_array($coverRegister->type_of_bus, ['FPR', 'FNP'])) {
                DB::table('coverripart')->where('endorsement_no', $coverRegister->endorsement_no)->delete();
                DB::table('rein_notes')->where('endorsement_no', $coverRegister->endorsement_no)->delete();
                DB::table('cover_installments')->where('endorsement_no', $coverRegister->endorsement_no)->delete();
            }

            foreach ($request->treaty as $treatyIndex => $treaty) {
                foreach ($treaty['reinsurers'] as $reinsurerIndex => $reinsurerData) {

                    $tran_no = DB::transaction(function () {
                        $max = (int) CoverRipart::withTrashed()->max('tran_no');

                        return $max + 1;
                    });

                    $coverRipart = new CoverRipart;
                    $coverRipart->cover_no = $coverRegister->cover_no;
                    $coverRipart->endorsement_no = $coverRegister->endorsement_no;
                    $coverRipart->tran_no = $tran_no;
                    // $coverRipart->amount_type = $reinsurerData['amount_type'];
                    $coverRipart->period_year = $this->_year;
                    $coverRipart->period_month = $this->_month;
                    $coverRipart->partner_no = $reinsurerData['reinsurer'];
                    $coverRipart->share = $this->parseNumber($reinsurerData['share']);
                    $coverRipart->written_lines = $this->parseNumber($reinsurerData['written_share']);
                    $coverRipart->comm_rate = $this->parseNumber($reinsurerData['comm_rate'] ?? 0);
                    $coverRipart->wht_rate = $this->parseNumber($reinsurerData['wht_rate'] ?? 0);

                    if (in_array($coverRegister->type_of_bus, ['FPR', 'FNP'])) {

                        $coverRipart->total_sum_insured = $coverRegister->total_sum_insured ?? 0;
                        $coverRipart->total_premium = $coverRegister->rein_premium ?? 0;
                        $coverRipart->total_commission = $coverRegister->rein_comm_amount ?? 0;

                        $coverRipart->sum_insured = $this->parseNumber($reinsurerData['sum_insured'] ?? 0);
                        $coverRipart->premium = $this->parseNumber($reinsurerData['premium'] ?? 0);
                        $coverRipart->commission = $this->parseNumber($reinsurerData['comm_amt'] ?? 0);
                        $coverRipart->fronting_rate = $this->parseNumber($reinsurerData['fronting_rate'] ?? 0);

                        if ($coverRipart->fronting_rate > 0) {
                            $netPremium = $coverRipart->premium - $coverRipart->commission;
                            $coverRipart->fronting_amt = ($coverRipart->fronting_rate / 100) * $netPremium;
                        } else {
                            $coverRipart->fronting_amt = 0;
                        }

                        if ($coverRipart->wht_rate > 0) {
                            $netPremium = $coverRipart->premium - $coverRipart->commission;
                            $coverRipart->wht_amt = ($coverRipart->wht_rate / 100) * $netPremium;
                        } else {
                            $coverRipart->wht_amt = 0;
                        }

                        $brokerageType = $reinsurerData['brokerage_comm_type'] ?? 'R';

                        if ($brokerageType === 'R') {
                            $cedantCommRate = $coverRegister->cedant_comm_rate ?? 0;
                            $reinCommRate = $coverRipart->comm_rate;
                            $brokerageRate = max(0, $reinCommRate - $cedantCommRate);

                            $coverRipart->brokerage_comm_rate = $brokerageRate;
                            $coverRipart->brokerage_comm_amt = ($brokerageRate / 100) * $coverRipart->premium;
                        } else {
                            $brokerageAmt = $this->parseNumber($reinsurerData['brokerage_comm_amt'] ?? 0);
                            $coverRipart->brokerage_comm_amt = $brokerageAmt;

                            if ($coverRipart->premium > 0) {
                                $coverRipart->brokerage_comm_rate = ($brokerageAmt / $coverRipart->premium) * 100;
                            } else {
                                $coverRipart->brokerage_comm_rate = 0;
                            }
                        }
                    } elseif (in_array($coverRegister->type_of_bus, ['TPR', 'TNP'])) {
                        $coverRipart->treaty_code = $treaty['treaty'] ?? null;

                        $coverRipart->sum_insured = 0;
                        $coverRipart->premium = 0;
                        $coverRipart->commission = 0;
                        $coverRipart->fronting_amt = 0;
                        $coverRipart->wht_amt = 0;
                        $coverRipart->brokerage_comm_amt = 0;
                        $coverRipart->share = $this->parseNumber($reinsurerData['written_share']);
                        $coverRipart->commission_mode = $reinsurerData['amount_type'] ?? 'gross';

                        // $coverRipart->compulsory_acceptance = $this->parseNumber($reinsurerData['compulsory_acceptance'] ?? 0);
                        // $coverRipart->optional_acceptance = $this->parseNumber($reinsurerData['optional_acceptance'] ?? 0);
                        // $coverRipart->total_acceptance = $this->parseNumber($reinsurerData['share'] ?? 0);
                        $coverRipart->net_amount = 0;
                    }

                    // Calculate net_amount for facultative business types
                    if (in_array($coverRegister->type_of_bus, ['FPR', 'FNP'])) {
                        $coverRipart->net_amount = max(
                            0,
                            ($coverRipart->premium ?? 0)
                                - ($coverRipart->commission ?? 0)
                                - ($coverRipart->brokerage_comm_amt ?? 0)
                                - ($coverRipart->wht_amt ?? 0)
                                - ($coverRipart->fronting_amt ?? 0)
                        );
                    }

                    $coverRipart->created_by = Auth::user()->user_name;
                    $coverRipart->updated_by = Auth::user()->user_name;

                    $coverRipart->save();

                    $payMethodCode = $reinsurerData['pay_method'];
                    $payMethod = PayMethod::where('pay_method_code', $payMethodCode)->first();

                    if (! $payMethod) {
                        throw new \Exception("Payment method {$payMethodCode} not found");
                    }

                    if ($payMethod->short_description === 'I' || $payMethod->pay_method_code === 'INS' || $payMethod->pay_method_code === 'INST') {

                        $installments = $reinsurerData['installments'] ?? [];

                        if (empty($installments)) {
                            throw new \Exception('Installments are required for installment payment method');
                        }

                        foreach ($installments as $installment) {
                            DB::table('cover_installments')->insert([
                                'cover_no' => $coverRegister->cover_no,
                                'endorsement_no' => $coverRegister->endorsement_no,
                                'layer_no' => 0,
                                'trans_type' => $coverRegister->type_of_bus,
                                'entry_type' => $coverRegister->transaction_type,
                                'dr_cr' => 'CR',
                                'partner_no' => $reinsurerData['reinsurer'],
                                'installment_no' => $installment['number'],
                                'installment_date' => Carbon::parse($installment['due_date'])->format('Y-m-d'),
                                'installment_amt' => $this->parseNumber($installment['amount']),
                                'created_by' => Auth::user()->user_name,
                                'updated_by' => Auth::user()->user_name,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    } elseif ($payMethod->short_description === 'A') {

                        $totalDr = $coverRipart->premium ?? 0;
                        $totalCr = $coverRipart->commission ?? 0;
                        $whtAmt = $coverRipart->wht_amt ?? 0;
                        $frontingAmt = $coverRipart->fronting_amt ?? 0;

                        $installmentAmount = max(0, $totalDr - $totalCr - $whtAmt - $frontingAmt);

                        $premiumPaymentDays = (int) ($coverRegister->premium_payment_days ?? 30);
                        $dueDate = Carbon::parse($coverRegister->cover_from)
                            ->addDays($premiumPaymentDays)
                            ->format('Y-m-d');

                        DB::table('cover_installments')->insert([
                            'cover_no' => $coverRegister->cover_no,
                            'endorsement_no' => $coverRegister->endorsement_no,
                            'layer_no' => 0,
                            'trans_type' => $coverRegister->type_of_bus,
                            'entry_type' => $coverRegister->transaction_type,
                            'dr_cr' => 'CR',
                            'partner_no' => $reinsurerData['reinsurer'],
                            'installment_no' => 1,
                            'installment_date' => $dueDate,
                            'installment_amt' => $installmentAmount,
                            'created_by' => Auth::user()->user_name,
                            'updated_by' => Auth::user()->user_name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    if (in_array($coverRegister->type_of_bus, ['FPR', 'FNP'])) {

                        ReinNote::where('endorsement_no', $coverRegister->endorsement_no)
                            ->where('partner_no', $coverRipart->partner_no)
                            ->forceDelete();

                        $premItemTypes = [
                            'PRM' => [
                                'descr' => 'Gross Premium',
                                'dr_cr' => 'CR',
                                'tax_rate' => $coverRipart->share,
                                'total_amount' => $coverRegister->rein_premium,
                                'amount' => $coverRipart->premium,
                            ],
                            'COM' => [
                                'descr' => 'Commission',
                                'dr_cr' => 'DR',
                                'tax_rate' => $coverRipart->comm_rate,
                                'amount' => $coverRipart->commission,
                                'total_amount' => $coverRipart->premium,
                            ],
                            'BRC' => [
                                'descr' => 'Brokerage Commission',
                                'dr_cr' => 'DR',
                                'amount' => $coverRipart->brokerage_comm_amt,
                                'tax_rate' => $coverRipart->brokerage_comm_rate,
                                'total_amount' => $coverRipart->premium,
                            ],
                            'WHT' => [
                                'descr' => 'Withholding Tax',
                                'dr_cr' => 'DR',
                                'tax_rate' => $coverRipart->wht_rate,
                                'amount' => $coverRipart->wht_amt,
                                'total_amount' => $coverRipart->premium - $coverRipart->commission,
                            ],
                            'FRF' => [
                                'descr' => 'Fronting Fees',
                                'dr_cr' => 'DR',
                                'tax_rate' => $coverRipart->fronting_rate,
                                'amount' => $coverRipart->fronting_amt,
                                'total_amount' => $coverRipart->premium - $coverRipart->commission,
                            ],
                        ];

                        foreach ($premItemTypes as $key => $premItemType) {

                            if (($premItemType['amount'] ?? 0) == 0 && $key !== 'PRM') {
                                continue;
                            }

                            $reinTranNo = DB::transaction(function () use ($coverRegister) {
                                $max = ReinNote::where('endorsement_no', $coverRegister->endorsement_no)
                                    ->max('tran_no');

                                return ($max ?? 0) + 1;
                            });

                            $lnNo = DB::transaction(function () use ($coverRegister, $key) {
                                $count = ReinNote::where('endorsement_no', $coverRegister->endorsement_no)
                                    ->where('transaction_type', $coverRegister->transaction_type)
                                    ->where('entry_type_descr', $key)
                                    ->count();

                                return $count + 1;
                            });

                            $reinNote = new ReinNote;
                            $reinNote->cover_no = $coverRegister->cover_no;
                            $reinNote->endorsement_no = $coverRegister->endorsement_no;
                            $reinNote->partner_no = $coverRipart->partner_no;
                            $reinNote->transaction_type = $coverRegister->transaction_type;
                            $reinNote->account_year = $this->_year;
                            $reinNote->account_month = $this->_month;
                            $reinNote->share = $coverRipart->share;
                            $reinNote->tran_no = $reinTranNo;
                            $reinNote->ln_no = $lnNo;
                            $reinNote->entry_type_descr = $key;
                            $reinNote->item_title = $premItemType['descr'];
                            $reinNote->dr_cr = $premItemType['dr_cr'];
                            $reinNote->rate = $premItemType['tax_rate'] ?? 0;
                            $reinNote->total_gross = $premItemType['total_amount'] ?? 0;
                            $reinNote->gross = $premItemType['amount'] ?? 0;
                            $reinNote->net_amt = $premItemType['amount'] ?? 0;
                            $reinNote->created_by = Auth::user()->user_name;
                            $reinNote->updated_by = Auth::user()->user_name;

                            $reinNote->save();
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'success' => true,
                'message' => 'Reinsurer placement saved successfully',
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            DB::rollback();

            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'success' => false,
                'message' => 'Failed to save reinsurer data: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function parseNumber($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        return (float) str_replace(',', '', $value);
    }

    public function editReinsurerData(Request $request)
    {
        try {
            return $this->coverRepository->editReinsurer($request);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save',
            ]);
        }
    }

    public function deleteReinsurerData(Request $request)
    {
        try {
            $request->validate([
                'tran_no' => 'required',
                'endorsement_no' => 'required',
                'reinsurer' => 'required',
            ]);

            $reinsurer = CoverRipart::where('tran_no', $request->tran_no)
                ->where('endorsement_no', $request->endorsement_no)
                ->where('partner_no', $request->reinsurer);

            if ($reinsurer->first()) {
                $reinsurer->delete();
            }

            $cover = CoverInstallments::where('endorsement_no', $request->endorsement_no)
                ->where('partner_no', $request->reinsurer)
                ->where('trans_type', 'FPR');
            if ($cover->get()) {
                $cover->delete();
            }

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Reinsurer removed successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to Remove Reinsurer',
            ]);
        }
    }

    public function schedules_datatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');
        $query = CoverRisk::query()->with('schedule_header')->where('endorsement_no', $endorsement_no);
        $actionable = static::coverDebitedCommited($endorsement_no);

        return datatables::of($query)
            ->addColumn('details', function ($data) {
                // $truncated = Str::limit($data->details, 170);
                return '---';
            })
            ->addColumn('action', function ($data) use ($actionable) {
                $result = json_decode($data);
                $btn = '';
                if ($actionable) {
                    $btn .= "<button class='btn btn-outline-dark btn-sm btn-sm-action edit-schedule me-2' data-title='{$result->title}' data-sum_insured='{$result->sum_insured}' data-header='{$result?->schedule_header?->name}' data-details='{$result?->details}' data-schedule_id='{$result?->schedule_header?->id}' data-id='{$data?->id}' data-bs-toggle='modal' data-bs-target='#schedulesModal'>Edit <i class='bx bx-edit'></i></button>";
                    $btn .= "<button class='btn btn-outline-danger btn-sm btn-sm-action remove-schedule' data-name='{$data?->schedule_header?->name}' data-id='{$data->id}'>Remove <i class='bx bx-trash'></i></button>";
                }

                return $btn;
            })
            ->rawColumns(['details', 'action'])
            ->make(true);
    }

    public function installments_datatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');
        $query = CoverInstallments::where(['endorsement_no' => $endorsement_no, 'dr_cr' => 'DR'])->orderBy('installment_no', 'ASC');
        $actionable = static::coverDebitedCommited($endorsement_no);

        return datatables::of($query)
            ->addColumn('action', function ($data) use ($actionable) {
                $btn = '';
                if ($actionable) {
                    // $btn .= "<button class='btn btn-outline-primary btn-sm edit-installment' data-data='{$data}' data-id='{$data->id}'
                    //         data-bs-toggle='modal' data-bs-target='#installmentModal' >Edit</button>";
                    // $btn .= " <button class='btn btn-outline-danger btn-sm remove-installment' data-data='{$data}' data-id='{$data->id}'>Remove</button>";
                }

                return $btn;
            })
            ->rawColumns(['details', 'action'])
            ->make(true);
    }

    public function classes_datatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');
        $CoverRegister = CoverRegister::where('endorsement_no', $endorsement_no)->first();
        // $query = CoverClass::query()->with('insurance_class','ri_class')->where('endorsement_no',$endorsement_no);
        $query = CoverClass::join('classes', 'cover_classes.class', '=', 'classes.class_code')
            ->join('reinsclasses', 'cover_classes.reinclass', '=', 'reinsclasses.class_code')
            ->select('cover_classes.id as id', 'cover_classes.class as class', 'classes.class_name as class_name', 'reinsclasses.class_name as reinclass_name')
            ->where('cover_classes.endorsement_no', $endorsement_no);
        // ->get();

        $actionable = static::coverDebitedCommited($endorsement_no);

        return datatables::of($query)
            ->addColumn('action', function ($data) use ($actionable, $CoverRegister) {
                $btn = '';
                if ($actionable) {
                    if ($CoverRegister->transaction_type == 'EXT' || $CoverRegister->transaction_type == 'RFN' || $CoverRegister->transaction_type == 'CNC' || $CoverRegister->transaction_type == 'NEW' || $CoverRegister->transaction_type == 'REN') {
                        //  $btn .= "<button class='btn btn-outline-primary btn-sm' data-id='{$data->id}'>Edit</button>";
                        $btn .= " <button class='btn btn-outline-danger btn-sm' data-id='{$data->id}'>Remove</button>";
                    } else {
                        $btn .= ' ';
                    }
                }

                return $btn;
            })
            ->make(true);
    }

    public function reinsurers_datatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');
        $query = CoverRipart::query()->where('endorsement_no', $endorsement_no)
            ->with('partner')->orderBy('tran_no', 'asc');
        $cover = CoverRegister::where('endorsement_no', $endorsement_no)->first();
        $actionable = static::coverDebitedCommited($endorsement_no);

        return datatables::of($query)
            ->editColumn('partner_name', function ($data) {
                return $data->partner->name;
            })
            ->addColumn('action', function ($data) use ($actionable, $endorsement_no, $cover) {
                $btn = '';
                $partner_emails = [];
                $partner_emails[] = $data?->partner?->email;
                if ($actionable) {
                    $distributedShare = 0;
                    switch ($cover->type_of_bus) {
                        case 'FPR':
                            $distributedShare = CoverRipart::where('endorsement_no', $endorsement_no)->sum('share');
                            break;
                        case 'TPR':
                        case 'TNP':
                            $distributedShare = CoverRipart::where('endorsement_no', $endorsement_no)
                                ->where('treaty_code', $data->treaty_code)
                                ->sum('share');
                            break;
                    }
                    $reinsurer = Customer::where('customer_id', $data->partner_no)->first();
                    if (($cover->transaction_type == 'NEW' || $cover->transaction_type == 'REN' || $cover->transaction_type == 'EXT' || $cover->transaction_type == 'CNC' || $cover->transaction_type == 'RFN')) {
                        $btn .= "<button class='btn btn-outline-dark btn-wave waves-effect waves-light edit-reinsurer datatable-action-btn' data-distributed-share='{$distributedShare}' data-reinsurer='{$reinsurer}' data-data='{$data}' data-bs-toggle='modal' data-bs-target='#edit-reinsurer-modal'>Edit</button>";
                        $btn .= "<button class='btn btn-outline-danger btn-wave waves-effect waves-light remove-reinsurer datatable-action-btn mx-2' data-reinsurer='{$reinsurer}' data-data='{$data}'>Remove</button>";
                    } else {
                        $btn .= '';
                    }
                } else {
                    $creditNoteUrl = route('docs.reincreditnotes', ['endorsement_no' => $endorsement_no, 'partner_no' => $data->partner_no]);
                    $coverSlipUrl = route('docs.coverslip', ['endorsement_no' => $endorsement_no, 'partner_no' => $data->partner_no]);
                    $client_emails = json_encode($partner_emails);
                    $client_name = $data?->partner?->name;

                    $endorsementNo = $endorsement_no;
                    $coverNo = $cover?->cover_no;
                    $tmp_attachments = json_encode(['attachments' => []]);

                    if (($cover->type_of_bus == 'TPR' || $cover->type_of_bus == 'TNP') && ($cover->transaction_type == 'NEW' || $cover->transaction_type == 'REN')) {
                        $btn .= '';
                    } else {
                        // $endorsementSlipUrl = route('docs.endorsementslip', ['endorsement_no' => $endorsementNo, 'partner_no' => $data->partner_no]);

                        $btn .= "<a href='{$creditNoteUrl}' data-endorsementno='{$endorsementNo}' data-partnerno='{$data->partner_no}' target='_blank' rel='noopener noreferrer' class='print-out-link pr-3 rein_credit_note_btn'><i class='bx bx-file me-1 align-middle'></i>Credit Note</a>";
                        $btn .= "<a href='{$coverSlipUrl}' data-endorsementno='{$endorsementNo}' target='_blank' rel='noopener noreferrer' class='print-out-link pr-3 rein_cover_slip_btn'>
                                    <i class='bx bx-file'></i> Cover Slip</a>";
                        // $btn .= "<a href='{$endorsementSlipUrl}' data-endorsementno='{$endorsementNo}' target='_blank' rel='noopener noreferrer' class='print-out-link pr-3 rein_endorsement_slip_btn'>
                        //             <i class='bx bx-file'></i> Endors. Slip</a>";
                        $btn .= "<a href='#' target='_blank' class='print-out-link send_reinsurer_email' data-client_emails='{$client_emails}' data-cover_no='{$coverNo}' data-endorsement_no='{$endorsementNo}' data-client_name='{$client_name}' data-client_docs='{$tmp_attachments}'>
                                    <i class='bx bx-mail-send' style='font-size: 15px; vertical-align: -2px;'></i> Send E-Mail</a>";
                    }
                }

                return $btn;
            })
            ->rawColumns(['action', 'partner_name'])
            ->make(true);
    }

    public function attachments_datatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');
        $query = CoverAttachment::query()->where('endorsement_no', $endorsement_no);
        $actionable = static::coverDebitedCommited($endorsement_no);

        return datatables::of($query)
            ->addColumn('action', function ($data) use ($actionable) {
                $btn = '';
                if ($actionable) {
                    $btn .= " <button class='btn btn-outline-dark btn-sm view-attachment' data-id='{$data->id}' data-mime='{$data->mime_type}' data-base64='{$data->file_base64}'
                        data-bs-target='#attachment-document-modal' data-bs-toggle='modal'>View <i class='bx bx-send'></i></button>";
                    $btn .= " <button class='btn btn-outline-dark btn-sm edit-attachment' data-title='{$data->title}' data-id='{$data->id}'
                        data-bs-toggle='modal' data-bs-target='#attachments-modal'>Edit</button>";
                    $btn .= " <button class='btn btn-outline-danger btn-sm remove-attachment' data-title='{$data->title}' data-id='{$data->id}'>Remove</button>";
                }

                return $btn;
            })
            ->make(true);
    }

    public function clauses_datatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');
        $query = CoverClause::query()->where('endorsement_no', $endorsement_no);
        $actionable = static::coverDebitedCommited($endorsement_no);

        return datatables::of($query)
            ->addColumn('clause_wording', function ($data) {
                // $truncated = Str::limit($data->clause_wording, 300);
                return '---';
            })
            ->addColumn('action', function ($data) use ($actionable) {
                $btn = '';
                if ($actionable) {
                    $btn .= " <button class='btn btn-outline-danger btn-sm remove-clause' data-title='{$data->clause_title}' data-id='{$data->clause_id}'>Remove</button>";
                }

                return $btn;
            })
            ->rawColumns(['clause_wording', 'action'])
            ->make(true);
    }

    public function approvals_datatable(Request $request)
    {
        $results = [];
        try {
            $endorsement_no = $request->get('endorsement_no');
            $approvalAction = SystemProcessAction::where('nice_name', 'verify_cover')->first();
            $aprovalIds = ApprovalSourceLink::where('process_id', $approvalAction->process_id)
                ->where('process_action', $approvalAction->id)
                ->where('source_table', 'cover_register')
                ->where('source_column_name', 'endorsement_no')
                ->where('source_column_data', $endorsement_no)
                ->pluck('approval_id');

            $results = ApprovalsTracker::query()->whereIn('id', $aprovalIds);
            $actionable = static::coverDebitedCommited($endorsement_no);
        } catch (\Exception $e) {

            $results = [];
        }

        return Datatables::of($results)
            ->editColumn('approver', function ($data) {
                $approver = User::where('id', $data->approver)->first('name');

                return $approver->name;
            })
            ->addColumn('status', function ($data) {
                $btn = '';
                switch ($data->status) {
                    case 'P':
                        $btn .= " <span class='badge bg-danger-gradient'>Pending</span>";
                        break;
                    case 'A':
                        $btn .= " <span class='badge bg-success-gradient'>Approved</span>";
                        break;
                    case 'R':
                        $btn .= " <span class=badge bg-danger-gradient'>Rejected</span>";
                        break;
                }

                return $btn;
            })
            ->addColumn('action', function ($data) use ($actionable) {
                $btn = '';
                if ($actionable) {
                    switch ($data->status) {
                        case 'P':
                            $btn .= " <button class='btn btn-outline-dark btn-wave waves-effect waves-light edit-reinsurer datatable-action-btn' data-id='{$data->id}' id='re-escalate'>Re-escalate</button>";
                            break;
                        case 'A':
                            $btn .= " <span class='badge badge-success' disabled>Closed</span>";
                            break;
                        case 'R':
                            $btn .= " <button class='btn btn-outline-primary btn-sm' data-id='{$data->id}' id='re-send'>Re-send</button>";
                            break;
                    }
                }

                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function debits_datatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');
        $query = CoverDebit::query()->where('endorsement_no', $endorsement_no);
        $cover = CoverRegister::query()->where('endorsement_no', $endorsement_no)->with('customer')->first();
        $actionable = static::coverDebitedCommited($endorsement_no);

        return datatables::of($query)
            ->addColumn('cedant', function ($data) use ($cover) {
                $customer_name = $cover?->customer?->name;

                return $customer_name;
            })
            ->editColumn('dr_no', function ($data) {
                return $data->document . '/' . $data->dr_no . '/' . $data->period_year;
            })
            ->addColumn('sum_insured', function () use ($cover) {
                return $cover?->total_sum_insured;
            })
            ->addColumn('share', function () use ($cover) {
                return $cover?->share_offered ? number_format($cover?->share_offered, 2) : 0;
            })
            ->addColumn('premium', function () use ($cover) {
                return $cover?->cedant_premium;
            })
            ->addColumn('gross', function () use ($cover) {
                return $cover?->cedant_premium;
            })
            ->addColumn('action', function ($data) use ($actionable, $endorsement_no, $cover) {
                $btn = '';
                $partner_emails = [];
                $partner_emails[] = $cover?->customer?->email;
                $user = auth()->user()->name;
                // $role = User::where('id', auth()->user()->id)->load('role');
                if ($actionable) {
                    $distributedShare = 0;
                    switch ($cover->type_of_bus) {
                        case 'FPR':
                            $distributedShare = CoverRipart::where('endorsement_no', $endorsement_no)->sum('share');
                            break;
                        case 'TPR':
                        case 'TNP':
                            $distributedShare = CoverRipart::where('endorsement_no', $endorsement_no)
                                ->where('treaty_code', $data->treaty_code)
                                ->sum('share');
                            break;
                    }
                    $reinsurer = Customer::where('customer_id', $data->partner_no)->first();
                    if (($cover->transaction_type == 'NEW' || $cover->transaction_type == 'REN' || $cover->transaction_type == 'EXT' || $cover->transaction_type == 'CNC' || $cover->transaction_type == 'RFN')) {
                        $btn .= "<button class='btn btn-outline-dark btn-wave waves-effect waves-light edit-reinsurer datatable-action-btn' data-distributed-share='{$distributedShare}' data-reinsurer='{$reinsurer}' data-data='{$data}' data-bs-toggle='modal' data-bs-target='#edit-reinsurer-modal'>Edit</button>";
                        $btn .= "<button class='btn btn-outline-danger btn-wave waves-effect waves-light remove-reinsurer datatable-action-btn mx-2' data-reinsurer='{$reinsurer}' data-data='{$data}'>Remove</button>";
                    } else {
                        $btn .= '';
                    }
                } else {
                    $dbtNoteUrl = route('docs.coverdebitnote', ['endorsement_no' => $endorsement_no]);
                    $coverNoteUrl = route('docs.coverslip', ['endorsement_no' => $endorsement_no, 'covernote' => 'true']);
                    $client_emails = json_encode($partner_emails);
                    $client_name = $cover?->customer?->name;
                    $endorsementNo = $endorsement_no;
                    $coverNo = $cover?->cover_no;
                    $tmp_attachments = json_encode(['attachments' => []]);

                    if (($cover->type_of_bus == 'TPR' || $cover->type_of_bus == 'TNP') && ($cover->transaction_type == 'NEW' || $cover->transaction_type == 'REN')) {
                        $btn .= '';
                    } else {
                        $btn .= "<a href='{$dbtNoteUrl}' target='_blank' rel='noopener noreferrer' class='print-out-link pr-3'><i class='bx bx-file me-1 align-middle'></i>Debit Note</a>";
                        $btn .= "<a href='{$coverNoteUrl}' target='_blank' rel='noopener noreferrer' class='print-out-link pr-3'>
                                    <i class='bx bx-file'></i> Cover Note</a>";
                        // $btn .= "<a href='#' target='_blank' class='print-out-link send-cedant-email' data-user='{$user}' data-client_emails='{$client_emails}' data-cover_no='{$coverNo}' data-endorsement_no='{$endorsementNo}' data-client_name='{$client_name}' data-client_docs='{$tmp_attachments}'>
                        //             <i class='bx bx-mail-send' style='font-size: 15px; vertical-align: -2px;'></i> Send E-Mail</a>";
                    }
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function policy_renewal_datatable(Request $request)
    {

        $renewals = PolicyRenewal::where('policy_number', $request->cover_no)->with('documents');
        $attachments = [];
        $reinsurers_emails = [];
        $cover = CoverRegister::with('customer')
            ->where('cover_no', $request->cover_no)
            ->latest()
            ->first();

        $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->with('partner')->get();
        if (count($reinsurers) > 0) {
            foreach ($reinsurers as $reinsurer) {
                $reinsurers_emails[] = ['value' => $reinsurer?->partner?->email, 'text' => $reinsurer?->partner?->email];
            }
        }

        return datatables::of($renewals)
            ->addColumn('name', function ($data) {
                return $data->doc_name;
            })
            ->addColumn('renewal_type', function ($data) {
                return $data->notice_status;
            })
            ->addColumn('expires', function ($data) {
                return Carbon::parse($data->renewal_date)->format('Y-m-d');
            })
            ->addColumn('renewal_notice', function ($data) {
                return Carbon::parse($data->last_notice_sent)->format('Y-m-d');
            })
            ->addColumn('actions', function ($data) use ($attachments, $reinsurers_emails) {
                $btn = '';
                $downloadDocUrl = route('docs.download.renewal_notice', ['policy_number' => $data->policy_number]);
                $viewDocUrl = route('docs.view.renewal_notice', ['policy_number' => $data->policy_number]);
                $cedant_email = ['text' => $data->client_email, 'value' => $data->client_email];
                $policy_emails = array_merge(['cedant' => [$cedant_email]], ['reinsurer' => $reinsurers_emails]);
                $tmp_emails = json_encode($policy_emails);

                if (count($data->documents) > 0) {
                    foreach ($data->documents as $document) {
                        $pdfSizeMb = number_format($document->doc_size / 1048576, 2);
                        $attachments[] = [
                            'name' => $document->doc_name,
                            'size' => (float) $pdfSizeMb,
                            'url' => $document->doc_path,
                        ];
                    }
                }
                $tmp_attachments = json_encode(['attachments' => $attachments]);

                $btn .= " <select class='btn-outline-dark notice-action datatable-action-btn' data-id='{$data->id}' id='selected_renewal_action'><option value='cedant' selected>Cedant</option><option value='reinsurer'>Reinsurer</option></select>";
                $btn .= " <button class='btn btn-outline-dark btn-wave waves-effect waves-light renewal-send_mail_doc datatable-action-btn' data-id='{$data->id}' data-client_name='{$data->client_name}' data-client_docs={$tmp_attachments} data-policy_no={$data->policy_number} data-client_emails={$tmp_emails} id='send_renewalmail_doc' data-bs-target='#view-renewaldocument-modal' data-bs-toggle='modal'><i class='bx bx-mail-send me-1'></i>Send E-Mail</button>";
                $btn .= " <a href='{$viewDocUrl}' data-policy_no={$data->policy_number} target='_blank' rel='noreferrer' class='btn btn-outline-dark btn-wave waves-effect waves-light renewal-view_doc datatable-action-btn' data-id='{$data->id}' id='view_doc'><i class='bx bx-file me-1'></i>View</a>";
                $btn .= " <a href='{$downloadDocUrl}' data-policy_no={$data->policy_number} class='btn btn-outline-dark btn-wave waves-effect waves-light renewal-doc_download datatable-action-btn' data-id='{$data->id}' id='doc_download'><i class='bx bx-download me-1'></i>Download</a>";
                $btn .= " <button class='btn btn-outline-danger btn-wave waves-effect waves-light remove-renewal_doc datatable-action-btn' data-title='{$data->doc_name}' data-id='{$data->id}'><i class='bx bx-trash'></i></button>";

                return $btn;
            })
            ->rawColumns(['expires', 'renewal_notice', 'actions'])
            ->make(true);
    }

    public function generateDebitAndCredit(Request $request)
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->toArray();
            $existingQuarter = CustomerAccDet::where('endorsement_no', $validatedData['endorsement_no'])
                ->where('quarter', $validatedData['posting_quarter'])
                ->exists();

            if ($existingQuarter) {
                throw new BusinessRuleException("Quarterly figures for {$validatedData['posting_quarter']} have already been generated for this endorsement.");
            }

            $cover = CoverRegister::where('endorsement_no', $validatedData['endorsement_no'])->lockForUpdate()->first();

            if (! $cover) {
                throw new Exception("Cover register not found for endorsement: {$validatedData['endorsement_no']}");
            }

            $debitData = $this->prepareDebitData($validatedData, $cover);
            $creditData = $this->prepareCreditData($validatedData, $cover);

            if ($debitData['isTreaty']) {
                $this->validateBusinessRules($cover, $validatedData);
            }

            $redirectUrl = null;

            $message = $this->getBusinessTypeLabel($debitData['typeOfBus']) . ' Debit/Credit note generated successfully';

            if ($debitData['isFacultative']) {
                $redirectUrl = null;
                $this->createCoverDebit($debitData, $cover);
            } elseif ($debitData['isTreaty']) {
                $redirectUrl = route('cover.transactions.index', [
                    'coverNo' => $cover->cover_no,
                ]);

                $this->createTreatyDebit($debitData, $cover);
                $this->createTreatyCredit($creditData, $cover);
            }

            $this->createCustomerAccount($debitData, $cover);

            $cover->commited = 'Y';
            $cover->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_CREATED,
                'redirectUrl' => $redirectUrl,
                'message' => $message,
            ], 201);
        } catch (ValidationException $e) {
            DB::rollback();
            logger($e);

            return response()->json([
                'success' => false,
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => 'Internal error occured!',
            ], 422);
        } catch (BusinessRuleException $e) {
            DB::rollBack();
            logger($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => [],
            ], 422);
        } catch (Exception $e) {
            DB::rollback();
            logger($e);

            return response()->json([
                'success' => false,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function createTreatyDebit($debitData, $cover)
    {
        $cover->account_year = $this->_year;
        $cover->account_month = $this->_month;
        $cover->update();

        $debitNote = $this->debitNoteService->create($debitData, $cover);
        $debit = $debitNote->fresh(['items']);

        return $debit;
    }

    private function prepareCreditData(array $validatedData, CoverRegister $coverRegister): array
    {
        $typeOfBus = $validatedData['type_of_bus'];
        $netAmount = $this->parseNumber($validatedData['amount']);
        $brokerageRate = (float) ($validatedData['brokerage_rate'] ?? 0);
        $premiumTaxRate = (float) ($validatedData['premium_levy'] ?? 0);
        $commissionRate = (float) ($coverRegister->comm_rate ?? 0);

        $enhancedItems = $this->enhanceItemsWithDeductions(
            $validatedData['items'] ?? [],
            $brokerageRate,
            $premiumTaxRate,
            $commissionRate
        );

        return [
            'typeOfBus' => $typeOfBus,
            'isTreaty' => in_array($typeOfBus, ['TPR', 'TNP']),
            'isFacultative' => in_array($typeOfBus, ['FPR', 'FNP']),
            'isProportional' => in_array($typeOfBus, ['TPR', 'FPR']),
            'coverNo' => $validatedData['cover_no'],
            'endorsementNo' => $validatedData['endorsement_no'],
            'installment' => $validatedData['installment'],
            'grossAmount' => $netAmount,
            'netAmount' => $netAmount,
            'docType' => 'CRN',
            'drCr' => 'C',
            'drCrNo' => null,
            'sourceCode' => null,
            'reference' => null,
            'currencyRate' => $coverRegister->currency_rate ?? 1,
            'entryTypeDescr' => 'quarterly-figures',
            'postingYear' => $validatedData['posting_year'],
            'postingQuarter' => $validatedData['posting_quarter'],
            'postingDate' => $validatedData['posting_date'],
            'brokerageRate' => $brokerageRate,
            'comments' => $validatedData['comments'] ?? '',
            'items' => $enhancedItems,
            'computePremiumTax' => $premiumTaxRate,
            'computeReinsuranceTax' => $validatedData['reinsurance_levy'] ?? 0,
            'computeWithholdingTax' => $validatedData['wht_rate'] ?? 0,
            'lossParticipation' => $validatedData['loss_participation'] ?? 0,
            'showCedant' => $validatedData['show_cedant'] ?? false,
            'showReinsurer' => $validatedData['show_reinsurer'] ?? false,
            'reinsurerPosting' => 'NET',
            'premiumPayTerms' => '',
        ];
    }

    public function createTreatyCredit($creditData, $cover)
    {
        $cover->account_year = $this->_year;
        $cover->account_month = $this->_month;
        $cover->update();

        return $this->creditNoteService->create($creditData, $cover);
    }

    protected function validateBusinessRules(CoverRegister $cover, array $data): void
    {
        if (! $cover->isActive()) {
            throw BusinessRuleException::inactiveCover(
                $cover->cover_no,
                $cover->status
            );
        }

        // $existingDebit = DebitNote::where('cover_no', $cover->cover_no)
        //     ->where('endorsement_no', $data['endorsement_no'])
        //     ->where('installment_no', $data['installment'])
        //     ->whereNotIn('status', [
        //         DebitNote::STATUS_CANCELLED,
        //         DebitNote::STATUS_REVERSED
        //     ])
        //     ->exists();

        // if ($existingDebit) {
        //     throw BusinessRuleException::duplicateDebitNote(
        //         $cover->cover_no,
        //         $data['installment']
        //     );
        // }

        $postingDate = Carbon::parse($data['posting_date']);
        $fiscalYear = (int) $data['posting_year'];

        if ($postingDate->year !== $fiscalYear) {
            throw BusinessRuleException::invalidPostingPeriod(
                $data['posting_date'],
                "Year {$fiscalYear}"
            );
        }

        // $expectedQuarter = $this->getQuarterFromDate($postingDate);
        // if ($expectedQuarter !== $data['posting_quarter']) {
        //     throw BusinessRuleException::invalidPostingPeriod(
        //         $data['posting_date'],
        //         $data['posting_quarter']
        //     );
        // }

        // $totalAmount = collect($data['items'])->sum('amount');
        // if ($totalAmount <= 0) {
        //     throw new BusinessRuleException(
        //         'Total transaction amount must be greater than zero',
        //         'INVALID_AMOUNT'
        //     );
        // }
    }

    protected function getQuarterFromDate(Carbon $date): string
    {
        return 'Q' . $date->quarter;
    }

    private function createCoverDebit(array &$debitData, CoverRegister $coverRegister): void
    {
        $debitData['drCrNo'] = SystemSerials::nextSerial($debitData['docType']);

        $id = (int) CoverDebit::withTrashed()->max('id') + 1;

        $debit = new CoverDebit;
        $debit->id = $id;
        $debit->dr_no = $debitData['drCrNo'];
        $debit->document = $debitData['docType'];
        $debit->type_of_bus = $debitData['typeOfBus'];
        $debit->cover_no = $debitData['coverNo'];
        $debit->endorsement_no = $debitData['endorsementNo'];
        $debit->period_year = $this->_year;
        $debit->period_month = $this->_month;
        $debit->installment = $debitData['installment'];
        $debit->gross = $debitData['grossAmount'];
        $debit->net_amt = $debitData['netAmount'];
        $debit->created_by = Auth::user()->user_name;
        $debit->updated_by = Auth::user()->user_name;
        $debit->gl_updated = 'N';
        $debit->gl_updated_errors = '';

        if ($coverRegister->cover_from && $coverRegister->premium_payment_days) {
            $debit->premium_payment_due_date = $coverRegister->cover_from
                ->addDays((int) $coverRegister->premium_payment_days);
        }

        $debit->save();
    }

    private function createCustomerAccount(array &$debitData, CoverRegister $coverRegister): void
    {
        if (! $debitData['drCrNo']) {
            $debitData['drCrNo'] = SystemSerials::nextSerial($debitData['docType']);
        }

        $debitData['sourceCode'] = $debitData['isTreaty'] ? 'TRT' : 'FAC';
        $debitData['reference'] = $this->generateDebitReference($debitData, $coverRegister);

        $custAccount = new CustomerAccDet;
        $custAccount->branch = $coverRegister->branch_code;
        $custAccount->customer_id = $coverRegister->customer_id;
        $custAccount->source_code = $debitData['sourceCode'];
        $custAccount->doc_type = $debitData['docType'];
        $custAccount->entry_type_descr = $debitData['entryTypeDescr'];
        $custAccount->reference = $debitData['reference'];
        $custAccount->account_year = $this->_year;
        $custAccount->account_month = $this->_month;
        $custAccount->quarter = $debitData['postingQuarter'] ?? null;
        $custAccount->line_no = 1;
        $custAccount->cheque_no = ' ';
        $custAccount->cheque_date = null;
        $custAccount->cover_no = $coverRegister->cover_no;
        $custAccount->endorsement_no = $coverRegister->endorsement_no;
        $custAccount->insured = $coverRegister->insured_name;
        $custAccount->class = $coverRegister->class_code;
        $custAccount->currency_code = $coverRegister->currency_code;
        $custAccount->currency_rate = $debitData['currencyRate'];
        $custAccount->created_by = Auth::user()->user_name;
        $custAccount->created_date = Carbon::now();
        $custAccount->created_at = Carbon::now();
        $custAccount->created_time = Carbon::now();
        $custAccount->updated_by = Auth::user()->user_name;
        $custAccount->updated_datetime = Carbon::now();
        $custAccount->dr_cr = $debitData['drCr'];
        $custAccount->foreign_basic_amount = $debitData['grossAmount'];
        $custAccount->local_basic_amount = $debitData['grossAmount'] * $debitData['currencyRate'];
        $custAccount->foreign_taxes_amount = 0;
        $custAccount->local_taxes_amount = 0;
        $custAccount->foreign_nett_amount = $debitData['netAmount'];
        $custAccount->local_nett_amount = $debitData['netAmount'] * $debitData['currencyRate'];
        $custAccount->allocated_amount = 0;
        $custAccount->unallocated_amount = $debitData['grossAmount'] * $debitData['currencyRate'];

        $custAccount->save();
    }

    private function generateDebitReference(array $debitData, CoverRegister $coverRegister): string
    {
        $quarter = $debitData['postingQuarter'] ?? 'Q1';
        $year = $debitData['postingYear'] ?? $this->_year;
        $prefix = $this->getDebitReferencePrefix($coverRegister->treaty_type);
        $classCode = $coverRegister->type_of_bus;

        $random = random_int(1000, 9999);

        return strtoupper("{$quarter}{$prefix}{$year}{$classCode}{$random}");
    }

    private function getDebitReferencePrefix(?string $treatyType): string
    {
        return match ($treatyType) {
            'SURP' => 'SP',
            'QUOT' => 'QS',
            'SPQT' => 'SP-QS',
            default => 'UN',
        };
    }

    private function enhanceItemsWithDeductions(
        array $items,
        float $brokerageRate,
        float $premiumTaxRate = 0,
        float $commissionRate = 0
    ): array {
        $expandedItems = [];

        foreach ($items as $item) {
            $amount = (float) ($item['amount'] ?? 0);
            $lineRate = (float) ($item['line_rate'] ?? 0);
            $ledger = $item['ledger'] ?? 'DR';
            $classGroup = $item['class_group'] ?? null;
            $className = $item['class_name'] ?? null;

            if ($ledger === 'DR' && $amount > 0) {
                $sharedAmount = $amount * ($lineRate / 100);

                $expandedItems[] = [
                    'item_type' => 'DEBIT',
                    'item_code' => 'IT01',
                    'description' => 'Gross Premium',
                    'class_group' => $classGroup,
                    'class_name' => $className,
                    'line_rate' => $lineRate,
                    'ledger' => 'DR',
                    'amount' => $amount,
                    'item_amount' => $sharedAmount,
                    'commission' => 0,
                    'brokerage' => 0,
                    'premium_tax' => 0,
                    'net_amount' => $sharedAmount,
                ];

                $commissionOnShare = $sharedAmount * ($commissionRate / 100);

                if ($commissionOnShare > 0) {
                    $expandedItems[] = [
                        'item_type' => 'CREDIT',
                        'item_code' => 'IT03',
                        'description' => 'Commission',
                        'class_group' => $classGroup,
                        'class_name' => $className,
                        'line_rate' => $commissionRate,
                        'ledger' => 'CR',
                        'amount' => $sharedAmount,
                        'item_amount' => $commissionOnShare,
                        'commission' => $commissionOnShare,
                        'brokerage' => 0,
                        'premium_tax' => 0,
                        'net_amount' => $commissionOnShare,
                    ];
                }

                $premiumTaxAmount = $sharedAmount * ($premiumTaxRate / 100);

                if ($premiumTaxAmount > 0) {
                    $expandedItems[] = [
                        'item_type' => 'CREDIT',
                        'item_code' => 'IT05',
                        'description' => 'Premium Tax',
                        'class_group' => $classGroup,
                        'class_name' => $className,
                        'line_rate' => $premiumTaxRate,
                        'ledger' => 'CR',
                        'amount' => $sharedAmount,
                        'item_amount' => $premiumTaxAmount,
                        'commission' => 0,
                        'brokerage' => 0,
                        'premium_tax' => $premiumTaxAmount,
                        'net_amount' => $premiumTaxAmount,
                    ];
                }
            } else {
                $sharedAmount = $amount * ($lineRate / 100);
                $brokerageOnShare = $sharedAmount * ($brokerageRate / 100);

                $expandedItems[] = [
                    'item_type' => 'CREDIT',
                    'item_code' => $item['item_code'] ?? 'IT02',
                    'description' => 'Claims',
                    'class_group' => $classGroup,
                    'class_name' => $className,
                    'line_rate' => $lineRate,
                    'ledger' => 'CR',
                    'amount' => $amount,
                    'item_amount' => $sharedAmount,
                    'commission' => 0,
                    'brokerage' => 0,
                    'premium_tax' => 0,
                    'net_amount' => $sharedAmount,
                ];

                if ($brokerageOnShare > 0) {
                    $expandedItems[] = [
                        'item_type' => 'CREDIT',
                        'item_code' => 'IT04',
                        'description' => 'Brokerage',
                        'class_group' => $classGroup,
                        'class_name' => $className,
                        'line_rate' => $brokerageRate,
                        'ledger' => 'CR',
                        'amount' => $sharedAmount,
                        'item_amount' => $brokerageOnShare,
                        'commission' => 0,
                        'brokerage' => $brokerageOnShare,
                        'premium_tax' => 0,
                        'net_amount' => $brokerageOnShare,
                    ];
                }
            }
        }

        return $expandedItems;
    }

    private function prepareDebitData(array $validatedData, CoverRegister $coverRegister): array
    {
        $typeOfBus = $validatedData['type_of_bus'];
        $netAmount = $this->parseNumber($validatedData['amount']);
        $brokerageRate = (float) ($validatedData['brokerage_rate'] ?? 0);
        $premiumTaxRate = (float) ($validatedData['premium_levy'] ?? 0);
        $commissionRate = (float) ($coverRegister->comm_rate ?? 0);

        $enhancedItems = $this->enhanceItemsWithDeductions(
            $validatedData['items'] ?? [],
            $brokerageRate,
            $premiumTaxRate,
            $commissionRate
        );

        return [
            'typeOfBus' => $typeOfBus,
            'isTreaty' => in_array($typeOfBus, ['TPR', 'TNP']),
            'isFacultative' => in_array($typeOfBus, ['FPR', 'FNP']),
            'isProportional' => in_array($typeOfBus, ['TPR', 'FPR']),
            'coverNo' => $validatedData['cover_no'],
            'endorsementNo' => $validatedData['endorsement_no'],
            'installment' => $validatedData['installment'],
            'grossAmount' => $netAmount,
            'netAmount' => $netAmount,
            'docType' => $netAmount > 0 ? 'DRN' : 'CRN',
            'drCr' => $netAmount > 0 ? 'D' : 'C',
            'drCrNo' => null,
            'sourceCode' => null,
            'reference' => null,
            'currencyRate' => $coverRegister->currency_rate ?? 1,
            'entryTypeDescr' => 'quarterly-figures',
            'postingYear' => $validatedData['posting_year'],
            'postingQuarter' => $validatedData['posting_quarter'],
            'postingDate' => $validatedData['posting_date'],
            'brokerageRate' => $brokerageRate,
            'comments' => $validatedData['comments'] ?? '',
            'items' => $enhancedItems,
            'computePremiumTax' => $premiumTaxRate,
            'computeReinsuranceTax' => $validatedData['reinsurance_levy'] ?? 0,
            'computeWithholdingTax' => $validatedData['wht_rate'] ?? 0,
            'lossParticipation' => $validatedData['loss_participation'] ?? 0,
            'showCedant' => $validatedData['show_cedant'] ?? false,
            'showReinsurer' => $validatedData['show_reinsurer'] ?? false,
            'reinsurerPosting' => 'NET',
            'premiumPayTerms' => '',
        ];
    }

    private function getBusinessTypeLabel(string $typeOfBus): string
    {
        return match ($typeOfBus) {
            'TPR' => 'Treaty Proportional',
            'TNP' => 'Treaty Non-Proportional',
            'FPR' => 'Facultative Proportional',
            'FNP' => 'Facultative Non-Proportional',
            default => 'Unknown'
        };
    }

    public function saveAttachment(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'endorsement_no' => 'required',
                'title' => 'required',
                'file' => 'required|mimes:pdf,doc,docx,jpeg,png',
            ]);

            $file = $request->file('file');
            $fileName = date('dmYhis') . '_' . $file->getClientOriginalName();
            $file->storeAs('cover_attachments', $fileName, 'public');
            $mimeType = $file->getClientMimeType();

            $base64Encoded = base64_encode(File::get($file->path()));

            $id = (int) CoverAttachment::withTrashed()->max('id') + 1;
            $CoverRegister = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();

            CoverAttachment::create([
                'id' => $id,
                'cover_no' => $CoverRegister->cover_no,
                'endorsement_no' => $CoverRegister->endorsement_no,
                'title' => $request->title,
                'description' => $request->title,
                'file' => $fileName,
                'file_base64' => $base64Encoded,
                'mime_type' => $mimeType,
                'created_by' => Auth::user()->user_name,
                'updated_by' => Auth::user()->user_name,
            ]);
            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function amendAttachment(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'endorsement_no' => 'required',
                'id' => 'required',
                'title' => 'required',
                // 'description' => 'required',
                'file' => 'required|mimes:pdf,doc,docx,jpeg,png',
            ]);

            $file = $request->file('file');
            $fileName = date('dmYhis') . '_' . $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $file->storeAs('cover_attachments', $fileName, 'public');

            $base64Encoded = base64_encode(File::get($file->path()));

            $attachment = CoverAttachment::where('id', $request->id)->first();

            $attachment->title = $request->title;
            $attachment->description = $request->title;
            $attachment->file = $fileName;
            $attachment->file_base64 = $base64Encoded;
            $attachment->mime_type = $mimeType;
            $attachment->updated_by = Auth::user()->user_name;
            $attachment->save();

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();

            // dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function deleteAttachment(Request $request)
    {
        DB::beginTransaction();
        try {

            $request->validate([
                'endorsement_no' => 'required',
                'id' => 'required',
            ]);

            $attachment = CoverAttachment::where('id', $request->id)->first();
            $attachment->delete();

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Item deleted successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function saveClauses(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'cover_no' => 'required',
                'endorsement_no' => 'required',
                'clauses' => 'required',
            ]);

            // CoverClause::where('endorsement_no', $request->endorsement_no)->delete();
            foreach ($request->clauses as $value) {
                $clause = ClauseParam::where('clause_id', $value)->first();
                CoverClause::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $request->endorsement_no,
                    'clause_id' => $clause->clause_id,
                    'clause_title' => $clause->clause_title,
                    'clause_wording' => $clause->clause_wording,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function amendClauses(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'cover_no' => 'required',
                'endorsement_no' => 'required',
                'clauses' => 'required',
            ]);

            $del_exist_clauses = CoverClause::where('endorsement_no', $request->endorsement_no)->delete();
            // foreach ($clauses as $key => $value) {
            foreach ([] as $key => $value) {
                $clause = ClauseParam::where('clause_id', $value);
                CoverClause::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $request->endorsement_no,
                    'clause_id' => $clause->clause_id,
                    'clause_title' => $clause->clause_title,
                    'clause_wording' => $clause->clause_wording,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function deleteClause(Request $request)
    {
        DB::beginTransaction();
        try {

            $request->validate([
                'cover_no' => 'required',
                'endorsement_no' => 'required',
                'clause_id' => 'required',
            ]);

            $del_clause = CoverClause::where('endorsement_no', $request->endorsement_no)->where('clause_id', $request->clause_id)->delete();

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'clause deleted successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            dd($e);

            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function saveInsuranceClasses(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'reinclass' => 'required',
                'class' => 'required',
                'cover_no' => 'required',
                'endorsement_no' => 'required',
            ]);

            foreach ($validated['class'] as $class) {
                $id = (int) CoverClass::withTrashed()->max('id') + 1;

                CoverClass::create([
                    'id' => $id,
                    'reinclass' => $request->reinclass,
                    'cover_no' => $validated['cover_no'],
                    'endorsement_no' => $validated['endorsement_no'],
                    'class' => $class,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            dd($e);

            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getReinpremType(Request $request)
    {
        $reinsclass = $request->reinclass;
        $selectedCodes = $request->selectedCodes ?? [];

        $result = ReinclassPremtype::where('reinclass', $reinsclass)
            ->whereNotIn('premtype_code', $selectedCodes)
            ->get();

        return response()->json($result);
    }

    public function commitCover(Request $request)
    {

        try {
            $CoverRegister = CoverRegister::where('endorsement_no', $request->endorsement_no)
                ->update([
                    'commited' => 'Y',
                    'commited_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Failed to save',
            ]);
        }
    }

    public static function coverDebitedCommited($endorsement): bool
    {
        $cover = CoverRegister::select('endorsement_no', 'cover_no', 'commited', 'verified')
            ->where('endorsement_no', $endorsement)
            ->first();

        $debitted = CoverDebit::where('endorsement_no', $endorsement)->count();

        $actionable = true;
        // if TNP| TPR inital cover commited
        if ($cover->commited == 'Y' || $debitted > 0) {
            $actionable = false;
        }

        return $actionable;
    }

    public function StoreQuaterlyFigures(Request $request)
    {
        DB::beginTransaction();
        try {
            $trans_type = 'QTR';
            $type_of_bus = $request->type_of_bus;
            $prev_endorsement_no = $request->endorsement_no;

            $endorsement = $this->coverRepository->generateEndorseNo($type_of_bus, $trans_type);
            $new_endorsement_no = $endorsement->endorsement_no;
            $this->_endorsement_no = $new_endorsement_no;
            $cover_serial_no = $endorsement->serial_no;

            $prevCover = CoverRegister::where('endorsement_no', $prev_endorsement_no)->first();
            $base_cover = CoverRegister::where('cover_no', $prevCover->cover_no)
                ->where('transaction_type', ['NEW', 'REN'])
                ->orderBy('dola', 'DESC')
                ->first();

            if ($request->quarter == 1) {
                $quarter_name = 'FIRST QUARTER';
            } elseif ($request->quarter == 2) {
                $quarter_name = 'SECOND QUARTER';
            } elseif ($request->quarter == 3) {
                $quarter_name = 'THIRD QUARTER';
            } elseif ($request->quarter == 4) {
                $quarter_name = 'FOURTH QUARTER';
            } else {
                $quarter_name = '';
            }

            $prem_tax_rate = $prevCover->prem_tax_rate;
            $ri_tax_rate = $prevCover->ri_tax_rate;

            $cover = $prevCover->replicate();
            $cover->cover_serial_no = $cover_serial_no;
            $cover->endorsement_no = $new_endorsement_no;
            $cover->orig_endorsement_no = $base_cover->endorsement_no;
            $cover->transaction_type = $trans_type;
            $cover->cover_title = 'TREATY PROPORTIONAL ACCOUNT ' . trim($quarter_name) . '-' . $request->cover_year;
            $cover->verified = null;
            $cover->status = 'A';
            $cover->commited = null;
            $cover->account_year = $this->_year;
            $cover->account_month = $this->_month;
            $cover->dola = Carbon::now();
            $cover->created_by = Auth::user()->user_name;
            $cover->updated_by = Auth::user()->user_name;
            $cover->save();

            $reinclass_code = $request->reinclass_code;
            $premtype_codes = $request->premtype_code;
            $premtype_name = $request->premtype_name;
            $treaty = $request->treaty;
            $comm_rate = $request->comm_rate;
            $basic_amount = $request->premium_amount;
            $claim_amt = $request->claim_amount;

            foreach ($premtype_codes as $index => $premtype_code) {
                if ($basic_amount[$index] != 0) {
                    // Premiums
                    $CoverPremium = new CoverPremium;
                    $CoverPremium->cover_no = $request->cover_no;
                    $CoverPremium->endorsement_no = $new_endorsement_no;
                    $CoverPremium->orig_endorsement_no = $prev_endorsement_no;
                    $CoverPremium->transaction_type = $trans_type;
                    $CoverPremium->premium_type_code = $premtype_code;
                    $CoverPremium->premtype_name = $premtype_name[$index];
                    $CoverPremium->quarter = (float) $request->quarter ? $request->quarter : 0;
                    $CoverPremium->entry_type_descr = 'PRM';
                    $CoverPremium->premium_type_order_position = 1;
                    $CoverPremium->premium_type_description = 'Gross Premium';
                    $CoverPremium->type_of_bus = $request->type_of_bus;
                    $CoverPremium->class_code = $reinclass_code[$index];
                    $CoverPremium->basic_amount = str_replace(',', '', $basic_amount[$index]);
                    $CoverPremium->apply_rate_flag = 'N';
                    $CoverPremium->treaty = $treaty[$index];
                    $CoverPremium->rate = 0;
                    if ($CoverPremium->transaction_type == 'RFN' || $CoverPremium->transaction_type == 'CNC') {
                        $CoverPremium->dr_cr = 'CR';
                    } else {
                        $CoverPremium->dr_cr = 'DR';
                    }
                    $CoverPremium->final_amount = str_replace(',', '', $basic_amount[$index]);
                    $CoverPremium->created_at = Carbon::now();
                    $CoverPremium->updated_at = Carbon::now();
                    $CoverPremium->created_by = Auth::user()->user_name;
                    $CoverPremium->updated_by = Auth::user()->user_name;
                    $CoverPremium->save();

                    // Commissions
                    $rate = (float) $comm_rate[$index] ? $comm_rate[$index] : 0;

                    $CoverPremium = new CoverPremium;
                    $CoverPremium->cover_no = $request->cover_no;
                    $CoverPremium->endorsement_no = $new_endorsement_no;
                    $CoverPremium->orig_endorsement_no = $prev_endorsement_no;
                    $CoverPremium->transaction_type = $trans_type;
                    $CoverPremium->premium_type_code = $premtype_code;
                    $CoverPremium->premtype_name = $premtype_name[$index];
                    $CoverPremium->quarter = (float) $request->quarter ? $request->quarter : 0;
                    $CoverPremium->entry_type_descr = 'COM';
                    $CoverPremium->premium_type_order_position = 2;
                    $CoverPremium->premium_type_description = 'Commission';
                    $CoverPremium->type_of_bus = $request->type_of_bus;
                    $CoverPremium->class_code = $reinclass_code[$index];
                    $CoverPremium->treaty = $treaty[$index];
                    $CoverPremium->basic_amount = str_replace(',', '', $basic_amount[$index]);
                    $CoverPremium->apply_rate_flag = 'Y';
                    $CoverPremium->rate = $rate;
                    if ($CoverPremium->transaction_type == 'RFN' || $CoverPremium->transaction_type == 'CNC') {
                        $CoverPremium->dr_cr = 'DR';
                    } else {
                        $CoverPremium->dr_cr = 'CR';
                    }
                    $CoverPremium->final_amount = ($rate / 100) * str_replace(',', '', $basic_amount[$index]);
                    $CoverPremium->created_at = Carbon::now();
                    $CoverPremium->updated_at = Carbon::now();
                    $CoverPremium->created_by = Auth::user()->user_name;
                    $CoverPremium->updated_by = Auth::user()->user_name;
                    $CoverPremium->save();

                    // Premium Tax
                    $CoverPremium = new CoverPremium;
                    $CoverPremium->cover_no = $request->cover_no;
                    $CoverPremium->endorsement_no = $new_endorsement_no;
                    $CoverPremium->orig_endorsement_no = $prev_endorsement_no;
                    $CoverPremium->transaction_type = $trans_type;
                    $CoverPremium->premium_type_code = $premtype_code;
                    $CoverPremium->premtype_name = $premtype_name[$index];
                    $CoverPremium->quarter = (float) $request->quarter ? $request->quarter : 0;
                    $CoverPremium->entry_type_descr = 'PTX';
                    $CoverPremium->premium_type_order_position = 3;
                    $CoverPremium->premium_type_description = 'Premium Tax';
                    $CoverPremium->type_of_bus = $request->type_of_bus;
                    $CoverPremium->class_code = $reinclass_code[$index];
                    $CoverPremium->treaty = $treaty[$index];
                    $CoverPremium->basic_amount = str_replace(',', '', $basic_amount[$index]);
                    $CoverPremium->apply_rate_flag = 'Y';
                    if ($CoverPremium->transaction_type == 'RFN' || $CoverPremium->transaction_type == 'CNC') {
                        $CoverPremium->dr_cr = 'DR';
                    } else {
                        $CoverPremium->dr_cr = 'CR';
                    }
                    $CoverPremium->rate = $prem_tax_rate;
                    $CoverPremium->final_amount = ($prem_tax_rate / 100) * str_replace(',', '', $basic_amount[$index]);
                    $CoverPremium->created_at = Carbon::now();
                    $CoverPremium->updated_at = Carbon::now();
                    $CoverPremium->created_by = Auth::user()->user_name;
                    $CoverPremium->updated_by = Auth::user()->user_name;
                    $CoverPremium->save();

                    // Reinsurance Tax
                    $CoverPremium = new CoverPremium;
                    $CoverPremium->cover_no = $request->cover_no;
                    $CoverPremium->endorsement_no = $new_endorsement_no;
                    $CoverPremium->orig_endorsement_no = $prev_endorsement_no;
                    $CoverPremium->transaction_type = $trans_type;
                    $CoverPremium->premium_type_code = $premtype_code;
                    $CoverPremium->premtype_name = $premtype_name[$index];
                    $CoverPremium->quarter = (float) $request->quarter ? $request->quarter : 0;
                    $CoverPremium->entry_type_descr = 'RTX';
                    $CoverPremium->premium_type_order_position = 4;
                    $CoverPremium->premium_type_description = 'Reinsurance Tax';
                    $CoverPremium->type_of_bus = $request->type_of_bus;
                    $CoverPremium->class_code = $reinclass_code[$index];
                    $CoverPremium->treaty = $treaty[$index];
                    $CoverPremium->basic_amount = str_replace(',', '', $basic_amount[$index]);
                    $CoverPremium->apply_rate_flag = 'Y';
                    $CoverPremium->rate = $ri_tax_rate;
                    if ($CoverPremium->transaction_type == 'RFN' || $CoverPremium->transaction_type == 'CNC') {
                        $CoverPremium->dr_cr = 'DR';
                    } else {
                        $CoverPremium->dr_cr = 'CR';
                    }
                    $CoverPremium->final_amount = ($ri_tax_rate / 100) * str_replace(',', '', $basic_amount[$index]);
                    $CoverPremium->created_at = Carbon::now();
                    $CoverPremium->updated_at = Carbon::now();
                    $CoverPremium->created_by = Auth::user()->user_name;
                    $CoverPremium->updated_by = Auth::user()->user_name;
                    $CoverPremium->save();
                }

                if ($claim_amt[$index] != 0) {
                    // claim
                    $CoverPremium = new CoverPremium;
                    $CoverPremium->cover_no = $request->cover_no;
                    $CoverPremium->endorsement_no = $new_endorsement_no;
                    $CoverPremium->orig_endorsement_no = $prev_endorsement_no;
                    $CoverPremium->transaction_type = $trans_type;
                    $CoverPremium->premium_type_code = $premtype_code;
                    $CoverPremium->premtype_name = $premtype_name[$index];
                    $CoverPremium->quarter = (float) $request->quarter ? $request->quarter : 0;
                    $CoverPremium->entry_type_descr = 'CLM';
                    $CoverPremium->premium_type_order_position = 5;
                    $CoverPremium->premium_type_description = 'Claims';
                    $CoverPremium->type_of_bus = $request->type_of_bus;
                    $CoverPremium->class_code = $reinclass_code[$index];
                    $CoverPremium->treaty = $treaty[$index];
                    $CoverPremium->basic_amount = str_replace(',', '', $claim_amt[$index]);
                    $CoverPremium->apply_rate_flag = 'N';
                    $CoverPremium->rate = 0;
                    if ($CoverPremium->transaction_type == 'RFN' || $CoverPremium->transaction_type == 'CNC') {
                        $CoverPremium->dr_cr = 'DR';
                    } else {
                        $CoverPremium->dr_cr = 'CR';
                    }
                    $CoverPremium->final_amount = str_replace(',', '', $claim_amt[$index]);
                    $CoverPremium->created_at = Carbon::now();
                    $CoverPremium->updated_at = Carbon::now();
                    $CoverPremium->created_by = Auth::user()->user_name;
                    $CoverPremium->updated_by = Auth::user()->user_name;
                    $CoverPremium->save();
                }
            }

            // begin replication from previous

            $this->coverRepository->replicateFromPrevious($prev_endorsement_no);
            // $this->coverPremToReinNote();

            DB::commit();

            $redirectUrl = route('cover.CoverHome', ['endorsement_no' => $new_endorsement_no]);

            // Redirect back with success message and endorsement data as a request parameter
            return redirect($redirectUrl)->with('success', 'Quarterly Figures information saved successfully');
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();

            // dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function StoreProfitCommission(Request $request)
    {
        DB::beginTransaction();
        try {
            $trans_type = 'PC';
            $type_of_bus = $request->type_of_bus;
            $prev_endorsement_no = $request->endorsement_no;

            $endorsement = $this->coverRepository->generateEndorseNo($type_of_bus, $trans_type);
            $new_endorsement_no = $endorsement->endorsement_no;
            $this->_endorsement_no = $new_endorsement_no;
            $cover_serial_no = $endorsement->serial_no;

            $prevCover = CoverRegister::where('endorsement_no', $prev_endorsement_no)->first();
            $prem_tax_rate = $prevCover->prem_tax_rate;
            $ri_tax_rate = $prevCover->ri_tax_rate;
            $mgnt_exp_rate = $prevCover->mgnt_exp_rate;
            $profit_comm_rate = $prevCover->profit_comm_rate ? $prevCover->profit_comm_rate : 0;
            $profit_comm_ratio = $profit_comm_rate ? ($profit_comm_rate / 100) : 0;

            $cover = $prevCover->replicate();
            $cover->cover_serial_no = $cover_serial_no;
            $cover->endorsement_no = $new_endorsement_no;
            $cover->transaction_type = $trans_type;
            $cover->cover_title = 'PROFIT COMMISSION STATEMENT';
            $cover->verified = null;
            $cover->status = 'A';
            $cover->commited = null;
            $cover->account_year = $this->_year;
            $cover->account_month = $this->_month;
            $cover->dola = Carbon::now();
            $cover->created_by = Auth::user()->user_name;
            $cover->updated_by = Auth::user()->user_name;
            $cover->save();

            $treaty_year = $request->treaty_year;
            $quarter = $request->pc_quarter;
            $reinclass = $request->pc_reinclass;
            $treaty = $request->pc_treaty;
            $premiums = $request->pc_premium;
            $commissions = $request->pc_commission;
            $premium_taxes = $request->pc_premium_tax;
            $reinsurance_taxes = $request->pc_reinsurance_tax;
            $claim_amounts = $request->pc_claim_amount;
            $port_entry_prem = $request->port_entry_prem;
            $port_entry_loss = $request->port_entry_loss;
            $port_withdrawal_prem = $request->port_withdrawal_prem;
            $port_withdrawal_loss = $request->port_withdrawal_loss;
            $totalPrem = 0;
            $totalPremiumTax = 0;
            $totalCom = 0;
            $totalReinsuranceTax = 0;
            $totalClaim = 0;
            $mgnt_exp_amt = 0;
            // Premiums

            foreach ($premiums as $index => $premium) {
                if ($premium != 0) {
                    $premium = (int) str_replace(',', '', $premium);
                    $totalPrem = $totalPrem + $premium;
                }
            }
            if ($totalPrem != 0) {
                $mgnt_exp_amt = ($mgnt_exp_rate / 100) * $totalPrem;
                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => 'Gross Premium',
                    'quarter' => 0,
                    'entry_type_descr' => 'PRM',
                    'premium_type_order_position' => 1,
                    'premium_type_description' => 'Gross Premium',
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $totalPrem),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'SURP',
                    'rate' => $profit_comm_rate,
                    'dr_cr' => 'DR',
                    'final_amount' => (float) str_replace(',', '', $totalPrem) * $profit_comm_ratio,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            // Commissions
            foreach ($commissions as $index => $commission) {
                if ($commission != 0) {
                    $commission = (int) str_replace(',', '', $commission);
                    $totalCom = $totalCom + $commission;
                }
            }
            if ($totalCom != 0) {

                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => 'Commission',
                    'quarter' => 0,
                    'entry_type_descr' => 'COM',
                    'premium_type_order_position' => 2,
                    'premium_type_description' => 'Commission',
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $totalCom),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'SURP',
                    'rate' => $profit_comm_rate,
                    'dr_cr' => 'CR',
                    'final_amount' => (float) str_replace(',', '', $totalCom) * $profit_comm_ratio,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            // Premium_taxes
            foreach ($premium_taxes as $index => $premium_tax) {
                if ($premium_tax != 0) {
                    $premium_tax = (int) str_replace(',', '', $premium_tax);
                    $totalPremiumTax = $totalPremiumTax + $premium_tax;
                }
            }
            if ($totalPremiumTax != 0) {

                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => 'Premium Tax',
                    'quarter' => 0,
                    'entry_type_descr' => 'PTX',
                    'premium_type_order_position' => 3,
                    'premium_type_description' => 'Premium Tax',
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $totalPremiumTax),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'SURP',
                    'rate' => $profit_comm_rate,
                    'dr_cr' => 'CR',
                    'final_amount' => (float) str_replace(',', '', $totalPremiumTax) * $profit_comm_ratio,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            // reinsurance_taxes
            foreach ($reinsurance_taxes as $index => $reinsurance_tax) {
                if ($reinsurance_tax != 0) {
                    $reinsurance_tax = (int) str_replace(',', '', $reinsurance_tax);
                    $totalReinsuranceTax = $totalReinsuranceTax + $reinsurance_tax;
                }
            }
            if ($totalReinsuranceTax != 0) {

                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => 'Reinsurance Tax',
                    'quarter' => 0,
                    'entry_type_descr' => 'RTX',
                    'premium_type_order_position' => 4,
                    'premium_type_description' => 'Reinsurance Tax',
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $totalReinsuranceTax),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'SURP',
                    'rate' => $profit_comm_rate,
                    'dr_cr' => 'CR',
                    'final_amount' => (float) str_replace(',', '', $totalReinsuranceTax) * $profit_comm_ratio,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            // claims
            foreach ($claim_amounts as $index => $claim) {
                if ($claim != 0) {
                    $claim = (int) str_replace(',', '', $claim);
                    $totalClaim = $totalClaim + $claim;
                }
            }
            if ($totalClaim != 0) {

                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => 'Claims',
                    'quarter' => 0,
                    'entry_type_descr' => 'CLM',
                    'premium_type_order_position' => 5,
                    'premium_type_description' => 'Claims',
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $totalClaim),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'SURP',
                    'rate' => $profit_comm_rate,
                    'dr_cr' => 'CR',
                    'final_amount' => (float) str_replace(',', '', $totalClaim) * $profit_comm_ratio,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            // port_entry_prems
            // foreach ($port_entry_prems as $index => $port_entry_prem) {
            if ($port_entry_prem != 0) {

                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => 'Portfolio Entry Premium',
                    'quarter' => 0,
                    'entry_type_descr' => 'PEP',
                    'premium_type_order_position' => 6,
                    'premium_type_description' => 'Portfolio Entry Premium',
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $port_entry_prem),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'SURP',
                    'rate' => $profit_comm_rate,
                    'dr_cr' => 'DR',
                    'final_amount' => (float) str_replace(',', '', $port_entry_prem) * $profit_comm_ratio,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            // }

            // port_entry_prems
            // foreach ($port_entry_losses as $index => $port_entry_loss) {
            if ($port_entry_loss != 0) {

                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => 'Portfolio Entry Loss',
                    'quarter' => 0,
                    'entry_type_descr' => 'PEL',
                    'premium_type_order_position' => 7,
                    'premium_type_description' => 'Portfolio Entry Loss',
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $port_entry_loss),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'SURP',
                    'rate' => $profit_comm_rate,
                    'dr_cr' => 'DR',
                    'final_amount' => (float) str_replace(',', '', $port_entry_loss) * $profit_comm_ratio,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            // }
            // port_withdrawal_prems
            // foreach ($port_withdrawal_prems as $index => $port_withdrawal_prem) {
            if ($port_withdrawal_prem != 0) {

                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => 'Portfolio Withdrawal Premium',
                    'quarter' => 0,
                    'entry_type_descr' => 'PWP',
                    'premium_type_order_position' => 8,
                    'premium_type_description' => 'Portfolio Withdrawal Premium',
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $port_withdrawal_prem),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'SURP',
                    'rate' => $profit_comm_rate,
                    'dr_cr' => 'CR',
                    'final_amount' => (float) str_replace(',', '', $port_withdrawal_prem) * $profit_comm_ratio,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            // }

            if ($port_withdrawal_loss != 0) {

                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => 'Portfolio Withdrawal Loss',
                    'quarter' => 0,
                    'entry_type_descr' => 'PWL',
                    'premium_type_order_position' => 9,
                    'premium_type_description' => 'Portfolio Withdrawal Loss',
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $port_withdrawal_loss),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'SURP',
                    'rate' => $profit_comm_rate,
                    'dr_cr' => 'CR',
                    'final_amount' => (float) str_replace(',', '', $port_withdrawal_loss) * $profit_comm_ratio,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }

            if ($mgnt_exp_amt != 0) {

                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => 'Management Expenses',
                    'quarter' => 0,
                    'entry_type_descr' => 'MXP',
                    'premium_type_order_position' => 10,
                    'premium_type_description' => 'Management Expenses',
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $mgnt_exp_amt),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'SURP',
                    'rate' => $profit_comm_rate,
                    'dr_cr' => 'CR',
                    'final_amount' => (float) str_replace(',', '', $mgnt_exp_amt) * $profit_comm_ratio,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            // }

            $this->coverRepository->replicateFromPrevious($prev_endorsement_no);
            $this->coverPremToReinNote();

            DB::commit();

            $redirectUrl = route('cover.CoverHome', ['endorsement_no' => $new_endorsement_no]);

            return redirect($redirectUrl)->with('success', 'Profit Commission information saved successfully');
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getQuaterlyFigures(Request $request)
    {
        $cover_no = $request->cover_no;
        $treaty_year = $request->treaty_year;

        $origEndorsementNos = DB::select("
                                SELECT a.endorsement_no from cover_register a
                                WHERE cover_no ='$cover_no'
                                AND transaction_type IN ('NEW', 'REN')
                                AND EXTRACT(YEAR FROM cover_from) <= $treaty_year
                            ");

        $endorsementNosArray = array_map(function ($item) {
            return $item->endorsement_no;
        }, $origEndorsementNos);

        $results = DB::table('cover_premiums')
            ->whereIn('orig_endorsement_no', $endorsementNosArray)
            ->where('transaction_type', 'QTR')
            ->whereIn('endorsement_no', function ($query) {
                $query->select('endorsement_no')
                    ->from('cover_debit');
            })
            ->select(
                'quarter',
                'class_code',
                'treaty',
                DB::raw('SUM(CASE WHEN entry_type_descr = \'PRM\' THEN final_amount ELSE 0 END) AS Premium'),
                DB::raw('SUM(CASE WHEN entry_type_descr = \'COM\' THEN final_amount ELSE 0 END) AS Commission'),
                DB::raw('SUM(CASE WHEN entry_type_descr = \'PTX\' THEN final_amount ELSE 0 END) AS Premium_Tax'),
                DB::raw('SUM(CASE WHEN entry_type_descr = \'RTX\' THEN final_amount ELSE 0 END) AS Reinsurance_Tax'),
                DB::raw('SUM(CASE WHEN entry_type_descr = \'CLM\' THEN final_amount ELSE 0 END) AS Claims')
            )
            ->groupBy('quarter', 'class_code', 'treaty')
            ->orderBy('quarter', 'asc')
            ->orderBy('class_code', 'asc')
            ->orderBy('treaty', 'asc')
            ->get();

        return response()->json($results);
    }

    public function getQuarterlyFiguresByQuarter(Request $request)
    {
        $request->validate([
            'cover_no' => 'required|string',
            'quarter' => 'required|string|in:Q1,Q2,Q3,Q4',
            'posting_year' => 'nullable|integer',
        ]);

        $coverNo = $request->cover_no;
        $quarter = $request->quarter;
        $postingYear = $request->posting_year ?? Carbon::now()->year;

        $quarterlyData = CustomerAccDet::where('cover_no', $coverNo)
            ->where('quarter', $quarter)
            ->where('entry_type_descr', 'quarterly-figures')
            ->where('account_year', $postingYear)
            ->orderBy('line_no', 'asc')
            ->get();

        if ($quarterlyData->isEmpty()) {
            return response()->json([
                'success' => true,
                'has_data' => false,
                'data' => [],
                'message' => 'No data found for the selected quarter'
            ]);
        }

        $debitNotes = DebitNote::where('cover_no', $coverNo)
            ->where('posting_quarter', $quarter)
            ->where('posting_year', $postingYear)
            ->with('items')
            ->get();

        $items = [];
        foreach ($debitNotes as $debitNote) {
            foreach ($debitNote->items as $item) {
                $items[] = [
                    'item_code' => $item->item_code,
                    'description' => $item->description,
                    'item_type' => $item->item_type,
                    'class_group' => $item->class_group_code,
                    'class_name' => $item->class_code,
                    'line_rate' => $item->original_line_rate ?? 0,
                    'ledger' => in_array(strtoupper($item->item_code), ['IT01', 'IT26']) ? 'DR' : 'CR',
                    'amount' => $item->original_amount ?? 0,
                ];
            }
        }
        
        $firstRecord = $quarterlyData->first();

        return response()->json([
            'success' => true,
            'has_data' => true,
            'data' => [
                'posting_year' => $firstRecord->account_year,
                'posting_quarter' => $quarter,
                'posting_date' => $firstRecord->created_date,
                'currency_code' => $firstRecord->currency_code,
                'currency_rate' => $firstRecord->currency_rate,
                'items' => $items,
                'total_amount' => $quarterlyData->sum('foreign_basic_amount'),
            ],
            'message' => 'Data loaded successfully for ' . $quarter
        ]);
    }

    public function saveMdpInstallments(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'endorsement_no' => 'required',
                'installment_no.*' => 'required',
                'installment_date.*' => 'required',
                'installment_amt.*' => 'required',
            ];

            $messages = [
                'installment_no.*.required' => 'Installment Number field is required.',
                'installment_date.*.required' => 'Installment Date field is required.',
                'installment_amt.*.required' => 'Installment Amount field is required.',
            ];

            $request->validate($rules, $messages);

            $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();
            $reinLayers = CoverReinLayer::where('endorsement_no', $cover->orig_endorsement_no)->get();

            $totalIns = count($request->installment_no);
            for ($i = 0; $i < $totalIns; $i++) {
                foreach ($reinLayers as $layer) {
                    $insAmt = $layer->min_deposit / $totalIns;
                    $data = [
                        'cover_no' => $request->cover_no,
                        'endorsement_no' => $request->endorsement_no,
                        'layer_no' => $layer->layer_no,
                        'trans_type' => 'MDP',
                        'entry_type' => 'MDP',
                        'installment_no' => $request->installment_no[$i],
                        'installment_date' => $request->installment_date[$i],
                        'installment_amt' => $insAmt,
                        'created_by' => Auth::user()->user_name,
                        'updated_by' => Auth::user()->user_name,
                    ];
                    CoverInstallments::create(array_merge($data, ['dr_cr' => 'DR']));
                    CoverInstallments::create(array_merge($data, ['dr_cr' => 'CR']));
                }
            }

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            dd($e);
            DB::rollback();

            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save',
            ]);
        }
    }

    public function saveFacInstallments(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'endorsement_no' => 'required',
                'installment_no.*' => 'required',
                'installment_date.*' => 'required',
                'installment_amt.*' => 'required',
            ];

            // Define custom error messages
            $messages = [
                'installment_no.*.required' => 'Installment Number field is required.',
                'installment_date.*.required' => 'Installment Date field is required.',
                'installment_amt.*.required' => 'Installment Amount field is required.',
            ];

            // Validate the request data
            $request->validate($rules, $messages);
            $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();
            CoverInstallments::where(['endorsement_no' => $request->endorsement_no, 'dr_cr' => 'DR'])->delete();

            $totalIns = count($request->installment_no);
            for ($i = 0; $i < $totalIns; $i++) {

                // $installment = CoverInstallments::create([
                //     'cover_no'          => $request->cover_no,
                //     'endorsement_no'    => $request->endorsement_no,
                //     'layer_no'          => 0,
                //     'trans_type'        => $request->trans_type,
                //     'entry_type'        => $request->entry_type,
                //     'installment_no'    => $request->installment_no[$i],
                //     'installment_date'  => $request->installment_date[$i],
                //     'installment_amt'   => str_replace(",", "", $request->installment_amt[$i]),
                //     'created_by'        =>  Auth::user()->user_name,
                //     'updated_by'        => Auth::user()->user_name,
                // ]);
            }

            // DB::commit();
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully',
            ]);
        } catch (ValidationException $e) {
            // If validation fails, return a JSON response with errors
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            dd($e);
            DB::rollback();

            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save',
            ]);
        }
    }

    public function deleteMdpInstallments(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'installment_no' => 'required',
            ];

            $messages = [
                'installment_no.required' => 'Installment Number field is required.',
            ];

            $request->validate($rules, $messages);

            for ($i = 0; $i < count($request->installment_no); $i++) {
                $installment = CoverInstallments::where([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $request->endorsement_no,
                    'installment_no' => $request->installment_no,
                ])
                    ->delete();
            }

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Record Deleted successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollback();

            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to Delete',
            ]);
        }
    }

    public function mdpInstallmentEndorsement(Request $request)
    {
        DB::beginTransaction();
        try {
            $rules = [
                'cover_no' => 'required',
                'endorsement_no' => 'required',
                'installment_no' => 'required',
            ];

            // Define custom error messages
            $messages = [
                'installment_no.required' => 'Installment Number field is required.',
            ];

            // Validate the request data
            $request->validate($rules, $messages);

            $trans_type = 'MDP';
            $type_of_bus = $request->type_of_bus;
            $prev_endorsement_no = $request->endorsement_no;

            $endorsement = $this->coverRepository->generateEndorseNo($type_of_bus, $trans_type);
            $new_endorsement_no = $endorsement->endorsement_no;
            $this->_endorsement_no = $new_endorsement_no;
            $cover_serial_no = $endorsement->serial_no;

            $newCover = CoverRegister::where('cover_no', $request->cover_no)
                ->where('transaction_type', 'NEW')
                ->first();
            $prevCover = CoverRegister::where('endorsement_no', $prev_endorsement_no)->first();
            $prem_tax_rate = $prevCover->prem_tax_rate;
            $brokerage_comm_rate = $prevCover->brokerage_comm_rate;
            $ri_tax_rate = $prevCover->ri_tax_rate;
            if ($request->installment_no == 1) {
                $installment_name = 'FIRST INSTALLMENT';
            } elseif ($request->installment_no == 2) {
                $installment_name = 'SECOND INSTALLMENT';
            } elseif ($request->installment_no == 3) {
                $installment_name = 'THIRD INSTALLMENT';
            } elseif ($request->installment_no == 4) {
                $installment_name = 'FOURTH INSTALLMENT';
            } elseif ($request->installment_no == 5) {
                $installment_name = 'FIFTH INSTALLMENT';
            } elseif ($request->installment_no == 6) {
                $installment_name = 'SIXTH INSTALLMENT';
            } elseif ($request->installment_no == 7) {
                $installment_name = 'SEVENTH INSTALLMENT';
            } elseif ($request->installment_no == 8) {
                $installment_name = 'EIGHTH INSTALLMENT';
            } elseif ($request->installment_no == 9) {
                $installment_name = 'NINETH INSTALLMENT';
            } elseif ($request->installment_no == 10) {
                $installment_name = 'TENTH INSTALLMENT';
            } elseif ($request->installment_no == 11) {
                $installment_name = 'ELEVENTH INSTALLMENT';
            } elseif ($request->installment_no == 12) {
                $installment_name = 'TWELVETH INSTALLMENT';
            }

            $cover = $prevCover->replicate();
            $cover->cover_serial_no = $cover_serial_no;
            $cover->endorsement_no = $new_endorsement_no;
            $cover->transaction_type = $trans_type;
            $cover->verified = null;
            $cover->cover_title = 'TREATY NON PROPORTIONAL ACCOUNT - MDP(' . $installment_name . ')';
            $cover->status = 'A';
            $cover->commited = null;
            $cover->account_year = $this->_year;
            $cover->account_month = $this->_month;
            $cover->dola = Carbon::now();
            $cover->created_by = Auth::user()->user_name;
            $cover->updated_by = Auth::user()->user_name;
            $cover->save();

            $mdpInstallments = CoverInstallments::where([
                'cover_no' => $request->cover_no,
                'endorsement_no' => $newCover->endorsement_no,
                'installment_no' => $request->installment_no,
                'dr_cr' => 'DR',
            ])
                ->get();

            $premItemTypes = [
                'PRM' => [
                    'descr' => 'Premium',
                    'tax_rate' => 0,
                    'dr_cr' => 'DR',
                ],
                'RTX' => [
                    'descr' => 'Reinsurance Tax',
                    'tax_rate' => $ri_tax_rate,
                    'dr_cr' => 'CR',
                ],
                'PTX' => [
                    'descr' => 'Premium Tax',
                    'tax_rate' => $prem_tax_rate,
                    'dr_cr' => 'CR',
                ],
                // 'BCM' => [
                //     'descr' =>'Brokerage Commission',
                //     'tax_rate' => $brokerage_comm_rate,
                //     'dr_cr' => 'CR',
                // ],
            ];

            foreach ($mdpInstallments as $mdpInstallment) {
                foreach ($premItemTypes as $key => $premItemType) {
                    $mdp_amt = $mdpInstallment->installment_amt;
                    $tax_rate = (float) $premItemType['tax_rate'];
                    $basic_amt = $mdp_amt;
                    $amt = 0;
                    switch ($key) {
                        case 'PRM':
                            $amt = $mdp_amt;
                            break;
                        default:
                            $amt = ($tax_rate * $mdp_amt) / 100;
                            break;
                    }

                    $position = (int) CoverPremium::where('endorsement_no', $cover->enodrsement_no)
                        ->where('entry_type_descr', $premItemType['descr'])
                        ->max('premium_type_order_position') + 1;
                    CoverPremium::create([
                        'cover_no' => $cover->cover_no,
                        'endorsement_no' => $cover->endorsement_no,
                        'layer_no' => $mdpInstallment->layer_no,
                        'installment_no' => $mdpInstallment->installment_no,
                        'orig_endorsement_no' => $cover->orig_endorsement_no,
                        'transaction_type' => $trans_type,
                        'premium_type_code' => 'ALL',
                        'premtype_name' => 'ALL CLASSES',
                        'dr_cr' => $premItemType['dr_cr'],
                        'quarter' => $this->_quarter,
                        'entry_type_descr' => $key,
                        'premium_type_order_position' => $position,
                        'premium_type_description' => $premItemType['descr'],
                        'type_of_bus' => $cover->type_of_bus,
                        'treaty' => 'ALL',
                        'class_code' => 'ALL',
                        'basic_amount' => $basic_amt,
                        'apply_rate_flag' => $tax_rate != 0 ? 'Y' : 'N',
                        'rate' => $tax_rate,
                        'final_amount' => $amt,
                        'created_by' => Auth::user()->user_name,
                        'updated_by' => Auth::user()->user_name,
                    ]);
                }
            }

            $this->coverRepository->replicateFromPrevious($prev_endorsement_no);
            $this->coverPremToReinNote();

            DB::commit();

            return redirect()->route('cover.CoverHome', ['endorsement_no' => $new_endorsement_no])->with('success', 'MDP installemnt information saved successfully');
        } catch (Throwable $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'MDP installemnt information Failed to save');
        }
    }

    public function coverPremToReinNote()
    {
        $coverPremiums = DB::select("SELECT * FROM cover_premiums where endorsement_no='$this->_endorsement_no'");
        foreach ($coverPremiums as $coverPrem) {
            if ($coverPrem->dr_cr == 'DR') {
                $dr_cr = 'CR';
            } elseif ($coverPrem->dr_cr == 'CR') {
                $dr_cr = 'DR';
            }

            $amtData = [
                'entry_type_descr' => $coverPrem->entry_type_descr,
                'total' => $coverPrem->basic_amount,
                'amount' => $coverPrem->final_amount,
                'rate' => $coverPrem->rate,
                'dr_cr' => $dr_cr,
            ];
            $this->InsertReinNote($amtData);
        }
    }

    public function InsertReinNote($amtData)
    {
        $cover = CoverRegister::where('endorsement_no', $this->_endorsement_no)->first();
        $reinsurers = CoverRipart::where('endorsement_no', $this->_endorsement_no)->get();

        foreach ($reinsurers as $ripart) {

            $total_gross = ($amtData['total'] * $ripart->share) / 100;
            $gross = ($amtData['amount'] * $ripart->share) / 100;
            $net_amt = $gross;

            $data = [
                'cover_no' => $cover->cover_no,
                'endorsement_no' => $cover->endorsement_no,
                'partner_no' => $ripart->partner_no,
                'transaction_type' => $cover->transaction_type,
                'account_year' => $this->_year,
                'account_month' => $this->_month,
                'share' => (float) $ripart->share,
                'created_by' => Auth::user()->user_name,
                'updated_by' => Auth::user()->user_name,
            ];

            if (in_array($amtData['entry_type_descr'], ['PRM', 'MDP'])) {
                $tran_no = (int) ReinNote::where('endorsement_no', $this->_endorsement_no)->max('tran_no') + 1;

                $ln_no = (int) ReinNote::where('endorsement_no', $this->_endorsement_no)
                    ->where('transaction_type', $cover->transaction_type)
                    ->where('entry_type_descr', 'WHT')
                    ->count() + 1;

                $wht_rate = (float) $ripart->wht_rate;
                $wht_amt = ($wht_rate * $gross) / 100;

                $data['tran_no'] = $tran_no;
                $data['ln_no'] = $ln_no;
                $data['entry_type_descr'] = 'WHT';
                $data['dr_cr'] = 'DR';
                $data['rate'] = $wht_rate;
                $data['total_gross'] = $wht_amt;
                $data['gross'] = $wht_amt;
                $data['net_amt'] = $wht_amt;

                ReinNote::create($data);
            }

            $tran_no = (int) ReinNote::where('endorsement_no', $this->_endorsement_no)->max('tran_no') + 1;

            $ln_no = (int) ReinNote::where('endorsement_no', $this->_endorsement_no)
                ->where('transaction_type', $cover->transaction_type)
                ->where('entry_type_descr', $amtData['entry_type_descr'])
                ->count() + 1;

            $data['tran_no'] = $tran_no;
            $data['ln_no'] = $ln_no;
            $data['entry_type_descr'] = $amtData['entry_type_descr'];
            $data['dr_cr'] = $amtData['dr_cr'];
            $data['rate'] = $amtData['rate'];
            $data['total_gross'] = $total_gross;
            $data['gross'] = $gross;
            $data['net_amt'] = $net_amt;

            ReinNote::create($data);
        }
    }

    public function StorePropPortfolio(Request $request)
    {
        DB::beginTransaction();
        try {
            $trans_type = 'POT';
            $type_of_bus = $request->type_of_bus;
            $prev_endorsement_no = $request->orig_endorsement;
            $portfolio_type = $request->portfolio_type;
            $port_reinsurer = $request->port_reinsurer;
            $port_reinsurer_share = $request->port_share;
            $total_port_amt = str_replace(',', '', $request->port_amt);

            $endorsement = $this->coverRepository->generateEndorseNo($type_of_bus, $trans_type);
            if ($portfolio_type == 'IN') {
                $cover_title = 'TREATY PROPORTIONAL ACCOUNT - PORTFOLIO IN';
                $premium_desc = 'PREMIUM PORTFOLIO ENTRY - IN';
                $loss_desc = 'LOSS PORTFOLIO ENTRY - IN';
            } elseif ($portfolio_type == 'OUT') {
                $cover_title = 'TREATY PROPORTIONAL ACCOUNT - PORTFOLIO OUT';
                $premium_desc = 'PREMIUM PORTFOLIO ENTRY - OUT';
                $loss_desc = 'LOSS PORTFOLIO ENTRY - OUT';
            }
            $new_endorsement_no = $endorsement->endorsement_no;
            // dd($new_endorsement_no);
            $this->_endorsement_no = $new_endorsement_no;
            $cover_serial_no = $endorsement->serial_no;

            $prevCover = CoverRegister::where('endorsement_no', $prev_endorsement_no)->first();
            $port_share = $prevCover->share_offered;
            $port_amt = ($port_share / 100) * $total_port_amt;
            $port_prm_rate = $request->port_prm_rate;
            $port_prm_amt = ($port_prm_rate / 100) * $port_amt;
            $port_loss_rate = $request->port_loss_rate;
            $port_loss_amt = ($port_loss_rate / 100) * $port_amt;
            $treaty_year = $request->portfolio_year;

            $prem_tax_rate = $prevCover->prem_tax_rate;
            $ri_tax_rate = $prevCover->ri_tax_rate;
            $mgnt_exp_rate = $prevCover->mgnt_exp_rate;
            $profit_comm_rate = $prevCover->profit_comm_rate ? $prevCover->profit_comm_rate : 0;
            $profit_comm_ratio = $profit_comm_rate ? ($profit_comm_rate / 100) : 0;

            $cover = $prevCover->replicate();
            $cover->cover_serial_no = $cover_serial_no;
            $cover->endorsement_no = $new_endorsement_no;
            $cover->transaction_type = $trans_type;
            $cover->cover_title = $cover_title;
            $cover->verified = null;
            $cover->status = 'A';
            $cover->commited = null;
            $cover->account_year = $this->_year;
            $cover->account_month = $this->_month;
            $cover->dola = Carbon::now();
            $cover->created_by = Auth::user()->user_name;
            $cover->updated_by = Auth::user()->user_name;
            $cover->save();

            $totalPrem = 0;
            $totalPremiumTax = 0;
            $totalCom = 0;
            $totalReinsuranceTax = 0;
            $totalClaim = 0;
            $mgnt_exp_amt = 0;
            // Premiums
            if ($port_prm_amt != 0) {
                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => $premium_desc,
                    'quarter' => 0,
                    'entry_type_descr' => 'PRM',
                    'premium_type_order_position' => 1,
                    'premium_type_description' => $premium_desc,
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $port_amt),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'ALL',
                    'rate' => $port_prm_rate,
                    'dr_cr' => 'DR',
                    'final_amount' => (float) str_replace(',', '', $port_prm_amt),
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }
            // Losses
            if ($port_loss_amt != 0) {
                CoverPremium::create([
                    'cover_no' => $request->cover_no,
                    'endorsement_no' => $new_endorsement_no,
                    'orig_endorsement_no' => $prev_endorsement_no,
                    'transaction_type' => $trans_type,
                    'premium_type_code' => 0,
                    'premtype_name' => $loss_desc,
                    'quarter' => 0,
                    'entry_type_descr' => 'CLM',
                    'premium_type_order_position' => 1,
                    'premium_type_description' => $loss_desc,
                    'type_of_bus' => $type_of_bus,
                    'class_code' => 'ALL',
                    'basic_amount' => str_replace(',', '', $port_amt),
                    'apply_rate_flag' => 'Y',
                    'treaty' => 'ALL',
                    'rate' => $port_loss_rate,
                    'dr_cr' => 'DR',
                    'final_amount' => (float) str_replace(',', '', $port_loss_amt),
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }

            if ($portfolio_type == 'OUT') {
                $reinsurers = CoverRipart::where('endorsement_no', $base_cover->endorsement_no)->where('partner_no', $request->port_reinsurer)->get();

                foreach ($reinsurers as $ripart) {
                    $tran_no = (int) CoverRipart::withTrashed()->max('tran_no') + 1;

                    $data = $ripart->getAttributes();
                    $data['tran_no'] = $tran_no;
                    $data['endorsement_no'] = $this->_endorsement_no;
                    $data['period_year'] = $this->_year;
                    $data['period_month'] = $this->_month;
                    $data['total_sum_insured'] = 0;
                    $data['total_premium'] = $port_prm_amt;
                    $data['total_commission'] = 0;
                    $data['sum_insured'] = 0;
                    $data['premium'] = $port_prm_amt * ($port_reinsurer_share / 100);
                    $data['comm_rate'] = 0;
                    $data['commission'] = 0;
                    $data['wht_rate'] = 0;
                    $data['wht_amt'] = 0;
                    $data['written_lines'] = 0;
                    $data['prem_tax_rate'] = 0;
                    $data['prem_tax'] = 0;
                    $data['ri_tax_rate'] = 0;
                    $data['ri_tax'] = 0;
                    $data['total_claim_amt'] = $port_loss_amt;
                    $data['claim_amt'] = $port_loss_amt * ($port_reinsurer_share / 100);
                    $data['total_mdp_amt'] = 0;
                    $data['mdp_amt'] = 0;
                    $data['created_by'] = Auth::user()->user_name;
                    $data['updated_by'] = Auth::user()->user_name;
                    $data['created_at'] = Carbon::now();
                    $data['updated_at'] = Carbon::now();

                    CoverRipart::create($data);
                }
            }
            if ($portfolio_type == 'IN') {
                $CoverRegister = CoverRegister::where('endorsement_no', $new_endorsement_no)->first();
                $reinsurer = Customer::where('customer_id', $port_reinsurer)->first();
                $tran_no = (int) CoverRipart::withTrashed()->max('tran_no') + 1;

                $coverRipart = new CoverRipart;

                // Assign values from the request to the model attributes
                $coverRipart->cover_no = $CoverRegister->cover_no;
                $coverRipart->endorsement_no = $CoverRegister->endorsement_no;
                $coverRipart->tran_no = $tran_no;
                $coverRipart->period_year = $this->_year;
                $coverRipart->period_month = $this->_month;
                $coverRipart->partner_no = $reinsurer->customer_id;
                $coverRipart->share = $port_reinsurer_share;
                $coverRipart->written_lines = $port_reinsurer_share;
                $coverRipart->comm_rate = 0;
                $coverRipart->wht_rate = 0;
                $wht_amt = 0;
                $coverRipart->total_sum_insured = 0;
                $coverRipart->total_premium = $port_prm_amt;
                $coverRipart->total_commission = 0;
                $coverRipart->wht_amt = 0;
                $coverRipart->sum_insured = 0;
                $coverRipart->premium = $port_prm_amt * ($port_reinsurer_share / 100);
                $coverRipart->commission = 0;
                $coverRipart->treaty_code = 'ALL';
                $coverRipart->total_claim_amt = $port_loss_amt;
                $coverRipart->claim_amt = $port_loss_amt * ($port_reinsurer_share / 100);
                $coverRipart->total_mdp_amt = 0;
                $coverRipart->mdp_amt = 0;
                $coverRipart->prem_tax_rate = 0;
                $coverRipart->prem_tax = 0;
                $coverRipart->ri_tax_rate = 0;
                $coverRipart->ri_tax = 0;
                $coverRipart->created_by = Auth::user()->user_name;
                $coverRipart->updated_by = Auth::user()->user_name;

                $coverRipart->save();
            }
            $this->coverPremToReinNote();

            DB::commit();

            $redirectUrl = route('cover.CoverHome', ['endorsement_no' => $new_endorsement_no]);

            // Redirect back with success message and endorsement data as a request parameter
            return redirect($redirectUrl)->with('success', 'Profit Commission information saved successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            dd($e);

            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            dd($e);

            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function getTreatyCover(Request $request)
    {
        $cover_no = $request->cover_no;
        $treaty_year = $request->treaty_year;

        $origEndorsementNos = DB::select("
                                    SELECT a.endorsement_no,a.cover_from,a.cover_to from cover_register a
                                    WHERE cover_no ='$cover_no'
                                    AND transaction_type IN ('NEW', 'REN')
                                    AND EXTRACT(YEAR FROM cover_from) <= $treaty_year
                                ");

        return response()->json($origEndorsementNos);
    }

    public function getReinsurersOrigEndorsement(Request $request)
    {
        $portfolio_type = $request->portfolio_type;
        $orig_endorsement = $request->orig_endorsement;
        $reinsurers = [];
        $count = 0;
        if ($orig_endorsement != null) {
            if ($portfolio_type == 'IN') {
                $cusTypes = ['REINCO'];
                $cusTypes_str = implode(',', array_map(function ($item) {
                    return "'" . $item . "'";
                }, $cusTypes));

                $reinsurers = DB::select("
                                            SELECT DISTINCT a.customer_id, a.name from customers a
                                            JOIN customer_types b ON b.type_id = a.customer_type
                                            where b.code in($cusTypes_str)
                                        ");

                $count = count($reinsurers);
            } elseif ($portfolio_type == 'OUT') {
                $partner_nos = CoverRipart::where('endorsement_no', $orig_endorsement)->pluck('partner_no')->toArray();
                $partner_nos_str = implode(',', array_map(function ($item) {
                    return "'" . $item . "'";
                }, $partner_nos));

                $reinsurers = DB::select("
                                        SELECT a.customer_id, a.name,b.partner_no,b.share,b.treaty_code from customers a
                                            JOIN coverripart b ON b.partner_no = a.customer_id
                                            where a.customer_id in($partner_nos_str) and b.endorsement_no='$orig_endorsement'
                                        ");
                $count = count($reinsurers);
            }
        }

        return response()->json([
            'reinsurers' => $reinsurers,
            'count' => $count,
        ]);
    }

    public function preCoverVerification(Request $request)
    {
        return $this->coverRepository->preCoverVerification($request);
    }

    public function policyRenewal(Request $request)
    {
        try {
            $policy = PolicyRenewal::query();
            $cover = CoverRegister::with('customer')->where('customer_id', $request->customer_id)->where('cover_no', $request->cover_no)->latest()->first();

            $policies = $policy->where('policy_number', $cover->cover_no)->orderBy('created_at', 'asc')
                ->paginate(10);

            return view('cover.renewal_notice', [
                'policies' => $policies,
                'cover_no' => $cover?->cover_no,
                'customer_id' => $cover?->customer_id,
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An internal server error occurred.',
                'error' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function generatePolicyRenewal(Request $request)
    {
        try {
            $company = Company::first();
            $customer = Customer::where('customer_id', $request->customer_id)
                ->first(['customer_id', 'name', 'postal_address', 'postal_town', 'city', 'email', 'telephone', 'country_iso', 'customer_type']);

            if (! $customer) {
                return response()->json(['message' => 'Customer not found'], Response::HTTP_NOT_FOUND);
            }
            $cover = CoverRegister::with('customer')
                ->where('customer_id', $customer->customer_id)
                ->where('cover_no', $request->cover_no)
                ->latest()
                ->first();

            if (! $cover) {
                return response()->json(['message' => 'Cover not found'], Response::HTTP_NOT_FOUND);
            }

            $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->with('partner')->get();
            $class_name = ($cover->class_code == 'TRT') ? 'TREATY' : Classes::where('class_code', $cover->class_code)->value('class_name');

            $created_at = Carbon::now()->format('d-M-Y');
            $expiration_date = Carbon::parse($cover->cover_to)->addDay()->format('d/m/Y');
            $shared_data = [
                'company' => $company,
                'class_name' => $class_name,
                'cover' => $cover,
                'customer' => $customer,
                'created_at' => $created_at,
                'expiration_date' => $expiration_date,
                'reinsurers' => $reinsurers,
            ];

            $documents = [
                [
                    'view_name' => 'printouts.fac_debit_renewal_notice',
                    'type' => 'cedant',
                    'prefix' => 'Cedant_Renewal_Notice',
                ],
                [
                    'view_name' => 'printouts.fac_rein_renewal_notice',
                    'type' => 'reinsurer',
                    'prefix' => 'Reinsurer_Renewal_Notice',
                ],
            ];

            $yearThreshold = 1;
            $prevPolicy = PolicyRenewal::where('policy_number', $cover->cover_no)
                ->whereDate('renewal_date', '<=', Carbon::now()->addYears($yearThreshold))
                ->whereDate('renewal_date', '>=', Carbon::now())
                ->first();

            if ($prevPolicy) {
                // Delete associated documents
                foreach ($prevPolicy->documents as $document) {
                    if (File::exists(storage_path('/app/public/renewals/' . $document->doc_name))) {
                        File::delete(storage_path('/app/public/renewals/' . $document->doc_name));
                    }
                    $document->delete();
                }
                $prevPolicy->delete();
            }

            $policyRenewal = PolicyRenewal::updateOrCreate(
                ['policy_number' => $cover->cover_no],
                [
                    'client_name' => $cover->customer->name ?? null,
                    'doc_name' => 'Renewal_Notice_' . time() . '.pdf',
                    'client_email' => $cover->customer->email ?? null,
                    'renewal_date' => Carbon::parse($created_at)->addYears(1)->format('d-M-Y'),
                    'last_notice_sent' => Carbon::parse($created_at)->format('d-M-Y'),
                    'notice_status' => 'Option to renew',
                ]
            );

            foreach ($documents as $document) {
                $pdf = Pdf::loadView($document['view_name'], $shared_data)
                    ->setPaper('a4', 'portrait')
                    ->setWarnings(false);

                $pdf->set_option('isHtml5ParserEnabled', true);
                $pdf->set_option('isPhpEnabled', true);
                $pdf->set_option('isRemoteEnabled', true);

                $pdfFilename = $document['prefix'] . '_' . time() . '.pdf';
                $pdfPath = storage_path('app/public/renewals/' . $pdfFilename);

                Storage::put('public/renewals/' . $pdfFilename, $pdf->output());
                $pdfSize = filesize($pdfPath);

                // Create document record
                PolicyRenewalDocument::create([
                    'policy_renewal_id' => $policyRenewal->id,
                    'doc_name' => $pdfFilename,
                    'doc_path' => '/uploads/renewals/' . $pdfFilename,
                    'doc_size' => $pdfSize,
                    'doc_type' => $document['type'],
                ]);
            }

            return response()->json([
                'message' => 'Renewal policy documents created successfully',
                'status' => Response::HTTP_CREATED,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An internal server error occurred.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function sendPolicyRenewal(Request $request)
    {
        try {
            $policy = PolicyRenewal::find($request->policyId)->load('documents');

            if ($policy) {
                $results = [
                    'policy' => $policy,
                    'request' => $request->all(),
                ];
                SendRenewalNoticeJob::dispatch($results);
            }

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Renewal notice has been sent',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to queue renewal notice',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteRenewalNotice(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'cover_no' => 'required',
            ]);

            $policy = PolicyRenewal::find($request->id);
            if ($policy) {
                $policy->delete();
            }

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Renewal notice deleted successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to delete renewal notice',
            ]);
        }
    }

    public function deletePolicyCover(Request $request)
    {
        try {
            $request->validate([
                'endorsement_no' => 'required',
                'cover_no' => 'required',
                'customer_id' => 'required',
            ]);

            $cover = CoverRegister::where([
                'endorsement_no' => $request->endorsement_no,
                'cover_no' => $request->cover_no,
                'customer_id' => $request->customer_id,
            ]);
            if ($cover) {
                EndorsementNarration::where([
                    'endorsement_no' => $request->endorsement_no,
                    'cover_no' => $request->cover_no,
                ])->delete();
                $this->coverRepository->deleteCoverData($request->cover_no, $request->endorsement_no);
                $cover->delete();
            }

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Cover deleted successfully',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to delete cover',
            ]);
        }
    }

    public function endorseNarrationDatatable(Request $request)
    {
        $query = EndorsementNarration::query()->where(['endorsement_no' => $request->endorsement_no, 'cover_no' => $request->cover_no])
            ->where('endorse_type_slug', $request->endorse_type_slug);

        return Datatables::of($query)
            ->addColumn('partner_name', function ($data) {
                return '';
            })
            ->addColumn('endorsement_type', function ($data) {
                return '';
            })
            ->addColumn('action', function ($data) {
                return '';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function claims_datatable(Request $request)
    {
        $cover_no = $request->get('cover_no');
        $endorsement_no = $request->get('endorsement_no');

        $query = ClaimRegister::query()
            ->where('cover_no', $cover_no)
            ->where('endorsement_no', $endorsement_no)
            ->orderByDesc('claim_serial_no');

        return datatables::of($query)
            ->editColumn('claim_no', function ($data) {
                return $data->claim_no;
            })
            ->editColumn('claim_date', function ($data) {
                return $data->created_date ? formatDate($data->created_date) : 'N/A';
            })
            ->editColumn('date_of_loss', function ($data) {
                return $data->date_of_loss ? formatDate($data->date_of_loss) : 'N/A';
            })
            ->editColumn('claim_amount', function ($data) {
                return formatCurrency($data->claim_amount ?? 0);
            })
            ->editColumn('claimed_amount', function ($data) {
                return formatCurrency($data->claimed_amount ?? 0);
            })
            ->editColumn('status', function ($data) {
                $status = $data->status;
                $badge = '';
                switch ($status) {
                    case 'A':
                        $badge = '<span class="badge bg-success-gradient">Active</span>';
                        break;
                    case 'C':
                        $badge = '<span class="badge bg-danger-gradient">Closed</span>';
                        break;
                    default:
                        $badge = '<span class="badge bg-info-gradient">' . ($status ?: 'Pending') . '</span>';
                        break;
                }

                return $badge;
            })
            ->addColumn('actions', function ($data) {
                $btn = '<div class="btn-list">';
                $btn .= '<a href="javascript:void(0);" data-claim-no="' . $data->claim_no . '" class="btn btn-sm btn-outline-primary btn-wave view-claim"><i class="ri-eye-line me-1"></i>View</a>';
                $btn .= '</div>';

                return $btn;
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    public function statements_datatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');

        $query = CoverRipart::query()
            ->where('endorsement_no', $endorsement_no)
            ->with(['partner'])
            ->orderBy('tran_no');

        return datatables::of($query)
            ->addColumn('partner_name', function ($data) {
                return $data->partner->name ?? 'N/A';
            })
            ->editColumn('share', function ($data) {
                return number_format($data->share ?? 0, 2) . '%';
            })
            ->editColumn('gross_premium', function ($data) {
                return formatCurrency($data->gross_premium ?? 0);
            })
            ->editColumn('commission', function ($data) {
                return formatCurrency($data->commission ?? 0);
            })
            ->editColumn('brokerage', function ($data) {
                return formatCurrency($data->brokerage ?? 0);
            })
            ->editColumn('premium_tax', function ($data) {
                return formatCurrency($data->premium_tax ?? 0);
            })
            ->editColumn('wht_amount', function ($data) {
                return formatCurrency($data->wht_amount ?? 0);
            })
            ->editColumn('ri_tax', function ($data) {
                return formatCurrency($data->ri_tax ?? 0);
            })
            ->addColumn('net_amount', function ($data) {
                $net = ($data->gross_premium ?? 0)
                    - ($data->commission ?? 0)
                    - ($data->brokerage ?? 0)
                    + ($data->premium_tax ?? 0)
                    + ($data->wht_amount ?? 0)
                    + ($data->ri_tax ?? 0);

                return formatCurrency($net);
            })
            ->addColumn('status', function ($data) {
                return '<span class="badge bg-success-gradient">Active</span>';
            })
            ->addColumn('actions', function ($data) {
                $btn = '<div class="btn-list">';
                $btn .= '<a href="javascript:void(0);" data-tran-no="' . $data->tran_no . '" class="btn btn-sm btn-outline-primary btn-wave view-statement"><i class="ri-eye-line me-1"></i>View</a>';
                $btn .= '</div>';

                return $btn;
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    public function sendReinsurerEmail(Request $request)
    {
        try {
            $coverRepart = CoverRipart::where(['cover_no' => $request->coverNo, 'endorsement_no' => $request->endorsementNo])->first();

            if ($coverRepart) {
                SendReinsurerEmailJob::dispatch(
                    $request->endorsementNo,
                    $coverRepart->partner_no,
                    $request
                );
            }

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Email sent notice has been sent to reinsurer',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProspectData($prospectId)
    {
        try {
            $prospect = PipelineOpportunity::where([
                'handed_over' => 'Y',
                'opportunity_id' => $prospectId,
            ])
                ->has('handovers')
                ->with('handovers')
                ->first();

            if (! $prospect) {
                return response()->json([
                    'status' => false,
                    'message' => 'Prospect not found',
                ], 404);
            }

            $existingCover = CoverRegister::where('prospect_id', $prospectId)->first();

            if ($existingCover) {
                return response()->json([
                    'status' => false,
                    'message' => 'Prospect already integrated!',
                ], 409);
            }

            $coverType = CoverType::where('short_description', 'N')->first();

            $data = [
                'customer_id' => $prospect->customer_id,
                'trans_type' => 'NEW',
                'type_of_bus' => $prospect->type_of_bus,
                'covertype' => $coverType?->type_id,
                'branchcode' => $prospect->branchcode,
                'broker_flag' => $prospect->broker_flag ?? 'N',
                'prospect_id' => $prospect->id,
                'division' => $prospect->divisions,
                'pay_method' => $prospect->pay_method ?? '101',
                'no_of_installments' => $prospect->no_of_installments,
                'currency_code' => $prospect->currency_code,
                'today_currency' => $prospect->today_currency,
                'premium_payment_term' => $prospect->premium_payment_term,
                'class_group' => $prospect->class_group,
                'classcode' => $prospect->classcode,
                'insured_name' => $prospect->insured_name,
                'fac_date_offered' => Carbon::parse($prospect->fac_date_offered)->format('Y-m-d'),
                'sum_insured_type' => $prospect->sum_insured_type,
                'total_sum_insured' => $prospect->total_sum_insured,
                'eml_rate' => $prospect->eml_rate,
                'eml_amt' => $prospect->eml_amt,
                'apply_eml' => $prospect->apply_eml,
                'effective_sum_insured' => $prospect->effective_sum_insured,
                'risk_details' => $prospect->risk_details,
                'cede_premium' => $prospect->cede_premium,
                'rein_premium' => $prospect->rein_premium,
                'fac_share_offered' => $prospect->fac_share_offered,
                'comm_rate' => $prospect->comm_rate,
                'comm_amt' => $prospect->comm_amt,
                'reins_comm_type' => $prospect->reins_comm_type,
                'reins_comm_rate' => $prospect->reins_comm_rate,
                'reins_comm_amt' => $prospect->reins_comm_amt,
                'brokerage_comm_type' => $prospect->brokerage_comm_type,
                'brokerage_comm_amt' => $prospect->brokerage_comm_amt,
                'brokerage_comm_rate' => $prospect->brokerage_comm_rate,
                'brokerage_comm_rate_amnt' => $prospect->brokerage_comm_rate_amnt,
                'vat_charged' => $prospect->vat_charged,
                'limit_per_reinclass' => $prospect->limit_per_reinclass,
                'layer_no' => $prospect->layer_no,
                'nonprop_reinclass' => $prospect->nonprop_reinclass,
                'nonprop_reinclass_desc' => $prospect->nonprop_reinclass_desc,
                'indemnity_treaty_limit' => $prospect->indemnity_treaty_limit,
                'underlying_limit' => $prospect->underlying_limit,
                'coverfrom' => Carbon::parse($prospect->effective_date)->format('Y-m-d'),
                'coverto' => Carbon::parse($prospect->closing_date)->format('Y-m-d'),
                'brokercode' => $prospect->brokercode,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Success',
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching prospect data',
            ], 500);
        }
    }

    public function fetchReinsurers(Request $request)
    {
        try {
            $validated = $request->validate([
                'cover_no' => 'required|string',
                'endorsement_no' => 'required|string',
            ]);

            $cover = CoverRegister::where('cover_no', $validated['cover_no'])->where('endorsement_no', $validated['endorsement_no'])->firstOrFail();

            $isTreaty = in_array($cover->type_of_bus, ['TPR', 'TNP']);

            $responseData = [];

            if ($isTreaty) {
                $responseData = $this->fetchTreatyReinsurers($cover);
            } else {
                $responseData = $this->fetchFacultativeReinsurers($cover);
            }

            return response()->json([
                'success' => true,
                'data' => $responseData,
                'cover_type' => $cover->type_of_bus,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cover not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reinsurers data',
            ], 500);
        }
    }

    private function fetchTreatyReinsurers(CoverRegister $cover): array
    {
        // $treaties = CoverTreaty::where('cover_id', $cover->id)
        //     ->with(['reinsurers' => function ($query) {
        //         $query->with(['partner', 'installments']);
        //     }])
        //     ->get();

        $treaties = collect([]);

        return [
            'treaties' => $treaties->map(function ($treaty) use ($cover) {
                return [
                    'treaty_id' => $treaty->treaty_id,
                    'layer_no' => $treaty->layer_no,
                    'reinsurers' => $treaty->reinsurers->map(function ($reinsurer) use ($cover) {
                        return $this->mapReinsurerData($reinsurer, $cover, true);
                    })->toArray(),
                ];
            })->toArray(),
        ];
    }

    private function fetchFacultativeReinsurers(CoverRegister $cover): array
    {
        $reinsurers = BdFacReinsurer::where('opportunity_id', $cover->prospect_id)->get();

        return [
            'reinsurers' => $reinsurers->map(function ($reinsurer) use ($cover) {
                return $this->mapReinsurerData($reinsurer, $cover, false);
            })->toArray(),
        ];
    }

    private function mapReinsurerData($reinsurer, $cover, bool $isTreaty): array
    {
        $data = [
            'id' => $reinsurer->id,
            'partner_no' => $reinsurer->reinsurer_id,
            'reinsurer_id' => $reinsurer->reinsurer_id,
            'reinsurer_name' => $reinsurer->reinsurer_name,
            'written_share' => $reinsurer->updated_written_share,
            'share' => $reinsurer->updated_signed_share,
            'wht_rate' => $reinsurer->wht_rate ?? '0.00',
            'premium_type' => $reinsurer->premium_type ?? 'net',
            'pay_method' => $cover->pay_method_code,
        ];

        if ($isTreaty) {
            $data = array_merge($data, [
                'compulsory_acceptance' => $reinsurer->compulsory_acceptance,
                'optional_acceptance' => $reinsurer->optional_acceptance,
            ]);
        } else {
            $data = array_merge($data, [
                'sum_insured' => $cover->total_sum_insured,
                'premium' => $cover->cedant_premium,
                'comm_rate' => $cover->cedant_comm_rate,
                'comm_amt' => $cover->cedant_comm_amount,
                'brokerage_comm_type' => $cover->brokerage_comm_type,
                'brokerage_comm_amt' => $cover->brokerage_comm_amt,
                'brokerage_comm_rate' => $cover->brokerage_comm_rate,
                'brokerage_comm_rate_amnt' => 0,
                'apply_fronting' => 'N',
                'fronting_rate' => 0,
                'fronting_amt' => 0,
                'no_of_installments' => $cover->no_of_installments,
            ]);

            // if ($cover->installments && $cover->installments->count() > 0) {
            //     $data['installments'] = $reinsurer->installments->map(function ($inst) {
            //         return [
            //             'due_date' => $inst->due_date?->format('Y-m-d'),
            //             'amount' => $inst->amount,
            //             'status' => $inst->status,
            //         ];
            //     })->toArray();
            // }
        }

        return $data;
    }
}
