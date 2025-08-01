<?php

namespace App\Http\Controllers;

use App\Models\ClassGroup;
use Illuminate\Http\Request;
use App\Models\ClaimAckParams;
use App\Models\ClaimStatusParam;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;

class SettingsClaimController extends Controller
{

    public function claimAckDoc(Request $request)
    {
        $classGroups = ClassGroup::get(['group_code', 'group_name']);
        // dd(!$request->ajax());
        if (!$request->ajax()) {
            return view('settings.claims.claim_ack_docs', [
                'classGroups' => $classGroups,
            ]);
        } else {
            // $docs = ClaimAckParams::all();
            // $docs = ClaimAckParams::with('classGroup')->get();
            $docs = ClaimAckParams::select('claim_ack_params.*', 'class_groups.group_name')
                ->join('class_groups', 'claim_ack_params.class_group', '=', 'class_groups.group_code')
                ->get();
            // dd($docs);
            return DataTables::of($docs)
                ->addColumn('action', function ($data) {
                    return "<button class='btn btn-outline-primary btn-sm edit' data-data='$data' data-bs-toggle='modal' data-bs-target='#docsModal'>
                                <i class='fa fa-pencil'></i> Edit
                            </button>
                            <button class='btn btn-outline-danger btn-sm delete'  data-data='$data'> <i class='fa fa-trash'></i> Delete</button>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function saveClaimAckDoc(Request $request)
    {
        $request->validate([
            'class_group' => 'required',
            'doc_name' => 'required',
        ]);

        try {
            $id = (int)ClaimAckParams::withTrashed()->max('id') + 1;

            $docs = new ClaimAckParams();
            $docs->id = $id;
            $docs->class_group = $request->class_group;
            $docs->doc_name = $request->doc_name;
            $docs->created_by = Auth::user()->user_name;
            $docs->updated_by = Auth::user()->user_name;
            $docs->save();

            return redirect()->route('settings.claims.claimAckDoc')->with('success', 'Claim Document added successfully');
        } catch (\Throwable $e) {
            dd($e);
            return redirect()->route('settings.claims.claimAckDoc')->with('error', 'Failed to add Claim Document');
        }
    }

    public function editClaimAckDoc(Request $request)
    {
        // dd($request);
        $request->validate([
            'class_group' => 'required',
            'id' => 'required',
            'doc_name' => 'required',
        ]);

        try {
            $ClaimAckDoc = ClaimAckParams::where('id', $request->id)
                ->update([
                    'doc_name' => $request->doc_name,
                    'class_group' => $request->class_group,
                ]);

            return redirect()->route('settings.claims.claimAckDoc')->with('success', 'Claim Document edited successfully');
        } catch (\Throwable $e) {
            return redirect()->route('settings.claims.claimAckDoc')->with('error', 'Failed to edit Claim Document');
        }
    }

    public function deleteClaimAckDoc(Request $request)
    {
        // dd($request);
        $request->validate([
            'id' => 'required',
        ]);

        try {
            $ClaimAckDoc = ClaimAckParams::where('id', $request->id)->first();
            $ClaimAckDoc->delete();

            return [
                'status' => Response::HTTP_OK,
                'message' => 'Item deleted successfully'
            ];
        } catch (\Throwable $e) {
            return [
                'status' => $e->getCode(),
                'message' => 'Failed to delete item'
            ];
        }
    }

    public function claimStatus(Request $request)
    {
        if (!$request->ajax()) {
            return view('settings.claims.claim_status');
        } else {
            $whtRates = ClaimStatusParam::all();
            return DataTables::of($whtRates)
                ->addColumn('action', function ($data) {
                    return "<button class='btn btn-outline-primary btn-sm edit' data-data='$data' data-bs-toggle='modal' data-bs-target='#statusModal'>
                                <i class='fa fa-pencil'></i> Edit
                            </button>
                            <button class='btn btn-outline-danger btn-sm delete'  data-data='$data'> <i class='fa fa-trash'></i> Delete</button>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function saveClaimStatus(Request $request)
    {
        $request->validate([
            'description' => 'required',
        ]);

        try {
            $id = (int)ClaimStatusParam::max('id') + 1;

            $docs = new ClaimStatusParam();
            $docs->id = $id;
            $docs->description = $request->description;
            $docs->created_by = Auth::user()->user_name;
            $docs->updated_by = Auth::user()->user_name;
            $docs->save();

            return redirect()->route('settings.claims.claimStatus')->with('success', 'Claim Document added successfully');
        } catch (\Throwable $e) {
            dd($e);
            return redirect()->route('settings.claims.claimStatus')->with('error', 'Failed to add Claim Document');
        }
    }

    public function editClaimStatus(Request $request)
    {
        // dd($request);
        $request->validate([
            'id' => 'required',
            'description' => 'required',
        ]);

        try {
            $ClaimStatus = ClaimStatusParam::where('id', $request->id)
                ->update([
                    'description' => $request->description,
                ]);

            return redirect()->route('settings.claims.claimStatus')->with('success', 'Claim Document edited successfully');
        } catch (\Throwable $e) {
            return redirect()->route('settings.claims.claimStatus')->with('error', 'Failed to edit Claim Document');
        }
    }

    public function deleteClaimStatus(Request $request)
    {
        // dd($request);
        $request->validate([
            'id' => 'required',
        ]);

        try {
            $ClaimStatus = ClaimStatusParam::where('id', $request->id)->first();
            $ClaimStatus->delete();

            return [
                'status' => Response::HTTP_OK,
                'message' => 'Item deleted successfully'
            ];
        } catch (\Throwable $e) {
            return [
                'status' => $e->getCode(),
                'message' => 'Failed to delete item'
            ];
        }
    }
}
