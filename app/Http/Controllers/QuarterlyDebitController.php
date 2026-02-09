<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CoverRipart;
use App\Models\DebitNote;
use App\Services\S3AttachmentHandler;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class QuarterlyDebitController extends Controller
{
    protected $s3Service;

    public function __construct(S3AttachmentHandler $s3Service)
    {
        $this->s3Service = $s3Service;
    }

    public function index(Request $request, $coverId)
    {
        $cover = $this->getCover($coverId);

        if (! $cover) {
            abort(404, 'Cover not found');
        }

        $customer = $this->getCustomer($cover->customer_id ?? null);
        $cedantDetails = $this->getCedantDetails($cover);

        return view('treaty.quarterly-debit-statement', compact(
            'cover',
            'customer',
            'cedantDetails'
        ));
    }

    public function getDebitItems(Request $request): JsonResponse
    {
        try {
            $coverNo = $request->input('cover_no');
            $endorsementNo = $request->input('endorsement_no');

            if (empty($coverNo) || empty($endorsementNo)) {
                return response()->json([
                    'draw' => intval($request->input('draw', 1)),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'cover_no and endorsement_no are required',
                ], 400);
            }

            $draw = $request->input('draw', 1);
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $search = $request->input('search.value', '');
            $orderColumn = $request->input('order.0.column', 2);
            $orderDir = $request->input('order.0.dir', 'desc');

            $columns = [
                0 => 'tdi.id',
                1 => 'tdi.item_no',
                2 => 'dn.posting_date',
                3 => 'tdi.class_group_code',
                4 => 'tdi.class_code',
                5 => 'tdi.amount',
                6 => 'tdi.line_rate',
                7 => 'tdi.net_amount',
                8 => 'dn.net_amount',
                9 => 'dn.status',
            ];

            $orderBy = $columns[$orderColumn] ?? 'dn.posting_date';

            $query = DB::table('debit_note_items as tdi')
                ->join('debit_notes as dn', 'tdi.debit_note_id', '=', 'dn.id')
                ->leftJoin('treaty_item_codes as tc', 'tdi.item_code', '=', 'tc.item_code')
                ->leftJoin('class_groups as cg', 'tdi.class_group_code', '=', 'cg.group_code')
                ->leftJoin('reinclass_premtypes as c', function ($join) {
                    $join->on('tdi.class_code', '=', 'c.premtype_code')
                        ->on('tdi.class_group_code', '=', 'c.reinclass');
                })
                ->where('dn.cover_no', $coverNo)
                ->where('dn.endorsement_no', $endorsementNo)
                ->select([
                    'tdi.id',
                    'tdi.item_code',
                    'tdi.item_no',
                    'tdi.line_no',
                    'tdi.description',
                    'tdi.class_group_code',
                    'cg.group_name',
                    'tdi.class_code',
                    'c.premtype_name as class_name',
                    'tdi.line_rate',
                    'tdi.amount as gross_amount',
                    'tdi.ledger',
                    'dn.debit_note_no',
                    'dn.posting_date',
                    'dn.commission_amount',
                    'tdi.net_amount',
                    'tdi.status',
                    'tc.description',
                ]);

            $totalRecords = (clone $query)->count();

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('tdi.item_code', 'like', "%{$search}%")
                        ->orWhere('tdi.description', 'like', "%{$search}%")
                        ->orWhere('tdi.class_group_code', 'like', "%{$search}%")
                        ->orWhere('tdi.class_code', 'like', "%{$search}%")
                        ->orWhere('tdi.status', 'like', "%{$search}%");
                });
            }

            $filteredRecords = (clone $query)->count();

            $data = $query
                ->orderBy($orderBy, $orderDir)
                ->skip($start)
                ->take($length)
                ->get()
                ->map(function ($row) {
                    $lineRate = (int) $row->line_rate == 0 ? '--' : $row->line_rate;

                    return [
                        'id' => $row->id,
                        'item_code' => $row->item_code,
                        'line_no' => $row->line_no,
                        'item_no' => $row->item_no,
                        'description' => $row->description,
                        'class_group_code' => $row->class_group_code,
                        'class_code' => $row->class_code,
                        'line_rate' => $lineRate,
                        'ledger' => $row->ledger,
                        'debit_note_no' => $row->debit_note_no,
                        'posting_date' => $row->posting_date,
                        'gross_amount' => $row->gross_amount,
                        'commission_amount' => $row->commission_amount,
                        'net_amount' => $row->net_amount,
                        'treaty_type' => 'surplus',
                        'status' => $row->status,
                        'class_name' => $row->class_name,
                        'group_name' => $row->group_name,
                        'transaction_type' => $row->description,
                        'status_badge' => $this->getStatusBadge($row->status),
                    ];
                });

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to fetch debit items: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getCreditItems(Request $request): JsonResponse
    {
        try {
            $coverNo = $request->input('cover_no');
            $endorsementNo = $request->input('endorsement_no');

            if (empty($coverNo) || empty($endorsementNo)) {
                return response()->json([
                    'draw' => intval($request->input('draw', 1)),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'cover_no and endorsement_no are required',
                ], 400);
            }

            $draw = $request->input('draw', 1);
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $search = $request->input('search.value', '');
            $orderColumn = $request->input('order.0.column', 2);
            $orderDir = $request->input('order.0.dir', 'desc');

            $columns = [
                0 => 'tci.id',
                1 => 'tci.item_no',
                2 => 'cn.posting_date',
                3 => 'tci.class_group_code',
                4 => 'tci.class_code',
                5 => 'tci.amount',
                6 => 'tci.line_rate',
                7 => 'tci.net_amount',
                8 => 'cn.net_amount',
                9 => 'cn.status',
            ];

            $orderBy = $columns[$orderColumn] ?? 'cn.posting_date';

            $query = DB::table('credit_note_items as tci')
                ->join('credit_notes as cn', 'tci.credit_note_id', '=', 'cn.id')
                ->leftJoin('treaty_item_codes as tc', 'tci.item_code', '=', 'tc.item_code')
                ->leftJoin('class_groups as cg', 'tci.class_group_code', '=', 'cg.group_code')
                ->leftJoin('reinclass_premtypes as c', function ($join) {
                    $join->on('tci.class_code', '=', 'c.premtype_code')
                        ->on('tci.class_group_code', '=', 'c.reinclass');
                })
                ->where('cn.cover_no', $coverNo)
                ->where('cn.endorsement_no', $endorsementNo)
                ->select([
                    'tci.id',
                    'tci.item_code',
                    'tci.item_no',
                    'tci.line_no',
                    'tci.description',
                    'tci.class_group_code',
                    'cg.group_name',
                    'tci.class_code',
                    'c.premtype_name as class_name',
                    'tci.line_rate',
                    'tci.amount as gross_amount',
                    'tci.ledger',
                    'cn.credit_note_no',
                    'cn.posting_date',
                    'cn.commission_amount',
                    'tci.net_amount',
                    'tci.status',
                    'tc.description as item_description',
                ]);

            $totalRecords = (clone $query)->count();

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('tci.item_code', 'like', "%{$search}%")
                        ->orWhere('tci.description', 'like', "%{$search}%")
                        ->orWhere('tci.class_group_code', 'like', "%{$search}%")
                        ->orWhere('tci.class_code', 'like', "%{$search}%")
                        ->orWhere('tci.status', 'like', "%{$search}%");
                });
            }

            $filteredRecords = (clone $query)->count();

            $data = $query
                ->orderBy($orderBy, $orderDir)
                ->skip($start)
                ->take($length)
                ->get()
                ->map(function ($row) {
                    $lineRate = (int) $row->line_rate == 0 ? '--' : $row->line_rate;

                    return [
                        'id' => $row->id,
                        'item_code' => $row->item_code,
                        'line_no' => $row->line_no,
                        'item_no' => $row->item_no,
                        'description' => $row->description,
                        'class_group_code' => $row->class_group_code,
                        'class_code' => $row->class_code,
                        'line_rate' => $lineRate,
                        'ledger' => $row->ledger,
                        'credit_note_no' => $row->credit_note_no,
                        'posting_date' => $row->posting_date,
                        'gross_amount' => $row->gross_amount,
                        'commission_amount' => $row->commission_amount,
                        'net_amount' => $row->net_amount,
                        'treaty_type' => 'surplus',
                        'status' => $row->status,
                        'class_name' => $row->class_name,
                        'group_name' => $row->group_name,
                        'transaction_type' => $row->description,
                        'status_badge' => $this->getStatusBadge($row->status),
                    ];
                });

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Failed to fetch credit items: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getStatusBadge(?string $status): string
    {
        if (empty($status)) {
            return '<span class="badge bg-secondary">Unknown</span>';
        }

        $badges = [
            'DRAFT' => '<span class="badge bg-secondary">Draft</span>',
            'SUBMITTED' => '<span class="badge bg-info">Submitted</span>',
            'APPROVED' => '<span class="badge bg-success">Approved</span>',
            'REJECTED' => '<span class="badge bg-danger">Rejected</span>',
            'CANCELLED' => '<span class="badge bg-warning">Cancelled</span>',
            'POSTED' => '<span class="badge bg-primary">Posted</span>',
        ];

        $normalizedStatus = strtoupper(trim($status));

        return $badges[$normalizedStatus] ?? '<span class="badge bg-secondary">' . e($status) . '</span>';
    }

    public function storeDebitItem(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cover_id' => 'required|integer|exists:treaty_covers,id',
            'treaty_type' => 'required|string|in:SURPLUS,QUOTA_SHARE,EXCESS_OF_LOSS',
            'item_date' => 'required|date',
            'class_group' => 'required|string|max:100',
            'class_name' => 'required|string|max:255',
            'reinsurer_id' => 'required|integer|exists:reinsurers,id',
            'gross_premium' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|string|in:pending,paid,overdue',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $grossPremium = $request->input('gross_premium');
            $commissionRate = $request->input('commission_rate');
            $commissionAmount = $grossPremium * ($commissionRate / 100);
            $netAmount = $grossPremium - $commissionAmount;

            $itemNumber = $this->generateItemNumber();

            $id = DB::table('treaty_debit_items')->insertGetId([
                'cover_id' => $request->input('cover_id'),
                'item_number' => $itemNumber,
                'treaty_type' => $request->input('treaty_type'),
                'item_date' => $request->input('item_date'),
                'class_group' => $request->input('class_group'),
                'class_name' => $request->input('class_name'),
                'reinsurer_id' => $request->input('reinsurer_id'),
                'gross_premium' => $grossPremium,
                'commission_rate' => $commissionRate,
                'commission_amount' => $commissionAmount,
                'net_amount' => $netAmount,
                'status' => $request->input('status', 'pending'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Debit item created successfully',
                'data' => ['id' => $id, 'item_number' => $itemNumber],
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create debit item',
            ], 500);
        }
    }

    public function showDebitItem($id): JsonResponse
    {
        try {
            $item = DB::table('treaty_debit_items as tdi')
                ->leftJoin('reinsurers as r', 'tdi.reinsurer_id', '=', 'r.id')
                ->where('tdi.id', $id)
                ->select([
                    'tdi.*',
                    'r.name as reinsurer',
                ])
                ->first();

            if (! $item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $item,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch item',
            ], 500);
        }
    }

    public function updateDebitItem(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'treaty_type' => 'required|string|in:SURPLUS,QUOTA_SHARE,EXCESS_OF_LOSS',
            'item_date' => 'required|date',
            'class_group' => 'required|string|max:100',
            'class_name' => 'required|string|max:255',
            'reinsurer_id' => 'required|integer|exists:reinsurers,id',
            'gross_premium' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|string|in:pending,paid,overdue',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $exists = DB::table('treaty_debit_items')->where('id', $id)->exists();
            if (! $exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found',
                ], 404);
            }

            $grossPremium = $request->input('gross_premium');
            $commissionRate = $request->input('commission_rate');
            $commissionAmount = $grossPremium * ($commissionRate / 100);
            $netAmount = $grossPremium - $commissionAmount;

            DB::table('treaty_debit_items')
                ->where('id', $id)
                ->update([
                    'treaty_type' => $request->input('treaty_type'),
                    'item_date' => $request->input('item_date'),
                    'class_group' => $request->input('class_group'),
                    'class_name' => $request->input('class_name'),
                    'reinsurer_id' => $request->input('reinsurer_id'),
                    'gross_premium' => $grossPremium,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'net_amount' => $netAmount,
                    'status' => $request->input('status', 'pending'),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Debit item updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item',
            ], 500);
        }
    }

    public function destroyDebitItem($id): JsonResponse
    {
        try {
            $deleted = DB::table('treaty_debit_items')->where('id', $id)->delete();

            if (! $deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Debit item deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item',
            ], 500);
        }
    }

    public function getReinsurers(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cover_no' => 'required|string',
                'endorsement_no' => 'required|string',
                'draw' => 'nullable|integer',
                'start' => 'nullable|integer',
                'length' => 'nullable|integer',
            ]);

            $coverNo = $validated['cover_no'];
            $endorsementNo = $validated['endorsement_no'];

            $draw = (int) $request->input('draw', 1);
            $start = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 10);

            $query = CoverRipart::where([
                'cover_no' => $coverNo,
                'endorsement_no' => $endorsementNo,
            ])->with('partner');

            $debit = DebitNote::where([
                'cover_no' => $coverNo,
                'endorsement_no' => $endorsementNo,
            ])->first();

            $recordsTotal = $query->count();
            $recordsFiltered = $recordsTotal;

            $reinsurers = $query
                ->skip($start)
                ->take($length)
                ->get();

            $data = $reinsurers->map(function ($rein) {
                return [
                    'id' => $rein->id,
                    'partner_no' => $rein->partner_no,
                    'name' => $rein->partner?->name ?? '',
                    'email' => $rein->partner?->email ?? '',
                    'share_percentage' => $rein->share ?? 0,
                    'gross_premium' => $rein->total_premium ?? 0,
                    'commission' => $rein->commission ?? 0,
                    'brokerage_amount' => $rein->brokerage_comm_amt ?? 0,
                    'premium_tax_amount' => $rein->prem_tax ?? 0,
                    'wht_amount' => $rein->wht_amt ?? 0,
                    'ri_tax' => $rein->ri_tax ?? 0,
                    'net_amount' => $rein->net_amount ?? 0,
                    'status' => 'active',
                ];
            });

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Invalid request parameters',
            ], 422);
        } catch (Exception $e) {

            return response()->json([
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while fetching data',
            ], 500);
        }
    }

    public function listReinsurers(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'data' => [],
            ], 500);
        }
    }

    public function getCedantDetailsApi($coverId): JsonResponse
    {
        try {
            $cover = $this->getCover($coverId);

            if (! $cover) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cover not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cedant details',
            ], 500);
        }
    }

    public function getDocuments(Request $request): JsonResponse
    {
        try {
            $coverNo = $request->input('cover_no');
            $endorsementNo = $request->input('endorsement_no');

            if (empty($coverNo) || empty($endorsementNo)) {
                return response()->json([
                    'draw' => intval($request->input('draw', 1)),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'cover_no and endorsement_no are required',
                ], 400);
            }

            $draw = $request->input('draw', 1);
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $search = $request->input('search.value', '');
            $orderColumn = $request->input('order.0.column', 4);
            $orderDir = $request->input('order.0.dir', 'desc');

            $columns = [
                0 => 'id',
                1 => 'document_type',
                2 => 'reference',
                3 => 'description',
                4 => 'generated_date',
                5 => 'generated_by',
                6 => 'status',
            ];

            $orderBy = $columns[$orderColumn] ?? 'generated_date';

            $query = DB::table('treaty_documents')
                ->where('cover_no', $coverNo)
                ->where('endorsement_no', $endorsementNo)
                ->select([
                    'id',
                    'document_type',
                    'reference',
                    'description',
                    'generated_date',
                    'generated_by',
                    'status',
                    'file_path',
                ]);

            $totalRecords = (clone $query)->count();

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('document_type', 'like', "%{$search}%")
                        ->orWhere('reference', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('generated_by', 'like', "%{$search}%");
                });
            }

            $filteredRecords = (clone $query)->count();

            $data = $query
                ->orderBy($orderBy, $orderDir)
                ->skip($start)
                ->take($length)
                ->get();

            return response()->json([
                'draw' => intval($draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'draw' => 1,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ], 500);
        }
    }

    public function generateDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cover_no' => 'required|string|exists:cover_register,cover_no',
            'endorsement_no' => 'required|string|exists:cover_register,endorsement_no',
            'document_type' => 'required|string|in:debit_note,credit_note,statement,bordereau,closing_slip',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $endorsementNo = $request->endorsement_no;
            $documentType = $request->document_type;

            $result = match ($documentType) {
                'debit_note' => $this->generateDebitNote($endorsementNo, $request),
                'credit_note' => $this->generateCreditNote($endorsementNo, $request),
                'statement' => $this->generateStatementDocument($endorsementNo, $request),
                'bordereau' => $this->generateBordereau($endorsementNo, $request),
                'closing_slip' => $this->generateClosingSlip($endorsementNo, $request),
                default => throw new Exception("Unsupported document type: {$documentType}")
            };

            return response()->json([
                'success' => true,
                'message' => 'Document generated successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate document: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function generateCreditNote($endorsementNo, Request $request): array
    {
        $coverNo = $request->cover_no;

        $cover = DB::table('cover_register')
            ->where('endorsement_no', $endorsementNo)
            ->where('cover_no', $coverNo)
            ->first();

        if (! $cover) {
            throw new Exception('Cover not found');
        }

        $cedant = DB::table('customers')
            ->where('customer_id', $cover->customer_id)
            ->first();

        $creditNote = DB::table('credit_notes')
            ->where('cover_no', $coverNo)
            ->where('endorsement_no', $endorsementNo)
            ->select([
                'id',
                'credit_note_no',
                'cover_no',
                'type_of_bus',
                'posting_year',
                'posting_quarter',
                'posting_date',
                'gross_amount',
                'net_amount',
                'commission_amount',
                'created_at',
            ])
            ->first();

        if (! $creditNote) {
            throw new Exception('Credit note not found for this cover and endorsement');
        }

        $creditItems = DB::table('credit_note_items as tci')
            ->join('credit_notes as cn', 'tci.credit_note_id', '=', 'cn.id')
            ->leftJoin('class_groups as cg', 'tci.class_group_code', '=', 'cg.group_code')
            ->leftJoin('classes as c', 'tci.class_code', '=', 'c.class_code')
            ->leftJoin('treaty_item_codes as tic', 'tci.item_code', '=', 'tic.item_code')
            ->where('cn.cover_no', $coverNo)
            ->where('cn.endorsement_no', $endorsementNo)
            ->select([
                'tci.id',
                'tci.item_code',
                DB::raw('COALESCE(tic.description, tci.description) as item_name'),
                'tci.line_no',
                'tci.description',
                'tci.class_group_code',
                'cg.group_name',
                'tci.class_code',
                'c.class_name',
                'tci.line_rate',
                'tci.amount as item_amount',
                'tci.ledger',
                'cn.credit_note_no',
                'cn.posting_date',
                'cn.gross_amount',
                'cn.commission_amount',
                'cn.net_amount',
                'cn.status',
            ])
            ->orderBy('posting_date', 'desc')
            ->get();

        $totalGross = $creditNote->gross_amount;
        $totalCommission = $creditNote->commission_amount;
        $totalNet = $creditNote->net_amount;

        $referenceNo = $creditNote->credit_note_no;
        $viewName = 'printouts.accounts.treaty-credit-note';

        $company = Company::first();

        // Fetch reinsurers for this cover
        $reinsurers = CoverRipart::where([
            'cover_no' => $coverNo,
            'endorsement_no' => $endorsementNo,
        ])->with('partner')->get();

        $documentData = [
            'reference_no' => $referenceNo,
            'document_type' => 'Credit Note',
            'generated_date' => Carbon::now(),
            'cover' => $cover,
            'customer' => $cedant,
            'credit' => $creditNote,
            'debit' => $creditNote,
            'credit_items' => $creditItems,
            'reinsurers' => $reinsurers,
            'company' => $company,
            'totals' => (object) [
                'gross_premium' => $totalGross,
                'commission' => $totalCommission,
                'net_amount' => $totalNet,
            ],
            'currency' => $cover->currency ?? 'KES',
            'period' => [
                'from' => Carbon::parse($cover->cover_from)->format('d M Y'),
                'to' => Carbon::parse($cover->cover_to)->format('d M Y'),
            ],
        ];

        $pdf = Pdf::loadView($viewName, $documentData)->setPaper('a4', 'portrait')->setWarnings(false);
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isPhpEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);

        try {
            $existingDocument = DB::table('treaty_documents')
                ->where('reference', $referenceNo)
                ->first();

            if ($existingDocument) {
                return [
                    'document_id' => $existingDocument->id,
                    'reference_no' => $referenceNo,
                    'download_url' => route('treaty.documents.download', $existingDocument->id),
                    'message' => 'Document already exists.',
                    'already_exists' => true,
                ];
            }

            $documentId = $this->saveDocumentRecord([
                'endorsement_no' => $endorsementNo,
                'cover_no' => $coverNo,
                'document_type' => 'Credit Note',
                'reference' => $referenceNo,
                'description' => "Treaty Credit Note - Cover {$cover->cover_no} (Endorsement {$cover->endorsement_no})",
                'file_path' => $this->saveDocumentFile($pdf, $referenceNo, 'credit_note'),
                'generated_by' => auth()->user()->user_name ?? 'System',
                'status' => 'generated',
            ]);

            return [
                'document_id' => $documentId,
                'reference_no' => $referenceNo,
                'download_url' => route('treaty.documents.download', $documentId),
                'message' => 'Document generated successfully.',
                'already_exists' => false,
            ];
        } catch (QueryException $e) {
            if ($e->getCode() === '23505' || str_contains($e->getMessage(), 'duplicate key value')) {
                $existingDocument = DB::table('treaty_documents')
                    ->where('reference', $referenceNo)
                    ->first();

                if ($existingDocument) {
                    return [
                        'document_id' => $existingDocument->id,
                        'reference_no' => $referenceNo,
                        'download_url' => route('treaty.documents.download', $existingDocument->id),
                        'message' => "A document with reference {$referenceNo} has already been generated.",
                        'already_exists' => true,
                    ];
                }
            }

            throw $e;
        }
    }

    protected function generateStatementDocument($endorsementNo, Request $request): array
    {
        throw new Exception('Statement generation not yet implemented');
    }

    protected function generateBordereau($endorsementNo, Request $request): array
    {
        throw new Exception('Bordereau generation not yet implemented');
    }

    protected function generateClosingSlip($endorsementNo, Request $request): array
    {
        throw new Exception('Closing slip generation not yet implemented');
    }

    protected function generateDebitNote($endorsementNo, Request $request)
    {
        $coverNo = $request->cover_no;

        $cover = DB::table('cover_register')
            ->where('endorsement_no', $endorsementNo)
            ->where('cover_no', $coverNo)
            ->first();

        if (! $cover) {
            throw new Exception('Cover not found');
        }

        $cedant = DB::table('customers')
            ->where('customer_id', $cover->customer_id)
            ->first();

        $debitNote = DB::table('debit_notes')
            ->where('cover_no', $coverNo)
            ->where('endorsement_no', $endorsementNo)
            ->select([
                'id',
                'debit_note_no',
                'cover_no',
                'type_of_bus',
                'posting_year',
                'posting_quarter',
                'posting_date',
                'gross_amount',
                'net_amount',
                'commission_amount',
                'created_at',
            ])
            ->first();

        if (! $debitNote) {
            throw new Exception('Debit note not found for this cover and endorsement');
        }

        $debitItems = DB::table('debit_note_items as tdi')
            ->join('debit_notes as dn', 'tdi.debit_note_id', '=', 'dn.id')
            ->leftJoin('treaty_item_codes as tc', 'tdi.item_code', '=', 'tc.item_code')
            ->leftJoin('class_groups as cg', 'tdi.class_group_code', '=', 'cg.group_code')
            ->leftJoin('reinclass_premtypes as c', function ($join) {
                $join->on('tdi.class_code', '=', 'c.premtype_code')
                    ->on('tdi.class_group_code', '=', 'c.reinclass');
            })
            ->where('dn.cover_no', $coverNo)
            ->where('dn.endorsement_no', $endorsementNo)
            ->select([
                'tdi.id',
                'tdi.item_code',
                'tdi.item_no',
                'tdi.line_no',
                DB::raw('COALESCE(tc.description, tdi.description) as item_name'),
                'tdi.class_group_code',
                'cg.group_name',
                'tdi.class_code',
                'c.premtype_name as class_name',
                'tdi.line_rate',
                'tdi.amount as gross_amount',
                'tdi.ledger',
                'dn.debit_note_no',
                'dn.posting_date',
                'dn.commission_amount',
                'tdi.net_amount',
                'tdi.status',
                'tdi.amount as item_amount',
                'tdi.description',
                'tdi.original_amount'
            ])
            ->orderBy('posting_date', 'desc')
            ->get();

        $totalGross = $debitNote->gross_amount;
        $totalCommission = $debitNote->commission_amount;
        $totalNet = $debitNote->net_amount;

        $referenceNo = $debitNote->debit_note_no;
        $viewName = 'printouts.accounts.treaty-debit-note';

        $documentData = [
            'reference_no' => $referenceNo,
            'document_type' => 'Debit Note',
            'generated_date' => Carbon::now(),
            'cover' => $cover,
            'customer' => $cedant,
            'debit' => $debitNote,
            'debit_items' => $debitItems,
            'totals' => (object) [
                'gross_premium' => $totalGross,
                'commission' => $totalCommission,
                'net_amount' => $totalNet,
            ],
            'currency' => $cover->currency ?? 'KES',
            'period' => [
                'from' => Carbon::parse($cover->cover_from)->format('d M Y'),
                'to' => Carbon::parse($cover->cover_to)->format('d M Y'),
            ],
        ];

        $pdf = Pdf::loadView($viewName, $documentData)->setPaper('a4', 'portrait')->setWarnings(false);
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isPhpEnabled', true);
        $pdf->set_option('isRemoteEnabled', true);

        try {
            $existingDocument = DB::table('treaty_documents')
                ->where('reference', $referenceNo)
                ->first();

            if ($existingDocument) {
                return [
                    'document_id' => $existingDocument->id,
                    'reference_no' => $referenceNo,
                    'download_url' => route('treaty.documents.download', $existingDocument->id),
                    'message' => 'Document already exists.',
                    'already_exists' => true,
                ];
            }

            $documentId = $this->saveDocumentRecord([
                'endorsement_no' => $endorsementNo,
                'cover_no' => $coverNo,
                'document_type' => 'Debit Note',
                'reference' => $referenceNo,
                'description' => "Treaty Debit Note - Cover {$cover->cover_no} (Endorsement {$cover->endorsement_no})",
                'file_path' => $this->saveDocumentFile($pdf, $referenceNo, 'debit_note'),
                'generated_by' => auth()->user()->user_name ?? 'System',
                'status' => 'generated',
            ]);

            return [
                'document_id' => $documentId,
                'reference_no' => $referenceNo,
                'download_url' => route('treaty.documents.download', $documentId),
                'message' => 'Document generated successfully.',
                'already_exists' => false,
            ];
        } catch (QueryException $e) {
            if ($e->getCode() === '23505' || str_contains($e->getMessage(), 'duplicate key value')) {
                $existingDocument = DB::table('treaty_documents')
                    ->where('reference', $referenceNo)
                    ->first();

                if ($existingDocument) {
                    return [
                        'document_id' => $existingDocument->id,
                        'reference_no' => $referenceNo,
                        'download_url' => route('treaty.documents.download', $existingDocument->id),
                        'message' => "A document with reference {$referenceNo} has already been generated.",
                        'already_exists' => true,
                    ];
                }
            }

            throw $e;
        }
    }

    protected function saveDocumentRecord(array $data)
    {
        return DB::table('treaty_documents')->insertGetId(array_merge($data, [
            'generated_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]));
    }

    protected function saveDocumentFile($pdf, $referenceNo, $type)
    {
        $fileName = "{$referenceNo}_{$type}_" . Carbon::now()->format('YmdHis') . '.pdf';

        $pdfContent = $pdf->output();

        $data = $this->s3Service->storeInS3($pdfContent, $fileName);

        $path = $data['s3_url'];

        return $path;
    }

    public function downloadDocument($id)
    {
        $document = DB::table('treaty_documents')->where('id', $id)->first();

        if (! $document) {
            abort(404, 'Document not found');
        }

        $filePath = $document->file_path;

        if (empty($filePath)) {
            abort(404, 'Document file path not found');
        }

        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return redirect()->away($filePath);
        }

        if (Storage::exists($filePath)) {
            return Storage::download($filePath);
        }

        if (file_exists(storage_path('app/' . $filePath))) {
            return response()->download(storage_path('app/' . $filePath));
        }
        //   $query = CoverRipart::where([
        //         'cover_no' => $coverNo,
        //         'endorsement_no' => $endorsementNo
        //     ])->with('partner');

        //     $debit = DebitNote::where([
        //         'cover_no' => $coverNo,
        //         'endorsement_no' => $endorsementNo
        //     ])->first();

        //     $recordsTotal = $query->count();
        //     $recordsFiltered = $recordsTotal;

        //     $reinsurers = $query
        //         ->skip($start)
        //         ->take($length)
        //         ->get();

        //     $data = $reinsurers->map(function ($rein) {
        //         return [
        //             'id' => $rein->id,
        //             'name' => $rein->partner?->name ?? '',
        //             'share_percentage' => $rein->share ?? 0,
        //             'gross_premium' => $rein->total_premium ?? 0,
        //             'commission' => $rein->commission ?? 0,
        //             'brokerage_amount' => $rein->brokerage_comm_amt ?? 0,
        //             'premium_tax_amount' => $rein->prem_tax ?? 0,
        //             'wht_amount' => $rein->wht_amt ?? 0,
        //             'ri_tax' => $rein->ri_tax ?? 0,
        //             'net_amount' => $rein->net_amount ?? 0,
        //             'status' => 'active'
        //         ];

        abort(404, 'Document file not found');
    }

    public function previewSlip($coverId)
    {
        $cover = $this->getCover($coverId);

        if (! $cover) {
            abort(404, 'Cover not found');
        }

        return view('treaty.slip-preview', compact('cover'));
    }

    public function generateStatement(Request $request, $coverId): JsonResponse
    {
        try {
            $cover = $this->getCover($coverId);

            if (! $cover) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cover not found',
                ], 404);
            }

            $reference = 'SOA-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            DB::table('treaty_documents')->insert([
                'cover_no' => $cover->cover_no,
                'endorsement_no' => $cover->endorsement_no,
                'document_type' => 'Statement of Account',
                'reference' => $reference,
                'description' => 'Quarterly Statement - ' . date('F Y'),
                'generated_date' => now()->toDateString(),
                'generated_by' => auth()->user()->user_name ?? 'System',
                'status' => 'generated',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Statement generated successfully',
                'download_url' => route('treaty.documents.download', ['id' => $reference]),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate statement',
            ], 500);
        }
    }

    public function exportData($coverId)
    {
        $cover = $this->getCover($coverId);

        if (! $cover) {
            abort(404, 'Cover not found');
        }

        $debitItems = DB::table('treaty_debit_items as tdi')
            ->leftJoin('reinsurers as r', 'tdi.reinsurer_id', '=', 'r.id')
            ->where('tdi.cover_id', $coverId)
            ->select([
                'tdi.item_number',
                'tdi.item_date',
                'tdi.treaty_type',
                'tdi.class_group',
                'tdi.class_name',
                'r.name as reinsurer',
                'tdi.gross_premium',
                'tdi.commission_rate',
                'tdi.commission_amount',
                'tdi.net_amount',
                'tdi.status',
            ])
            ->get();

        $filename = 'debit_items_' . $cover->cover_no . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($debitItems) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Item Number',
                'Date',
                'Treaty Type',
                'Class Group',
                'Class Name',
                'Reinsurer',
                'Gross Premium',
                'Commission Rate (%)',
                'Commission Amount',
                'Net Amount',
                'Status',
            ]);

            foreach ($debitItems as $item) {
                fputcsv($file, [
                    $item->item_number,
                    $item->item_date,
                    $item->treaty_type,
                    $item->class_group,
                    $item->class_name,
                    $item->reinsurer,
                    $item->gross_premium,
                    $item->commission_rate,
                    $item->commission_amount,
                    $item->net_amount,
                    $item->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get summary statistics for the cover transaction in real-time
     * This endpoint provides financial totals for the summary cards
     */
    public function getSummaryStats(Request $request): JsonResponse
    {
        try {
            $coverNo = $request->input('cover_no');
            $endorsementNo = $request->input('endorsement_no');

            if (empty($coverNo) || empty($endorsementNo)) {
                return response()->json([
                    'success' => false,
                    'message' => 'cover_no and endorsement_no are required',
                ], 400);
            }

            // Get debit note totals
            $debitTotals = DB::table('debit_note_items as tdi')
                ->join('debit_notes as dn', 'tdi.debit_note_id', '=', 'dn.id')
                ->where('dn.cover_no', $coverNo)
                ->where('dn.endorsement_no', $endorsementNo)
                ->selectRaw('
                    COALESCE(SUM(tdi.amount), 0) as total_gross_premium,
                    COALESCE(SUM(dn.commission_amount), 0) as total_commission,
                    COALESCE(SUM(tdi.net_amount), 0) as total_net_amount,
                    COUNT(tdi.id) as total_debit_items
                ')
                ->first();

            // Get reinsurer share totals
            $reinsurerTotals = CoverRipart::where([
                'cover_no' => $coverNo,
                'endorsement_no' => $endorsementNo,
            ])
                ->selectRaw('
                    COALESCE(SUM(total_premium), 0) as total_reinsurer_share,
                    COALESCE(SUM(commission), 0) as total_reinsurer_commission,
                    COALESCE(SUM(brokerage_comm_amt), 0) as total_brokerage,
                    COALESCE(SUM(prem_tax), 0) as total_premium_tax,
                    COALESCE(SUM(wht_amt), 0) as total_wht,
                    COALESCE(SUM(ri_tax), 0) as total_ri_tax,
                    COALESCE(SUM(net_amount), 0) as total_reinsurer_net,
                    COUNT(*) as total_reinsurers
                ')
                ->first();

            // Get document counts
            $documentCounts = DB::table('treaty_documents')
                ->where('cover_no', $coverNo)
                ->where('endorsement_no', $endorsementNo)
                ->selectRaw('
                    COUNT(*) as total_documents,
                    SUM(CASE WHEN LOWER(status) = \'generated\' THEN 1 ELSE 0 END) as generated_count,
                    SUM(CASE WHEN LOWER(status) = \'sent\' THEN 1 ELSE 0 END) as sent_count,
                    SUM(CASE WHEN LOWER(status) = \'signed\' THEN 1 ELSE 0 END) as signed_count
                ')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'financial' => [
                        'total_gross_premium' => (float) ($debitTotals->total_gross_premium ?? 0),
                        'total_commission' => (float) ($debitTotals->total_commission ?? 0),
                        'total_net_amount' => (float) ($debitTotals->total_net_amount ?? 0),
                        'total_reinsurer_share' => (float) ($reinsurerTotals->total_reinsurer_share ?? 0),
                        'total_reinsurer_commission' => (float) ($reinsurerTotals->total_reinsurer_commission ?? 0),
                        'total_brokerage' => (float) ($reinsurerTotals->total_brokerage ?? 0),
                        'total_premium_tax' => (float) ($reinsurerTotals->total_premium_tax ?? 0),
                        'total_wht' => (float) ($reinsurerTotals->total_wht ?? 0),
                        'total_ri_tax' => (float) ($reinsurerTotals->total_ri_tax ?? 0),
                        'total_reinsurer_net' => (float) ($reinsurerTotals->total_reinsurer_net ?? 0),
                    ],
                    'counts' => [
                        'debit_items' => (int) ($debitTotals->total_debit_items ?? 0),
                        'reinsurers' => (int) ($reinsurerTotals->total_reinsurers ?? 0),
                        'documents' => (int) ($documentCounts->total_documents ?? 0),
                        'documents_generated' => (int) ($documentCounts->generated_count ?? 0),
                        'documents_sent' => (int) ($documentCounts->sent_count ?? 0),
                        'documents_signed' => (int) ($documentCounts->signed_count ?? 0),
                    ],
                    'last_updated' => now()->toIso8601String(),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch summary stats: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getCover($id)
    {
        return DB::table('cover_register')->where('id', $id)->first();
    }

    private function getCustomer($customerId)
    {
        if (! $customerId) {
            return null;
        }

        return DB::table('customers')->where('customer_id', $customerId)->first();
    }

    private function getCedantDetails($cover)
    {
        $customer = $this->getCustomer($cover->customer_id ?? null);

        return (object) [
            'name' => $customer->name ?? 'N/A',
            'registration_no' => $customer->registration_no ?? 'N/A',
            'address' => $customer->address ?? 'N/A',
            'contact_person' => $customer->contact_person ?? 'N/A',
            'designation' => $customer->designation ?? 'N/A',
            'email' => $customer->email ?? 'N/A',
            'phone' => $customer->phone ?? 'N/A',
            'treaty_year' => Carbon::parse($cover->cover_from)->format('Y'),
            'treaty_period' => Carbon::parse($cover->cover_from)->format('d M Y') . ' - ' . Carbon::parse($cover->cover_to)->format('d M Y'),
            'retention_limit' => $cover->retention_limit ?? 0,
            'treaty_capacity' => $cover->treaty_capacity ?? $cover->sum_insured ?? 0,
        ];
    }

    private function generateItemNumber(): string
    {
        return DB::transaction(function () {
            $year = date('Y');
            $lastItem = DB::table('treaty_debit_items')
                ->where('item_number', 'like', "ITM-{$year}-%")
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastItem) {
                $lastNumber = intval(substr($lastItem->item_number, -5));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            return 'ITM-' . $year . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function viewReinsurerCreditNote(Request $request)
    {
        try {
            $validated = $request->validate([
                'cover_no' => 'required|string',
                'endorsement_no' => 'required|string',
                'partner_no' => 'required|string',
                'with_brokerage' => 'nullable|in:0,1',
            ]);

            $coverNo = $validated['cover_no'];
            $endorsementNo = $validated['endorsement_no'];
            $partnerNo = $validated['partner_no'];
            $withBrokerage = ($request->input('with_brokerage', '1') === '1');

            $cover = DB::table('cover_register')
                ->where('endorsement_no', $endorsementNo)
                ->where('cover_no', $coverNo)
                ->first();

            if (! $cover) {
                abort(404, 'Cover not found');
            }

            $cedant = DB::table('customers')
                ->where('customer_id', $cover->customer_id)
                ->first();

            $creditNote = DB::table('credit_notes')
                ->where('cover_no', $coverNo)
                ->where('endorsement_no', $endorsementNo)
                ->select([
                    'id',
                    'credit_note_no',
                    'cover_no',
                    'type_of_bus',
                    'posting_year',
                    'posting_quarter',
                    'posting_date',
                    'gross_amount',
                    'net_amount',
                    'commission_amount',
                    'created_at',
                ])
                ->first();

            if (! $creditNote) {
                abort(404, 'Credit note not found for this cover and endorsement');
            }

            $creditItems = DB::table('credit_note_items as tci')
                ->join('credit_notes as cn', 'tci.credit_note_id', '=', 'cn.id')
                ->leftJoin('treaty_item_codes as tic', 'tci.item_code', '=', 'tic.item_code')
                ->leftJoin('class_groups as cg', 'tci.class_group_code', '=', 'cg.group_code')
                ->leftJoin('reinclass_premtypes as c', function ($join) {
                    $join->on('tci.class_code', '=', 'c.premtype_code')
                        ->on('tci.class_group_code', '=', 'c.reinclass');
                })
                ->where('cn.cover_no', $coverNo)
                ->where('cn.endorsement_no', $endorsementNo)
                ->select([
                    'tci.id',
                    'tci.item_code',
                    DB::raw('COALESCE(tic.description, tci.description) as item_name'),
                    'tci.line_no',
                    'tci.description',
                    'tci.class_group_code',
                    'cg.group_name',
                    'tci.class_code',
                    'c.premtype_name as class_name',
                    'tci.line_rate',
                    'tci.amount as item_amount',
                    'tci.ledger',
                    'cn.credit_note_no',
                    'cn.posting_date',
                    'cn.gross_amount',
                    'cn.commission_amount',
                    'cn.net_amount',
                    'cn.status',
                    'tci.original_amount'
                ])
                ->orderBy('id', 'asc')
                ->get();

            $totalGross = $creditNote->gross_amount;
            $totalCommission = $creditNote->commission_amount;
            $totalNet = $creditNote->net_amount;

            $company = Company::first();

            $reinsurer = CoverRipart::where([
                'cover_no' => $coverNo,
                'endorsement_no' => $endorsementNo,
                'partner_no' => $partnerNo,
            ])->with('partner')->first();

            if (! $reinsurer) {
                abort(404, 'Reinsurer not found for this cover');
            }

            $reinsurers = collect([$reinsurer]);

            $documentData = [
                'reference_no' => $creditNote->credit_note_no,
                'document_type' => 'Credit Note',
                'generated_date' => Carbon::now(),
                'cover' => $cover,
                'customer' => $cedant,
                'credit' => $creditNote,
                'credit_items' => $creditItems,
                'reinsurers' => $reinsurers,
                'company' => $company,
                'treat_type' => 'Surplus Treaty',
                'bus_class' => 'Fire',
                'totals' => (object) [
                    'gross_premium' => $totalGross,
                    'commission' => $totalCommission,
                    'net_amount' => $totalNet,
                ],
                'currency' => $cover->currency ?? 'KES',
                'period' => [
                    'from' => Carbon::parse($cover->cover_from)->format('d M Y'),
                    'to' => Carbon::parse($cover->cover_to)->format('d M Y'),
                ],
                'with_brokerage' => $withBrokerage,
            ];

            logger()->debug(json_encode($documentData, JSON_PRETTY_PRINT));


            $pdf = Pdf::loadView('printouts.accounts.treaty-credit-note', $documentData)
                ->setPaper('a4', 'portrait')
                ->setWarnings(false);
            $pdf->set_option('isHtml5ParserEnabled', true);
            $pdf->set_option('isPhpEnabled', true);
            $pdf->set_option('isRemoteEnabled', true);

            $filename = 'credit-note-' . $creditNote->credit_note_no . '-' . $reinsurer->partner_no . '.pdf';

            return $pdf->stream($filename);
        } catch (ValidationException $e) {
            abort(422, 'Invalid request parameters');
        } catch (Exception $e) {
            abort(500, 'Failed to generate credit note: ' . $e->getMessage());
        }
    }

    public function viewCedantDebitNote(Request $request)
    {
        try {
            $validated = $request->validate([
                'cover_no' => 'required|string',
                'endorsement_no' => 'required|string',
                'cedant_id' => 'required|string',
            ]);

            $coverNo = $validated['cover_no'];
            $endorsementNo = $validated['endorsement_no'];

            $cover = DB::table('cover_register')
                ->where('endorsement_no', $endorsementNo)
                ->where('cover_no', $coverNo)
                ->first();

            if (! $cover) {
                abort(404, 'Cover not found');
            }

            $cedant = DB::table('customers')
                ->where('customer_id', $cover->customer_id)
                ->first();

            $debitNote = DB::table('debit_notes')
                ->where('cover_no', $coverNo)
                ->where('endorsement_no', $endorsementNo)
                ->select([
                    'id',
                    'debit_note_no',
                    'cover_no',
                    'type_of_bus',
                    'posting_year',
                    'posting_quarter',
                    'posting_date',
                    'gross_amount',
                    'net_amount',
                    'commission_amount',
                    'created_at',
                    'other_deductions'
                ])
                ->first();

            if (! $debitNote) {
                abort(404, 'Debit note not found for this cover and endorsement');
            }

            $debitItems = DB::table('debit_note_items as tdi')
                ->join('debit_notes as dn', 'tdi.debit_note_id', '=', 'dn.id')
                ->leftJoin('treaty_item_codes as tc', 'tdi.item_code', '=', 'tc.item_code')
                ->leftJoin('class_groups as cg', 'tdi.class_group_code', '=', 'cg.group_code')
                ->leftJoin('reinclass_premtypes as c', function ($join) {
                    $join->on('tdi.class_code', '=', 'c.premtype_code')
                        ->on('tdi.class_group_code', '=', 'c.reinclass');
                })
                ->where('dn.cover_no', $coverNo)
                ->where('dn.endorsement_no', $endorsementNo)
                ->select([
                    'tdi.id',
                    'tdi.item_code',
                    'tdi.item_no',
                    'tdi.line_no',
                    DB::raw('COALESCE(tc.description, tdi.description) as item_name'),
                    'tdi.class_group_code',
                    'cg.group_name',
                    'tdi.class_code',
                    'c.premtype_name as class_name',
                    'tdi.line_rate',
                    'tdi.amount as gross_amount',
                    'tdi.ledger',
                    'dn.debit_note_no',
                    'dn.posting_date',
                    'dn.commission_amount',
                    'tdi.net_amount',
                    'tdi.status',
                    'tdi.amount as item_amount',
                    'tdi.description',
                    'tdi.original_amount'
                ])
                ->orderBy('id', 'asc')
                ->get();

            $totalGross = $debitNote->gross_amount;
            $totalCommission = $debitNote->commission_amount;
            $totalNet = $debitNote->net_amount;
            $otherDeductions = $debitNote->other_deductions;

            $company = Company::first();

            $documentData = [
                'reference_no' => $debitNote->debit_note_no,
                'document_type' => 'Debit Note',
                'generated_date' => Carbon::now(),
                'cover' => $cover,
                'customer' => $cedant,
                'debit' => $debitNote,
                'debit_items' => $debitItems,
                'company' => $company,
                'totals' => (object) [
                    'gross_premium' => $totalGross,
                    'commission' => $totalCommission,
                    'net_amount' => $totalNet,
                    'total_debits' => $totalGross,
                    'total_credits' => $otherDeductions + $totalCommission,
                ],
                'currency' => $cover->currency ?? 'KES',
                'period' => [
                    'from' => Carbon::parse($cover->cover_from)->format('d M Y'),
                    'to' => Carbon::parse($cover->cover_to)->format('d M Y'),
                ],
            ];

            $pdf = Pdf::loadView('printouts.accounts.treaty-debit-note', $documentData)
                ->setPaper('a4', 'portrait')
                ->setWarnings(false);
            $pdf->set_option('isHtml5ParserEnabled', true);
            $pdf->set_option('isPhpEnabled', true);
            $pdf->set_option('isRemoteEnabled', true);

            $filename = 'debit-note-' . $debitNote->debit_note_no . '-cedant.pdf';

            return $pdf->stream($filename);
        } catch (ValidationException $e) {
            abort(422, 'Invalid request parameters');
        } catch (Exception $e) {
            abort(500, 'Failed to generate debit note: ' . $e->getMessage());
        }
    }
}
