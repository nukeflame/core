<?php

namespace App\Http\Controllers;

// use App\Jobs\SendClaimNotificationJob;
use App\Jobs\SendClaimReinNotificationJob;
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
use Illuminate\Http\Request;
use App\Models\ClaimRegister;
use App\Models\ClaimReinNote;
use App\Models\CoverRegister;
use App\Models\SystemProcess;
use App\Models\SystemSerials;
use App\Models\ClaimAckParams;
use App\Models\ClaimNtfAckDocs;
use App\Models\ClaimNtfRegister;
use App\Models\CustomerAccDet;
use App\Models\ClaimStatusParam;
use App\Models\Company;
use App\Models\CoverDebit;
use Illuminate\Support\Facades\DB;
use App\Models\SystemProcessAction;
use App\Services\MailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ClaimController extends Controller
{
    private $_year;
    private $_month;
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->_year = Carbon::now()->year;
        $this->_month = Carbon::now()->month;
        // $this->_quarter = Carbon::now()->quarter;

        $this->mailService = $mailService;
    }
    public function ClaimForm(Request $request)
    {
        $covers = CoverRegister::where('customer_id', $request->customer_id)
            ->where('cancelled', '<>', 'Y')
            ->whereIn('type_of_bus', ['FPR', 'FNP', 'TNP'])
            ->orderBy('dola', 'desc')->get();

        $customer = Customer::where('customer_id', $request->customer_id)->get(['customer_id', 'name', 'postal_address', 'postal_town', 'city', 'email', 'telephone', 'country_iso', 'customer_type'])[0];

        return view('claim.claim_form', [
            'covers' => $covers,
            'customer' => $customer,
        ]);
    }

    function GetLossEndorsements(Request $request)
    {
        $cover_no = $request->cover_no;
        $loss_date = $request->loss_date;

        $claim_endorsements = CoverRegister::Where([
            ['cover_no', '=', $cover_no],
            ['cover_from', '<=', Carbon::parse($loss_date)->format('Y-m-d')],
            ['cover_to', '>=', Carbon::parse($loss_date)->format('Y-m-d')],
            ['cancelled', '<>', 'Y']
        ])->orderBy('dola', 'desc')->get();

        return response()->json(['endorsements' => $claim_endorsements]);
    }

    function GetEndorsementInfo(Request $request)
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
            $claim_serial_no = ClaimRegister::max('claim_serial_no') + 1;
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
                $brokercode = $endorse->broker_code;
                $classcode = $endorse->class_code;
                $covertype = $endorse->cover_type;
                $currency_code = $endorse->currency_code;
                $currency_rate = $endorse->currency_rate;


                $customer_id = $request->customer_id;
                $cover_no = $request->cover_no;
                $loss_date = $request->loss_date;
                $coverfrom = $request->cover_from;
                $coverto = $request->cover_to;
                $insured_name = $request->insured_name;

                $currentYear = Carbon::now()->year;
                $currentMonth = str_pad($this->_month, 2, '0', STR_PAD_LEFT);
                $claim_no = 'CLM' . $branchcode . $classcode . $claim_serial_no . $currentYear . $currentMonth;
                $endorsement_no = $request->endorsement_no;

                $branch = Branch::where('branch_code', $branchcode)->first();
                $broker = Broker::where('broker_code', $brokercode)->first();
                $class = Classes::where('class_code', $classcode)->first();
                $customer = Customer::where('customer_id', $customer_id)->first();
                $reinsurers = Customer::all();

                $cover = CoverType::where('type_id', $covertype)->first();
                $coverpart = CoverRipart::where('endorsement_no', $endorsement_no)->get();
                $busType = BusinessType::where('bus_type_id', $type_of_bus)->get(['bus_type_id', 'bus_type_name'])[0];

                ClaimRegister::create(
                    [
                        'claim_serial_no' => $claim_serial_no,
                        'cover_no' => $cover_no,
                        'customer_id' => $customer_id,
                        'date_of_loss' => $loss_date,
                        'type_of_bus' => $type_of_bus,
                        'branch_code' => $branchcode,
                        'broker_code' => $brokercode ? $brokercode : 0,
                        'cover_type' => $covertype,
                        'class_group_code' => $class ? $class->class_group_code : 'ALL',
                        'class_code' => $classcode ? $classcode : 'ALL',
                        'claim_no' => $claim_no,
                        'endorsement_no' => $endorsement_no,
                        'insured_name' => $insured_name,
                        'cover_from' => $coverfrom,
                        'cover_to' => $coverto,
                        'created_by' => Auth::user()->user_name,
                        'created_date' => Carbon::now(),
                        'currency_code' => $currency_code,
                        'currency_rate' => $currency_rate,
                        'date_notified_insurer' => $request->date_notify_insurer,
                        'date_notified_reinsurer' => $request->date_notify_reinsurer,
                        'cause_of_loss' => $request->cause_of_loss,
                        'loss_narration' => $request->loss_desc,
                        // 'intimation_no' => $request->intimation_no,
                        'status' => 'A'
                    ]
                );
                Session::Flash('success', 'Claim: ' . $claim_no . ' has been registered');

                return redirect()->route('claim.detail', [
                    'claim_no' => $claim_no
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
        $results = ClaimRegister::query()->where('customer_id', $customer_id)->distinct('claim_no')->get();
        $query = collect($results)->sortByDesc('created_at')->values();

        return datatables::of($query)
            ->editColumn('claim_no', function ($fn) {
                return $fn->claim_no;
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
            ->editColumn(
                'created_at',
                function ($fn) {
                    return $fn->created_date;
                }
            )
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
            ->editColumn('actions', function () {
                $viewUrl = '#';
                return '<a href="' . $viewUrl . '" class="btn btn-sm btn-primary btn-sm-action"  id="view-coverlist-table">View <i class="bx bx-send"></i></a>';
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    public function ClaimDetails(Request $request)
    {
        $claim_no = $request->claim_no;

        $claimRegister = ClaimRegister::where('claim_no', $claim_no)->first();

        if (!$claimRegister) {
            return redirect()->back()->withErrors(['Claim not found']);
        }

        $branch = Branch::where('branch_code', $claimRegister->branch_code)->first();
        $broker = Broker::where('broker_code', $claimRegister->broker_code)->first();
        $class = Classes::where('class_code', $claimRegister->class_code)->first();
        $customer = Customer::where('customer_id', $claimRegister->customer_id)->first();
        $reinsurers = Customer::all();
        $cover = CoverType::where('type_id', $claimRegister->cover_type)->first();

        $coverpart = CoverRipart::where('endorsement_no', $claimRegister->endorsement_no)
            ->with([
                'partner:customer_id,name,email',
                'contacts',
            ])
            ->get();

        $busType = BusinessType::where('bus_type_id', $claimRegister->type_of_bus)->first();
        $endorse = CoverRegister::where('endorsement_no', $claimRegister->endorsement_no)->first();
        $verifiers = User::permission('app.claims_administration.manage')
            ->where('user_name', '<>', Auth::user()->user_name)
            ->get();

        $process = SystemProcess::where('nice_name', 'claim_registration')->first();
        $verifyprocessAction = SystemProcessAction::where('nice_name', 'verify_claim')->first();

        // Calculate debit amounts
        $CRperilTotal = ClaimPeril::where('claim_no', $claim_no)->where('dr_cr_note_no', 0)->where('dr_cr', 'CR')->sum('final_amount') ?? 0;
        $DRperilTotal = ClaimPeril::where('claim_no', $claim_no)->where('dr_cr_note_no', 0)->where('dr_cr', 'DR')->sum('final_amount') ?? 0;

        if ($CRperilTotal > $DRperilTotal) {
            $nextDebitAmount = $CRperilTotal - $DRperilTotal;
        } else {
            $nextDebitAmount = $DRperilTotal - $CRperilTotal;
        }

        $uploadedDocs = ClaimDocs::where('claim_no', $claim_no)->get();
        $ClaimAckDocs = ClaimAckDocs::where('claim_no', $claim_no)->get();
        $ack_docs = ClaimAckParams::where('class_group', $endorse->class_group_code ?? '')->get();
        $clmStatuses = ClaimStatusParam::all();
        $claimperils = ClaimPeril::where('claim_no', $claim_no)->get();
        $perilTypes = Peril::where('status', 'A')->get();

        $finalTotalDR = $claimperils->where('dr_cr', 'DR')->sum('basic_amount');
        $finalTotalCR = $claimperils->where('dr_cr', 'CR')->sum('basic_amount');
        $totalClaimAmount = (float) $finalTotalDR - $finalTotalCR;

        // Process uploaded documents
        $files = collect($uploadedDocs)->map(function ($query) {
            return [
                'id' => $query->id,
                'original_name' => $query->title,
                'extension' => 'pdf',
                'size' => 2048576,
                'mime_type' => 'application/pdf',
                'created_at' => Carbon::now()->subDays(2),
                'file_path' => $query->file,
                'uploaded_by' => ''
            ];
        });

        $attachedFiles = collect([
            (object) [
                'id' => 1,
                'original_name' => 'claim_notice_letter.pdf',
                'extension' => 'pdf',
                'size' => 2048576,
                'mime_type' => 'application/pdf',
                'created_at' => Carbon::now()->subDays(2),
                'file_path' => 'claims/documents/claim_notice_letter.pdf',
                'uploaded_by' => ''
            ],
            (object) [
                'id' => 2,
                'original_name' => 'debit_note.pdf',
                'extension' => 'pdf',
                'size' => 2048576,
                'mime_type' => 'application/zip',
                'created_at' => Carbon::now()->subDays(1),
                'file_path' => 'claims/documents/debit_note.pdf',
                'uploaded_by' => ''
            ],
        ]);

        $cedantAttachedFiles = collect([
            (object) [
                'id' => 1,
                'original_name' => 'acknowledgement_letter.pdf',
                'extension' => 'pdf',
                'size' => 2048576,
                'mime_type' => 'application/pdf',
                'created_at' => Carbon::now()->subDays(2),
                'file_path' => 'claims/documents/claim_form_2024.pdf',
                'uploaded_by' => 'John Doe'
            ],
        ]);

        $emailFrom = Company::where('company_id', 1)->first()->email ?? '';
        $cedant = Customer::where('customer_id', $claimRegister->customer_id)->first();
        $reinserEmail = $cedant->email ?? '';

        $claimSubject = collect([
            'Claim Notification',
            $claimRegister->insured_name ?? '',
            $claimRegister->cover_from ?? '',
            $claimRegister->class_code ?? '',
            $claimRegister->intimation_no ?? '',
            $customer->name ?? ''
        ])->filter()->implode(' - ');


        // Filter to only allowed mail folders
        $folders = ['inbox', 'sent', 'drafts'];
        $limit = (int) $request->get('limit', 50);
        $search = $request->get('search');
        $allEmails = [];

        foreach ($folders as $folder) {
            try {
                $results = $this->mailService->getMailData($folder, $search, $limit);
                $allEmails = [...$allEmails, ...$results['emails']];
            } catch (\Exception $e) {
                logger()->error("Mail index failed for folder: {$folder}", [
                    'error' => $e->getMessage(),
                    'folder' => $folder
                ]);
            }
        }

        // logger()->info(json_encode($results['emails'], JSON_PRETTY_PRINT));


        return view('claim.claim_home', [
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
            'ack_docs' => $ack_docs,
            'clmStatuses' => $clmStatuses,
            'claimperils' => $claimperils,
            'perilTypes' => $perilTypes,
            'ClaimAckDocs' => $ClaimAckDocs,
            'totalClaimAmount' => $totalClaimAmount,
            'emailFrom' => $emailFrom,
            'reinserEmail' => $reinserEmail,
            'claimSubject' => $claimSubject,
            'attachedFiles' => $files, // Use the actual files instead of empty array
            'cedant' => $cedant?->name,
            'recipients' => $cedant?->contacts,
            'cedantAttachedFiles' => $cedantAttachedFiles,
            'defaultCedantMessage' => $this->getCedantDefaultMessage($claimRegister, $customer),
            'defaultMessage' => $this->getDefaultMessage($claimRegister, $customer),
            'emails' => $allEmails
        ]);
    }

    private function getDefaultMessage($claim, $customer): string
    {
        return "Dear {recipient_name},\n\n" .
            "Greetings,\n\n" .
            "We regret to inform you of a loss which occurred on " . Carbon::parse($claim->date_of_loss)->format('Y-m-d') . " due to {$claim->cause_of_loss}.\n\n" .
            "Kindly find attached copies of claim documents and debit note for your review and settlement.\n\n" .
            "We acknowledge the notification of the subject claim and have proceeded to recover the same from the securities as per the attached supporting documents.\n\n" .
            "Best regards,\n" .
            config('app.name');
    }

    public function ClaimsEnquiryDatatable(Request $request)
    {
        $query = ClaimRegister::query()->distinct('claim_no');

        return datatables::of($query)
            ->addColumn('id', function ($fn) {
                return $fn->claim_no;
            })
            ->editColumn('claim_no', function ($fn) {
                return $fn->claim_no;
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
                switch ($fn->verified) {
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
            ->addColumn('action', function ($data) {
                $detailUrl = route('claim.detail', $data->claim_no);
                return '<button class="btn btn-sm btn-primary view_claim" data-claim_no="' . $data->claim_no . '" data-detail-url="' . $detailUrl . '">View <i class="bx bx-send"></i></button>';
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    public function savePeril(Request $request)
    {
        $currentYear = str_pad($this->_year, 4, '0', STR_PAD_LEFT);
        $currentMonth = str_pad($this->_month, 2, '0', STR_PAD_LEFT);
        $claim_no = $request->claim_no;

        DB::beginTransaction();
        try {
            $peril_ids = $request->peril_name;
            $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();
            foreach ($peril_ids as $index => $peril_id) {
                $tran_no = ClaimPeril::count('tran_no') + 1;
                $peril_amount = (float)str_replace(',', '', $request->peril_amount[$index]);
                $perilDtl = Peril::where('id', $peril_id)->first();

                $newclaimperil = new ClaimPeril();
                $newclaimperil->claim_no = $claim_no;
                $newclaimperil->tran_no = $tran_no;
                $newclaimperil->peril_id = $peril_id;
                $newclaimperil->peril_name = $perilDtl->description;
                $newclaimperil->dr_cr_note_no = '0';
                $newclaimperil->dr_cr = $perilDtl->dr_cr;
                $newclaimperil->entry_type_descr = 'NET';
                $newclaimperil->basic_amount = $peril_amount;
                $newclaimperil->rate = $cover->share_offered;
                $newclaimperil->final_amount = ($cover->share_offered / 100) * $peril_amount;
                $newclaimperil->status = 'A';
                $newclaimperil->account_year = $currentYear;
                $newclaimperil->account_month = $currentMonth;
                $newclaimperil->created_by =  Auth::user()->user_name;
                $newclaimperil->updated_by =  Auth::user()->user_name;
                $newclaimperil->save();
            }

            DB::commit();
            Session::Flash('success', 'Perils has been saved successfully');
            return redirect()->route('claim.detail', [
                'claim_no' => $claim_no
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Session::Flash('success', 'Perils has Failed');
            return redirect()->route('claim.detail', [
                'claim_no' => $claim_no
            ]);
        }
    }

    function ClaimPerilDatatable(Request $request)
    {
        $claim_no = $request->get('claim_no');
        $query = ClaimPeril::query()->where('claim_no', $claim_no);

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
            ->editColumn('action', function ($fn) {
                return $fn->final_amount;
            })
            ->make(true);
    }

    public function ClaimReinsurerDatatable(Request $request)
    {
        $endorsement_no = $request->get('endorsement_no');
        $cover_no = $request->get('cover_no');
        $claim_no = $request->get('claim_no');

        $query = CoverRipart::query()
            ->where('endorsement_no', $endorsement_no)
            ->with(['partner:customer_id,name']);

        $debit = ClaimDebit::query()->where('claim_no', $claim_no)->first();
        $intimation = ClaimNtfRegister::query()
            ->where(['cover_no' => $cover_no, 'endorsement_no' => $endorsement_no])
            ->first();

        return datatables::of($query)
            ->addColumn('partner_name', function ($data) {
                return $data->partner->name ?? 'N/A';
            })
            ->addColumn('credit_no', function ($data) {
                return 'CRN/' . $data->tran_no . '/' . ($data->period_year ?? '');
            })
            ->addColumn('action', function ($data) use ($debit, $claim_no, $intimation) {
                $btn = "";
                $debit_url = '';
                $claim_notice_url = '';

                if ($debit) {
                    $debit_url = route('docs.fac_clm_reindebit_note', [
                        'endorsement_no' => $data->endorsement_no,
                        'claim_no' => $claim_no,
                        'id' => $debit->id,
                        'partner_no' => $data->partner_no,
                    ]);

                    $btn .= '<a href="' . $debit_url . '" target="_blank" rel="noopener noreferrer" class="link me-2">
                                <i class="bx bx-file"></i> Debit Note
                             </a>';

                    if ($intimation && $intimation->intimation_no) {
                        $claim_notice_url = route('docs.claimntf-docs-notc-letter', [
                            'intimation_no' => $intimation->intimation_no,
                            'partner_no' => $data->partner_no
                        ]);

                        $btn .= ' <a class="print-out-link me-2"
                                    href="' . $claim_notice_url . '"
                                    target="_blank" rel="noopener noreferrer">
                                    <i class="bx bx-file"></i> Claim Notice
                                 </a>';
                    }
                }

                $btn .= '<a href="#" class="link me-2 send_rein_email" data-tran_no="' . $data->tran_no . '" data-debit_url="' . $debit_url . '" data-claim_notice_url="' . $claim_notice_url . '" >
                            <i class="bx bx-mail-send"></i> Send E-Mail
                         </a>';

                return $btn;
            })
            ->make(true);
    }


    public function generateDebit(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'cover_no' => 'required',
                'endorsement_no' => 'required',
                'claim_no' => 'required',
                'amount' => 'required',
            ]);

            $CoverRegister = CoverRegister::where('endorsement_no', $request->endorsement_no)
                ->firstOrFail();
            $cover_debit = CoverDebit::where('endorsement_no', $request->endorsement_no)->firstOrFail();

            $id = (int) ClaimDebit::withTrashed()->max('id') + 1;
            $net_amt = (float) str_replace(',', '', $request->amount ?: '0');

            $CRperilTotal = ClaimPeril::where('claim_no', $request->claim_no)
                ->where('dr_cr_note_no', 0)
                ->where('dr_cr', 'CR')
                ->sum('final_amount');

            $DRperilTotal = ClaimPeril::where('claim_no', $request->claim_no)
                ->where('dr_cr_note_no', 0)
                ->where('dr_cr', 'DR')
                ->sum('final_amount');

            $netBalance = $DRperilTotal - $CRperilTotal;

            if ($netBalance > 0) {
                $doc_type = 'CRN';
                $dr_cr = 'C';
            } else {
                $doc_type = 'DRN';
                $dr_cr = 'D';
            }

            if (!empty($net_amt) && round($net_amt) > 0) {
                $dr_cr_no = SystemSerials::nextSerial($doc_type);
                $credit = new ClaimDebit();
                $credit->id = $id;
                $credit->dr_no = $dr_cr_no;
                $credit->document = $doc_type;
                $credit->cover_no = $request->cover_no;
                $credit->endorsement_no = $request->endorsement_no;
                $credit->claim_no = $request->claim_no;
                $credit->period_year = $cover_debit->period_year;
                $credit->period_month = $cover_debit->period_month;
                $credit->installment = $cover_debit->installment;
                $credit->gross = (float)str_replace(',', '', $request->amount) ?? 0;
                $credit->net_amt = $net_amt;
                $credit->created_by = Auth::user()->user_name;
                $credit->updated_by = Auth::user()->user_name;
                $credit->save();

                $serial_no = str_pad($credit->dr_no, 6, '0', STR_PAD_LEFT);
                $custaccount = new CustomerAccDet();
                $custaccount->branch                = $CoverRegister->branch_code;
                $custaccount->customer_id           = $CoverRegister->customer_id;
                $custaccount->source_code           = 'CLM';
                $custaccount->doc_type              = $doc_type;
                $custaccount->entry_type_descr      = 'NET';
                $custaccount->reference             = $serial_no . $this->_year;
                $custaccount->account_year          = $this->_year;
                $custaccount->account_month         = $this->_month;
                $custaccount->line_no               = 1;
                $custaccount->cheque_no             = ' ';
                $custaccount->cheque_date           = null;
                $custaccount->cover_no              = $CoverRegister->cover_no;
                $custaccount->endorsement_no        = $CoverRegister->endorsement_no;
                $custaccount->insured               = $CoverRegister->insured_name;
                $custaccount->class                 = $CoverRegister->class_code;
                $custaccount->currency_code         = $CoverRegister->currency_code;
                $custaccount->currency_rate         = $CoverRegister->currency_rate;
                $custaccount->created_by            = Auth::user()->user_name;
                $custaccount->created_date          = now();
                $custaccount->created_time          = now();
                $custaccount->updated_by            = Auth::user()->user_name;
                $custaccount->updated_datetime      = now();
                $custaccount->dr_cr                 = $dr_cr;
                $custaccount->foreign_basic_amount  = $cover_debit->gross;
                $custaccount->local_basic_amount    = $cover_debit->gross * $CoverRegister->currency_rate;
                $custaccount->foreign_taxes_amount  = 0;
                $custaccount->local_taxes_amount    = 0;
                $custaccount->foreign_nett_amount   = $cover_debit->net_amt;
                $custaccount->local_nett_amount     = $cover_debit->net_amt * $CoverRegister->currency_rate;
                $custaccount->allocated_amount      = 0;
                $custaccount->unallocated_amount    = $cover_debit->gross * $CoverRegister->currency_rate;
                $custaccount->save();

                $ripart = CoverRipart::where('endorsement_no', $cover_debit->endorsement_no)->get();
                if (count($ripart) > 0) {
                    foreach ($ripart as $part) {
                        $CRperilTotal = ClaimPeril::where('claim_no', $request->claim_no)->where('dr_cr_note_no', $serial_no . $this->_year)->where('dr_cr', 'CR')->sum('basic_amount') ?? 0;
                        $DRperilTotal = ClaimPeril::where('claim_no', $request->claim_no)->where('dr_cr_note_no', $serial_no . $this->_year)->where('dr_cr', 'DR')->sum('basic_amount') ?? 0;
                        if ($dr_cr = 'C') {
                            $dr_cr = 'C';
                            $netTotalAmount = $DRperilTotal - $CRperilTotal;
                        } else {
                            $dr_cr = 'D';
                            $netTotalAmount = $CRperilTotal - $DRperilTotal;
                        }
                        $maxTranNo = ClaimReinNote::where('claim_no', $request->claim_no)->max('tran_no');
                        $tran_no = $maxTranNo !== null ? (int) floor($maxTranNo) : 0;
                        $tran_no = $tran_no + 1;
                        $ln_no = (int) ClaimReinNote::where('claim_no', $request->claim_no)
                            ->where('transaction_type', $CoverRegister->type_of_bus)
                            ->where('entry_type_descr', $custaccount->entry_type_descr)
                            ->count() + 1;
                        $share = (float) $part->share ?? 0;

                        $ClaimReinNote = new ClaimReinNote();
                        $ClaimReinNote->cover_no            = $part->cover_no;
                        $ClaimReinNote->endorsement_no      = $part->endorsement_no;
                        $ClaimReinNote->claim_no            = $request->claim_no;
                        $ClaimReinNote->partner_no          = $part->partner_no;
                        $ClaimReinNote->dr_no               = $dr_cr_no;
                        $ClaimReinNote->transaction_type    = $CoverRegister->type_of_bus;
                        $ClaimReinNote->account_year        = $part->period_year;
                        $ClaimReinNote->account_month       = $part->period_month;
                        $ClaimReinNote->share               = $share;
                        $ClaimReinNote->created_by          = auth()->user()->user_name;
                        $ClaimReinNote->updated_by          = auth()->user()->user_name;
                        $ClaimReinNote->tran_no             = $tran_no;
                        $ClaimReinNote->ln_no               = $ln_no;
                        $ClaimReinNote->entry_type_descr    = $custaccount->entry_type_descr;
                        $ClaimReinNote->item_title          = 'Claim Payable';
                        $ClaimReinNote->dr_cr               = $dr_cr;
                        $ClaimReinNote->rate                = $share;
                        $ClaimReinNote->total_gross         = $netTotalAmount;
                        $ClaimReinNote->gross               = $netTotalAmount * $share / 100;
                        $ClaimReinNote->net_amt             = $netTotalAmount * $share / 100;
                        $ClaimReinNote->save();
                    }
                }
            }

            ClaimRegister::where('claim_no', $request->claim_no)->update(['verified' => 'A']);
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
        } catch (\Exception $e) {
            logger($e);
            DB::rollback();
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function debits_datatable(Request $request)
    {
        $claim_no = $request->get('claim_no');
        $cover_no = $request->get('cover_no');
        $endorsement_no = $request->get('endorsement_no');
        $claim_debit = ClaimDebit::query()->where('claim_no', $claim_no)->first();
        $query = CoverRegister::query()->where('endorsement_no', $endorsement_no)->with('customer');
        $debit = CoverDebit::query()->where('endorsement_no', $endorsement_no)->first();

        $ClaimRegister = ClaimNtfRegister::where(['cover_no' => $cover_no, 'endorsement_no' => $endorsement_no])->first();
        // logger(['$ClaimRegister' => $ClaimRegister]);

        return datatables::of($query)
            ->addColumn('id', function ($data) {
                return $data?->id;
            })
            ->addColumn('installment', function () use ($debit) {
                return $debit?->installment;
            })
            ->addColumn('cedant', function ($data) {
                $customer_name = $data?->customer?->name;
                return $customer_name;
            })
            ->addColumn('net_amt', function () use ($debit) {
                return $debit?->net_amt;
            })
            ->editColumn('dr_no', function () use ($debit) {
                return 'DRN/' . $debit?->dr_no . '/' . $debit?->period_year ?? '';
            })
            ->addColumn('sum_insured', function ($data) {
                return $data?->total_sum_insured;
            })
            ->addColumn('share', function ($data) {
                return $data?->share_offered ? number_format($data?->share_offered, 2) : 0;
            })
            ->addColumn('premium', function ($data) {
                return $data?->cedant_premium;
            })
            ->addColumn('gross', function ($data) {
                return $data?->cedant_premium;
            })
            ->addColumn('action', function () use ($claim_debit, $ClaimRegister) {
                $btn = "";
                if ($claim_debit) {
                    $url = route('docs.claimcreditnote', [
                        'endorsement_no' => $claim_debit->endorsement_no,
                        'claim_no' => $claim_debit->claim_no,
                        'id' => $claim_debit->id,
                    ]);

                    $btn .= '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="link me-2">
                            <i class="bx bx-file"></i> Credit Note
                        </a>';

                    $ackUrl = route('docs.claimntf-docs-ack-letter', [
                        'intimation_no' =>  $ClaimRegister->intimation_no,
                        'documented' => 1
                    ]);

                    $btn .= '  <a href="' . $ackUrl . '" class="print-out-link me-2" target="_blank" rel="noopener noreferrer">
                    <i class="bx bx-file"></i> Acknowledgement Letter
                    </a>';
                }

                $btn .= '<a href="#" class="send_debit_letter link me-2">
                              <i class="bx bx-mail-send"></i> Send E-Mail
                        </a>';

                return $btn;
            })
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
        $claim_no = $request->claim_no;
        $query = ClaimDocs::query()->where('claim_no', $claim_no);
        $actionable = static::claimClosed($claim_no);

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
                'recieved_date',
                function ($data) {
                    $clm = ClaimNtfAckDocs::where([
                        'cover_no' => $data?->intimation_no,
                        'endorsement_no' => $data?->endorsement_no,
                        'intimation_no' => $data?->intimation_no,
                    ])->first();

                    return $clm?->date_received;
                }
            )
            ->addColumn(
                'filename',
                function ($data) {
                    return $data?->file;
                }
            )
            ->addColumn('action', function ($data) use ($actionable) {
                $btn = "";
                if ($actionable) {
                    $btn .= " <button class='btn btn-primary btn-sm view-attachment p-0 m-0 px-3' data-id='{$data->id}' data-mime='{$data->mime_type}' data-base64='{$data->file_base64}'
                        data-bs-toggle='modal' data-bs-target='#attachmentDocumentModal'>View <i class='bi bi-send'></i></button>";
                    // $btn .= " <button class='btn btn-outline-primary btn-sm edit-attachment' data-data='{$data}' data-id='{$data->id}'
                    //     data-bs-toggle='modal' data-bs-target='#attachments-modal'>Edit</button>";
                    // $btn .= " <button class='btn btn-outline-danger btn-sm remove-attachment' data-title='{$data->title}' data-id='{$data->id}'>Remove</button>";
                }
                return $btn;
            })
            ->rawColumns(['recieved_date', 'action'])
            ->make(true);
    }


    public function saveAttachment(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'claim_no' => 'required',
                'endorsement_no' => 'required',
                'title' => 'required',
                'date_received' => 'required',
                'description' => 'required',
                'file' => 'required|mimes:pdf,doc,docx,jpeg,png',
            ]);

            $file = $request->file('file');
            $fileName = date('dmYhis') . '_' . $file->getClientOriginalName();
            $file->storeAs('cover_attachments', $fileName, 'public');
            $mimeType = $file->getClientMimeType();
            // Read the file contents and encode it to base64
            $base64Encoded = base64_encode(File::get($file->path()));

            $id = (int) ClaimDocs::withTrashed()->max('id') + 1;
            $CoverRegister = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();

            ClaimDocs::create([
                'id' => $id,
                'claim_no' => $request->claim_no,
                'cover_no' => $CoverRegister->cover_no,
                'endorsement_no' => $CoverRegister->endorsement_no,
                'title' => $request->title,
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
            // dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function amendAttachment(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {
            $request->validate([
                'claim_no' => 'required',
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

            $attachment = ClaimDocs::where('id', $request->id)->first();

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
        // dd($request->all());
        DB::beginTransaction();
        try {

            $request->validate([
                'claim_no' => 'required',
                'endorsement_no' => 'required',
                'id' => 'required',
            ]);

            $attachment = ClaimDocs::where('id', $request->id)->first();
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
        $claim_no = $request->claim_no;
        $query = ClaimAckDocs::query()->where('claim_no', $claim_no);
        $actionable = static::claimClosed($claim_no);

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


    public function saveDocAcknowledgement(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'claim_no' => 'required',
                'endorsement_no' => 'required',
                'document' => 'required'
            ]);

            $CoverRegister = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();
            $file = null;
            $fileName = null;
            $mimeType = null;
            $base64Encoded = null;
            for ($i = 0; $i < count($request->document); $i++) {
                $id = (int)ClaimAckDocs::max('id') + 1;
                $max_doc_id = (int)ClaimDocs::max('id') + 1;
                if ($request->date_received[$i] !== null) {
                    $file = $request->file('file')[$i];
                    $fileName = date('dmYhis') . '_' . $file->getClientOriginalName();
                    $file->storeAs('claim_ntf_attachments', $fileName, 'public');
                    $mimeType = $file->getClientMimeType();
                    $base64Encoded = base64_encode(File::get($file->path()));
                }
                $doc = ClaimAckParams::where('id', $request->document[$i])->first();
                $checkifExists = ClaimAckDocs::where('claim_no', $request->claim_no)->where('doc_id', $request->document[$i])->count();
                if ($checkifExists == 0) {

                    ClaimAckDocs::create([
                        'id' => $id,
                        'claim_no' => $request->claim_no,
                        'cover_no' => $CoverRegister->cover_no,
                        'endorsement_no' => $CoverRegister->endorsement_no,
                        'doc_id' => $request->document[$i],
                        'date_received' => $request->date_received[$i],
                        'doc_name' => $doc->doc_name,
                        'file' => $fileName,
                        'file_base64' => $base64Encoded,
                        'mime_type' => $mimeType,
                        'created_by' => Auth::user()->user_name,
                        'updated_by' => Auth::user()->user_name,
                    ]);

                    if ($base64Encoded !== null) {
                        ClaimDocs::create([
                            'id' => $max_doc_id,
                            'claim_no' => $request->claim_no,
                            'cover_no' => $CoverRegister->cover_no,
                            'endorsement_no' => $CoverRegister->endorsement_no,
                            'doc_id' => $doc->id,
                            'title' => $doc->doc_name,
                            'description' => $doc->doc_name,
                            'file' => $fileName,
                            'file_base64' => $base64Encoded,
                            'mime_type' => $mimeType,
                            'created_by' => Auth::user()->user_name,
                            'updated_by' => Auth::user()->user_name,
                        ]);
                    }
                } else {
                    ClaimAckDocs::where('claim_no', $request->claim_no)->where('doc_id', $request->document[$i])->update([
                        'date_received' => $request->date_received[$i],
                        'doc_name' => $doc->doc_name,
                        'file' => $fileName,
                        'file_base64' => $base64Encoded,
                        'mime_type' => $mimeType,
                        'updated_by' => Auth::user()->user_name,

                    ]);

                    if ($base64Encoded !== null) {
                        ClaimDocs::create([
                            'id' => $max_doc_id,
                            'claim_no' => $request->claim_no,
                            'cover_no' => $CoverRegister->cover_no,
                            'endorsement_no' => $CoverRegister->endorsement_no,
                            'doc_id' => $doc->id,
                            'title' => $doc->doc_name,
                            'description' => $doc->doc_name,
                            'file' => $fileName,
                            'file_base64' => $base64Encoded,
                            'mime_type' => $mimeType,
                            'created_by' => Auth::user()->user_name,
                            'updated_by' => Auth::user()->user_name,
                        ]);
                    }
                }
            }
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
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleteDocAcknowledgement(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try {

            $request->validate([
                'claim_no' => 'required',
                'endorsement_no' => 'required',
                'id' => 'required',
            ]);

            $ackDoc = ClaimAckDocs::where('id', $request->id)->first();
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
            // dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function claimClosed($claim_no): bool
    {
        $claim = ClaimRegister::where('claim_no', $claim_no)->first();

        $actionable = true;
        if ($claim->status == 'C') {
            $actionable = false;
        }
        return $actionable;
    }


    public function claimStatusDatatable(Request $request)
    {
        $claim_no = $request->claim_no;
        $query = ClaimStatus::query()->with('status')->where('claim_no', $claim_no);
        $actionable = static::claimClosed($claim_no);

        return datatables::of($query)
            ->editColumn('created_at', function ($data) {
                return formatDate($data->created_at);
            })
            ->addColumn('action', function ($data) use ($actionable) {
                $btn = "";
                // if($actionable)
                // {
                //     $btn .= " <button class='btn btn-outline-danger btn-sm remove-acknowledgements' data-title='{$data->status->description}' data-id='{$data->status_id}'>Remove</button>";
                // }
                return $btn;
            })
            ->make(true);
    }


    public function saveClaimStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'claim_no' => 'required',
                'endorsement_no' => 'required',
                'status' => 'required',
            ]);

            ClaimStatus::create([
                'status_id' => $request->status,
                'claim_no' => $request->claim_no,
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

    private function getCedantDefaultMessage($claim, $customer): string
    {
        return "Dear {$customer->name},\n\n" .
            "Greetings,\n\n" .
            "We confirm receipt of the subject claim notification and have proceeded to notify the securities for their review and settlement.\n\n" .
            "Best regards,\n" .
            // "Reinsurance Department\n" .
            config('app.name');
    }



    public function sendReinDocumentEmail(Request $request)
    {
        try {
            DB::beginTransaction();
            $claim = ClaimRegister::where('claim_no', $request->claim_no)->first();
            // $claim->update([
            //     'notificaction_status' => 'notification_sent',
            //     // 'notification_sent_at' => now(),
            //     // 'notification_sent_by' => auth()->id()
            // ]);

            $message = $this->formatMessageForHtml($request->message);
            $request->merge(['message' => $message]);

            // Dispatch email job
            SendClaimReinNotificationJob::dispatch(
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

    private function formatMessageForHtml($message)
    {
        $html = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
        $html = str_replace("\n\n", "</p><p>", $html);
        $html = "<p>" . $html . "</p>";
        $html = preg_replace('/<p>\s*<\/p>/', '', $html);
        $html = preg_replace('/<p>(\d+\..*?)<\/p>/', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ol>$1</ol>', $html);

        return $html;
    }

    public function showClaimEnquiry()
    {
        return view('claim/claims_enquiry');
    }
}
