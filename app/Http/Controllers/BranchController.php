<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    public function branches(Request $request)
    {
        return view('settings.branches', []);
    }

    public function branchesDatatable()
    {
        $branches = Branch::where('status', 'A');

        return DataTables::of($branches)
            ->addColumn('id', function ($data) {
                return $data->department_code;
            })
            ->addColumn('status', function ($data) {
                return $data->status === 'A' ? '<span class="badge bg-outline-success">Active</span>' : '<span class="badge bg-outline-danger">Inactive</span>';
            })
            ->addColumn('action', function ($data) {
                $btn = '';
                if ($data?->department_code != 2030) {
                    $btn .= "<button class='btn btn-primary btn-sm btn-sm-action edit' data-data='$data' data-bs-toggle='modal' data-bs-target='#departmentModal'>
                            <i class='bx bx-pencil'></i> Edit
                        </button>
                        <button class='btn btn-danger btn-sm btn-sm-action delete'  data-data='$data'> <i class='bx bx-trash'></i> Delete</button>";
                }
                return $btn;
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }
}
