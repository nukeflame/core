<?php

namespace App\Http\Controllers;

use App\Enums\SystemActionEnums;
use App\Events\ApprovalTrackerEvent;
use App\Http\Traits\ApprovalTrackerTrait;
use Carbon\Carbon;
use App\Models\GLBatch;
use Illuminate\Http\JsonResponse;
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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class ApprovalsController extends Controller
{
    use ApprovalTrackerTrait;

    const STATUS_PENDING = 'P';
    const STATUS_APPROVED = 'A';
    const STATUS_REJECTED = 'R';

    const NOTIFICATION_EXPIRY_DAYS = 7;
    const COMMENT_MIN_LENGTH = 7;
    const COMMENT_MAX_LENGTH = 500;

    public function index()
    {
        $userId = Auth::id();

        $counts = [
            'all' => $this->getApprovalCount($userId),
            'claim' => $this->getApprovalCount($userId, 'claim'),
            'fac' => $this->getApprovalCount($userId, 'facultative'),
            'treaty' => $this->getApprovalCount($userId, 'treaty'),
            'pending' => $this->getApprovalCount($userId, null, self::STATUS_PENDING),
        ];

        return view('approvals.notifications', [
            'approvals' => [],
            'counts' => $counts
        ]);
    }

    private function getApprovalCount($userId, $notificationType = null, $status = null)
    {
        $query = ApprovalsTracker::where('approver', $userId);

        if ($notificationType) {
            $query->whereHas('notification', function ($q) use ($notificationType) {
                $q->where('notification_type', $notificationType);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->count();
    }

    public function sendForApproval(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $this->validateApprovalRequest($request);

            $user = Auth::user();
            $username = $user->user_name ?? $user->name ?? 'System';

            $approvalAction = SystemProcessAction::findOrFail($validated['process_action']);

            $link_id = $this->getNextIdSafe('approval_source_link');
            $approval_id = $this->getNextIdSafe('approvals_tracker');

            $result = $this->processApprovalAction($request, $approvalAction);

            if (empty($result['data']) || empty($result['source_data'])) {
                throw new Exception('Failed to process approval data');
            }

            $approval = $this->createApproval(
                $approval_id,
                $request,
                $result['data'],
                $username
            );

            $this->createSourceLinks(
                $link_id,
                $approval_id,
                $request,
                $result['source_data'],
                $username
            );

            $this->updateSourceTables($result['source_data']);

            $approvalNotice = $this->createNotification(
                $request,
                $approvalAction,
                $result['message'],
                $result['data'],
                $approval
            );

            event(new ApprovalTrackerEvent(
                $approvalNotice,
                'create',
                $user,
                $approval
            ));

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Approval request sent successfully',
                'data' => [
                    'approval_id' => $approval_id,
                    'approver' => User::find($request->approver)->name,
                    'priority' => $request->priority,
                ]
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Failed to process approval request. Please try again.',
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function reEscalate(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'approval_id' => 'required|integer|exists:approvals_tracker,id',
            ]);

            $approval = ApprovalsTracker::with(['notification'])
                ->findOrFail($validated['approval_id']);

            if ($approval->status !== self::STATUS_PENDING) {
                throw new Exception('Only pending approvals can be re-escalated');
            }

            $existingNotification = $approval->notification;
            $approverId = (int) $approval->approver;
            $username = Auth::user()->name ?? Auth::user()->user_name ?? 'System';

            Notification::create([
                'created_by' => $approverId,
                'updated_by' => $approverId,
                'title' => $existingNotification->title ?? 'Re-escalated Approval Request',
                'link' => '/approvals',
                'message' => $existingNotification->message ?? 'This approval request has been re-escalated.',
                'type' => $existingNotification->type ?? 'verify_cover',
                'effective_from' => Carbon::now(),
                'expired_at' => Carbon::now()->addDays(self::NOTIFICATION_EXPIRY_DAYS),
                'notification_type' => $existingNotification->notification_type ?? 'general',
                'status' => 'pending',
                'priority' => $approval->priority ?? 'low',
                'amount' => $existingNotification->amount ?? 0,
                'client' => $existingNotification->client ?? null,
                'cover_no' => $existingNotification->cover_no ?? null,
                'endorsement_no' => $existingNotification->endorsement_no ?? null,
                'customer_id' => $existingNotification->customer_id ?? null,
                'underwriter' => $username,
                'approval_tracker_id' => $approval->id,
            ]);

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_OK,
                'success' => true,
                'message' => 'Approval re-escalated successfully',
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'success' => false,
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function validateApprovalRequest(Request $request)
    {
        return $request->validate([
            'process' => [
                'required',
                'exists:system_process,id',
            ],
            'process_action' => [
                'required',
                'exists:system_process_action,id',
                function ($attribute, $value, $fail) use ($request) {
                    $action = SystemProcessAction::find($value);
                    if ($action && $action->process_id != $request->process) {
                        $fail('Process action does not belong to selected process');
                    }
                },
            ],
            'approver' => [
                'required',
                'exists:users,id',
                'different:' . Auth::id(), // Can't approve own requests
            ],
            'comment' => [
                'required',
                'string',
                'min:' . self::COMMENT_MIN_LENGTH,
                'max:' . self::COMMENT_MAX_LENGTH,
                'regex:/^[\p{L}\p{N}\s\.,!?\-()]+$/u', // Sanitize input
            ],
            'priority' => 'required|in:critical,high,medium,low',
            'endorsement_no' => 'required_if:action_type,verify_cover',
            'intimation_no' => 'required_if:action_type,verify_claim',
            'batch_no' => 'required_if:action_type,verify-glbatch',
            'requisition_no' => 'required_if:action_type,authorize-requisition,approve-requisition',
        ], [
            'process.required' => 'Process is required',
            'process.exists' => 'Invalid process selected',
            'process_action.required' => 'Process action is required',
            'process_action.exists' => 'Invalid process action selected',
            'approver.required' => 'Please select an approver',
            'approver.exists' => 'Selected approver does not exist',
            'approver.different' => 'You cannot approve your own requests',
            'comment.required' => 'Comment is required',
            'comment.min' => 'Comment must be at least ' . self::COMMENT_MIN_LENGTH . ' characters',
            'comment.max' => 'Comment cannot exceed ' . self::COMMENT_MAX_LENGTH . ' characters',
            'comment.regex' => 'Comment contains invalid characters',
            'priority.required' => 'Priority is required',
            'priority.in' => 'Invalid priority selected',
        ]);
    }

    private function processApprovalAction(Request $request, SystemProcessAction $approvalAction): array
    {
        return match ($approvalAction->nice_name) {
            SystemActionEnums::VERIFY_COVER_PROCESS => $this->processVerifyCover($request),
            SystemActionEnums::VERIFY_CLAIM_INTIMATION_PROCESS => $this->processVerifyClaim($request),
            'verify-glbatch' => $this->processVerifyGLBatch($request),
            'authorize-requisition' => $this->processAuthorizeRequisition($request),
            'approve-requisition' => $this->processApproveRequisition($request),
            default => throw new Exception("Unsupported approval action: {$approvalAction->nice_name}"),
        };
    }

    private function createApproval(
        int $approval_id,
        Request $request,
        array $data,
        string $username
    ): ApprovalsTracker {
        return ApprovalsTracker::create([
            'id' => $approval_id,
            'process_id' => $request->process,
            'process_action' => $request->process_action,
            'approver' => $request->approver,
            'comment' => $request->comment,
            'priority' => $request->priority,
            'status' => self::STATUS_PENDING,
            'data' => json_encode($data),
            'created_by' => $username,
            'updated_by' => $username,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSourceLinks(
        int $link_id,
        int $approval_id,
        Request $request,
        array $source_data,
        string $username
    ): void {
        $sourceLinks = collect($source_data)->map(function ($source_d, $index) use ($link_id, $approval_id, $request, $username) {
            return [
                'id' => $link_id + $index,
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
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        ApprovalSourceLink::insert($sourceLinks);
    }

    private function updateSourceTables(array $source_data): void
    {
        foreach ($source_data as $source_d) {
            $this->updateSourceTable($source_d);
        }
    }

    private function createNotification(
        Request $request,
        SystemProcessAction $approvalAction,
        array $message,
        array $data,
        ApprovalsTracker $approval
    ): Notification {
        $user = User::findOrFail($request->approver);

        return Notification::create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'title' => $message['title'],
            'link' => '/approvals',
            'message' => $message['text'],
            'type' => $approvalAction->nice_name,
            'effective_from' => Carbon::now(),
            'expired_at' => Carbon::now()->addDays(self::NOTIFICATION_EXPIRY_DAYS),
            'notification_type' => $data['type'] ?? 'general',
            'status' => 'pending',
            'priority' => $request->priority,
            'amount' => $data['amount'] ?? 0,
            'client' => $data['customer'] ?? null,
            'cover_no' => $data['cover_no'] ?? null,
            'endorsement_no' => $data['endorsement_no'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'underwriter' => Auth::user()->name,
            'approval_tracker_id' => $approval->id,
        ]);
    }

    private function updateSourceTable(array $source_d): void
    {
        if (empty($source_d['source_approval_column'])) {
            return;
        }

        $approval_columns = array_map('trim', explode(',', $source_d['source_approval_column']));

        $update_data = collect($approval_columns)
            ->mapWithKeys(fn($column) => [$column => self::STATUS_PENDING])
            ->toArray();

        if (!empty($update_data)) {
            DB::table($source_d['source_table'])
                ->where($source_d['source_column_name'], $source_d['source_column_data'])
                ->update($update_data);
        }
    }

    private function getNextIdSafe(string $table): int
    {
        return DB::transaction(function () use ($table) {
            $result = DB::selectOne(
                "SELECT COALESCE(MAX(id), 0) + 1 as next_id FROM {$table}"
            );

            return (int) $result->next_id;
        });
    }

    private function processVerifyCover(Request $request): array
    {
        if (!$request->has('endorsement_no')) {
            throw new Exception('Endorsement number is required for cover verification');
        }

        $cover = CoverRegister::with(['customer:customer_id,name'])
            ->select(
                'customer_id',
                'cover_no',
                'endorsement_no',
                'type_of_bus',
                'no_of_installments',
                'total_sum_insured',
                'verified'
            )
            ->where('endorsement_no', $request->endorsement_no)
            ->firstOrFail();

        $existingApproval = ApprovalSourceLink::where('source_table', 'cover_register')
            ->where('source_column_name', 'endorsement_no')
            ->where('source_column_data', $cover->endorsement_no)
            ->whereHas('approval', function ($query) {
                $query->where('status', self::STATUS_PENDING);
            })
            ->exists();

        if ($existingApproval) {
            throw new Exception('This cover is already pending approval');
        }

        if ($cover->verified === self::STATUS_APPROVED) {
            throw new Exception('This cover has already been verified');
        }

        $data = [
            'cover_no' => $cover->cover_no,
            'amount' => (float) $cover->total_sum_insured,
            'endorsement_no' => $cover->endorsement_no,
            'business_type' => $cover->type_of_bus,
            'no_of_instalments' => $cover->no_of_installments,
            'customer' => $cover->customer->name ?? 'N/A',
            'customer_id' => $cover->customer_id,
            'type' => 'facultative'
        ];

        $source_data[] = [
            'source_table' => 'cover_register',
            'source_column_name' => 'endorsement_no',
            'source_approval_column' => 'verified',
            'source_approval_by_column' => 'verified_by',
            'source_approval_at_column' => 'verified_at',
            'source_column_data' => $cover->endorsement_no,
        ];

        $user = Auth::user();
        $username = $user->name ?? $user->user_name ?? 'System';

        $message = [
            'text' => "Cover No: <b>" . e($cover->cover_no) . "</b> (Endorsement: " . e($cover->endorsement_no) . ") has been sent for verification. Please review and approve.",
            'title' => "New Cover Verification Request from " . e($username)
        ];

        return compact('data', 'source_data', 'message');
    }

    public function approvalAction(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'id' => 'required|exists:approvals_tracker,id',
                'action' => 'required|in:' . self::STATUS_APPROVED . ',' . self::STATUS_REJECTED,
                'comment' => 'required|string|min:' . self::COMMENT_MIN_LENGTH . '|max:' . self::COMMENT_MAX_LENGTH,
            ]);

            $approval = ApprovalsTracker::with('sourceLinks')->findOrFail($validated['id']);

            if ($approval->approver != Auth::id()) {
                throw new Exception('You are not authorized to act on this approval');
            }

            if ($approval->status !== self::STATUS_PENDING) {
                throw new Exception('This approval has already been ' .
                    ($approval->status === self::STATUS_APPROVED ? 'approved' : 'rejected'));
            }

            $user = Auth::user();
            $username = $user->user_name ?? $user->name ?? 'System';

            $approval->update([
                'status' => $request->action,
                'approver_comment' => $request->comment,
                'updated_by' => $username,
                'actioned_at' => now(),
            ]);

            foreach ($approval->sourceLinks as $sourceLink) {
                $this->updateSourceTableStatus($sourceLink, $request->action, $username);
            }

            $notificationStatus = [
                self::STATUS_PENDING => 'pending',
                self::STATUS_APPROVED => 'approved',
                self::STATUS_REJECTED => 'rejected'
            ][$request->action] ?? 'pending';

            Notification::where('approval_tracker_id', $approval->id)
                ->update(['status' => $notificationStatus]);

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_OK,
                'message' => 'Approval ' . ($request->action === self::STATUS_APPROVED ? 'approved' : 'rejected') . ' successfully',
                'data' => [
                    'approval_id' => $approval->id,
                    'status' => $notificationStatus,
                ]
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => config('app.debug') ? $e->getMessage() : 'Failed to process approval action',
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function updateSourceTableStatus(
        ApprovalSourceLink $sourceLink,
        string $action,
        string $username
    ): void {
        $updateData = [];

        if ($sourceLink->source_approval_column) {
            $columns = array_map('trim', explode(',', $sourceLink->source_approval_column));
            foreach ($columns as $column) {
                $updateData[$column] = $action;
            }
        }

        if ($sourceLink->source_approval_by_column) {
            $updateData[$sourceLink->source_approval_by_column] = $username;
        }

        if ($sourceLink->source_approval_at_column) {
            $updateData[$sourceLink->source_approval_at_column] = now();
        }

        if (!empty($updateData)) {
            DB::table($sourceLink->source_table)
                ->where($sourceLink->source_column_name, $sourceLink->source_column_data)
                ->update($updateData);
        }
    }

    private function processAuthorizeRequisition(Request $request): array
    {
        if (!$request->has('requisition_no')) {
            throw new Exception('Requisition number is required');
        }

        $data = [
            'requisition_no' => $request->requisition_no,
            'action' => 'Requisition Authorization',
            'comment' => $request->comment,
            'amount' => 0,
            'type' => 'requisition'
        ];

        $source_data[] = [
            'source_table' => 'cbrequisitions',
            'source_column_name' => 'requisition_no',
            'source_approval_column' => 'authorized_flag',
            'source_approval_by_column' => 'authorized_by',
            'source_approval_at_column' => 'authorized_date',
            'source_column_data' => $request->requisition_no,
        ];

        $user = Auth::user();
        $username = $user->name ?? $user->user_name ?? 'System';

        $message = [
            'text' => "Requisition No: <b>" . e($request->requisition_no) . "</b> requires authorization.",
            'title' => "New Requisition Authorization Request from " . e($username)
        ];

        return compact('data', 'source_data', 'message');
    }

    private function processApproveRequisition(Request $request): array
    {
        if (!$request->has('requisition_no')) {
            throw new Exception('Requisition number is required');
        }

        $data = [
            'requisition_no' => $request->requisition_no,
            'action' => 'Requisition Approval',
            'comment' => $request->comment,
            'amount' => 0,
            'type' => 'requisition'
        ];

        $source_data[] = [
            'source_table' => 'cbrequisitions',
            'source_column_name' => 'requisition_no',
            'source_approval_column' => 'approved_flag',
            'source_approval_by_column' => 'approved_by',
            'source_approval_at_column' => 'approved_date',
            'source_column_data' => $request->requisition_no,
        ];

        $user = Auth::user();
        $username = $user->name ?? $user->user_name ?? 'System';

        $message = [
            'text' => "Requisition No: <b>" . e($request->requisition_no) . "</b> requires approval.",
            'title' => "New Requisition Approval Request from " . e($username)
        ];

        return compact('data', 'source_data', 'message');
    }

    private function processVerifyClaim(Request $request): array
    {
        if (!$request->has('intimation_no')) {
            throw new Exception('Intimation number is required for claim verification');
        }

        $claim = ClaimNtfRegister::with(['customer:customer_id,name'])
            ->select(
                'customer_id',
                'cover_no',
                'endorsement_no',
                'type_of_bus',
                'intimation_no',
                'date_of_loss',
                'insured_name',
                'reserve_amount',
                'approval_status',
                'reserve_approval_status'
            )
            ->where('intimation_no', $request->intimation_no)
            ->firstOrFail();

        $amount = 0;
        $source_approval_column = '';

        if ($request->has('process_type')) {
            if ($request->process_type == 'reserve') {
                if ($claim->reserve_approval_status === self::STATUS_APPROVED) {
                    throw new Exception('Reserve for this claim has already been approved');
                }

                $amount = (float) $claim->reserve_amount;
                $source_approval_column = 'reserve_approval_status';
            } elseif ($request->process_type == 'claim') {
                if ($claim->approval_status === self::STATUS_APPROVED) {
                    throw new Exception('This claim has already been approved');
                }

                $other_perils = ClaimNtfPeril::where('intimation_no', $request->intimation_no)
                    ->where('dr_cr_note_no', 0)
                    ->where('dr_cr', '!=', 'CR')
                    ->sum('basic_amount');

                $salvage = ClaimNtfPeril::where('intimation_no', $request->intimation_no)
                    ->where('dr_cr_note_no', 0)
                    ->where('dr_cr', 'CR')
                    ->sum('basic_amount');

                $amount = $other_perils - $salvage;
                $source_approval_column = 'approval_status,reserve_approval_status';
            }
        }

        $data = [
            'cover_no' => $claim->cover_no,
            'amount' => $amount,
            'endorsement_no' => $claim->endorsement_no,
            'no_of_instalments' => 0,
            'type' => 'claim',
            'intimation_no' => $claim->intimation_no,
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

        $user = Auth::user();
        $username = $user->name ?? $user->user_name ?? 'System';

        $message = [
            'text' => "Claim Intimation No: <b>" . e($claim->intimation_no) . "</b> for Cover No: <b>" . e($claim->cover_no) . "</b> has been sent for approval. Please check and review.",
            'title' => "New Claim Approval Request from " . e($username)
        ];

        return compact('data', 'source_data', 'message');
    }

    // public function approvalDatatable(Request $request)
    // {
    //     $userId = Auth::id();

    //     $query = ApprovalsTracker::with(['source', 'notification'])
    //         ->where('approver', $userId)
    //         ->orderBy('created_at', 'desc');

    //     if ($request->type && $request->type !== 'all') {
    //         $notificationTypeMap = [
    //             'fac' => 'facultative',
    //             'claim' => 'claim',
    //             'treaty' => 'treaty'
    //         ];

    //         if (isset($notificationTypeMap[$request->type])) {
    //             $query->whereHas('notification', function ($q) use ($notificationTypeMap, $request) {
    //                 $q->where('notification_type', $notificationTypeMap[$request->type]);
    //             });
    //         }
    //     }

    //     $approvals = $query->get();

    //     return DataTables::of($approvals)
    //         ->addColumn('client', fn($item) => e($item?->notification?->client))
    //         ->addColumn('amount', fn($item) => number_format($item?->notification?->amount, 2))
    //         ->addColumn('title', function ($item) {
    //             $typeClass = [
    //                 'claim' => 'Claim',
    //                 'facultative' => 'Fac',
    //                 'treaty' => 'Treaty'
    //             ][$item?->notification?->notification_type] ?? '';

    //             return e($typeClass) . ' #' . e($item?->notification?->cover_no);
    //         })
    //         ->addColumn('comment', fn($item) => e($item?->comment))
    //         ->addColumn('date', fn($item) => $item?->notification?->created_at)
    //         ->addColumn('status', function ($item) {
    //             $status = $item?->notification?->status ?? 'pending';

    //             $statusClass = [
    //                 'pending' => 'bg-warning-gradient',
    //                 'approved' => 'bg-success-gradient',
    //                 'rejected' => 'bg-danger-gradient'
    //             ][$status] ?? 'bg-secondary';

    //             $statusIcon = [
    //                 'pending' => '<i class="bx ln-6 bx-time"></i>',
    //                 'approved' => '<i class="bx ln-6 bx-check"></i>',
    //                 'rejected' => '<i class="bx ln-6 bx-x"></i>'
    //             ][$status] ?? '';

    //             return '<span class="badge ln-6 badge-sm-action ' . e($statusClass) . '">'
    //                 . e(ucfirst($status)) . ' ' . $statusIcon . '</span>';
    //         })
    //         ->addColumn('priority_badge', function ($item) {
    //             $priority = $item?->notification?->priority ?? 'low';

    //             $priorityClass = [
    //                 'critical' => 'bg-danger-transparent',
    //                 'high' => 'bg-warning-transparent',
    //                 'medium' => 'bg-purple-transparent',
    //                 'low' => 'bg-success-transparent'
    //             ][$priority] ?? 'bg-secondary';

    //             return '<span class="badge ln-6 ' . e($priorityClass) . '">'
    //                 . e(ucfirst($priority)) . '</span>';
    //         })
    //         ->addColumn('type_badge', function ($item) {
    //             $type = $item?->notification?->notification_type ?? 'general';

    //             $typeClass = [
    //                 'claim' => 'bg-outline-dark',
    //                 'facultative' => 'bg-outline-info',
    //                 'treaty' => 'bg-outline-success'
    //             ][$type] ?? 'bg-outline-secondary';

    //             $typeLabel = [
    //                 'claim' => 'Claim',
    //                 'facultative' => 'Facultative',
    //                 'treaty' => 'Treaty'
    //             ][$type] ?? ucfirst($type);

    //             return '<span class="badge ln-6 ' . e($typeClass) . '">'
    //                 . e($typeLabel) . '</span>';
    //         })
    //         ->addColumn('actions', function ($item) {
    //             $actionButtons = '';
    //             $status = $item?->notification?->status ?? 'pending';

    //             if ($status === 'pending') {
    //                 $actionButtons .= '<button type="button" class="btn btn-sm btn-success btn-sm-action approve-btn" data-id="'
    //                     . e($item->id) . '">Approve <i class="bx bx-check"></i></button> ';
    //             }

    //             $actionButtons .= '<div id="dropdown-' . e($item->id) . '" class="btn-group my-0">
    //             <button type="button" id="dropdown-btn-' . e($item->id) . '" class="btn btn-icon btn-sm btn-light p-0" data-bs-toggle="dropdown" aria-expanded="false">
    //             <i class="bi bi-three-dots-vertical"></i>
    //             </button>
    //             <ul id="dropdown-menu-' . e($item->id) . '" class="dropdown-menu">
    //                 <li><a id="action-item-' . e($item->id) . '" class="dropdown-item review-btn" href="javascript:void(0);"
    //                     data-endorsement_no="' . e($item?->notification?->endorsement_no) . '"
    //                     data-type="' . e($item?->notification?->notification_type) . '"
    //                     data-cover_no="' . e($item?->notification?->cover_no) . '"
    //                     data-customer_id="' . e($item?->notification?->customer_id) . '"
    //                     data-intimation_no="' . e($item?->source?->source_column_data) . '">Review</a></li>';

    //             if ($status === 'pending') {
    //                 $actionButtons .= '<li><a id="reject-action-' . e($item->id) . '" class="dropdown-item decline-btn" href="javascript:void(0);" data-id="' . e($item->id) . '">Reject</a></li>';
    //             }

    //             if ($status === 'approved') {
    //                 $actionButtons .= '<li><a id="approver-comment-action-' . e($item->id) . '" class="dropdown-item" href="javascript:void(0);">Approver comment</a></li>';
    //             }

    //             if ($status === 'rejected') {
    //                 $actionButtons .= '<li><a id="rejected-comment-action-' . e($item->id) . '" class="dropdown-item" href="javascript:void(0);">Rejected comment</a></li>';
    //             }

    //             $actionButtons .= '</ul></div>';

    //             return $actionButtons;
    //         })
    //         ->rawColumns(['status', 'priority_badge', 'type_badge', 'date', 'actions'])
    //         ->make(true);
    // }

    private function processVerifyGLBatch(Request $request): array
    {
        if (!$request->has('batch_no')) {
            throw new Exception('Batch number is required for GL batch verification');
        }

        $batch = GLBatch::where('batch_no', trim($request->batch_no))->firstOrFail();

        $existingApproval = ApprovalSourceLink::where('source_table', 'glbatch')
            ->where('source_column_name', 'batch_no')
            ->where('source_column_data', $batch->batch_no)
            ->whereHas('approval', function ($query) {
                $query->where('status', self::STATUS_PENDING);
            })
            ->exists();

        if ($existingApproval) {
            throw new Exception('This GL batch is already pending approval');
        }

        $data = [
            'batch_no' => $batch->batch_no,
            'batch_name' => $batch->batch_description,
            'amount' => $batch->total_amount ?? 0,
            'type' => 'glbatch'
        ];

        $source_data[] = [
            'source_table' => 'glbatch',
            'source_column_name' => 'batch_no',
            'source_approval_column' => 'verified',
            'source_approval_by_column' => 'verified_by',
            'source_approval_at_column' => 'verified_at',
            'source_column_data' => $batch->batch_no,
        ];

        $user = Auth::user();
        $username = $user->name ?? $user->user_name ?? 'System';

        $message = [
            'text' => "GL Batch No: <b>" . e($batch->batch_no) . "</b> has been sent for verification.",
            'title' => "New GL Batch Verification Request from " . e($username)
        ];

        return compact('data', 'source_data', 'message');
    }

    public function bdApprovalAction(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'id' => 'required|exists:pipeline_opportunities,id',
                'type' => 'required|in:approve,decline',
                'action' => 'required',
                'comment' => 'required|string',
            ]);

            $prospect = PipelineOpportunity::findOrFail($validated['id']);
            $prospect->bd_status = 'Approved';
            $prospect->save();

            $handover = HandoverApproval::where('prospect_id', $prospect->opportunity_id)->firstOrFail();

            $message = $this->processBdApprovalAction($handover, $validated);

            DB::commit();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => $message,
                'data' => [
                    'handover_id' => $handover->id,
                    'approval_status' => $handover->approval_status,
                ]
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_NOT_FOUND,
                'message' => 'Record not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => config('app.debug') ? $e->getMessage() : 'Failed to process approval action',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function processBdApprovalAction(HandoverApproval $handover, array $validated): string
    {
        $userId = Auth::id();
        $timestamp = now();

        switch ($validated['type']) {
            case 'approve':

                $handover->update([
                    'approval_status' =>  1,
                    'intergrate' => false,
                    'approval_comment' => $validated['comment'],
                    'approved_by' => $userId,
                    'approved_at' => $timestamp,

                    'reason_for_rejection' => null,
                    'rejected_by' => null,
                    'rejected_at' => null,
                ]);

                return 'Handover approved successfully';

            case 'decline':

                $handover->update([
                    'approval_status' => 0,
                    'intergrate' => false,
                    'reason_for_rejection' => $validated['comment'],
                    'rejected_by' => $userId,
                    'rejected_at' => $timestamp,
                    'approval_comment' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                ]);

                return 'Handover rejected successfully';

            default:
                throw new Exception('Invalid approval type');
        }
    }

    public function getApprovalDetails($id)
    {
        try {
            $approval = ApprovalsTracker::with([
                'notification',
                'source',
                'processAction',
                'approverUser' => function ($query) {
                    $query->select('id', 'name', 'user_name', 'email');
                },
                'creator' => function ($query) {
                    $query->select('id', 'name', 'user_name');
                }
            ])->findOrFail($id);

            if ($approval->approver != Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to view this approval'
                ], 403);
            }

            $data = $approval->data;

            $statusMap = [
                'P' => 'pending',
                'A' => 'approved',
                'R' => 'rejected'
            ];

            $priorityMap = [
                'critical' => '<span class="priority-critical">Critical</span>',
                'high' => '<span class="priority-high">High</span>',
                'medium' => '<span class="priority-medium">Medium</span>',
                'low' => '<span class="priority-low">Low</span>',
            ];

            $statusHtml = $this->getStatusBadgeHtml($approval->notification->status ?? 'pending');

            $response = [
                'success' => true,
                'data' => [
                    'id' => $approval->id,
                    'title' => $this->buildTitle($approval, $data),
                    'data' => $approval->data,
                    'comment' => $approval->comment,
                    'approver_comment' => $approval->approver_comment,
                    'status' => $statusHtml,
                    'status_raw' => $statusMap[$approval->status] ?? 'pending',
                    'priority' => $approval->priority,
                    'priority_badge' => $priorityMap[$approval->priority] ?? $approval->priority,
                    'amount' => number_format($approval->notification->amount ?? 0, 2),
                    'client' => $approval->notification->client ?? 'N/A',
                    'date' => $approval->created_at->toIso8601String(),
                    'created_by' => $approval->created_by ?? 'Unknown',
                    'actioned_at' => $approval->actioned_at ? $approval->actioned_at->toIso8601String() : null,
                    'process_name' => $approval->processAction->name ?? 'N/A',
                    'notification_type' => $approval->notification->notification_type ?? 'general',
                    'approver' => [
                        'id' => $approval->approverUser->id ?? null,
                        'name' => $approval->approverUser->name ?? 'N/A',
                        'email' => $approval->approverUser->email ?? 'N/A'
                    ],
                    'creator' => [
                        'name' => $approval->creator->name ?? $approval->created_by ?? 'Unknown',
                        'avatar' => $this->getAvatarUrl($approval->creator ?? null)
                    ],
                    'source_info' => $approval->source ? [
                        'table' => $approval->source->source_table,
                        'identifier' => $approval->source->source_column_data
                    ] : null,
                    'timeline' => $this->buildTimeline($approval),
                    'risk_level' => $this->assessRiskLevel($approval, $data),
                    'validation_checks' => $this->performValidationChecks($approval, $data),
                ]
            ];

            return response()->json($response);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Approval not found'
            ], 404);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Failed to load approval details'
            ], 500);
        }
    }

    private function buildTitle(ApprovalsTracker $approval, array $data): string
    {
        $type = $data['type'] ?? 'general';

        switch ($type) {
            case 'facultative':
                return 'Fac #' . ($data['cover_no'] ?? 'N/A');
            case 'claim':
                return 'Claim #' . ($data['intimation_no'] ?? 'N/A');
            case 'treaty':
                return 'Treaty #' . ($data['treaty_no'] ?? 'N/A');
            case 'glbatch':
                return 'GL Batch #' . ($data['batch_no'] ?? 'N/A');
            case 'requisition':
                return 'Requisition #' . ($data['requisition_no'] ?? 'N/A');
            default:
                return 'Approval #' . $approval->id;
        }
    }

    private function getStatusBadgeHtml(string $status): string
    {
        $statusClasses = [
            'pending' => 'bg-warning-gradient',
            'approved' => 'bg-success-gradient',
            'rejected' => 'bg-danger-gradient'
        ];

        $statusIcons = [
            'pending' => '<i class="bx bx-time"></i>',
            'approved' => '<i class="bx bx-check"></i>',
            'rejected' => '<i class="bx bx-x"></i>'
        ];

        $class = $statusClasses[$status] ?? 'bg-secondary';
        $icon = $statusIcons[$status] ?? '';

        return '<span class="badge ' . e($class) . '">' . e(ucfirst($status)) . ' ' . $icon . '</span>';
    }

    private function getAvatarUrl($user): string
    {
        if (!$user) {
            return '/assets/images/user-avatar.png';
        }

        return $user->avatar_url ?? '/assets/images/user-avatar.png';
    }

    private function buildTimeline(ApprovalsTracker $approval): array
    {
        $timeline = [
            [
                'event' => 'Approval Requested',
                'user' => $approval->created_by,
                'timestamp' => $approval->created_at->toIso8601String(),
                'icon' => 'bx-plus-circle',
                'color' => 'primary'
            ]
        ];

        if ($approval->actioned_at) {
            $timeline[] = [
                'event' => $approval->status === 'A' ? 'Approved' : 'Rejected',
                'user' => $approval->approverUser->name ?? 'Unknown',
                'timestamp' => $approval->actioned_at->toIso8601String(),
                'icon' => $approval->status === 'A' ? 'bx-check-circle' : 'bx-x-circle',
                'color' => $approval->status === 'A' ? 'success' : 'danger'
            ];
        }

        return $timeline;
    }

    /**
     * Assess risk level based on amount and other factors
     */
    private function assessRiskLevel(ApprovalsTracker $approval, array $data): string
    {
        $amount = $approval->notification->amount ?? 0;

        // Define thresholds (customize based on your business rules)
        if ($amount > 10000000) { // 10M
            return 'high';
        } elseif ($amount > 5000000) { // 5M
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Perform validation checks on the approval
     */
    private function performValidationChecks(ApprovalsTracker $approval, array $data): array
    {
        $checks = [];

        // Check 1: Amount validation
        $amount = $approval->notification->amount ?? 0;
        $checks[] = [
            'name' => 'Amount Validation',
            'status' => $amount > 0 ? 'pass' : 'warning',
            'message' => $amount > 0
                ? 'Amount is valid: KES ' . number_format($amount, 2)
                : 'Amount is zero or invalid',
            'icon' => $amount > 0 ? 'bx-check-circle' : 'bx-error-circle'
        ];

        // Check 2: Required documents (if applicable)
        $type = $data['type'] ?? null;
        if ($type === 'facultative' && isset($data['cover_no'])) {
            $checks[] = [
                'name' => 'Cover Reference',
                'status' => 'pass',
                'message' => 'Cover number present: ' . $data['cover_no'],
                'icon' => 'bx-check-circle'
            ];
        }

        // Check 3: Time sensitivity
        $ageInHours = now()->diffInHours($approval->created_at);
        $checks[] = [
            'name' => 'Time Sensitivity',
            'status' => $ageInHours < 48 ? 'pass' : 'warning',
            'message' => $ageInHours < 48
                ? "Within SLA ({$ageInHours} hours old)"
                : "Overdue ({$ageInHours} hours old)",
            'icon' => $ageInHours < 48 ? 'bx-time' : 'bx-error'
        ];

        // Check 4: Priority alignment
        $priorityAmountMatch = true;
        if ($approval->priority === 'critical' && $amount < 1000000) {
            $priorityAmountMatch = false;
        }

        $checks[] = [
            'name' => 'Priority Alignment',
            'status' => $priorityAmountMatch ? 'pass' : 'info',
            'message' => $priorityAmountMatch
                ? 'Priority matches amount threshold'
                : 'Priority may not match typical amount threshold',
            'icon' => 'bx-info-circle'
        ];

        return $checks;
    }

    public function approvalDatatable(Request $request)
    {
        $userId = Auth::id();

        $query = ApprovalsTracker::with(['source', 'notification', 'creator'])
            ->where('approver', $userId)
            ->orderBy('created_at', 'desc');

        if ($request->type && $request->type !== 'all') {
            $notificationTypeMap = [
                'fac' => 'facultative',
                'claim' => 'claim',
                'treaty' => 'treaty'
            ];

            if (isset($notificationTypeMap[$request->type])) {
                $query->whereHas('notification', function ($q) use ($notificationTypeMap, $request) {
                    $q->where('notification_type', $notificationTypeMap[$request->type]);
                });
            }
        }

        if ($request->status_filter) {
            $statusMap = [
                'pending' => 'P',
                'approved' => 'A',
                'rejected' => 'R'
            ];

            if (isset($statusMap[$request->status_filter])) {
                $query->where('status', $statusMap[$request->status_filter]);
            }
        }

        if ($request->priority_filter) {
            $query->where('priority', $request->priority_filter);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $approvals = $query->get();

        return DataTables::of($approvals)
            ->addColumn('client', fn($item) => e($item?->notification?->client ?? 'N/A'))
            ->addColumn('amount', fn($item) => number_format($item?->notification?->amount ?? 0, 2))
            ->addColumn('title', function ($item) {
                return $this->buildTitle($item, $item->data);
            })
            ->addColumn('comment', fn($item) => e($item?->comment))
            ->addColumn('date', fn($item) => $item?->created_at?->toIso8601String())
            ->addColumn('created_by', fn($item) => e($item->created_by ?? 'Unknown'))
            ->addColumn('data', fn($item) => $item->data)
            ->addColumn('status', function ($item) {
                return $this->getStatusBadgeHtml($item?->notification?->status ?? 'pending');
            })
            ->addColumn('status_raw', function ($item) {
                $statusMap = ['P' => 'pending', 'A' => 'approved', 'R' => 'rejected'];
                return $statusMap[$item->status] ?? 'pending';
            })
            ->addColumn('priority_badge', function ($item) {
                $priority = $item->priority ?? 'low';

                $priorityClasses = [
                    'critical' => 'priority-critical',
                    'high' => 'priority-high',
                    'medium' => 'priority-medium',
                    'low' => 'priority-low'
                ];

                $class = $priorityClasses[$priority] ?? 'priority-low';

                return '<span class="' . e($class) . '">' . e(ucfirst($priority)) . '</span>';
            })
            ->addColumn('type_badge', function ($item) {
                $type = $item?->notification?->notification_type ?? 'general';

                $typeClasses = [
                    'claim' => 'badge bg-dark',
                    'facultative' => 'badge bg-info',
                    'treaty' => 'badge bg-success',
                    'general' => 'badge bg-secondary'
                ];

                $typeLabels = [
                    'claim' => 'Claim',
                    'facultative' => 'Facultative',
                    'treaty' => 'Treaty',
                    'general' => 'General'
                ];

                $class = $typeClasses[$type] ?? 'badge bg-secondary';
                $label = $typeLabels[$type] ?? ucfirst($type);

                return '<span class="' . e($class) . '">' . e($label) . '</span>';
            })
            ->addColumn('actions', function ($item) {
                $status = $item?->notification?->status ?? 'pending';
                $data = $item->data;

                $actionButtons = '<div class="btn-group" role="group">';

                $actionButtons .= '<button type="button" class="btn btn-sm btn-primary" onclick="openReviewModal(' . $item->id . ')" title="Full Review">
                                        <i class="bx bx-show-alt"></i>
                                    </button>';

                if ($status === 'pending') {
                    $actionButtons .= '<button type="button" class="btn btn-sm btn-success approve-btn" data-id="' . $item->id . '" title="Quick Approve">
                                            <i class="bx bx-check"></i>
                                        </button>';

                    // $actionButtons .= '<button type="button" class="btn btn-sm btn-danger decline-btn" data-id="' . $item->id . '" title="Quick Reject">
                    //                         <i class="bx bx-x"></i>
                    //                     </button>';
                }

                if (isset($data['endorsement_no']) || isset($data['intimation_no'])) {
                    // $actionButtons .= '<button type="button" class="btn btn-sm btn-info review-btn"
                    // data-endorsement_no="' . e($data['endorsement_no'] ?? '') . '"
                    // data-type="' . e($data['type'] ?? '') . '"
                    // data-cover_no="' . e($data['cover_no'] ?? '') . '"
                    // data-customer_id="' . e($data['customer_id'] ?? '') . '"
                    // data-intimation_no="' . e($data['intimation_no'] ?? '') . '"
                    // title="View Source Document">
                    // <i class="bx bx-file"></i></button>';
                }

                $actionButtons .= '</div>';

                return $actionButtons;
            })
            ->rawColumns(['status', 'priority_badge', 'type_badge', 'actions'])
            ->make(true);
    }
}
