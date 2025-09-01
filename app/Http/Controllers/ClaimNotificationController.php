<?php

namespace App\Http\Controllers;

use App\Enums\SystemActionEnums;
use App\Jobs\SendClaimNotificationJob;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Peril;
use App\Models\Branch;
use App\Models\Broker;
use App\Models\Classes;
use App\Models\Customer;
use App\Models\ClaimDocs;
use App\Models\CoverType;
use App\Models\ClaimPeril;
use App\Models\CoverRipart;
use App\Models\BusinessType;
use App\Models\ClaimAckDocs;
use App\Models\ClaimNtfDocs;
use Illuminate\Http\Request;
use App\Models\ClaimNtfPeril;
use App\Models\ClaimRegister;
use App\Models\CoverRegister;
use App\Models\SystemProcess;
use App\Models\ClaimAckParams;
use App\Models\ClaimNtfStatus;
use App\Models\ClaimNtfAckDocs;
use App\Models\ClaimNtfRegister;
use App\Models\ClaimStatusParam;
use App\Models\Company;
use App\Models\CoverDebit;
use Illuminate\Support\Facades\DB;
use App\Models\SystemProcessAction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class ClaimNotificationController extends Controller
{
    private $_year;
    private $_month;
    private $_quarter;

    public function __construct()
    {
        $this->_year = Carbon::now()->year;
        $this->_month = Carbon::now()->month;
        $this->_quarter = Carbon::now()->quarter;
    }

    public function getCustomers(Request $request)
    {
        try {
            $customers = DB::table('customers as c')
                ->select([
                    'c.customer_id',
                    'c.name',
                    'c.postal_address',
                    'c.postal_town',
                    'c.city',
                    'c.email',
                    'c.telephone',
                    'c.country_iso',
                    'c.customer_type',
                    'cr.cover_no',
                    'cr.type_of_bus',
                    'cr.dola',
                    'cr.endorsement_no',
                    'cr.insured_name',
                    'cr.cover_from',
                    'cr.cover_to',
                    'cg.group_name as cover_type'
                ])
                ->leftJoin('cover_register as cr', function ($join) {
                    $join->on('c.customer_id', '=', 'cr.customer_id')
                        ->where('cr.cancelled', '<>', 'Y')
                        ->whereIn('cr.type_of_bus', ['FPR', 'FNP', 'TNP']);
                })
                ->leftJoin('class_groups as cg', function ($join) {
                    $join->on('cr.class_group_code', '=', 'cg.group_code')
                        ->where('cg.status', '=', 'A');
                })
                ->where('c.status', 'A')
                ->orderBy('c.customer_id')
                ->orderBy('cr.dola', 'desc')
                ->get();

            $groupedCustomers = $customers->groupBy('customer_id')->map(function ($customerGroup) {
                $customer = $customerGroup->first();
                $covers = $customerGroup->filter(function ($item) {
                    return !is_null($item->cover_no);
                });

                return [
                    'customer_id' => $customer->customer_id,
                    'name' => $customer->name,
                    'postal_address' => $customer->postal_address,
                    'postal_town' => $customer->postal_town,
                    'city' => $customer->city,
                    'email' => $customer->email,
                    'telephone' => $customer->telephone,
                    'country_iso' => $customer->country_iso,
                    'customer_type' => $customer->customer_type,
                    'covers' => $covers->map(function ($cover) {
                        return [
                            'cover_no' => $cover->cover_no,
                            'type_of_bus' => $cover->type_of_bus,
                            'dola' => $cover->dola,
                            'endorsement_number' => $cover->endorsement_no,
                            'cover_type' => $cover->cover_type,
                            'insured_name' => $cover->insured_name,
                            'cover_from' => $cover->cover_from,
                            'cover_to' => $cover->cover_to,
                            'endorsements' => $this->getEndorsements($cover->cover_no)
                        ];
                    })->values(),
                    'covers_count' => $covers->count()
                ];
            });

            $customersWithCovers = $groupedCustomers->values();

            return response()->json([
                'success' => true,
                'data' => $customersWithCovers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching customers: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getEndorsements($coverNo)
    {
        if (!$coverNo) return [];

        $lossDate = now()->format('Y-m-d');

        $covers = DB::table('cover_register as cr')
            ->select([
                'cr.cover_no',
                'cr.endorsement_no',
                'cr.cover_from',
                'cr.cover_to',
                'cr.transaction_type',
                'cr.insured_name',
                'cr.dola',
                'cg.group_name as cover_type'
            ])
            ->leftJoin('class_groups as cg', function ($join) {
                $join->on('cr.class_group_code', '=', 'cg.group_code')
                    ->where('cg.status', '=', 'A');
            })
            ->where('cr.cover_no', $coverNo)
            ->where('cr.cover_from', '<=', $lossDate)
            ->where('cr.cover_to', '>=', $lossDate)
            ->where('cr.cancelled', '<>', 'Y')
            ->orderBy('cr.dola', 'desc')
            ->get();

        $results = $covers->map(function ($cover) use ($lossDate) {
            return [
                'cover_no' => $cover->cover_no,
                'endorsement_no' => $cover->endorsement_no,
                'effective_from' => $cover->cover_from,
                'effective_to' => $cover->cover_to,
                'dola' => $cover->dola,
                'is_active' => true,
                'insured_name' => $cover->insured_name,
                'transaction_type' => $cover->transaction_type,
                'description' => $cover->transaction_type,
                'lossDate' => $lossDate
            ];
        })->unique('cover_no')
            ->values()
            ->toArray();

        return $results;
    }

    public function ClaimForm(Request $request)
    {
        $covers = CoverRegister::where('customer_id', $request->customer_id)
            ->where('cancelled', '<>', 'Y')
            ->whereIn('type_of_bus', ['FPR', 'FNP', 'TNP'])
            ->orderBy('dola', 'desc')->get();
        $customer = Customer::where('customer_id', $request->customer_id)->get(['customer_id', 'name', 'postal_address', 'postal_town', 'city', 'email', 'telephone', 'country_iso', 'customer_type'])[0];

        return view('claim.claim_notification_form', [
            'covers' => $covers,
            'customer' => $customer,
        ]);
    }

    public function GetLossEndorsements(Request $request)
    {
        $cover_no = $request->cover_no;
        $loss_date = $request->loss_date;

        $claim_endorsements = CoverRegister::Where([
            ['cover_no', '=', $cover_no],
            ['cover_from', '<=', $loss_date],
            ['cover_to', '>=', $loss_date],
            ['cancelled', '<>', 'Y']
        ])->orderBy('dola', 'desc')
            ->get();

        return response()->json(['endorsements' => $claim_endorsements]);
    }

    public function GetEndorsementInfo(Request $request)
    {
        $endorsement_no = $request->endorsement_no;
        $endorsement_info = DB::select("
                SELECT a.cover_no, a.endorsement_no, a.cover_from, a.cover_to, a.customer_id, a.insured_name,
                    b.name as customer_name, c.bus_type_name as type_of_bus
                FROM cover_register a
                JOIN customers b ON b.customer_id = a.customer_id
                JOIN business_types c ON c.bus_type_id = a.type_of_bus
                WHERE a.endorsement_no = '$endorsement_no'
            ");

        return response()->json(['endorsement_info' => $endorsement_info]);
    }

    public function ClaimRegister(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,customer_id',
                'cover_type' => 'required|string|max:255',
                'endorsement_number' => 'nullable|string|max:255',
                'date_of_loss' => 'required|date|before_or_equal:today',
                'date_reported' => 'required|date|before_or_equal:today|after_or_equal:date_of_loss',
                'date_notified' => 'required|date|before_or_equal:today',
                'cedant_claim_no' => 'nullable|string|max:100',
                'cause_of_loss' => 'required|string|min:10|max:1000',
                'loss_description' => 'required|string|min:10|max:2000',
                'cover_from' => 'nullable|date',
                'cover_to' => 'nullable|date|after_or_equal:cover_from',
                'insured_name' => 'nullable|string|max:255',
            ], [
                'customer_id.required' => 'Please select a customer',
                'customer_id.exists' => 'Selected customer does not exist',
                'cover_type.required' => 'Please select a cover policy',
                'date_of_loss.required' => 'Date of loss is required',
                'date_of_loss.before_or_equal' => 'Date of loss cannot be in the future',
                'date_reported.required' => 'Date reported is required',
                'date_reported.before_or_equal' => 'Date reported cannot be in the future',
                'date_reported.after_or_equal' => 'Date reported cannot be before date of loss',
                'date_notified.before_or_equal' => 'Date notified cannot be in the future',
                'cause_of_loss.required' => 'Cause of loss is required',
                'cause_of_loss.min' => 'Cause of loss must be at least 10 characters',
                'loss_description.required' => 'Loss description is required',
                'loss_description.min' => 'Loss description must be at least 10 characters',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $coverEndorsement = null;
            if ($request->endorsement_number) {
                $coverEndorsement = DB::table('cover_register')
                    ->where('endorsement_no', $request->endorsement_number)
                    ->first();
            }

            $intimationNo = $this->generateIntimationNumber();
            $maxSerialNo = DB::table('claim_ntf_register')->max('serial_no') ?? 0;
            $serial_no = $maxSerialNo + 1;

            $claimData = [
                'intimation_no' => $intimationNo,
                'customer_id' => $request->customer_id,
                'cover_no' => $request->cover_type,
                'endorsement_no' => $request->endorsement_number,
                'date_of_loss' => $request->date_of_loss,
                'date_notified_insurer' => $request->date_reported,
                'date_notified_reinsurer' => $request->date_notified,
                'cedant_claim_no' => $request->cedant_claim_no,
                'cause_of_loss' => trim($request->cause_of_loss),
                'loss_narration' => trim($request->loss_description),
                'cover_from' => $request->cover_from,
                'cover_to' => $request->cover_to,
                'insured_name' => $request->insured_name,
                'status' => 'p',
                'serial_no' => $serial_no,
                'notification_status' => 'PENDING',
                'created_by' => auth()->user()->user_name,
                'updated_by' => auth()->user()->user_name,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            if ($coverEndorsement) {
                $claimData = array_merge($claimData, [
                    'type_of_bus' => $coverEndorsement->type_of_bus ?? null,
                    'branch_code' => $coverEndorsement->branch_code ?? null,
                    'broker_code' => $coverEndorsement->broker_code ?? null,
                    'cover_type' => $coverEndorsement->cover_type ?? null,
                    'class_group_code' => $coverEndorsement->class_group_code ?? null,
                    'class_code' => $coverEndorsement->class_code ?? null,
                    'currency_code' => $coverEndorsement->currency_code ?? null,
                    'currency_rate' => $coverEndorsement->currency_rate ?? null,
                ]);
            }

            $serialNo = DB::table('claim_ntf_register')->insertGetId($claimData, 'serial_no');

            if (!$serialNo) {
                throw new \Exception('Failed to create claim record');
            }

            $statusLogData = [
                'claim_ntf_register_id' => $serialNo,
                'intimation_no' => $intimationNo,
                'status' => 'NOTIFICATION',
                'stage' => 'PENDING',
                'remarks' => 'Initial claim notification created',
                'created_by' => auth()->id(),
                'created_at' => Carbon::now(),
            ];

            DB::table('claim_status_logs')->insert($statusLogData);

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Claim notification submitted successfully!',
                'data' => [
                    'intimation_no' => $intimationNo,
                    'serial_no' => $serialNo,
                    'status' => 'NOTIFICATION'
                ],
                'redirect_url' => redirect()->route('claim.notification.claim_detail', ['intimation_no' => $intimationNo])
            ];

            return response()->json($response, 201);
        } catch (\Exception $e) {
            logger($e);
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.',
                'error_code' => 'CLAIM_CREATION_FAILED'
            ], 500);
        }
    }

    private function generateIntimationNumber()
    {
        $latestClaim = DB::table('claim_ntf_register')
            ->where('intimation_no', 'like', "INT-{$this->_year}-%")
            ->orderBy('intimation_no', 'desc')
            ->first();

        // $intimation_no = 'INT' . $branchcode . $classcode . $serial_no . $currentYear . $currentMonth;

        if ($latestClaim) {
            $parts = explode('-', $latestClaim->intimation_no);
            $lastSequence = (int) end($parts);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        $intimationNo = sprintf('INT-%d-%06d', $this->_year, $newSequence);

        $exists = DB::table('claim_ntf_register')
            ->where('intimation_no', $intimationNo)
            ->exists();

        if ($exists) {
            $newSequence++;
            $intimationNo = sprintf('INT-%d-%06d', $this->_year, $newSequence);
        }

        return $intimationNo;
    }

    public function ClaimDatatable(Request $request)
    {
        $customer_id = $request->get('customer_id');
        $query = ClaimNtfRegister::query()->where('customer_id', $customer_id)->distinct('intimation_no');

        return datatables::of($query)
            ->editColumn('intimation_no', function ($fn) {
                return $fn->intimation_no;
            })
            ->editColumn('cover_no', function ($fn) {
                return $fn->cover_no;
            })
            ->editColumn('endorsement_no', function ($fn) {
                return $fn->endorsement_no;
            })
            ->editColumn('type_of_bus', function ($fn) {
                $t = BusinessType::where('bus_type_id', $fn->type_of_bus)->first();
                return $t->bus_type_name;
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
                } else {
                    $class_desc = ' ';
                }
                return $class_desc;
            })
            ->editColumn('status', function ($fn) {
                $badge = '';
                switch ($fn->status) {
                    case ('P'):
                        $badge = '<span class="badge bg-danger-gradient badge-sm-action"> Pending</span>';
                        break;
                    case ('A'):
                        $badge = '<span class="badge bg-success-gradient badge-sm-action"> Approved</span>';
                        break;
                    case ('R'):
                        $badge = '<span class="badge bg-danger-gradient badge-sm-action"> Rejected</span>';
                        break;
                    default:
                        $badge = '<span class="badge bg-danger-gradient badge-sm-action"> Pending</span>';
                        break;
                }
                return $badge;
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function ClaimDetails(Request $request)
    {
        $intimation_no = $request->intimation_no;

        $claimRegister = ClaimNtfRegister::where('intimation_no', $intimation_no)->first();
        $branch = Branch::where('branch_code', $claimRegister->branch_code)->first();
        $broker = Broker::where('broker_code', $claimRegister->broker_code)->first();
        $class = Classes::where('class_code', $claimRegister->class_code)->first();
        $customer = Customer::where('customer_id', $claimRegister->customer_id)->first();
        $reinsurers = Customer::all();
        $cover = CoverType::where('type_id', $claimRegister->cover_type)->first();
        $coverpart = CoverRipart::where('endorsement_no', $claimRegister->endorsement_no)->get();
        $busType = BusinessType::where('bus_type_id', $claimRegister->type_of_bus)->first();
        $endorse = CoverRegister::query()->where('endorsement_no', $claimRegister->endorsement_no)->with('customer')->first();
        $verifiers = User::permission('app.claims_administration.manage')
            ->where('user_name', '<>', Auth::user()->user_name)
            ->get();

        $process = SystemProcess::where('nice_name', 'cover-registration')->first();

        $verifyprocessAction = SystemProcessAction::where('nice_name', 'verify-claim-notification')->first();
        $other_perils = ClaimNtfPeril::where('intimation_no', $intimation_no)
            ->where('dr_cr_note_no', 0)
            ->where('dr_cr', '!=', 'CR')
            ->sum('basic_amount');
        $salvage = ClaimNtfPeril::where('intimation_no', $intimation_no)
            ->where('dr_cr_note_no', 0)
            ->where('dr_cr', 'CR')
            ->sum('basic_amount');
        $total_claim =  $salvage ? (float) $other_perils - (float) $salvage : (float) $other_perils;
        $nextDebitAmount = $total_claim;

        $ClaimPerilsCount = ClaimNtfPeril::where('intimation_no', $intimation_no)->count();
        $all_docs_param = ClaimAckParams::where('class_group', $endorse->class_group_code)->get();
        $uploadedDocs = ClaimNtfDocs::where('intimation_no', $intimation_no)->pluck('doc_id')->toArray();
        $ClaimNtfAckDocs = ClaimNtfAckDocs::where('intimation_no', $intimation_no)->get();
        $clmStatuses = ClaimStatusParam::all();
        $perilTypes = Peril::where('status', 'A')->orderBy('id', 'asc')->get();

        //
        $attachedFiles = collect([
            (object) [
                'id' => 1,
                'original_name' => 'acknowledgement_letter.pdf',
                'extension' => 'pdf',
                'size' => 2048576,
                'mime_type' => 'application/pdf',
                'created_at' => Carbon::now()->subDays(2),
                'file_path' => 'claims/documents/claim_form_2024.pdf',
                'uploaded_by' => 'John Doe'
            ]
        ]);

        $emailFrom = Company::where('company_id', 1)->first()->email ?? '';
        $cedant = Customer::where('customer_id', $claimRegister->customer_id)->first();
        $claimSubject = collect([
            'Claim Notification',
            $claimRegister->insured_name ?? '',
            $claimRegister->cover_from ?? '',
            $claimRegister->class_code ?? '',
            $claimRegister->intimation_no ?? '',
            $customer->name ?? ''
        ])->filter()->implode(' - ');

        return view('claim.claim_notification_home', [
            'ClaimRegister' => $claimRegister,
            'branch' => $branch,
            'broker' => $broker,
            'class' => $class,
            'customer' => $customer,
            'covertype' => $cover,
            'reinsurers' => $reinsurers,
            'coverpart' => $coverpart,
            'type_of_bus' => $busType,
            'cover' => $endorse,
            'verifiers' => $verifiers,
            'process' => $process,
            'verifyprocessAction' => $verifyprocessAction,
            'nextDebitAmount' => $nextDebitAmount,
            'all_docs_param' => $all_docs_param,
            'clmStatuses' => $clmStatuses,
            'uploadedDocs' => $uploadedDocs,
            'perilTypes' => $perilTypes,
            'ClaimPerilsCount' => $ClaimPerilsCount,
            'ClaimNtfAckDocs' => $ClaimNtfAckDocs,
            'emailFrom' => $emailFrom,
            'attachedFiles' => $attachedFiles,
            'cedant' => $cedant?->name,
            'recipients' => $cedant?->contacts,
            'claimSubject' => $claimSubject,
            'defaultMessage' => $this->getDefaultMessage($claimRegister, $customer)
        ]);
    }

    public function ClaimsEnquiryDatatable(Request $request)
    {
        $query = ClaimNtfRegister::query()->orderBy('created_at', 'desc');

        if ($request->has('type')) {
            switch ($request->get('type')) {
                case 'reserved':
                    $query->where('status', 'R');
                    break;

                case 'claims':
                    $query->whereIn('status', ['P', 'A']);
                    break;

                default:
                    break;
            }
        }

        return datatables::of($query)
            ->editColumn('intimation_no', function ($fn) {
                return '<input type="checkbox" class="row-checkbox me-2" value="' . e($fn->intimation_no) . '"> ' . $fn->intimation_no;
            })
            ->editColumn('intimation_no', function ($fn) {
                return $fn->intimation_no;
            })
            ->editColumn('cover_no', function ($fn) {
                return $fn->cover_no;
            })
            ->editColumn('endorsement_no', function ($fn) {
                return $fn->endorsement_no;
            })
            ->editColumn('type_of_bus', function ($fn) {
                $t = DB::table('business_types')->where('bus_type_id', $fn->type_of_bus)->first();
                return $t ? $t->bus_type_name : 'Unknown';
            })
            ->editColumn('class_desc', function ($fn) {
                if ($fn->type_of_bus == 'FPR' || $fn->type_of_bus == 'FNP') {
                    $class_desc = Classes::where('class_code', $fn->class_code)->first();
                    if ($class_desc) {
                        $class_desc = 'Facultative - ' . $class_desc->class_name;
                    } else {
                        $class_desc = 'Unknown Class';
                    }
                } elseif ($fn->type_of_bus == 'TPR') {
                    $class_desc = 'Treaty - Proportional';
                } elseif ($fn->type_of_bus == 'TNP') {
                    $class_desc = 'Treaty - Non Proportional';
                } else {
                    $class_desc = 'Unknown';
                }
                return $class_desc;
            })
            ->editColumn('status', function ($fn) {
                $badges = [
                    'P' => '<span class="badge bg-warning-gradient badge-sm-action">Pending</span>',
                    'A' => '<span class="badge bg-success-gradient badge-sm-action">Approved</span>',
                    'R' => '<span class="badge bg-info-gradient badge-sm-action">Reserved</span>',
                    'C' => '<span class="badge bg-secondary-gradient badge-sm-action">Closed</span>',
                    'X' => '<span class="badge bg-danger-gradient badge-sm-action">Cancelled</span>',
                ];
                return $badges[$fn->status] ?? '<span class="badge bg-dark-gradient badge-sm-action">Unknown</span>';
            })
            ->editColumn('created_at', function ($fn) {
                return formatDate($fn->created_at);
            })
            ->addColumn('action', function ($fn) use ($request) {
                $actions = '<div class="btn-group" role="group">';
                $tableType = $request->get('type', 'claims');

                $actions .= '<a href="#" class="btn btn-sm btn-primary" id="view-notf-claimstatus"
                           data-intimation_no="' . e($fn->intimation_no) . '"
                           data-process_type="' . e($fn->process_type ?? '') . '"
                           title="View Details"><span class="pr-2">View</span>
                           <i class="bx bx-send"></i>
                        </a>';

                if ($fn->status == 'P') {
                    $actions .= '<a href="#" class="btn btn-sm btn-info ms-1" id="edit-claim"
                               data-intimation_no="' . e($fn->intimation_no) . '"
                               title="Edit Claim">
                               <i class="bx bx-edit"></i>
                            </a>';
                }

                switch ($tableType) {
                    case 'reserved':
                        if ($fn->status == 'R') {
                            $actions .= '<a href="#" class="btn btn-sm btn-success ms-1" id="activate-claim"
                                       data-intimation_no="' . e($fn->intimation_no) . '"
                                       title="Activate Claim">
                                       <i class="bx bx-play"></i>
                                    </a>';
                        }
                        break;

                    case 'claims':
                    default:
                        if ($fn->status == 'A' && empty($fn->converted_claim_no)) {
                            $actions .= '<a href="#" class="btn btn-sm btn-info ms-1" id="convert-to-claim"
                                       data-intimation_no="' . e($fn->intimation_no) . '"
                                       title="Convert to Claim">
                                       <i class="bx bx-transfer"></i>
                                    </a>';
                        }
                        break;
                }

                if (in_array($fn->status, ['P']) && empty($fn->converted_claim_no)) {
                    $actions .= '<a href="#" class="btn btn-sm btn-danger ms-1" id="delete-claim"
                               data-intimation_no="' . e($fn->intimation_no) . '"
                               data-status="' . e($fn->status) . '"
                               title="Delete Claim">
                               <i class="bx bx-trash"></i>
                            </a>';
                }

                if (in_array($fn->status, ['A']) && empty($fn->converted_claim_no)) {
                    $actions .= '<a href="#" class="btn btn-sm btn-outline-danger ms-1" id="cancel-claim"
                               data-intimation_no="' . e($fn->intimation_no) . '"
                               title="Cancel Claim">
                               <i class="bx bx-x"></i>
                            </a>';
                }

                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function savePeril(Request $request)
    {
        $currentYear = str_pad($this->_year, 4, '0', STR_PAD_LEFT);
        $currentMonth = str_pad($this->_month, 2, '0', STR_PAD_LEFT);
        $intimation_no = $request->intimation_no;

        DB::beginTransaction();
        try {
            $peril_ids = $request->peril_name;
            $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();
            foreach ($peril_ids as $index => $peril_id) {
                $tran_no = ClaimNtfPeril::count('tran_no') + 1;
                $peril_amount = (float)str_replace(',', '', $request->peril_amount[$index]);
                $perilDtl = Peril::where('id', $peril_id)->first();

                $rate = $cover->share_offered;
                $cedant_premium = $cover->cedant_premium;
                $gross_premium = ($cedant_premium * ($rate / 100));
                $final_amount = ($peril_amount * ($rate / 100));;

                $username = Auth::user()->user_name;
                $newclaimperil = new ClaimNtfPeril();
                $newclaimperil->intimation_no = $intimation_no;
                $newclaimperil->tran_no = $tran_no;
                $newclaimperil->peril_id = $peril_id;
                $newclaimperil->peril_name = $perilDtl->description;
                $newclaimperil->dr_cr_note_no = '0';
                $newclaimperil->dr_cr = $perilDtl->dr_cr;
                $newclaimperil->entry_type_descr = 'NET';
                $newclaimperil->basic_amount = $peril_amount;
                $newclaimperil->rate = $rate;
                $newclaimperil->final_amount = $final_amount;
                $newclaimperil->gross_premium = $gross_premium;
                $newclaimperil->status = 'A';
                $newclaimperil->account_year = $currentYear;
                $newclaimperil->account_month = $currentMonth;
                $newclaimperil->created_by =  $username;
                $newclaimperil->updated_by = $username;
                $newclaimperil->save();
            }

            DB::commit();
            Session::Flash('success', 'Perils has been saved successfully');
            return redirect()->route('claim.notification.claim_detail', [
                'intimation_no' => $intimation_no
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Session::Flash('success', 'Perils has Failed');
            return redirect()->route('claim.notification.claim_detail', [
                'intimation_no' => $intimation_no
            ]);
        }
    }

    private static function coverDebitedCommited($endorsement): bool
    {
        $cover = CoverRegister::select('endorsement_no', 'cover_no', 'commited', 'verified')
            ->where('endorsement_no', $endorsement)
            ->first();

        $debitted = CoverDebit::where('endorsement_no', $endorsement)
            ->count();

        $actionable = true;
        // if TNP| TPR inital cover commited
        if ($cover->commited == 'Y' || $debitted > 0) {
            $actionable = false;
        }

        return $actionable;
    }

    public function saveReserve(Request $request)
    {
        $intimation_no = $request->intimation_no;
        $NotificationCount = ClaimNtfRegister::where('intimation_no', $intimation_no)->count();

        DB::beginTransaction();
        try {
            if ($NotificationCount > 0) {
                $NotificationStatus = ClaimNtfRegister::where('intimation_no', $intimation_no)->first()->approval_status;
                if ($NotificationStatus != 'A' || $NotificationStatus != 'P') {
                    ClaimNtfRegister::where('intimation_no', $intimation_no)->update([
                        'reserve_amount' => str_replace(",", "", $request->reserve_amount)
                    ]);
                }
            }

            DB::commit();

            Session::Flash('success', 'Reserve has been updated successfully');

            return redirect()->route('claim.notification.claim_detail', [
                'intimation_no' => $intimation_no
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
            Session::Flash('success', 'Perils has Failed');

            return redirect()->route('claim.notification.claim_detail', [
                'intimation_no' => $intimation_no
            ]);
        }
    }

    public function ClaimPerilDatatable(Request $request)
    {
        $intimation_no = $request->get('intimation_no');
        $query = ClaimNtfPeril::query()->where('intimation_no', $intimation_no);

        return datatables::of($query)

            ->editColumn('tran_no', function ($fn) {
                return $fn->tran_no;
            })

            ->editColumn('peril_name', function ($fn) {
                return $fn->peril_name;
            })

            ->editColumn('basic_amount', function ($fn) {
                return $fn->basic_amount;
            })

            ->editColumn('rate', function ($fn) {
                return $fn->rate;
            })

            ->editColumn('final_amount', function ($fn) {
                return $fn->final_amount;
            })
            ->make(true);
    }

    public function ClaimReinsurerDatatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');
        $query = CoverRipart::query()->where('endorsement_no', $endorsement_no);
        $cover = CoverRegister::where('endorsement_no', $endorsement_no)->first();
        $actionable = static::coverDebitedCommited($endorsement_no);

        return datatables::of($query)
            ->addColumn('partner_name', function ($data) {
                $part = Customer::where('customer_id', $data->partner_no)->first();
                return $part->name;
            })
            ->addColumn(
                'credit_no',
                function ($data) {
                    return 'CRN/' . $data->tran_no . '/' . $data->period_year ?? '';
                }
            )
            ->addColumn('action', function ($data) use ($actionable, $endorsement_no, $cover) {
                $btn = "";
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
                        $btn .= "";
                    }
                } else {
                    $creditNoteUrl = route('docs.reincreditnotes', ['endorsement_no' => $endorsement_no, 'partner_no' => $data->partner_no]);
                    // $coverSlipUrl = route('docs.coverslip', ['endorsement_no' => $endorsement_no, 'partner_no' => $data->partner_no]);
                    // $client_emails = json_encode($partner_emails);
                    // $client_name = $data?->partner?->name;

                    // $endorsementNo = $endorsement_no;
                    // $coverNo = $cover?->cover_no;
                    // $tmp_attachments = json_encode(['attachments' => []]);

                    if (($cover->type_of_bus == 'TPR' || $cover->type_of_bus == 'TNP') && ($cover->transaction_type == 'NEW' || $cover->transaction_type == 'REN')) {
                        $btn .= "";
                    } else {
                        $btn .= "<a href='{$creditNoteUrl}' target='_blank' rel='noopener noreferrer' class='print-out-link pr-3'><i class='bx bx-file me-1 align-middle'></i>Credit Note</a>";
                    }
                }
                return $btn;
            })
            ->rawColumns(['action', 'credit_no'])
            ->make(true);
    }

    public function ClaimCedantDatatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');
        $query = CoverDebit::query()->where('endorsement_no', $endorsement_no);
        $cover = CoverRegister::query()->where('endorsement_no', $endorsement_no)->with('customer')->first();
        $actionable = static::coverDebitedCommited($endorsement_no);
        // $claim = ClaimDebit::where($endorsement_no);
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
                $btn = "";
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
                        $btn .= "";
                    }
                } else {
                    $dbtNoteUrl = route('docs.coverdebitnote', ['endorsement_no' => $endorsement_no]);
                    $coverNoteUrl = route('docs.claimntf-docs-ack-letter', ['intimation_no' => $data->intimation_no]);
                    $client_emails = json_encode($partner_emails);
                    $client_name = $data?->partner?->name;

                    $endorsementNo = $endorsement_no;
                    $coverNo = $cover?->cover_no;
                    $tmp_attachments = json_encode(['attachments' => []]);

                    if (($cover->type_of_bus == 'TPR' || $cover->type_of_bus == 'TNP') && ($cover->transaction_type == 'NEW' || $cover->transaction_type == 'REN')) {
                        $btn .= "";
                    } else {
                        $btn .= "<a href='{$dbtNoteUrl}' target='_blank' rel='noopener noreferrer' class='print-out-link'><i class='bx bx-file me-1 align-middle'></i>Debit Note</a>";
                        // $btn .= "<a href='{$coverNoteUrl}' target='_blank' rel='noopener noreferrer' class='print-out-link pr-3'>
                        //             <i class='bx bx-file'></i> Claim Acknowledgment</a>";
                        // $btn .= "<a href='#' target='_blank' class='print-out-link send-reinsurer-email' data-client_emails='{$client_emails}' data-cover_no='{$coverNo}' data-endorsement_no='{$endorsementNo}' data-client_name='{$client_name}' data-client_docs='{$tmp_attachments}'>
                        //             <i class='bx bx-mail-send' style='font-size: 15px; vertical-align: -2px;'></i> Send E-Mail</a>";
                    }
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function generateAcknowledgement(Request $request)
    {
        try {
        } catch (\Throwable $th) {
        }
    }

    public function attachments_datatable(Request $request)
    {
        $intimation_no = $request->intimation_no;
        $query = ClaimNtfDocs::query()->where('intimation_no', $intimation_no);
        $actionable = static::claimClosed($intimation_no);

        return datatables::of($query)
            ->addColumn(
                'type',
                function ($data) {
                    switch ($data->document_type) {
                        case 'received_doc':
                            return 'Submitted Document';
                        case 'missing_doc':
                            return 'Missing Document';
                        case 'new_doc':
                            return 'New Document';
                        default:
                            return '';
                    }
                }
            )
            ->addColumn(
                'filename',
                function ($data) {
                    return '<span class="text-primary">' . $data->file . '</span>';
                }
            )

            ->addColumn(
                'recieved_date',
                function ($data) {
                    $clm = ClaimNtfAckDocs::where(['doc_id' => $data?->doc_id])->first();
                    // logger($data);
                    return '';
                }
            )
            ->addColumn('action', function ($data) use ($actionable) {
                $btn = "";
                $fileUrl = $data->file ? asset('uploads/claim_ntf_attachments/' . $data->file) : '';
                // logger($fileUrl);
                if ($actionable) {
                    $btn .= " <button class='btn btn-primary btn-sm view-document p-0 m-0 px-3' data-document-id='{$data->id}' data-filename='{$data->file}' data-url='{$fileUrl}'><i class='bi bi-send'></i> View</button>";
                    // $btn .= " <button class='btn btn-outline-primary btn-sm edit-attachment' data-data='{$data}' data-id='{$data->id}'
                    //     data-bs-toggle='modal' data-bs-target='#attachments-modal'>Edit</button>";
                    $btn .= " <button class='btn btn-danger btn-sm p-0 m-0 px-3 remove-attachment' data-title='{$data->title}' data-id='{$data->id}'><i class='bi bi-trash'></i> Remove</button></button>";
                }
                return $btn;
            })
            ->rawColumns(['recieved_date', 'filename', 'action'])
            ->make(true);
    }

    public function saveAttachment(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'intimation_no' => 'required',
                'endorsement_no' => 'required',
                'title' => 'required',
                'description' => 'required',
                'file' => 'required|mimes:pdf,doc,docx,jpeg,png',
            ]);

            $file = $request->file('file');
            $fileName = date('dmYhis') . '_' . $file->getClientOriginalName();
            $file->storeAs('claim_ntf_attachments', $fileName, 'public');
            $mimeType = $file->getClientMimeType();
            // Read the file contents and encode it to base64
            $base64Encoded = base64_encode(File::get($file->path()));

            $id = (int)ClaimNtfDocs::max('id') + 1;
            $CoverRegister = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();
            $doc = ClaimAckParams::where('id', $request->title)->first();

            ClaimNtfDocs::create([
                'id' => $id,
                'intimation_no' => $request->intimation_no,
                'cover_no' => $CoverRegister->cover_no,
                'endorsement_no' => $CoverRegister->endorsement_no,
                'doc_id' => $doc->id,
                'title' => $doc->doc_name,
                'description' => $request->description,
                'file' => $fileName,
                'file_base64' => $base64Encoded,
                'mime_type' => $mimeType,
                'created_by' => Auth::user()->user_name,
                'updated_by' => Auth::user()->user_name,
            ]);

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            dd($e->getMessage());
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function amendAttachment(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'intimation_no' => 'required',
                'endorsement_no' => 'required',
                'id' => 'required',
                'title' => 'required',
                'description' => 'required',
                'file' => 'required|mimes:pdf,doc,docx,jpeg,png',
            ]);

            $file = $request->file('file');
            $fileName = date('dmYhis') . '_' . $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $file->storeAs('claim_attachments', $fileName, 'public');

            // Read the file contents and encode it to base64
            $base64Encoded = base64_encode(File::get($file->path()));

            $attachment = ClaimNtfDocs::where('id', $request->id)->first();

            $attachment->title = $request->title;
            $attachment->description = $request->description;
            $attachment->file = $fileName;
            $attachment->file_base64 = $base64Encoded;
            $attachment->mime_type = $mimeType;
            $attachment->updated_by = Auth::user()->user_name;
            $attachment->save();

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteAttachment(Request $request)
    {
        DB::beginTransaction();
        try {

            $request->validate([
                'intimation_no' => 'required',
                'endorsement_no' => 'required',
                'id' => 'required',
            ]);

            $attachment = ClaimNtfDocs::where('id', $request->id)->first();
            $attachment->delete();

            DB::commit();
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Item deleted successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            // dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function ack_docs_datatable(Request $request)
    {
        $intimation_no = $request->intimation_no;
        $query = ClaimAckDocs::query()->where('intimation_no', $intimation_no);
        $actionable = static::claimClosed($intimation_no);

        return datatables::of($query)
            ->addColumn('action', function ($data) use ($actionable) {
                $btn = "";
                if ($actionable) {
                    $btn .= " <button class='btn btn-outline-danger btn-sm remove-acknowledgements' data-title='{$data->ack_params->doc_name}' data-id='{$data->id}'>Remove</button>";
                }
                return $btn;
            })
            ->make(true);
    }

    private function generateFileMetadata($file, $docId): array
    {
        if (!$file || !$file->isValid()) {
            return [null, null, null];
        }

        $this->deletePreviousFiles($docId);

        $filename = time() . '_' . $docId . '.' . $file->getClientOriginalExtension();

        try {
            $file->storeAs('claim_ntf_attachments', $filename, 'public');

            return [
                $filename,
                $file->getClientMimeType(),
                base64_encode(File::get($file->path()))
            ];
        } catch (Exception $e) {
            throw new RuntimeException('File processing failed');
        }
    }

    private function deletePreviousFiles($docId): void
    {
        $directory = 'claim_ntf_attachments';
        $allFiles = Storage::disk('public')->files($directory);

        // Filter files that match the pattern *_docId.*
        $filesToDelete = array_filter($allFiles, function ($file) use ($docId) {
            $filename = basename($file);
            return preg_match('/.*_' . preg_quote($docId, '/') . '\..*/', $filename);
        });

        // Delete matching files
        foreach ($filesToDelete as $file) {
            Storage::disk('public')->delete($file);
        }
    }

    private function saveDocumentRecords($params)
    {
        $doc = ClaimAckParams::where('id', $params['docId'])->first();
        if ($params['documentType'] == 'received_doc') {
            $existingDocs = [
                'ack' => ClaimNtfAckDocs::where([
                    'intimation_no' => $params['intimationNo'],
                    'endorsement_no' => $params['endorsementNo'],
                    'doc_id' => $params['docId']
                ])->exists(),
                'ntf' => ClaimNtfDocs::where([
                    'intimation_no' => $params['intimationNo'],
                    'endorsement_no' => $params['endorsementNo'],
                    'doc_id' => $params['docId']
                ])->exists()
            ];

            if (!$existingDocs['ack'] && !$existingDocs['ntf']) {
                $commonData = [
                    'cover_no' => $params['coverNo'],
                    'file' => isset($params['filename']) ? $params['filename'] : null,
                    'mime_type' => isset($params['mimeType']) ? $params['mimeType'] : null,
                    'document_type' => isset($params['documentType']) ? $params['documentType'] : null,
                    'created_by' => auth()->user()->user_name,
                    'updated_by' => auth()->user()->user_name,
                ];
                $ackData = array_merge([
                    'id' => $params['ackDocsId'],
                    'intimation_no' => $params['intimationNo'],
                    'endorsement_no' => $params['endorsementNo'],
                    'doc_id' => $doc->id,
                    'date_received' => $params['dateReceived'],
                    'doc_name' => $doc->doc_name,
                ], $commonData);

                $ntfData = array_merge([
                    'id' => $params['ntfDocsId'],
                    'intimation_no' => $params['intimationNo'],
                    'endorsement_no' => $params['endorsementNo'],
                    'doc_id' => $doc->id,
                    'title' => $doc->doc_name,
                ], $commonData);

                ClaimNtfAckDocs::create($ackData);
                ClaimNtfDocs::create($ntfData);
            }
        } else {
            $commonData = [
                'cover_no' => $params['coverNo'],
                'doc_id' => $doc->id,
                'document_type' => $params['documentType'],
                'created_by' => auth()->user()->user_name,
                'updated_by' => auth()->user()->user_name,
            ];

            ClaimNtfAckDocs::updateOrCreate([
                'intimation_no' => $params['intimationNo'],
                'endorsement_no' => $params['endorsementNo'],
                'doc_id' => $doc->id
            ], array_merge([
                'id' => $params['ackDocsId'],
                'date_received' => $params['dateReceived'],
                'doc_name' => $doc->doc_name,
            ], $commonData));

            ClaimNtfDocs::updateOrCreate([
                'intimation_no' => $params['intimationNo'],
                'endorsement_no' => $params['endorsementNo'],
                'doc_id' => $doc->id
            ], array_merge([
                'id' => $params['ntfDocsId'],
                'title' => $doc->doc_name,
            ], $commonData));
        }
    }

    public function saveDocAcknowledgement(Request $request)
    {

        try {
            DB::beginTransaction();
            $request->validate([
                'intimation_no' => 'required',
                'endorsement_no' => 'required',
            ]);

            $coverRegister = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();

            if ($request->has('received_document')) {
                foreach ($request->received_document as $key => $docId) {
                    if (!$docId) continue;

                    [$filename, $mimeType, $base64Encoded] = $this->generateFileMetadata(
                        $request->file('received_file')[$key] ?? null,
                        $docId
                    );

                    $claimNtfAckDocsId = (int) ClaimNtfAckDocs::max('id') + 1;
                    $claimNtfDocsId = (int) ClaimNtfDocs::max('id') + 1;

                    $this->saveDocumentRecords([
                        'docId' => $docId,
                        'intimationNo' => $request->intimation_no,
                        'endorsementNo' => $request->endorsement_no,
                        'ackDocsId' => $claimNtfAckDocsId,
                        'ntfDocsId' => $claimNtfDocsId,
                        'dateReceived' => $request->date_received[$key] ?? null,
                        'coverNo' => $coverRegister->cover_no,
                        'filename' => $filename,
                        'mimeType' => $mimeType,
                        'documentType' => 'received_doc'
                    ]);
                }
            }

            if ($request->has('missing_document_ids')) {
                foreach ($request->missing_document_ids as $key => $docId) {
                    if (!$docId) continue;

                    $claimNtfAckDocsId = (int) ClaimNtfAckDocs::max('id') + 1;
                    $claimNtfDocsId = (int) ClaimNtfDocs::max('id') + 1;

                    $this->saveDocumentRecords([
                        'docId' => $docId,
                        'intimationNo' => $request->intimation_no,
                        'endorsementNo' => $request->endorsement_no,
                        'ackDocsId' => $claimNtfAckDocsId,
                        'ntfDocsId' => $claimNtfDocsId,
                        'dateReceived' => $request->date_received[$key] ?? null,
                        'coverNo' => $coverRegister->cover_no,
                        'documentType' => 'missing_doc'
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Documents uploaded successfully',
            ]);
        } catch (ValidationException $e) {
            logger($e);

            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            logger($e);

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong while uploading documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteDocAcknowledgement(Request $request)
    {
        DB::beginTransaction();
        try {

            $request->validate([
                'intimation_no' => 'required',
                'endorsement_no' => 'required',
                'id' => 'required',
            ]);

            $ackDoc = ClaimNtfAckDocs::where('id', $request->id)->first();
            $ackDoc->delete();

            DB::commit();
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Item deleted successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function claimClosed($intimation_no): bool
    {
        $claim = ClaimNtfRegister::where('intimation_no', $intimation_no)->first();

        $actionable = true;
        if ($claim->status == 'C') {
            $actionable = false;
        }
        return $actionable;
    }

    public function claimStatusDatatable(Request $request)
    {
        $intimation_no = $request->intimation_no;
        $query = ClaimNtfStatus::query()->with('statusReason')->where('intimation_no', $intimation_no);
        // $actionable = static::claimClosed($intimation_no);

        return datatables::of($query)
            ->editColumn('status', function ($data) {
                return $data->status == 'O' ? 'OPEN' : 'CLOSED';
            })
            ->editColumn('created_at', function ($data) {
                return formatDate($data->created_at);
            })
            ->addColumn('action', function ($data) {
                return "<button class='btn btn-outline-danger btn-sm remove-claimstatus p-0 m-0 px-3'><i class='bx bx-trash'></i> Remove</button>";
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function saveClaimStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'intimation_no' => 'required',
                'endorsement_no' => 'required',
                'status' => 'required',
                'status_reason' => 'required',
            ]);

            $id = (float)ClaimNtfStatus::max('id') + 1;
            ClaimNtfStatus::create([
                'id' => $id,
                'status_id' => $request->status_reason,
                'intimation_no' => $request->intimation_no,
                'description' => $request->description,
                'created_by' => Auth::user()->user_name,
                'updated_by' => Auth::user()->user_name,
            ]);

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Data saved successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function convertNotificationToClaim(Request $request)
    {
        $request->validate([
            'intimation_no' => 'required',
            'cover_no' => 'required',
            'date_of_loss' => 'required',
            'total_claim_amnt' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $intimation = ClaimNtfRegister::where('intimation_no', $request->intimation_no);
            if (!$intimation) {
                throw new Exception('Claim notification not found');
            }
            $intimation->update(['status' => 'A']);
            $intimation =  $intimation->first();
            $CoverRegister = CoverRegister::where('endorsement_no', $intimation->endorsement_no)->first();
            if (!$CoverRegister) {
                throw new \Exception('Cover register not found');
            }

            $claimData = (object) $intimation->getAttributes();
            $claim_serial_no = ClaimRegister::max('claim_serial_no') + 1;
            $currentYear = Carbon::now()->year;
            $currentMonth = str_pad($this->_month, 2, '0', STR_PAD_LEFT);
            $claim_no = 'CLM' . $claimData->branch_code . $claimData->class_code . $claim_serial_no . $currentYear . $currentMonth;

            ClaimRegister::create([
                'claim_serial_no' => $claim_serial_no,
                'cover_no' => $claimData->cover_no,
                'customer_id' => $claimData->customer_id,
                'date_of_loss' => $claimData->date_of_loss,
                'type_of_bus' => $claimData->type_of_bus,
                'branch_code' => $claimData->branch_code,
                'broker_code' => $claimData->broker_code,
                'cover_type' => $claimData->cover_type,
                'class_group_code' => $claimData->class_group_code,
                'class_code' => $claimData->class_code,
                'claim_no' => $claim_no,
                'endorsement_no' => $claimData->endorsement_no,
                'insured_name' => $claimData->insured_name,
                'cover_from' => $claimData->cover_from,
                'cover_to' => $claimData->cover_to,
                'created_by' => Auth::user()->user_name,
                'created_date' => Carbon::now(),
                'currency_code' => $claimData->currency_code,
                'currency_rate' => $claimData->currency_rate,
                'date_notified_insurer' => $claimData->date_notified_insurer,
                'date_notified_reinsurer' => $claimData->date_notified_reinsurer,
                'cause_of_loss' => $claimData->cause_of_loss,
                'loss_narration' => $claimData->loss_narration,
                'intimation_no' => $request->intimation_no,
                'status' => 'A',
                'verified' => 'P',
            ]);

            $perils = ClaimNtfPeril::where('intimation_no', $request->intimation_no)->get();
            foreach ($perils as $peril) {
                $tran_no = ClaimPeril::count('tran_no') + 1;
                $peril_amount = $peril->basic_amount;

                ClaimPeril::create([
                    'claim_no' => $claim_no,
                    'tran_no' => $tran_no,
                    'peril_id' => $peril->peril_id,
                    'peril_name' => $peril->peril_name,
                    'dr_cr_note_no' => '0',
                    'dr_cr' => $peril->dr_cr,
                    'entry_type_descr' => 'NET',
                    'basic_amount' => $peril_amount,
                    'rate' => $CoverRegister->share_offered,
                    'final_amount' => ($CoverRegister->share_offered / 100) * $peril_amount,
                    'status' => 'A',
                    'account_year' => $currentYear,
                    'account_month' => $currentMonth,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name
                ]);
            }

            $docs = ClaimNtfDocs::where('intimation_no', $request->intimation_no)->get();
            foreach ($docs as $doc) {
                if ($doc->document_type == 'received_doc' && $doc->file != null) {
                    ClaimDocs::create([
                        'id' => (int)ClaimDocs::max('id') + 1,
                        'claim_no' => $claim_no,
                        'cover_no' => $CoverRegister->cover_no,
                        'endorsement_no' => $CoverRegister->endorsement_no,
                        'title' => $doc->title,
                        'description' => $doc->description,
                        'file' => $doc->file,
                        'mime_type' => $doc->mime_type,
                        'created_by' => Auth::user()->user_name,
                        'updated_by' => Auth::user()->user_name
                    ]);
                }
            }

            $ackDocs = ClaimNtfAckDocs::where('intimation_no', $request->intimation_no)->get();
            foreach ($ackDocs as $ackDoc) {
                if ($doc->document_type == 'received_doc' && $doc->file != null) {
                    ClaimAckDocs::create([
                        'id' => (int) ClaimAckDocs::max('id') + 1,
                        'claim_no' => $claim_no,
                        'cover_no' => $CoverRegister->cover_no,
                        'endorsement_no' => $CoverRegister->endorsement_no,
                        'doc_id' => $ackDoc->doc_id,
                        'date_received' => $ackDoc->date_received,
                        'created_by' => Auth::user()->user_name,
                        'updated_by' => Auth::user()->user_name
                    ]);
                }
            }

            ClaimNtfRegister::where('intimation_no', $request->intimation_no)->update([
                'converted_claim_no' => $claim_no
            ]);


            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Successfully converted Notification to Claim',
                'claim_no' => $claim_no
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Failed to convert Notification to Claim: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function preNotificationVerification(Request $request)
    {
        try {
            $pending = [];
            $verifiers = collect();
            if (Permission::where('name', 'claims.notification.verify')->exists()) {
                $verifiers = User::permission('claims.notification.verify')
                    ->where('user_name', '<>', Auth::user()->user_name)
                    ->get();
            }

            $processSlug = SystemActionEnums::CLAIM_INTIMATION_PROCESS;
            $processActionSlug = SystemActionEnums::VERIFY_CLAIM_INTIMATION_PROCESS;

            $permissionSlug = 'claims.notification.verify';


            $permission = Permission::where('name', $permissionSlug)->first();
            $process = SystemProcess::where('nice_name', $processSlug)->first();
            $verifyprocessAction = SystemProcessAction::where('nice_name', $processActionSlug)->first();
            $claim = ClaimNtfRegister::where('intimation_no', $request->intimation_no)->first();

            if (empty($process)) {
                array_push($pending, 'Process "' . $processSlug . '" is missing. Contact system administrator.');
            }

            if (empty($verifyprocessAction)) {
                array_push($pending, 'Process action "' . $processActionSlug . '" is missing. Contact system administrator.');
            }

            if (empty($permission)) {
                array_push($pending, 'Permission "' . $permissionSlug . '" is missing. Contact system administrator.');
            }

            if ($verifiers->isEmpty()) {
                array_push($pending, 'Approvers missing for "' . $permissionSlug . '". Contact system administrator.');
            }

            if (is_null($claim)) {
                array_push($pending, 'Claim Intimation is missing from the system');
            } elseif ($claim->approval_status == 'P') {
                array_push($pending, 'Claim Intimation conversion to claim request has already been sent for approval');
            } elseif ($claim->approval_status == 'A') {
                array_push($pending, 'Claim Intimation conversion to claim request has already been approved');
            }

            if ($request->has('process_type') && $request->process_type === 'reserve') {
                if (empty($claim->reserve_amount) || (float) $claim->reserve_amount <= 0) {
                    $pending[] = 'Please specify a valid reserve amount greater than zero';
                }
            }

            if ($request->has('process_type') && $request->process_type === 'claim') {
                $perils = ClaimNtfPeril::where('intimation_no', $request->intimation_no)->get();
                if ($perils->isEmpty()) {
                    $pending[] = 'Please capture claim particulars first';
                }
            }

            return response()->json([
                'pending' => $pending,
                'verifiers' => $verifiers,
                'process' => $process,
                'verifyprocessAction' => $verifyprocessAction,
                'status' => $claim->status
            ]);
        } catch (Throwable $e) {
            return response()->json(['An internal error occured'], 500);
        }
    }

    public function sendDocumentEmail(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $claim = ClaimNtfRegister::where('intimation_no', $request->intimation_no)->first();
            $claim->update([
                'notificaction_status' => 'notification_sent',
                'notification_sent_at' => now(),
                'notification_sent_by' => auth()->id()
            ]);

            $message = $this->formatMessageForHtml($request->message);
            $request->merge(['message' => $message]);

            SendClaimNotificationJob::dispatch(
                $claim,
                $request->all()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Claim notification has been queued for sending',
            ]);
        } catch (\Exception $e) {
            logger($e);
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to send claim notification. Please try again.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getDefaultMessage($claim, $customer): string
    {
        return "Dear {recipient_name},\n\n" .
            "Greetings,\n\n" .
            "We confirm receipt of the subject claim notification and have proceeded to notify the securities for their review and settlement.\n\n" .
            "Best regards,\n" .
            config('app.name');
    }

    private function formatMessageForHtml($message)
    {
        // Convert line breaks to HTML
        $html = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

        // Add proper paragraph spacing
        $html = str_replace("\n\n", "</p><p>", $html);
        $html = "<p>" . $html . "</p>";

        // Clean up empty paragraphs
        $html = preg_replace('/<p>\s*<\/p>/', '', $html);

        // Format lists
        $html = preg_replace('/<p>(\d+\..*?)<\/p>/', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ol>$1</ol>', $html);

        return $html;
    }

    public function deleteClaim(Request $request)
    {
        try {
            $request->validate([
                'intimation_no' => 'required|string'
            ]);

            DB::beginTransaction();

            $claim = ClaimNtfRegister::where('intimation_no', $request->intimation_no)->first();

            if (!$claim) {
                return response()->json([
                    'success' => false,
                    'message' => 'Claim notification not found.'
                ], 404);
            }

            if ($claim->status === 'A') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete approved claim notifications.'
                ], 422);
            }

            if ($claim->status === 'C') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete closed claim notifications.'
                ], 422);
            }

            if (!empty($claim->converted_claim_no)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete claim notifications that have been converted to claims.'
                ], 422);
            }

            $this->deleteClaimRelatedData($request->intimation_no);

            $claim->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Claim notification deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            logger('Error deleting claim notification', [
                'intimation_no' => $request->intimation_no ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the claim notification.'
            ], 500);
        }
    }

    /**
     * Delete multiple claim notifications
     */
    public function bulkDeleteClaims(Request $request)
    {
        try {
            $request->validate([
                'intimation_nos' => 'required|array|min:1',
                'intimation_nos.*' => 'required|string'
            ]);

            DB::beginTransaction();

            $intimationNumbers = $request->intimation_nos;
            $deletedCount = 0;
            $errors = [];

            foreach ($intimationNumbers as $intimationNo) {
                try {
                    $claim = ClaimNtfRegister::where('intimation_no', $intimationNo)->first();

                    if (!$claim) {
                        $errors[] = "Claim {$intimationNo} not found.";
                        continue;
                    }

                    if (in_array($claim->status, ['A', 'C'])) {
                        $errors[] = "Cannot delete claim {$intimationNo} - already processed.";
                        continue;
                    }

                    if (!empty($claim->converted_claim_no)) {
                        $errors[] = "Cannot delete claim {$intimationNo} - already converted.";
                        continue;
                    }

                    $this->deleteClaimRelatedData($intimationNo);

                    $claim->delete();
                    $deletedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error deleting claim {$intimationNo}: " . $e->getMessage();
                    logger('Bulk delete error for claim', [
                        'intimation_no' => $intimationNo,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            $response = [
                'success' => $deletedCount > 0,
                'deleted_count' => $deletedCount,
                'total_requested' => count($intimationNumbers)
            ];

            if (count($errors) > 0) {
                $response['errors'] = $errors;
                $response['message'] = "{$deletedCount} claims deleted successfully. " . count($errors) . " claims could not be deleted.";
            } else {
                $response['message'] = "All {$deletedCount} claims deleted successfully.";
            }

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollback();

            logger('Error in bulk delete claims', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during bulk deletion.'
            ], 500);
        }
    }

    /**
     * Delete related data for a claim notification
     */
    private function deleteClaimRelatedData($intimationNo)
    {
        try {
            ClaimNtfPeril::where('intimation_no', $intimationNo)->delete();

            $documents = ClaimNtfDocs::where('intimation_no', $intimationNo)->get();
            foreach ($documents as $doc) {
                if ($doc->file && Storage::disk('public')->exists('claim_ntf_attachments/' . $doc->file)) {
                    Storage::disk('public')->delete('claim_ntf_attachments/' . $doc->file);
                }
            }
            ClaimNtfDocs::where('intimation_no', $intimationNo)->delete();

            ClaimNtfAckDocs::where('intimation_no', $intimationNo)->delete();

            ClaimNtfStatus::where('intimation_no', $intimationNo)->delete();

            DB::table('claim_status_logs')->where('intimation_no', $intimationNo)->delete();
        } catch (\Exception $e) {
            logger('Error deleting related claim data', [
                'intimation_no' => $intimationNo,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Soft delete (mark as cancelled instead of hard delete) - Alternative approach
     */
    public function cancelClaim(Request $request)
    {
        try {
            $request->validate([
                'intimation_no' => 'required|string',
                'cancellation_reason' => 'nullable|string|max:500'
            ]);

            DB::beginTransaction();

            $claim = ClaimNtfRegister::where('intimation_no', $request->intimation_no)->first();

            if (!$claim) {
                return response()->json([
                    'success' => false,
                    'message' => 'Claim notification not found.'
                ], 404);
            }

            $claim->update([
                'status' => 'X', // Cancelled status
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
                'cancelled_by' => auth()->user()->user_name,
                'updated_by' => auth()->user()->user_name
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Claim notification cancelled successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while cancelling the claim notification.'
            ], 500);
        }
    }

    public function getDashboardStats()
    {
        try {
            $currentDate = Carbon::now();
            $weekStart = $currentDate->copy()->startOfWeek();
            $monthStart = $currentDate->copy()->startOfMonth();
            $yearStart = $currentDate->copy()->startOfYear();

            $activeClaims = ClaimNtfRegister::whereIn('status', ['P', 'A'])
                ->where(function ($query) {
                    $query->whereNull('converted_claim_no')
                        ->orWhere('converted_claim_no', '');
                })
                ->count();

            $activeClaimsThisWeek = ClaimNtfRegister::whereIn('status', ['P', 'A'])
                ->where(function ($query) {
                    $query->whereNull('converted_claim_no')
                        ->orWhere('converted_claim_no', '');
                })
                ->where('created_at', '>=', $weekStart)
                ->count();

            $pendingSettlement = ClaimNtfRegister::where('status', 'A')
                ->where(function ($query) {
                    $query->whereNull('converted_claim_no')
                        ->orWhere('converted_claim_no', '');
                })
                ->count();

            $reservedClaims = ClaimNtfRegister::where('status', 'R')->count();

            $estimatedReserve = ClaimNtfRegister::whereNotNull('reserve_amount')
                ->where('reserve_amount', '>', 0)
                ->sum('reserve_amount');

            $pendingNotifications = ClaimNtfRegister::where('notification_status', 'PENDING')
                ->orWhereNull('notification_status')
                ->count();

            $lastWeekActiveClaims = ClaimNtfRegister::whereIn('status', ['P', 'A'])
                ->where(function ($query) {
                    $query->whereNull('converted_claim_no')
                        ->orWhere('converted_claim_no', '');
                })
                ->whereBetween('created_at', [
                    $weekStart->copy()->subWeek(),
                    $weekStart->copy()->subDay()
                ])
                ->count();

            $activeClaimsTrend = $activeClaimsThisWeek - $lastWeekActiveClaims;
            $activeClaimsTrendDirection = $activeClaimsTrend >= 0 ? 'up' : 'down';
            $activeClaimsTrendText = abs($activeClaimsTrend) . ' this week';

            $totalClaimsThisMonth = ClaimNtfRegister::where('created_at', '>=', $monthStart)->count();
            $totalClaimsThisYear = ClaimNtfRegister::where('created_at', '>=', $yearStart)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'active_claims' => [
                        'count' => $activeClaims,
                        'trend' => $activeClaimsTrend,
                        'trend_direction' => $activeClaimsTrendDirection,
                        'trend_text' => $activeClaimsTrendText > 0 ? "+{$activeClaimsTrend} this week" : $activeClaimsTrendText,
                        'icon' => 'bi-bell',
                        'color' => 'bg-modern-primary'
                    ],
                    'pending_settlement' => [
                        'count' => $pendingSettlement,
                        'trend_text' => $pendingSettlement > 0 ? 'Requires attention' : 'All up to date',
                        'icon' => 'bi-clock',
                        'color' => 'bg-modern-warning'
                    ],
                    'reserved_claims' => [
                        'count' => $reservedClaims,
                        'trend_text' => $reservedClaims > 0 ? 'Can proceed to next stage' : 'None reserved',
                        'icon' => 'bi-check',
                        'color' => 'bg-modern-success'
                    ],
                    'estimated_reserve' => [
                        'count' => number_format($estimatedReserve, 0),
                        'raw_amount' => $estimatedReserve,
                        'trend_text' => $pendingNotifications > 0 ? "{$pendingNotifications} pending notifications" : 'All notified',
                        'icon' => 'bi-wallet',
                        'color' => 'bg-modern-secondary'
                    ],
                    'additional_stats' => [
                        'total_this_month' => $totalClaimsThisMonth,
                        'total_this_year' => $totalClaimsThisYear,
                        'pending_notifications' => $pendingNotifications
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            logger('Dashboard stats error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard statistics',
                'data' => [
                    'active_claims' => ['count' => 0, 'trend_text' => 'Data unavailable'],
                    'pending_settlement' => ['count' => 0, 'trend_text' => 'Data unavailable'],
                    'reserved_claims' => ['count' => 0, 'trend_text' => 'Data unavailable'],
                    'estimated_reserve' => ['count' => '0', 'trend_text' => 'Data unavailable']
                ]
            ], 500);
        }
    }

    /**
     * Get detailed breakdown for specific card
     */
    public function getCardDetails(Request $request)
    {
        try {
            $cardType = $request->get('type');

            switch ($cardType) {
                case 'active_claims':
                    $claims = ClaimNtfRegister::whereIn('status', ['P', 'A'])
                        ->where(function ($query) {
                            $query->whereNull('converted_claim_no')
                                ->orWhere('converted_claim_no', '');
                        })
                        ->with(['customer'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    break;

                case 'pending_settlement':
                    $claims = ClaimNtfRegister::where('status', 'A')
                        ->where(function ($query) {
                            $query->whereNull('converted_claim_no')
                                ->orWhere('converted_claim_no', '');
                        })
                        ->with(['customer'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    break;

                case 'reserved_claims':
                    $claims = ClaimNtfRegister::where('status', 'R')
                        ->with(['customer'])
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                    break;

                default:
                    return response()->json(['success' => false, 'message' => 'Invalid card type']);
            }

            return response()->json([
                'success' => true,
                'data' => $claims->map(function ($claim) {
                    return [
                        'intimation_no' => $claim->intimation_no,
                        'customer_name' => $claim->customer->name ?? 'Unknown',
                        'cover_no' => $claim->cover_no,
                        'status' => $claim->status,
                        'created_at' => $claim->created_at->format('M d, Y'),
                        'reserve_amount' => $claim->reserve_amount ? number_format($claim->reserve_amount) : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load card details'
            ], 500);
        }
    }
}
