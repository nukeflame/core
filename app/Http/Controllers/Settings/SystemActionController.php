<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SystemProcess;
use App\Models\SystemProcessAction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;


class SystemActionController extends Controller
{
    /**
     * Display a listing of system actions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $systemActions = SystemProcessAction::with('process')
            ->orderBy('created_at', 'desc')
            ->get();
        $processes = SystemProcess::where('status', 'active')->get();
        return view('settings.system_process_action', [
            'systemActions' => $systemActions,
            'processes' => $processes
        ]);
    }


    public function store(Request $request)
    {
        try {
            $validatedData = $this->validateSystemAction($request);

            DB::beginTransaction();
            if ($validatedData) {
                $id = SystemProcessAction::max('id') + 1;
                SystemProcessAction::create([
                    'id'            => $id,
                    'process_id'    => $request->system_process_id,
                    'name'          => $request->name,
                    'module'         => $request->module,
                    'nice_name'     => Str::snake($request->name),
                    'created_by'    => auth()->user()->user_name,
                    'updated_by'    => auth()->user()->user_name,
                    'description'   => $request->description,
                    'status'        => $request->status,
                    'action_type'   => $request->action_type,
                    'performed_by'  => auth()->user()->id,
                    'performed_at'  => now(),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'System Action created successfully',
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create system action',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    private function validateSystemAction(Request $request, $id = null)
    {
        $uniqueRule = $id ? "unique:system_process_action,name,$id" : 'unique:system_process_action,name';

        return $request->validate([
            'name' => ['required', 'string', 'max:255', $uniqueRule],
            'module' => ['required', 'string', 'in:module,dashboard,approvals,business_development,cover_administration,claims_administration,reports,settings,reinsurance,user_management,integration_apis,all_modules'],
            'action_type' => ['required', 'string', 'in:action_type,create,update,delete,read,export,import,verify,approve'],
            'status' => ['required', 'string', 'in:pending,running,completed,failed,cancelled'],
            'priority' => ['nullable', 'string', 'in:low,medium,high,critical'],
            'system_process_id' => ['nullable', 'exists:system_process,id'],
            'description' => ['nullable', 'string'],
            'scheduled_at' => ['nullable', 'date']
        ]);
    }

    public function systemProcessActionDatatable(Request $request)
    {
        $processes = SystemProcessAction::with('process')->get();
        return DataTables::of($processes)
            ->addColumn('description', function ($data) {
                return $data->description ? $data->description : '--';
            })
            ->addColumn('action_type', function ($data) {
                return  '--';
            })
            ->addColumn('action', function ($data) {
                $btn = "";
                $btn .= "<button class='btn btn-primary btn-sm btn-sm-action edit' data-data='$data->id' data-bs-toggle='modal' data-bs-target='#departmentModal'>
                            <i class='bx bx-pencil'></i> Edit
                        </button>
                        <button class='btn btn-danger btn-sm btn-sm-action delete'  data-data='$data->id'> <i class='bx bx-trash'></i> Delete</button>";
                return $btn;
            })
            ->rawColumns(['action'])
            ->make('true');
    }
}
