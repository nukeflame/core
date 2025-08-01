<?php

namespace App\Http\Controllers;

use App\Models\StaffNotice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class StaffNoticeController extends Controller
{
    public function index()
    {
        return view('admin.staff_notice');
    }

    public function getData(Request $request)
    {
        $data = StaffNotice::latest()->get();
        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('priority_badge', function ($row) {
                $badge = '';
                if ($row->priority == 'low') {
                    $badge = '<span class="badge bg-warning text-dark">LOW</span>';
                } else if ($row->priority == 'medium') {
                    $badge = '<span class="badge bg-info">MEDIUM</span>';
                } else if ($row->priority == 'high') {
                    $badge = '<span class="badge bg-danger">HIGH</span>';
                }
                return $badge;
            })
            ->addColumn('action', function ($row) {
                $actionBtn = '<button class="edit btn btn-success btn-sm" data-id="' . $row->id . '"><i class="bi bi-pencil"></i></button>
                                 <button class="delete btn btn-danger btn-sm" data-id="' . $row->id . '"><i class="bi bi-trash"></i></button>';
                return $actionBtn;
            })
            ->rawColumns(['action', 'priority_badge'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notice' => 'required|string|max:255',
            'description' => 'nullable|string',
            // 'type' => 'required|string|max:255',
            'effective_from' => 'required|date',
            'expired_at' => 'nullable|date|after_or_equal:effective_from',
            // 'issued_by' => 'required|string|max:255',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            $issued_by = auth()->user()->name;
            $data = array_merge($request->all(), [
                'priority' => Str::lower($request->priority),
                'issued_by' => $issued_by,
                'type' => 'all_users',
            ]);

            StaffNotice::create($data);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Store notice created successfully',
            ]);
        } catch (\Exception $e) {
            Db::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create store notice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific notice for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $notice = StaffNotice::findOrFail($id);
        return response()->json($notice);
    }

    /**
     * Update the specified notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'notice' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:255',
            'effective_from' => 'required|date',
            'expired_at' => 'nullable|date|after_or_equal:effective_from',
            'issued_by' => 'required|string|max:255',
            'priority' => 'required|in:LOW,MEDIUM,HIGH',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $notice = StaffNotice::findOrFail($id);
        $notice->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Store notice updated successfully',
            'data' => $notice
        ]);
    }

    /**
     * Remove the specified notice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $notice = StaffNotice::findOrFail($id);
        $notice->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Store notice deleted successfully'
        ]);
    }

    /**
     * Get staff notices with filtering options
     */
    public function getNotices(Request $request): JsonResponse
    {
        try {
            $query = StaffNotice::query();

            if ($request->get('active_only', true)) {
                $query->where(function ($q) {
                    $q->where('expired_at', '>', now())
                        ->orWhereNull('expired_at');
                });
            }

            if ($request->get('effective_only', true)) {
                $query->where('effective_from', '<=', now());
            }

            if ($request->has('type')) {
                $query->where('type', $request->get('type'));
            }

            if ($request->has('priority')) {
                $query->where('priority', $request->get('priority'));
            }

            if ($request->has('issued_by')) {
                $query->where('issued_by', $request->get('issued_by'));
            }

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('notice', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $query->orderBy('priority', 'desc')
                ->orderBy('effective_from', 'desc');

            $perPage = $request->get('per_page', 20);
            $notices = $query->paginate($perPage);

            logger($notices->items());

            return response()->json([
                'success' => true,
                'data' => $notices->items(),
                'pagination' => [
                    'current_page' => $notices->currentPage(),
                    'last_page' => $notices->lastPage(),
                    'per_page' => $notices->perPage(),
                    'total' => $notices->total(),
                    'from' => $notices->firstItem(),
                    'to' => $notices->lastItem()
                ],
                'message' => 'Staff notices retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error retrieving staff notices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all notices without pagination
     */
    public function getAllNotices(Request $request): JsonResponse
    {
        try {
            $query = StaffNotice::query();

            // Apply same filters as above
            if ($request->get('active_only', true)) {
                $query->where(function ($q) {
                    $q->where('expired_at', '>', now())
                        ->orWhereNull('expired_at');
                });
            }

            if ($request->get('effective_only', true)) {
                $query->where('effective_from', '<=', now());
            }

            if ($request->has('type')) {
                $query->where('type', $request->get('type'));
            }

            $notices = $query->orderBy('priority', 'desc')
                ->orderBy('effective_from', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $notices,
                'count' => $notices->count(),
                'message' => 'All staff notices retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error retrieving staff notices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notice by ID
     */
    public function getNoticeById(Request $request, $id): JsonResponse
    {
        try {
            $notice = StaffNotice::find($id);

            if (!$notice) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Staff notice not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $notice,
                'message' => 'Staff notice retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving staff notice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notices summary/stats
     */
    public function getNoticesSummary(): JsonResponse
    {
        try {
            $total = StaffNotice::count();
            $active = StaffNotice::where('expired_at', '>', now())
                ->orWhereNull('expired_at')
                ->count();
            $expired = StaffNotice::where('expired_at', '<=', now())->count();

            $byType = StaffNotice::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get();

            $byPriority = StaffNotice::selectRaw('priority, count(*) as count')
                ->groupBy('priority')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_notices' => $total,
                    'active_notices' => $active,
                    'expired_notices' => $expired,
                    'by_type' => $byType,
                    'by_priority' => $byPriority
                ],
                'message' => 'Staff notices summary retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error retrieving summary: ' . $e->getMessage()
            ], 500);
        }
    }
}
