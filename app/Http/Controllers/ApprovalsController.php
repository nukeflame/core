<?php

namespace App\Http\Controllers;

use App\Enums\SystemActionEnums;
use App\Events\ApprovalTrackerEvent;
use App\Http\Traits\ApprovalTrackerTrait;
use Carbon\Carbon;
use App\Models\GLBatch;
use Illuminate\Http\Request;
use App\Models\CoverRegister;
use App\Models\ApprovalsTracker;
use App\Models\ClaimNtfRegister;
use App\Models\ApprovalSourceLink;
use App\Models\Bd\PipelineOpportunity;
use App\Models\ClaimNtfPeril;
use App\Models\HandoverApproval;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use App\Models\SystemProcessAction;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ApprovalsController extends Controller
{
    use ApprovalTrackerTrait;

    public function index()
    {
        $all = ApprovalsTracker::with(['source', 'notification'])
            ->where('approver', Auth::user()->id)
            ->count();

        $claim = ApprovalsTracker::with(['source', 'notification'])
            ->where('approver', Auth::user()->id)
            ->whereHas('notification', function ($query) {
                $query->where('notification_type', 'claim');
            })
            ->count();

        $fac = ApprovalsTracker::with(['source', 'notification'])
            ->where('approver', Auth::user()->id)
            ->whereHas('notification', function ($query) {
                $query->where('notification_type', 'facultative');
            })
            ->count();

        $treaty = ApprovalsTracker::with(['source', 'notification'])
            ->where('approver', Auth::user()->id)
            ->whereHas('notification', function ($query) {
                $query->where('notification_type', 'treaty');
            })
            ->count();

        $pending = ApprovalsTracker::with(['source', 'notification'])
            ->where('status', 'P')
            ->where('approver', Auth::user()->id)
            ->count();

        $counts = [
            'all' => $all,
            'claim' => $claim,
            'fac' => $fac,
            'treaty' => $treaty,
            'pending' => $pending,
        ];

        return view('approvals.notifications', [
            'approvals' => [],
            'counts' => $counts
        ]);
    }

    public function sendForApproval(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'process' => 'required',
                'process_action' => 'required',
                'approver' => 'required',
            ]);

            $data = [];
            $message = null;
            $source_data = [];
            $username = Auth::user()->user_name;
            $approvalAction = SystemProcessAction::where('id', $request->process_action)->first();
            $link_id = (int) ApprovalSourceLink::max('id') + 1;
            $approval_id = (int) ApprovalsTracker::max('id') + 1;

            switch ($approvalAction->nice_name) {
                case 'verify_cover':
                    $cover = CoverRegister::with([
                        'customer' => function ($query) {
                            $query->select('customer_id', 'name');
                        }
                    ])
                        ->select('cover_register.customer_id', 'cover_no', 'endorsement_no', 'type_of_bus', 'no_of_installments', 'total_sum_insured')
                        ->where('endorsement_no', $request->endorsement_no)
                        ->firstOrFail();

                    $data = [
                        'cover_no' => $cover->cover_no,
                        'amount' => $cover->total_sum_insured,
                        'endorsement_no' => $cover->endorsement_no,
                        'business_type' => $cover->type_of_bus,
                        'no_of_instalments' => $cover->no_of_installments,
                        'customer' => $cover->customer->name,
                        'customer_id' => $cover->customer->customer_id,
                        'type' => 'facultative'
                    ];

                    $source_data[] = [
                        'source_table' => 'cover_register',
                        'source_column_name' => 'endorsement_no',
                        'source_approval_column' => 'verified',
                        'source_approval_by_column' => null,
                        'source_approval_at_column' => null,
                        'source_column_data' => $cover->endorsement_no,
                    ];

                    $message = [
                        'text' => "Cover No: <b>{$cover->cover_no}</b> has been sent for approval. Please check and review.",
                        'title' => "You have a new approval request from {$username}"
                    ];

                    break;

                case SystemActionEnums::VERIFY_CLAIM_INTIMATION_PROCESS:
                    $claim = ClaimNtfRegister::with([
                        'customer' => function ($query) {
                            $query->select('customer_id', 'name');
                        }
                    ])
                        ->select('claim_ntf_register.customer_id', 'cover_no', 'endorsement_no', 'type_of_bus', 'intimation_no', 'date_of_loss', 'insured_name', 'reserve_amount')
                        ->where('intimation_no', $request->intimation_no)
                        ->firstOrFail();

                    $amount = 0;
                    $source_approval_column = '';
                    if ($request->has('process_type') && $request->process_type == 'reserve') {
                        $amount = $claim->reserve_amount;
                        $source_approval_column .= 'reserve_approval_status';
                    } else if ($request->has('process_type') && $request->process_type == 'claim') {
                        $other_perils = ClaimNtfPeril::where('intimation_no', $request->intimation_no)
                            ->where('dr_cr_note_no', 0)
                            ->where('dr_cr', '!=', 'CR')
                            ->sum('basic_amount');
                        $salvage = ClaimNtfPeril::where('intimation_no', $request->intimation_no)
                            ->where('dr_cr_note_no', 0)
                            ->where('dr_cr', 'CR')
                            ->sum('basic_amount');
                        $amount = $salvage ? (float) $other_perils - (float) $salvage : (float) $other_perils;
                        $source_approval_column .= 'approval_status,reserve_approval_status';
                    }

                    $data = [
                        'cover_no' => $claim->cover_no,
                        'amount' => $amount,
                        'endorsement_no' => $claim->endorsement_no,
                        'no_of_instalments' => 0,
                        'type' => 'claim',
                        'intimation_no: ' => $claim->intimation_no,
                        'loss_date' => $claim->date_of_loss,
                        'business_type' => $claim->type_of_bus,
                        'customer' => $claim->insured_name,
                        'customer_id' => $claim->customer_id,
                    ];

                    $source_data[] = [
                        'source_table' => 'claim_ntf_register',
                        'source_column_name' => 'intimation_no',
                        'source_approval_column' => $source_approval_column,
                        'source_approval_by_column' => 'approved_by',
                        'source_approval_at_column' => 'approved_date',
                        'source_column_data' => $claim->intimation_no,
                    ];

                    $message = [
                        'text' => "Cover No: <b>{$claim->cover_no}</b> has been sent for approval. Please check and review.",
                        'title' => "You have a new approval request from {$username}"
                    ];

                    break;

                case 'verify-glbatch':
                    $batch = GLBatch::whereRaw("trim(batch_no) = '" . $request->batch_no . "'")->first();
                    $data = [
                        'Batch no: ' => $batch->batch_no,
                        'Batch Name: ' => $batch->batch_description,
                    ];
                    $source_data[] = [
                        'source_table' => 'glbatch',
                        'source_column_name' => 'batch_no',
                        'source_approval_column' => 'verified',
                        'source_approval_by_column' => null,
                        'source_approval_at_column' => null,
                        'source_column_data' => $batch->batch_no,
                    ];
                    break;

                case 'authorize-requisition':
                    $data = [
                        'Requisition no: ' => $request->requisition_no,
                        'Action: ' => 'Requisition Authorization',
                        'Comment: ' => $request->comment,
                    ];
                    $source_data[] = [
                        'source_table' => 'cbrequisitions',
                        'source_column_name' => 'requisition_no',
                        'source_approval_column' => 'authorized_flag',
                        'source_approval_by_column' => 'authorized_by',
                        'source_approval_at_column' => 'authorized_date',
                        'source_column_data' => $request->requisition_no,
                    ];
                    break;

                case 'approve-requisition':
                    $data = [
                        'Requisition no: ' => $request->requisition_no,
                        'Action: ' => 'Requisition Approval',
                        'Comment: ' => $request->comment,
                    ];
                    $source_data[] = [
                        'source_table' => 'cbrequisitions',
                        'source_column_name' => 'requisition_no',
                        'source_approval_column' => 'approved_flag',
                        'source_approval_by_column' => 'approved_by',
                        'source_approval_at_column' => 'approved_date',
                        'source_column_data' => $request->requisition_no,
                    ];
                    break;
            }

            $sanitizedData = json_encode($data);
            $approval = ApprovalsTracker::create([
                'id' => $approval_id,
                'process_id' => $request->process,
                'process_action' => $request->process_action,
                'approver' => $request->approver,
                'comment' => $request->comment,
                'status' => 'P',
                'data' => $sanitizedData,
                'created_by' => $username,
                'updated_by' => $username,
            ]);

            foreach ($source_data as $source_d) {
                ApprovalSourceLink::create([
                    'id' => $link_id,
                    'approval_id' => $approval_id,
                    'process_id' => $request->process,
                    'process_action' => $request->process_action,
                    'source_table' => $source_d['source_table'],
                    'source_column_name' => $source_d['source_column_name'],
                    'source_column_data' => $source_d['source_column_data'],
                    'source_approval_column' => $source_d['source_approval_column'],
                    'source_approval_by_column' => $source_d['source_approval_by_column'],
                    'source_approval_at_column' => $source_d['source_approval_at_column'],
                    'created_by' => $username,
                    'updated_by' => $username,
                ]);

                $source_approval_column = explode(',', $source_d['source_approval_column']);
                $approval_column = collect($source_approval_column)->mapWithKeys(function ($item) {
                    return [$item => 'P'];
                })->toArray();

                DB::table($source_d['source_table'])
                    ->where($source_d['source_column_name'], $source_d['source_column_data'])
                    ->update($approval_column);
            }

            // Nofications
            $user = User::find($request->approver);
            $approvalNotice = Notification::create([
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'title' => $message['title'],
                'link' => '/approvals/index',
                'message' => $message['text'],
                'type' => $approvalAction->nice_name,
                'effective_from' => Carbon::now(),
                'expired_at' => Carbon::now()->addDays(7),
                'notification_type' => $data['type'],
                'status' => 'pending',
                'priority' => $request->priority,
                'amount' => $data['amount'],
                'client' => $data['customer'],
                'cover_no' => $data['cover_no'],
                'endorsement_no' => $data['endorsement_no'],
                'customer_id' => $data['customer_id'],
                'underwriter' => Auth::user()->name,
                'approval_tracker_id' => $approval->id,
            ]);

            $this->syncUsersOrModules($approvalAction, $approvalNotice);
            $this->syncReadStatus($approvalNotice, $approval);
            event(new ApprovalTrackerEvent($approvalNotice, 'create', $request->user(), $approval));

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
        } catch (Exception $e) {

            DB::rollBack();
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save data'
            ]);
        }
    }

    public function approvalAction(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'id' => 'required',
                'action' => 'required',
                'comment' => 'required',
            ]);

            $approval = ApprovalsTracker::findOrFail($request->id);
            $approval->status = $request->action;
            $approval->approver_comment = $request->comment;
            $approval->updated_by = Auth::user()->user_name;
            $approval->save();

            // $source_identifier = json_decode($approval->source_identifier);
            // $source_table = $source_identifier->table;
            // $source_columns = $source_identifier->identifierColumns;

            $sourceInfos = ApprovalSourceLink::where('approval_id', $approval->id)->get(['source_approval_column', 'source_approval_by_column', 'source_approval_at_column', 'source_table', 'source_column_name', 'source_column_data']);

            foreach ($sourceInfos as $sourceInfo) {
                $updateData = [];

                if (!empty($sourceInfo->source_approval_column)) {
                    $source_approval_column = explode(',', $sourceInfo->source_approval_column);
                    $action = (string) $request->action;
                    if (strlen($action) <= 1) {
                        foreach ($source_approval_column as $key) {
                            $updateData[$key] = $action;
                        }
                    } else {
                        continue;
                    }
                }

                if (!empty($sourceInfo->source_approval_by_column)) {
                    $updateData[$sourceInfo->source_approval_by_column] = Auth::user()->user_name;
                }

                if (!empty($sourceInfo->source_approval_at_column)) {
                    $updateData[$sourceInfo->source_approval_at_column] = Carbon::now();
                }

                if (!empty($updateData)) {
                    $verified = 'P';
                    if (isset($updateData['verified']) && $updateData['verified']) {
                        $verified = $updateData['verified'];
                    } else if (isset($updateData['reserve_approval_status']) && $updateData['reserve_approval_status']) {
                        $verified = $updateData['reserve_approval_status'];
                    } else if (isset($updateData['approval_status']) && $updateData['approval_status']) {
                        $verified = $updateData['approval_status'];
                    }

                    $updatedStatus = [
                        'P' => 'pending',
                        'A' => 'approved',
                        'R' => 'rejected'
                    ][$verified] ?? 'pending';

                    DB::table('notifications')
                        ->where('approval_tracker_id', $approval->id)
                        ->update(['status' => $updatedStatus]);

                    DB::table($sourceInfo->source_table)
                        ->where($sourceInfo->source_column_name, $sourceInfo->source_column_data)
                        ->update($updateData);
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
                'message' => 'Failed to save data'
            ]);
        }
    }

    public function approvalDatatable(Request $request)
    {
        $approvals = ApprovalsTracker::with(['source', 'notification'])
            ->where('approver', Auth::user()->id)
            ->orderBy('created_at', 'desc')->get();

        if ($request->type) {
            switch ($request->type) {
                case 'all':
                    $approvals = ApprovalsTracker::with(['source', 'notification'])
                        ->where('approver', Auth::user()->id)
                        ->orderBy('created_at', 'desc')->get();
                    break;

                case 'fac':
                    $approvals = ApprovalsTracker::with(['source', 'notification'])
                        ->where('approver', Auth::user()->id)
                        ->whereHas('notification', function ($query) {
                            $query->where('notification_type', 'facultative');
                        })
                        ->get();
                    break;

                case 'claim':
                    $approvals = ApprovalsTracker::with(['source', 'notification'])
                        ->where('approver', Auth::user()->id)
                        ->whereHas('notification', function ($query) {
                            $query->where('notification_type', 'claim');
                        })
                        ->get();

                    // logger(json_encode($approvals, JSON_PRETTY_PRINT));
                    break;

                case 'treaty':
                    $approvals = ApprovalsTracker::with(['source', 'notification'])
                        ->where('approver', Auth::user()->id)
                        ->whereHas('notification', function ($query) {
                            $query->where('notification_type', 'treaty');
                        })
                        ->get();
                    break;
            }
        }

        return DataTables::of($approvals)
            ->addColumn('client', function ($item) {
                return $item?->notification?->client;
            })
            ->addColumn('amount', function ($item) {
                return number_format($item?->notification?->amount, 2);
            })
            ->addColumn('title', function ($item) {
                $typeClass = [
                    'claim' => 'Claim',
                    'facultative' => 'Fac',
                    'treaty' => 'Treaty'
                ][$item?->notification?->notification_type] ?? '';

                return $typeClass . ' #' . $item?->notification?->cover_no;
            })
            ->addColumn('comment', function ($item) {
                return $item?->comment;
            })
            ->addColumn('date', function ($item) {
                return $item?->notification?->created_at;
            })
            ->addColumn('status', function ($item) {
                $statusClass = [
                    'pending' => 'bg-warning-gradient',
                    'approved' => 'bg-success-gradient',
                    'rejected' => 'bg-danger-gradient'
                ][$item?->notification?->status] ?? '';

                $statusIcon = [
                    'pending' => '<i class="bx  ln-6 bx-time"></i>',
                    'approved' => '<i class="bx  ln-6 bx-check"></i>',
                    'rejected' => '<i class="bx  ln-6 bx-x"></i>'
                ][$item?->notification?->status] ?? '';
                return '<span class="badge ln-6 badge-sm-action ' . $statusClass . '">' . ucfirst($item?->notification?->status) . ' ' . $statusIcon . '</span>';
            })
            ->addColumn('priority_badge', function ($item) {
                $priorityClass = [
                    'critical' => 'bg-danger-transparent',
                    'high' => ' bg-warning-transparent',
                    'medium' => 'bg-purple-transparent',
                    'low' => 'bg-success-transparent'
                ][$item?->notification?->priority] ?? 'bg-secondary';

                return '<span class="badge  ln-6 ' . $priorityClass . '">' . ucfirst($item?->notification?->priority) . '</span>';
            })
            ->addColumn('type_badge', function ($item) {
                $typeClass = [
                    'claim' => 'bg-outline-dark',
                    'facultative' => 'bg-outline-info',
                    'treaty' => 'bg-outline-success'
                ][$item?->notification?->notification_type] ?? 'bg-outline-secondary';

                $typeLabel = [
                    'claim' => 'Claim',
                    'facultative' => 'Facultative',
                    'treaty' => 'Treaty'
                ][$item?->notification?->notification_type] ?? ucfirst($item?->notification?->notification_type);

                return '<span class="badge ln-6 ' . $typeClass . '">' . $typeLabel . '</span>';
            })
            ->addColumn('actions', function ($item) {
                $actionButtons = '';
                if ($item?->notification?->status === 'pending') {
                    $actionButtons .= '<button type="button" class="btn btn-sm btn-success btn-sm-action approve-btn" data-id="' . $item->id . '">Approve <i class="bx bx-check"></i></button> ';
                }

                $actionButtons .= '<div id="dropdown-' . $item->id . '" class="btn-group my-0">
                <button type="button" id="dropdown-btn-' . $item->id . '" class="btn btn-icon btn-sm btn-light p-0" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul id="dropdown-menu-' . $item->id . '" class="dropdown-menu">
                    <li><a id="action-item-' . $item->id . '" class="dropdown-item review-btn" href="javascript:void(0);" data-endorsement_no="' . $item?->notification?->endorsement_no . '" data-type="' . $item?->notification?->notification_type . '" data-cover_no="' . $item?->notification?->cover_no . '" data-customer_id="' . $item?->notification?->customer_id . '" data-intimation_no="' . $item?->source?->source_column_data . '">Review</a></li>';

                if ($item?->notification?->status === 'pending') {
                    $actionButtons .= '<li><a id="reject-action-' . $item->id . '" class="dropdown-item decline-btn" href="javascript:void(0);" data-id="' . $item->id . '">Reject</a></li>';
                }
                if ($item?->notification?->status === 'approved') {
                    $actionButtons .= '<li><a id="approver-cpmment-action-' . $item->id . '" class="dropdown-item" href="javascript:void(0);">Approver comment</a></li>';
                }
                if ($item?->notification?->status === 'rejected') {
                    $actionButtons .= '<li><a id="rejected-comment-action-' . $item->id . '" class="dropdown-item" href="javascript:void(0);">Rejected comment</a></li>';
                }

                $actionButtons .= '</ul>
                </div>';
                return $actionButtons;
            })
            ->rawColumns(['status', 'priority_badge', 'type_badge', 'date', 'actions'])
            ->make(true);
    }

    public function bdApprovalAction(Request $request)
    {
        try {
            $prospect = PipelineOpportunity::where('id', $request->id)->firstOrFail();
            $handover = HandoverApproval::where('prospect_id', $prospect->opportunity_id)->first();

            $message = null;

            if ($request->type == 'approve') {
                $handover->update([
                    'approval_status' => $request->action,
                    'approval_comment' => $request->comment,
                ]);
                $message = 'Approved successfully';
            }

            if ($request->type == 'decline') {
                $handover->update([
                    'approval_status' => $request->action,
                    'reason_for_rejection' => $request->comment,
                ]);
                $message = 'Rejected successfully';
            }

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => $message
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to process approval action'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
