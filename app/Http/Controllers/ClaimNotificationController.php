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
use App\Models\ClaimDebit;
use App\Models\ClaimPeril;
use App\Models\ClaimStatus;
use App\Models\CoverRipart;
use App\Models\BusinessType;
use App\Models\ClaimAckDocs;
use App\Models\ClaimNtfDocs;
use Illuminate\Http\Request;
use App\Models\ClaimNtfPeril;
use App\Models\ClaimRegister;
use App\Models\CoverRegister;
use App\Models\SystemProcess;
use App\Models\SystemSerials;
use App\Models\ClaimAckParams;
use App\Models\ClaimNtfStatus;
use App\Models\CustomerAccDet;
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
use PHPUnit\Event\Telemetry\System;
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
            $customers = Customer::where('status', 'A')
                ->get([
                    'customer_id',
                    'name',
                    'postal_address',
                    'postal_town',
                    'city',
                    'email',
                    'telephone',
                    'country_iso',
                    'customer_type'
                ]);

            $allCovers = CoverRegister::where('cancelled', '<>', 'Y')
                ->whereIn('type_of_bus', ['FPR', 'FNP', 'TNP'])
                ->orderBy('dola', 'desc')
                ->get();

            $coversByCustomer = $allCovers->groupBy('customer_id');
            $customersWithCovers = $customers->map(function ($customer) use ($coversByCustomer) {
                $customerCovers = $coversByCustomer->get($customer->customer_id, collect());
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
                    'covers' => $customerCovers->map(function ($cover) {
                        $coverType  =  DB::table('class_groups')->where(['group_code' => $cover->class_group_code, 'status' => 'A'])->first(['group_name']);

                        logger()->info(json_encode($cover, JSON_PRETTY_PRINT));

                        return [
                            'cover_no' => $cover->cover_no ?? null,
                            'type_of_bus' => $cover->type_of_bus,
                            'dola' => $cover->dola,
                            'endorsement_number' => $cover->endorsement_no ?? null,
                            'cover_type' => $coverType?->group_name,
                            'insured_name' => $cover->insured_name
                        ];
                    }),
                    'covers_count' => $customerCovers->count()
                ];
            });


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
            $serial_no = ClaimNtfRegister::max('serial_no') + 1;
            $validator =  Validator::make($request->all(), [
                'cover_no' => 'required',
                'loss_date' => 'required',
                'cover_from' => 'required',
                'cover_to' => 'required',
                'endorsement_no' => 'required',
            ]);

            if ($validator) {
                $endorse = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();

                $type_of_bus = $endorse->type_of_bus;
                $branchcode = $endorse->branch_code;
                $classcode = $endorse->class_code;
                $covertype = $endorse->cover_type;
                $currency_code = $endorse->currency_code;
                $currency_rate = $endorse->currency_rate;
                $brokercode = $endorse->broker_code ?? 0;

                $customer_id = $request->customer_id;
                $cover_no = $request->cover_no;
                $loss_date = $request->loss_date;
                $coverfrom = $request->cover_from;
                $coverto = $request->cover_to;
                $insured_name = $request->insured_name;

                $currentYear = Carbon::now()->year;
                $currentMonth = str_pad($this->_month, 2, '0', STR_PAD_LEFT);
                $intimation_no = 'INT' . $branchcode . $classcode . $serial_no . $currentYear . $currentMonth;
                $endorsement_no = $request->endorsement_no;
                $cedant_claim_no = $request->cedant_claim_no;

                $class = Classes::where('class_code', $classcode)->first();
                $class_group_code = $class ? $class->class_group_code : 'ALL';
                $class_code = $classcode ? $classcode : 'ALL';
                $username = Auth::user()->user_name;
                $date_notified_insurer = $request->date_notify_insurer;
                $date_notified_reinsurer = $request->date_notify_reinsurer;
                $cause_of_loss = $request->cause_of_loss;
                $loss_narration = $request->loss_desc;
                $status = 'P';

                ClaimNtfRegister::create([
                    'serial_no' => $serial_no,
                    'intimation_no' => $intimation_no,
                    'cedant_claim_no' => $cedant_claim_no,
                    'cover_no' => $cover_no,
                    'customer_id' => $customer_id,
                    'date_of_loss' => $loss_date,
                    'type_of_bus' => $type_of_bus,
                    'branch_code' => $branchcode,
                    'broker_code' => $brokercode,
                    'cover_type' => $covertype,
                    'class_group_code' => $class_group_code,
                    'class_code' => $class_code,
                    'endorsement_no' => $endorsement_no,
                    'insured_name' => $insured_name,
                    'cover_from' => $coverfrom,
                    'cover_to' => $coverto,
                    'created_by' => $username,
                    'updated_by' => $username,
                    'currency_code' => $currency_code,
                    'currency_rate' => $currency_rate,
                    'date_notified_insurer' => $date_notified_insurer,
                    'date_notified_reinsurer' => $date_notified_reinsurer,
                    'cause_of_loss' => $cause_of_loss,
                    'loss_narration' => $loss_narration,
                    'status' => $status,
                ]);

                Session::Flash('success', 'Claim Notification: ' . $intimation_no . ' has been registered');

                return redirect()->route('claim.notification.claim_detail', [
                    'intimation_no' => $intimation_no
                ]);
            } else {
                Session::flash('error', 'some field data is required');
                return [
                    'code' => -1,
                    'msg' => $validator->errors(),
                ];
            }
        } catch (\Throwable $e) {
            throw $e;
        }
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



        // logger(json_encode($cedant->con, JSON_PRETTY_PRINT));

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
                    if ($class_desc) {
                        $class_desc = 'Facultative - ' . $class_desc->class_name;
                    } else {
                        $class_desc = 'Unknown Class';
                    }
                } elseif ($fn->type_of_bus == 'TPR') {

                    $class_desc = 'Treaty -  Proportional';
                } elseif ($fn->type_of_bus == 'TNP') {

                    $class_desc = 'Treaty  - Non Proportional';
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
            ->editColumn('created_at', function ($fn) {
                return formatDate($fn->created_at);
            })
            ->addColumn('action', function ($fn) {
                return '<a href="#" class="btn btn-sm btn-primary btn-sm-action" id="view-notf-claimstatus" data-intimation_no="' . e($fn->intimation_no) . '" data-process_type="' . e($fn->process_type) . '">View <i class="bx bx-send"></i></a>';
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
            // DB::rollback();
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

            // Dispatch email job
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
}
