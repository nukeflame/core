<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use App\Models\Classes;
use App\Models\ClassGroup;
use App\Models\ClauseParam;
use App\Models\CoverRegister;
use App\Models\CoverReinclass;
use App\Models\CoverRipart;
use App\Models\CoverSlipWording;
use App\Models\ReinsClass;
use App\Models\SlipTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class PrintoutSetupController extends Controller
{
    public function slipTemplate(Request $request)
    {
        $wording = SlipTemplate::where('treaty_type', $request->treaty_type)->first();
        $classes = ReinsClass::where('status', 'A')->get();
        $trans_type = $request->trans_type ?? 'NEW';
        $classGroups = ClassGroup::get(['group_code', 'group_name']);
        $class = Classes::where('status', 'A')->get(['class_code', 'class_name', 'status']);
        #
        return view('printouts.setup.sliptemplate', [
            'treaty_type' => $request->treaty_type,
            'wording' => $wording,
            'classes' => $classes,
            'trans_type' => $trans_type,
            'classGroups' => $classGroups,
            'class' => $class
        ]);
    }


    public function coverslip_datatable()
    {
        $query = DB::table('clauses_param')->get();
        $actionable = true;

        return DataTables::of($query)
            ->addColumn('clause_group', function ($data) {
                $class = Classes::where('class_code', $data->class_code)->first();
                $class_group = ClassGroup::where('group_code', $class?->class_group_code)->first();
                return $class_group?->group_name;
            })
            ->addColumn('class_name', function ($data) {
                $class = Classes::where('class_code', $data?->class_code)->first();
                return $class?->class_name;
            })
            ->addColumn('clause_wording', function ($data) {
                // $truncated = Str::limit($data->clause_wording, 170);
                return '---';
            })
            ->addColumn('status', function ($data) {
                $status = "";
                switch ($data->status) {
                    case 'A':
                        $status .= '<span class="badge bg-success-gradient">Accessible</span>';
                        break;
                    default:
                        $status .= '<span class="badge bg-success-gradient">Restricted</span>';
                }
                return $status;
            })
            ->addColumn('action', function ($data) use ($actionable) {
                $btn = "";
                if ($actionable) {
                    $btn .= "<button class='btn btn-outline-dark btn-sm edit-clause action-btn' data-clause-id='{$data->clause_id}' data-class-code='{$data->class_code}' data-class-group='{$data->class_group_code}'>Edit</button>";
                    $btn .= " <button class='btn btn-outline-danger btn-sm remove-clause action-btn'  data-clause-id='{$data->clause_id}' data-title='{$data->clause_title}' data-class-code='{$data->class_code}' data-class-group='{$data->class_group_code}'>Remove</button>";
                }
                return $btn;
            })
            ->rawColumns(['status', 'action', 'clause_wording'])
            ->make(true);
    }

    public function saveSlipTemplate(Request $request)
    {
        $request->validate([
            'classcode' => 'required',
            'class_group' => 'required',
        ]);
        $types_of_bus = BusinessType::get(['bus_type_id', 'bus_type_name']);
        $classGroup = ClassGroup::where('group_code', $request->get('class_group'))->first();
        $classcode = Classes::where('class_group_code', $classGroup->group_code)->first();

        $clauses = ClauseParam::where('clause_id', $request->clause)->first();

        return view('printouts.setup.sliptemplate_form', [
            'trans_type' => 'NEW',
            'types_of_bus' => $types_of_bus,
            'classGroup' => $classGroup,
            'classcode' => $classcode,
            'clauses' => $clauses,
        ]);
    }

    public function slip(Request $request)
    {
        $cover = CoverRegister::with('customer')->where('endorsement_no', $request->endorsement_no)->first();
        $reinClasses = CoverReinclass::where('endorsement_no', $cover->endorsement_no)->pluck('reinclass')->toArray();
        $reinsurers = CoverRipart::where('endorsement_no', $cover->endorsement_no)->get();
        $wording = CoverSlipWording::where('endorsement_no', $cover->endorsement_no)->first();
        $standardWording = SlipTemplate::where('treaty_type', $request->treaty_type)
            // ->whereIn('rein_class',$reinClasses)
            // ->get();
            ->first();

        return view('printouts.setup.slip', [
            'cover' => $cover,
            'reinsurers' => $reinsurers,
            'wording' => $wording,
            'standardWording' => $standardWording,
            'reinClasses' => $reinClasses,
        ]);
    }

    public function saveSlip(Request $request)
    {
        $request->validate([
            'endorsement_no' => 'required',
            'wording' => 'required'
        ]);

        try {
            $wording = CoverSlipWording::where('endorsement_no', $request->endorsement_no)->exists();
            if ($wording) {
                CoverSlipWording::where('endorsement_no', $request->endorsement_no)
                    ->update([
                        'wording' => $request->wording,
                        'updated_by' => Auth::user()->user_name,
                    ]);
            } else {
                $cover = CoverRegister::where('endorsement_no', $request->endorsement_no)->first();
                CoverSlipWording::create([
                    'cover_no' => $cover->cover_no,
                    'endorsement_no' => $cover->endorsement_no,
                    'wording' => $request->wording,
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }

            return redirect()->back()->with('success', 'Successfully Saved wording');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to save wording');
        }
    }

    public function saveCluse(Request $request)
    {
        $request->validate([
            'clause_title' => 'required',
            'type_of_bus' => 'required',
            'details' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $clause = ClauseParam::where('clause_title', $request->clause_title)
                ->where('type_of_bus', $request->type_of_bus)
                ->where('clause_wording', $request->details)->exists();
            $classes = Classes::where('class_code', $request->classcode)->first();
            $classGroup = ClassGroup::where('group_code', $request->class_group_code)->first();

            if ($clause) {
                ClauseParam::where('clause_title', $request->clause_title)
                    ->where('class_code', $request->type_of_bus)->where('details', $request->details)
                    ->update([
                        'clause_title' => $request->clause_title,
                        'class_code' => $classes->class_code,
                        'clause_wording' => $request->details,
                        'updated_by' => Auth::user()->user_name,
                    ]);
            } else {
                $id = (int) ClauseParam::max('clause_id') ?? 0;
                $clause_id = $id + 1;
                ClauseParam::create([
                    'clause_id' => $clause_id,
                    'clause_title' => $request->clause_title,
                    'class_code' => $classes->class_code,
                    'clause_wording' => $request->details,
                    'type_of_bus' => $request->type_of_bus,
                    'class_group_code' => $classGroup->group_code,
                    'status' => 'A',
                    'created_by' => Auth::user()->user_name,
                    'updated_by' => Auth::user()->user_name,
                ]);
            }

            DB::commit();
            Session::Flash('success', 'Clause has been saved successfully');
            return redirect()->route('docs-setup.slip-template');
        } catch (\Exception $e) {
            DB::rollback();
            Session::Flash('error', 'Failed to save Clause');
            return redirect()->back()->with('error', 'Failed to save Clause');
        }
    }

    public function editCluse(Request $request)
    {
        $request->validate([
            'clause_title' => 'required',
            'type_of_bus' => 'required',
            'details' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $clause = ClauseParam::where('clause_id', $request->clause_id)->first();
            $classes = Classes::where('class_code', $request->classcode)->first();
            $classGroup = ClassGroup::where('group_code', $request->class_group_code)->first();

            if ($clause) {
                ClauseParam::where('clause_id', $request->clause_id)
                    ->update([
                        'clause_title' => $request->clause_title,
                        'class_code' => $classes->class_code,
                        'clause_wording' => $request->details,
                        'type_of_bus' => $request->type_of_bus,
                        'updated_by' => Auth::user()->user_name,
                        'class_group_code' => $classGroup->group_code,
                    ]);
            }
            DB::commit();
            Session::Flash('success', 'Clause has been saved successfully');
            return redirect()->route('docs-setup.slip-template');
        } catch (\Exception $e) {
            DB::rollback();
            Session::Flash('error', 'Failed to save Clause');
            return redirect()->back()->with('error', 'Failed to save Clause');
        }
    }

    public function deleteClause(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
                'class_code' => 'required|max:20',
                'class_group_code' => 'required|string|max:20',
            ]);

            $clause = ClauseParam::where('clause_id', $request->id)->first();
            if ($clause) {
                $clause->delete();
            }

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Schedule item removed successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to remove clause item'
            ]);
        }
    }

    private static function getOS()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        if (stripos($user_agent, 'windows') !== false) {
            return 'windows';
        } elseif (stripos($user_agent, 'mac') !== false) {
            return 'mac';
        } elseif (stripos($user_agent, 'linux') !== false) {
            return 'linux';
        }
        return 'unknown';
    }

    private static function getWordPath()
    {
        $os = self::getOS();

        switch ($os) {
            case 'windows':
                // Common Windows Word locations
                $paths = [
                    'C:\\Program Files\\Microsoft Office\\root\\Office16\\WINWORD.EXE',
                    'C:\\Program Files (x86)\\Microsoft Office\\root\\Office16\\WINWORD.EXE',
                    'C:\\Program Files\\Microsoft Office\\Office16\\WINWORD.EXE'
                ];
                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        return $path;
                    }
                }
                return null;

            case 'mac':
                return '/Applications/Microsoft Word.app';

            case 'linux':
                $paths = [
                    '/usr/bin/libreoffice',
                    '/usr/bin/openoffice',
                    '/usr/bin/soffice'
                ];
                foreach ($paths as $path) {
                    if (file_exists($path)) {
                        return $path;
                    }
                }
                return null;

            default:
                return null;
        }
    }


    public function schedulesOpenWord(Request $request)
    {
        try {
            $os = self::getOS();
            $wordPath = self::getWordPath();

            $tempPath = storage_path('app/temp');
            $tempFile = $tempPath . '/newdocument.docx';

            if (!File::exists($tempPath)) {
                File::makeDirectory($tempPath, 0755, true);
            }

            if (!File::exists(storage_path('app/template.docx'))) {
                File::put(storage_path('app/template.docx'), '');
            }

            if (!File::exists($tempFile)) {
                File::copy(storage_path('app/template.docx'), $tempFile);
            }
            $tempFilePath = $tempFile;
            $formattedPath = str_replace('\\', '/', realpath($tempFilePath));

            return response()->json([
                'file_path' => $formattedPath,
                'os' => $os,
                'word_path' => $wordPath,
                'success' => true
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
