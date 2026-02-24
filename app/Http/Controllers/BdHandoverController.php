<?php

namespace App\Http\Controllers;

use App\Models\Bd\PipelineOpportunity;
use App\Models\CoverType;
use App\Models\HandoverApproval;
use App\Models\PayMethod;
use App\Repositories\CoverRepository;
use App\Services\CoverService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BdHandoverController extends Controller
{
    protected $coverService;
    protected $coverRepository;

    private const COVER_TYPE_NEW = 'N';
    private const PAY_METHOD_AUTOMATIC = 'A';
    private const TRANS_TYPE_NEW = 'NEW';

    public function __construct(
        CoverRepository $coverRepository,
        CoverService $coverService,
    ) {
        $this->coverRepository = $coverRepository;
        $this->coverService = $coverService;
    }

    public function index()
    {
        return view('pipeline.bd_handovers');
    }

    public function getStatistics()
    {
        try {
            $currentMonth = now()->startOfMonth();

            $query = PipelineOpportunity::where('handed_over', 'Y')->has('handovers')->with('handovers');

            $totalCount = $query->count();
            $thisMonthCount = $query->where('created_at', '>=', $currentMonth)->count();

            $pendingCount = $query->where('bd_status', 'Pending')->count();
            $approvedCount =  $query->where('bd_status', 'Approved')->count();
            $rejectedCount =  $query->where('bd_status', 'Rejected')->count();
            $processingCount =  $query->where('bd_status', 'Processing')->count();

            $premiumData = PipelineOpportunity::where('handed_over', 'Y')->selectRaw('
                SUM(cede_premium) as total_premium,
                AVG(cede_premium) as average_premium,
                currency_code
            ')
                ->groupBy('currency_code')
                ->orderByDesc('total_premium')
                ->first();

            $totalPremium = $premiumData->total_premium ?? 0;
            $averagePremium = $premiumData->average_premium ?? 0;
            $primaryCurrency = $premiumData->currency_code ?? 'USD';

            $divisions = PipelineOpportunity::where('handed_over', 'Y')->select('divisions as division_name', DB::raw('count(*) as count'))
                ->groupBy('division_name')
                ->orderByDesc('count')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->division_name ?? 'Unassigned',
                        'count' => $item->count
                    ];
                });

            $statuses = PipelineOpportunity::where('handed_over', 'Y')->select('bd_status as status', DB::raw('count(*) as count'))
                ->groupBy('bd_status')
                ->orderByDesc('count')
                ->get();

            $topCedants = PipelineOpportunity::where('handed_over', 'Y')
                ->whereNotNull('customer_id')
                ->select('customer_id', DB::raw('count(*) as count'))
                ->groupBy('customer_id')
                ->orderByDesc('count')
                ->limit(10)
                ->with('customer:customer_id,name')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->customer?->name ?? 'Unknown',
                        'count' => $item->count
                    ];
                });

            return response()->json([
                'status' => true,
                'data' => [
                    'total_count' => $totalCount,
                    'this_month_count' => $thisMonthCount,
                    'pending_count' => $pendingCount,
                    'approved_count' => $approvedCount,
                    'rejected_count' => $rejectedCount,
                    'processing_count' => $processingCount,
                    'total_premium' => $totalPremium,
                    'average_premium' => $averagePremium,
                    'primary_currency' => $primaryCurrency,
                    'divisions' => $divisions,
                    'statuses' => $statuses,
                    'top_cedants' => $topCedants,
                ]
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Failed to load statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function getDataTableData(Request $request)
    {
        try {
            $query = PipelineOpportunity::where('handed_over', 'Y')->has('handovers')->with('handovers');

            if ($request->has('status_filter') && $request->status_filter !== 'all') {
                $statusMap = [
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'processing' => 'Processing'
                ];

                if (isset($statusMap[$request->status_filter])) {
                    $query->where('bd_status', $statusMap[$request->status_filter]);
                }
            }

            return Datatables()->eloquent($query)
                ->editColumn('division_name', function ($d) {
                    $division = DB::table('reins_division')->where('division_code', $d->divisions)->first();
                    if (is_null($division)) {
                        return '';
                    }
                    return $division ? $division->division_name : 'N/A';
                })
                ->editColumn('business_class', function ($d) {
                    $business_class = DB::table('classes')->where('class_code', $d->classcode)->first();
                    if (is_null($business_class)) {
                        return '';
                    }
                    return $business_class ? $business_class->class_name : 'N/A';
                })
                ->editColumn('cedant', function ($handover) {
                    $customer = DB::table('customers')->where('customer_id', $handover->customer_id)->first();
                    return $customer?->name;
                })
                ->editColumn('cedant_premium', function ($d) {
                    return number_format($d->cede_premium, 2, '.', ',');
                })
                ->editColumn('effective_sum_insured', function ($d) {
                    return number_format($d->effective_sum_insured, 2, '.', ',');
                })
                ->editColumn('effective_date', function ($handover) {
                    return Carbon::parse($handover->effective_date)->format('d M Y');
                })
                ->editColumn('closing_date', function ($handover) {
                    return Carbon::parse($handover->closing_date)->format('d M Y');
                })
                ->editColumn('bd_status', function ($fn) {
                    $approvals = HandoverApproval::where('prospect_id', $fn->opportunity_id)
                        ->select('approval_status', 'intergrate')
                        ->get();

                    $status_label = '<span class="status-badge status-pending">Pending <i class="bx bx-time"></span>';

                    $pendingApproval = $approvals->contains(function ($approval) {
                        return is_null($approval->approval_status) || $approval->approval_status === '';
                    });

                    $isApproved = $approvals->contains(function ($approval) {
                        return $approval->approval_status === '1';
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
                        $status_label = '<span class="status-badge status-pending">Pending <i class="bx bx-time"></span>';
                    } else if ($isRejected) {
                        $status_label = '<span class="status-badge status-rejected">Rejected <i class="bx bx-x"></span>';
                    } else if ($isApproved) {
                        $status_label = '<span class="status-badge status-approved">Approved <i class="bx bx-check-circle"></i></span>';
                    } else if ($isApprovedNotIntegrated) {
                        $status_label = '<span class="status-badge status-processing">Waiting <i class="bx bx-time"></i></span>';
                    } else if ($isApprovedAndIntergrated) {
                        $status_label = '<span class="status-badge status-approved">Integrated <i class="bx bx-check"></i></span>';
                    }

                    return $status_label;
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

                    $actionButtons = '<div class="d-flex gap-1 align-items-center">';

                    if ($pendingApproval) {
                        $actionButtons .= sprintf(
                            '<button type="button" class="btn btn-sm btn-success btn-sm-action approve-btn" data-id="%s">Approve <i class="bx bx-check"></i></button>',
                            $fn->id
                        );
                    }

                    if ($isApprovedNotIntegrated) {
                        $actionButtons .= sprintf(
                            '<button type="button" class="btn btn-sm btn-dark btn-sm-action rotate-fill integrate-btn" data-id="%s">Integrate <i class="bx bx-analyse"></i></button>',
                            $fn->id
                        );
                    }

                    $actionButtons .= '<div class="btn-group" style="position: inherit;">';
                    $actionButtons .= '<button type="button" class="btn btn-icon btn-sm btn-light p-0" data-bs-toggle="dropdown" aria-expanded="false">';
                    $actionButtons .= '<i class="bi bi-three-dots-vertical"></i>';
                    $actionButtons .= '</button>';
                    $actionButtons .= '<ul class="dropdown-menu dropdown-menu-end">';

                    $reviewUrl = route('lead.handover', ['prospect' => $fn->opportunity_id, 'approval' => 1, 'action' => 'review']);
                    $actionButtons .= sprintf(
                        '<li><a class="dropdown-item review-btn" href="javascript:void(0);" data-url="%s">Review</a></li>',
                        $reviewUrl
                    );

                    if ($pendingApproval) {
                        $actionButtons .= sprintf(
                            '<li><a class="dropdown-item reject-bd-btn" href="javascript:void(0);" data-id="%s">Reject</a></li>',
                            $fn->id
                        );
                    }

                    if ($isRejected) {
                        $h = $fn->handovers ? $fn->handovers->where('prospect_id', $fn->opportunity_id)->first() : null;
                        if ($h && isset($h->reason_for_rejection)) {
                            $reason_for_rejection = $h->reason_for_rejection;
                        } else {
                            $reason_for_rejection = 'No reason provided';
                        }
                        $actionButtons .= sprintf(
                            '<li><a class="dropdown-item rejected-bd-comment" href="javascript:void(0);" data-reason="%s">Rejected comment</a></li>',
                            htmlspecialchars($reason_for_rejection, ENT_QUOTES)
                        );
                    }

                    $actionButtons .= '</ul>';
                    $actionButtons .= '</div>'; // Close btn-group
                    $actionButtons .= '</div>'; // Close outer div

                    return $actionButtons;
                })
                ->rawColumns(['action', 'bd_status'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load data'
            ], 500);
        }
    }

    public function export(Request $request)
    {
        // try {
        //     $format = $request->query('export', 'excel');
        //     $statusFilter = $request->query('status_filter', 'all');

        //     $query = BdHandover::query();

        //     // Apply status filter
        //     if ($statusFilter !== 'all') {
        //         $statusMap = [
        //             'pending' => 'Pending',
        //             'approved' => 'Approved',
        //             'rejected' => 'Rejected',
        //             'processing' => 'Processing'
        //         ];

        //         if (isset($statusMap[$statusFilter])) {
        //             $query->where('bd_status', $statusMap[$statusFilter]);
        //         }
        //     }

        //     $handovers = $query->get();

        //     // Use Laravel Excel or similar package
        //     switch ($format) {
        //         case 'excel':
        //             return Excel::download(
        //                 new BdHandoversExport($handovers),
        //                 'bd-handovers-' . now()->format('Y-m-d') . '.xlsx'
        //             );

        //         case 'pdf':
        //             return PDF::loadView('exports.bd-handovers-pdf', compact('handovers'))
        //                 ->download('bd-handovers-' . now()->format('Y-m-d') . '.pdf');

        //         case 'csv':
        //             return Excel::download(
        //                 new BdHandoversExport($handovers),
        //                 'bd-handovers-' . now()->format('Y-m-d') . '.csv',
        //                 \Maatwebsite\Excel\Excel::CSV
        //             );

        //         default:
        //             return response()->json(['error' => 'Invalid export format'], 400);
        //     }
        // } catch (\Exception $e) {
        //     \Log::error('BD Handover Export Error: ' . $e->getMessage());

        //     return response()->json([
        //         'error' => 'Failed to export data'
        //     ], 500);
        // }
    }

    public function createCover(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:pipeline_opportunities,id'
        ]);

        try {
            $p = PipelineOpportunity::findOrFail($validated['id']);

            $covertype = CoverType::where('short_description', self::COVER_TYPE_NEW)->first();
            if (!$covertype) {
                return $this->errorResponse('Cover type not found', 404);
            }

            $pay_method = PayMethod::where('short_description', self::PAY_METHOD_AUTOMATIC)->first();
            if (!$pay_method) {
                return $this->errorResponse('Payment method not found', 404);
            }

            $requestData = $this->buildCoverRequestData($p, $covertype, $pay_method);

            $validator = Validator::make($requestData, [
                'covertype' => 'required',
                'branchcode' => 'required',
                'customer_id' => 'required',
                'classcode' => 'required',
                'coverfrom' => 'required|date',
                'coverto' => 'required|date|after:coverfrom',
                'pay_method' => 'required',
                'type_of_bus' => 'required',
                'class_group' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                // $repositoryData = $this->coverService->transformRequestData($requestData);

                // $result = $this->coverRepository->registerCover((object) $repositoryData);
                $handover = HandoverApproval::where('prospect_id', $p->opportunity_id)->first();

                if (!$handover) {
                    throw new \Exception("HandoverApproval record not found for prospect_id: {$p->id}");
                }

                // $handover->update(['intergrate' => true]);

                DB::commit();

                $data  = [
                    'customerId' => $p?->customer_id,
                    'prospectId' => $handover->prospect_id,
                    'typeOfBus' => 'NEW'
                ];

                return response()->json([
                    'status' => true,
                    'message' => 'Cover Register information saved successfully',
                    'data' => $data
                ], 201);
            } catch (\Exception $e) {
                DB::rollBack();

                $message = 'An error occurred while processing your request';
                return response()->json([
                    'status' => false,
                    'message' => $message,
                    'error' => app()->environment('production') ? null : $e->getMessage()
                ], 500);
            }
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Pipeline opportunity not found', 404);
        } catch (\Exception $e) {

            return $this->errorResponse('An unexpected error occurred', 500);
        }
    }

    private function buildCoverRequestData(
        PipelineOpportunity $p,
        CoverType $covertype,
        PayMethod $pay_method
    ): array {
        return [
            'customer_id' => $p->customer_id,
            'trans_type' => self::TRANS_TYPE_NEW,
            'type_of_bus' => $p->type_of_bus,
            'covertype' => $covertype->type_id,
            'branchcode' => $p->branchcode,
            'broker_flag' => $p->broker_flag,
            'prospect_id' => $p->id,
            'division' => $p->division,
            'pay_method' => $pay_method->pay_method_code,
            'no_of_installments' => $p->no_of_installments,
            'currency_code' => $p->currency_code,
            'today_currency' => $p->today_currency,
            'premium_payment_term' => $p->premium_payment_term,
            'class_group' => $p->class_group,
            'classcode' => $p->classcode,
            'insured_name' => $p->insured_name,
            'fac_date_offered' => $p->fac_date_offered,
            'sum_insured_type' => $p->sum_insured_type,
            'total_sum_insured' => $p->total_sum_insured,
            'eml_rate' => $p->eml_rate,
            'eml_amt' => $p->eml_amt,
            'effective_sum_insured' => $p->effective_sum_insured,
            'risk_details' => $p->risk_details,
            'cede_premium' => $p->cede_premium,
            'rein_premium' => $p->rein_premium,
            'fac_share_offered' => $p->fac_share_offered,
            'comm_rate' => $p->comm_rate,
            'comm_amt' => $p->comm_amt,
            'reins_comm_type' => $p->reins_comm_type,
            'reins_comm_rate' => $p->reins_comm_rate,
            'reins_comm_amt' => $p->reins_comm_amt,
            'brokerage_comm_type' => $p->brokerage_comm_type,
            'brokerage_comm_amt' => $p->brokerage_comm_amt,
            'brokerage_comm_rate' => $p->brokerage_comm_rate,
            'brokerage_comm_rate_amnt' => $p->brokerage_comm_rate_amnt,
            'vat_charged' => $p->vat_charged,
            'limit_per_reinclass' => $p->limit_per_reinclass,
            'layer_no' => $p->layer_no,
            'nonprop_reinclass' => $p->nonprop_reinclass,
            'nonprop_reinclass_desc' => $p->nonprop_reinclass_desc,
            'indemnity_treaty_limit' => $p->indemnity_treaty_limit,
            'underlying_limit' => $p->underlying_limit,
            'coverfrom' => $p->effective_date,
            'coverto' => $p->closing_date,
            'brokercode' => $p->brokercode,
        ];
    }

    private function errorResponse(string $message, int $statusCode = 500)
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $statusCode);
    }


    // public function createCover(Request $request)
    // {
    //     $p = PipelineOpportunity::findOrFail($request->id);
    //     $covertype = CoverType::where('short_description', 'N')->firstOrFail();
    //     $pay_method = PayMethod::where('short_description', 'A')->firstOrFail();

    //     $requestData = [
    //         'customer_id' => $p->customer_id,
    //         'trans_type' => 'NEW',
    //         'type_of_bus' => $p->type_of_bus,
    //         'covertype' => $covertype->type_id,
    //         'branchcode' => $p->branchcode,
    //         'broker_flag' => $p->broker_flag,
    //         'prospect_id' => $p->id,
    //         'division' => $p->division,
    //         'pay_method' => $pay_method->pay_method_code,
    //         'no_of_installments' => $p->no_of_installments,
    //         'currency_code' => $p->currency_code,
    //         'today_currency' => $p->today_currency,
    //         'premium_payment_term' => $p->premium_payment_term,
    //         'class_group' => $p->class_group,
    //         'classcode' => $p->classcode,
    //         'insured_name' => $p->insured_name,
    //         'fac_date_offered' => $p->fac_date_offered,
    //         'sum_insured_type' => $p->sum_insured_type,
    //         'total_sum_insured' => $p->total_sum_insured,
    //         'eml_rate' => $p->eml_rate,
    //         'eml_amt' => $p->eml_amt,
    //         'effective_sum_insured' => $p->effective_sum_insured,
    //         'risk_details' => $p->risk_details,
    //         'cede_premium' => $p->cede_premium,
    //         'rein_premium' => $p->rein_premium,
    //         'fac_share_offered' => $p->fac_share_offered,
    //         'comm_rate' => $p->comm_rate,
    //         'comm_amt' => $p->comm_amt,
    //         'reins_comm_type' =>  $p->reins_comm_type,
    //         'reins_comm_rate' => $p->reins_comm_rate,
    //         'reins_comm_amt' => $p->reins_comm_amt,
    //         'brokerage_comm_type' => $p->brokerage_comm_type,
    //         'brokerage_comm_amt' => $p->brokerage_comm_amt,
    //         'brokerage_comm_rate' => $p->brokerage_comm_rate,
    //         'brokerage_comm_rate_amnt' => $p->brokerage_comm_rate_amnt,
    //         'vat_charged' => $p->vat_charged,
    //         'limit_per_reinclass' => $p->limit_per_reinclass,
    //         'layer_no' => $p->layer_no,
    //         'nonprop_reinclass' => $p->nonprop_reinclass,
    //         'nonprop_reinclass_desc' => $p->nonprop_reinclass_desc,
    //         'indemnity_treaty_limit' => $p->indemnity_treaty_limit,
    //         'underlying_limit' => $p->underlying_limit,
    //         'coverfrom' => $p->effective_date,
    //         'coverto' => $p->closing_date,
    //         'brokercode' => $p->brokercode,
    //     ];

    //     $validationRequest = new Request($requestData);

    //     $validator = Validator::make($validationRequest->all(), [
    //         'covertype' => 'required',
    //         'branchcode' => 'required',
    //         'customer_id' => 'required',
    //         'classcode' => 'required',
    //         'coverfrom' => 'required|date',
    //         'coverto' => 'required|date|after:coverfrom',
    //         'pay_method' => 'required',
    //         'type_of_bus' => 'required',
    //         'class_group' => 'required',
    //     ]);

    //     if (!$validator->fails()) {
    //         try {
    //             DB::beginTransaction();

    //             $result = $this->coverRepository->registerCover($validationRequest);
    //             $handover = HandoverApproval::where('prospect_id', $p->opportunity_id)->first();
    //             $handover->update([
    //                 'intergrate' => true,
    //             ]);

    //             DB::commit();

    //             return response()->json(
    //                 [
    //                     'status' => true,
    //                     'message' => 'Cover Register information saved successfully',
    //                     'customerId' => $result?->customer_id,
    //                     'prospectId' => $handover?->prospect_id
    //                 ]
    //             );
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'An error occurred while processing your request',
    //                 'error' => $e->getMessage()
    //             ], 500);
    //         }
    //     } else {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }
    // }
}
