<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function assign(Request $request)
    {
        $request->validate([
            'userId' => 'required|exists:users,id',
            'roleId' => 'required|exists:roles,id',
            'department_ids' => 'required|array',
            'department_ids.*' => 'exists:company_department,department_code',
        ]);

        try {
            $role = Role::where('id', $request->roleId)
                ->first(['id', 'name', 'description']);
            $departmentIds = Department::whereIn('department_code', $request->department_ids)
                ->pluck('id')->toArray();

            $role->departments()->sync($departmentIds);

            return response()->json([
                'success' => true,
                'message' => 'Department assigned successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign department: ' . $e->getMessage(),
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
