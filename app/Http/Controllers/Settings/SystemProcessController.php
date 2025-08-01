<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\SystemProcess;
use App\Models\SystemProcessAction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class SystemProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function systemProcess()
    {
        return view('settings.system_process');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:system_process,name',
                'description' => 'nullable|string',
                'category' => 'required|string',
                'execution_type' => 'required|string',
                'status' => 'required|string',
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,id',
                'priority' => 'nullable|in:low,medium,high,critical',
                'parameters.keys' => 'nullable|array',
                'parameters.values' => 'nullable|array'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $parameters = [];
        if (isset($validatedData['parameters']['keys']) && isset($validatedData['parameters']['values'])) {
            $keys = $validatedData['parameters']['keys'];
            $values = $validatedData['parameters']['values'];

            foreach ($keys as $index => $key) {
                if (!empty($key) && isset($values[$index])) {
                    $parameters[$key] = $values[$index];
                }
            }
        }

        try {
            DB::beginTransaction();
            $id = (int) SystemProcess::max('id') + 1;
            $systemProcess = SystemProcess::create([
                'id' => $id,
                'name'          => $validatedData['name'],
                'nice_name'     => Str::snake($validatedData['category']),
                'created_by'    => Auth::user()->user_name,
                'initiated_by'    => Auth::user()->id,
                'updated_by'    => Auth::user()->user_name,
                'description' => $validatedData['description'] ?? null,
                'execution_type' => $validatedData['execution_type'],
                'status' => $validatedData['status'],
                'priority' => $validatedData['priority'] ?? 'medium',
                'parameters' => json_encode($parameters),
                'started_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $systemProcess->permissions()->sync($validatedData['permissions']);
            DB::commit();

            return response()->json([
                'message' => 'System Process Created Successfully',
                'process' => $systemProcess->load('permissions')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create system process',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $process = SystemProcess::find($id);
            $process->delete();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Process deleted successfully'
            ]);
        } catch (Throwable $e) {
            DB::rollback();
            dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteAction(string $id)
    {
        try {
            $processAction = SystemProcessAction::find($id);
            $processAction->delete();

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Process action deleted successfully'
            ]);
        } catch (Throwable $e) {
            DB::rollback();
            dd($e);
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to save'
            ]);
        }
    }

    public function systemProcessesDatatable(Request $request)
    {
        $processes = SystemProcess::withCount('actions')->get();

        return DataTables::of($processes)
            ->addColumn('initiated_by', function ($data) {
                return $data->initiatedB ? Str::ucwords($data->initiatedBy->name) : '--';
            })
            ->addColumn('description', function ($data) {
                return $data->description ? $data->description : '--';
            })
            ->addColumn('started_at', function ($data) {
                return $data->started_at ? Carbon::parse($data->started_at)->format('d/m/Y H:i') : '--';
            })
            ->addColumn('completed_at', function ($data) {
                return $data->completed_at ? Carbon::parse($data->completed_at)->format('d/m/Y H:i') : '--';
            })
            ->addColumn('action', function ($data) {
                $btn = "";
                $btn .= "<button class='btn btn-primary btn-sm btn-sm-action edit' data-data='$data->id' data-bs-toggle='modal' data-bs-target='#departmentModal'>
                            <i class='bx bx-pencil'></i> Edit
                        </button>
                        <button class='btn btn-danger btn-sm btn-sm-action delete'  data-data='$data->id'> <i class='bx bx-trash'></i> Delete</button>";
                return $btn;
            })
            ->rawColumns(['action', 'sys_actions'])
            ->make('true');
    }
}
