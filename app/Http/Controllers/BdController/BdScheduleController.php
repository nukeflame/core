<?php

namespace App\Http\Controllers\BdController;

use App\Http\Controllers\Controller;
use App\Models\Bd\Leads\LeadStatus;
use App\Models\Bd\OperationChecklist;
use App\Models\BdScheduleData;
use App\Models\BusinessType;
use App\Models\Classes;
use App\Models\ClassGroup;
use App\Models\CustomerTypes;
use App\Models\Bd\DocType;
use App\Models\ReinsClass;
use App\Models\ScheduleHeader;
use App\Models\Bd\StageDocument;
use App\Models\TypeOfSumInsured;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Session;
use Yajra\Datatables\Datatables;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BdScheduleController extends Controller
{

    public function bd_schedule_info(Request $request)
    {
        $classes = Classes::select(['class_name', 'class_code'])->get();
        $classGroups = ClassGroup::select(['group_code', 'group_name'])->get();
        $type_of_sum_insured = TypeOfSumInsured::select(['sum_insured_code', 'sum_insured_name'])->get();

        return view('Bd_views.BdSchedule.Bd_schedule_info', compact(
            'classes',
            'classGroups',
            'type_of_sum_insured'
        ));
    }


    public function bd_schedule_add_form(Request $request)
    {
        $id = $request->id;
        $classes = Classes::select(['class_name', 'class_code'])->get();
        $classGroups = ClassGroup::select(['group_code', 'group_name'])->get();
        $type_of_sum_insured = TypeOfSumInsured::select(['sum_insured_code', 'sum_insured_name'])->get();
        $customerTypes = CustomerTypes::select(['type_id', 'type_name', 'code'])->get();
        if (isset($id)) {
            $schedule = ScheduleHeader::where('id', $id)->first();
            if ($schedule) {
                $schedule->business_type = $schedule->business_type ?? $schedule->bus_type ?? null;
                $schedule->class = $schedule->class ?? $schedule->class_code ?? null;
                $schedule->class_group = $schedule->class_group ?? $schedule->class_group_code ?? null;
                $schedule->sum_insured_type = $schedule->sum_insured_type ?? $schedule->type_of_sum_insured ?? null;
            }

            return view(
                'Bd_views.BdSchedule.schedule_header_add_form',
                compact(
                    'classes',
                    'classGroups',
                    'type_of_sum_insured',
                    'schedule'
                )
            );
        } else {
            return view(
                'Bd_views.BdSchedule.schedule_header_add_form',
                compact(
                    'classes',
                    'classGroups',
                    'type_of_sum_insured',
                )
            );
        }
    }

    public function bd_schedule_header_add(Request $request)
    {

        $id = $request->id;
        $bus_type = $request->business_type;
        $table = 'schedule_headers';
        $columns = Schema::getColumnListing($table);
        // dd($request->all());

        try {
            DB::beginTransaction();

            $rules = [
                'name' => 'required|string|max:100',
                'position' => 'required',
                'amount_field' => 'required',
            ];

            if ($bus_type == 'FAC') {
                $rules['class'] = 'required';
                $rules['class_group'] = 'required';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $scheduleData = [
                'name' => $request->name,
                'position' => $request->position,
                'amount_field' => $request->amount_field,
                'sum_insured_type' => $request->sum_insured_type ?? '',
                'data_determinant' => $request->data_determinant ?? '',
                'class' => $request->class ?? '',
                'class_group' => $request->class_group ?? '',
            ];

            if (in_array('business_type', $columns, true)) {
                $scheduleData['business_type'] = $request->business_type;
            }

            if (in_array('bus_type', $columns, true)) {
                $scheduleData['bus_type'] = $request->business_type;
            }

            if (in_array('class_code', $columns, true)) {
                $scheduleData['class_code'] = $request->class ?? '';
            }

            if (in_array('class_group_code', $columns, true)) {
                $scheduleData['class_group_code'] = $request->class_group ?? '';
            }

            if (in_array('type_of_sum_insured', $columns, true)) {
                $scheduleData['type_of_sum_insured'] = $request->sum_insured_type ?? '';
            }

            if (in_array('created_at', $columns, true)) {
                $scheduleData['created_at'] = Carbon::now();
            }

            if (in_array('updated_at', $columns, true)) {
                $scheduleData['updated_at'] = Carbon::now();
            }

            $scheduleData = collect($scheduleData)
                ->filter(function ($value, $key) use ($columns) {
                    return in_array($key, $columns, true);
                })
                ->all();

            if (isset($id)) {
                DB::table($table)->where('id', $id)->update($scheduleData);
            } else {
                $checkColumns = ['name', 'position', 'amount_field', 'sum_insured_type', 'data_determinant', 'class', 'class_group', 'business_type', 'bus_type'];
                $existsQuery = DB::table($table);
                foreach ($checkColumns as $checkColumn) {
                    if (array_key_exists($checkColumn, $scheduleData)) {
                        $existsQuery->where($checkColumn, $scheduleData[$checkColumn]);
                    }
                }
                $exists = $existsQuery->exists();
                // dd($exists);



                if ($exists) {
                    return redirect()->back()->with('error', 'Schedule header with the same details already exists');
                } else {
                    // dd($request->all());
                    DB::table($table)->insert($scheduleData);
                }
            }



            DB::commit();

            return redirect()->route('bd.schedule-headers.index');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save schedule header');
        }
    }

    public function bd_schedule_header_data()
    {
        $columns = Schema::getColumnListing('schedule_headers');
        $idColumn = in_array('id', $columns, true) ? 'id' : null;
        $nameColumn = in_array('name', $columns, true)
            ? 'name'
            : (in_array('header_name', $columns, true) ? 'header_name' : null);

        $scheduleheaders = DB::table('schedule_headers')->get();

        $mappedHeaders = $scheduleheaders->map(function ($row) use ($idColumn, $nameColumn) {
            $id = $idColumn ? ($row->{$idColumn} ?? null) : null;
            $name = $nameColumn ? ($row->{$nameColumn} ?? null) : null;
            $busType = $row->bus_type ?? $row->business_type ?? null;
            $sumInsuredType = $row->sum_insured_type ?? $row->type_of_sum_insured ?? null;
            $class = $row->class ?? $row->class_code ?? null;
            $classGroup = $row->class_group ?? $row->class_group_code ?? null;

            return [
                'id' => $id,
                'name' => $name ?: 'N/A',
                'bus_type' => $busType,
                'position' => $row->position ?? null,
                'amount_field' => $row->amount_field ?? null,
                'sum_insured_type' => $sumInsuredType,
                'data_determinant' => $row->data_determinant ?? null,
                'class' => $class,
                'class_group' => $classGroup,
                'is_template' => false,
            ];
        });

        return dataTables::of($mappedHeaders)->make(true);
    }
    public function delete_schedule_header(Request $request)
    {
        $id = $request->id;
        try {
            DB::beginTransaction();
            DB::table('schedule_headers')->where('id', $id)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Schedule header deleted successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => true, 'message' => 'Failed to delete schedule header']);
        }
    }

    public function bd_schedule_data()
    {
        $data = DB::table('schedule_headers');

        return dataTables::of($data)
            ->addColumn('name', function ($row) {
                return $row->name ?? $row->header_name ?? $row->clause_title ?? $row->type_of_bus ?? 'N/A';
            })
            ->addColumn('action', function ($row) {
                if (!Route::has('bd.schedule.data.create')) {
                    return '<span class="text-muted">N/A</span>';
                }

                $url = route('bd.schedule.data.create', ['id' => $row->id]);
                return '<a href="' . $url . '" class="btn btn-sm btn-primary">Open</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function bd_schedule_data_edit(Request $request)
    {
        $bd_schedule_data = BdScheduleData::all();
        return view('Bd_views.BdSchedule.Bd_schedule_data_edit', [
            'bd_schedule_data' => $bd_schedule_data,
        ]);
    }
    public function bd_schedule_data_create(Request $request)
    {
        $class = Classes::all();
        return view('Bd_views.BdSchedule.Bd_schedule_data_create', [
            'class' => $class,
        ]);
    }
    public function bd_schedule_slip_template(Request $request)
    {
        $wording = BdScheduleData::where('type_of_bus', $request->treaty_type)->first();
        $classes = ReinsClass::where('status', 'A')->get();
        $trans_type = 'NEW';
        $classGroups = ClassGroup::get(['group_code', 'group_name']);
        $class = Classes::where('status', 'A')->get(['class_code', 'class_name', 'status']);

        return view('printouts.setup.bdschedule_template_data', [
            'treaty_type' => $request->treaty_type,
            'wording' => $wording,
            'classes' => $classes,
            'trans_type' => $trans_type,
            'classGroups' => $classGroups,
            'class' => $class
        ]);
    }


    public function bd_schedule_template_datatable()
    {
        $query = DB::table('bd_schedule_template_data')->get();
        $actionable = true;

        return dataTables::of($query)
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
    public function save_schedule_template(Request $request)
    {

        $request->validate([
            'classcode' => 'required',
            'class_group' => 'required',
        ]);
        $types_of_bus = BusinessType::get(['bus_type_id', 'bus_type_name']);
        $classGroup = ClassGroup::where('group_code', $request->class_group)->first();
        $classcode = Classes::where('class_code', $request->classcode)->first();

        $clauses = BdScheduleData::where('clause_id', $request->clause)->first();
        if ($clauses && $clauses->type_of_bus) {
            $clauses->type_of_bus = json_decode($clauses->type_of_bus, true);
        }

        return view('printouts.setup.bd_schedule_template_form', [
            'trans_type' => 'NEW',
            'types_of_bus' => $types_of_bus,
            'classGroup' => $classGroup,
            'classcode' => $classcode,
            'clauses' => $clauses,
        ]);
    }

    public function bd_schedule_template(Request $request)
    {
        $wording = BdScheduleData::where('type_of_bus', $request->type_of_bus)->first();
        $classes = ReinsClass::where('status', 'A')->get();
        $trans_type = $request->type_of_bus ?? 'NEW';
        $classGroups = ClassGroup::get(['group_code', 'group_name']);
        $class = Classes::where('status', 'A')->get(['class_code', 'class_name', 'status']);
        #
        return view('printouts.setup.bd_schedule_template_data', [
            'type_of_bus' => $request->treaty_type,
            'wording' => $wording,
            'classes' => $classes,
            'trans_type' => $trans_type,
            'classGroups' => $classGroups,
            'class' => $class
        ]);
    }
    public function edit_bd_schedule(Request $request)
    {
        $request->validate([
            'clause_title' => 'required',
            'type_of_bus' => 'required',
            'details' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $clause = BdScheduleData::where('clause_id', $request->clause_id)->first();
            $classes = Classes::where('class_code', $request->classcode)->first();
            $classGroup = ClassGroup::where('group_code', $request->class_group_code)->first();

            if ($clause) {
                BdScheduleData::where('clause_id', $request->clause_id)
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
            Session::Flash('success', 'template has been saved successfully');
            return redirect()->route('docs-setup.bd-schedule-slip-template');
        } catch (\Exception $e) {
            DB::rollback();
            Session::Flash('error', 'Failed to save template');
            return redirect()->back()->with('error', 'Failed to edit template');
        }
    }
    public function save_bd_schedule_template(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'clause_title' => 'required',
            'type_of_bus' => 'required',
            'details' => 'required',
        ]);

        DB::beginTransaction();
        #
        try {
            $clause = BdScheduleData::where('clause_title', $request->clause_title)
                ->whereJsonContains('type_of_bus', json_encode($request->type_of_bus))
                ->where('clause_wording', $request->details)->exists();
            $classes = Classes::where('class_code', $request->classcode)->first();
            $classGroup = ClassGroup::where('group_code', $request->class_group_code)->first();

            if ($clause) {
                BdScheduleData::where('clause_title', $request->clause_title)
                    ->where('class_code', $request->type_of_bus)->where('details', $request->details)
                    ->update([
                        'clause_title' => strtolower($request->clause_title),
                        'class_code' => $classes->class_code,
                        'clause_wording' => $request->details,
                        'type_of_bus' => json_encode($request->type_of_bus),
                        'updated_by' => Auth::user()->user_name,
                    ]);
            } else {
                $id = (int) BdScheduleData::max('clause_id') ?? 0;
                $clause_id = $id + 1;
                $data = BdScheduleData::create([
                    'clause_id' => $clause_id,
                    'clause_title' => strtolower($request->clause_title),
                    'class_code' => $classes->class_code,
                    'clause_wording' => $request->details,
                    'type_of_bus' => json_encode($request->type_of_bus),
                    'class_group_code' => $classGroup->group_code,
                    'status' => 'A',
                    'created_by' => Auth::user()->user_name,
                    // 'updated_by' => Auth::user()->user_name,
                ]);
            }

            DB::commit();
            Session::Flash('success', 'Bd schedule template has been saved successfully');
            return redirect()->route('docs-setup.bd-schedule-slip-template');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Failed to save template');
            return redirect()->back()->with('error', 'Failed to save template');
        }
    }

    public function delete_schedule_template(Request $request)
    {

        try {
            $request->validate([
                'id' => 'required',
                'class_code' => 'required|max:20',
                'class_group_code' => 'required|string|max:20',
            ]);

            $clause = BdScheduleData::where('clause_id', $request->id)->first();
            if ($clause) {
                $clause->delete();
            }

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Schedule  template item removed successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => $e->getCode(),
                'message' => 'Failed to remove schedule template item'
            ]);
        }
    }

    //lead status
    public function bd_lead_status_info(Request $request)
    {
        return view('Bd_views.LeadStatus.Bd_lead_status_info');
    }
    public function bd_lead_status_add_form(Request $request)
    {
        $id = $request->id;
        if (isset($id)) {
            $LeadStatus = Leadstatus::where('lead_id', $id)->first();

            return view(
                'Bd_views.LeadStatus.lead_status_add_form',
                compact(
                    'LeadStatus'
                )
            );
        } else {
            return view(
                'Bd_views.LeadStatus.lead_status_add_form'
            );
        }
    }
    public function bd_lead_status_add(Request $request)
    {


        $id = $request->id;
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'stage' => 'required',
                'category_type' => 'required',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            if (isset($id)) {
                LeadStatus::where('lead_id', $id)->update([
                    'status_name' => $request->name,
                    'id' => $request->stage,
                    'category_type' => $request->category_type,
                    'updated_at' => Carbon::now(),
                ]);
            } else {

                LeadStatus::create([
                    'status_name' => $request->name,
                    'id' => $request->stage,
                    'category_type' => $request->category_type,
                    'created_at' => Carbon::now(),
                ]);
            }



            DB::commit();
            if (isset($id)) {
                Session::flash('success', 'Lead status  information updated successfully');
            } else {
                Session::flash('success', 'Lead status information saved successfully');
            }

            return redirect()->route('lead.status.info');
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('error', 'An error occurred while saving the lead status');
            return redirect()->back()->with('error', 'Failed to save lead status');
        }
    }
    public function bd_lead_status_data()
    {
        $LeadStatus = DB::table('lead_status')->get();
        return dataTables::of($LeadStatus)

            ->editColumn('category_type', function ($fn) {
                return $fn->category_type == '1' ? 'Quotation' : ($fn->category_type == '2' ? 'Facultative Offer' : 'N/A');
            })
            ->addColumn('edit', function ($fn) {

                return '<a href="#" class="text-white update_lead_status btn btn-sm btn-success rounded-pill" title="Update lead status" data-id="' . $fn->lead_id . '"> <i class="bx bx-refresh"></i>Edit</a>';
            })
            ->addColumn('delete', function ($fn) {

                return '<a href="#" class="text-white delete btn btn-sm btn-danger rounded-pill" title="Delete lead status" data-id="' . $fn->lead_id . '"> <i class="bx bx-trash"></i>Delete</a>';
            })
            ->rawColumns(['edit', 'delete'])
            ->make(true);
    }
    public function delete_lead_status(Request $request)
    {
        $id = $request->id;
        try {
            DB::beginTransaction();
            $LeadStatus = LeadStatus::where('lead_id', $id)->first();
            $LeadStatus->delete();
            DB::commit();
            Session::flash('success', 'lead status deleted successfully');
            return redirect()->route('bd.lead.status.info');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Failed to delete lead status');
            return redirect()->back()->with('error', 'Failed to delete lead status');
        }
    }

    // stage_doc
    public function bd_stage_doc_info(Request $request)
    {
        return view('Bd_views.DocTypes.stage_doc_info');
    }
    public function stage_doc_form(Request $request)
    {
        $id = $request->id;
        $Documents = DocType::get(['id', 'doc_type']);
        $types_of_bus = BusinessType::get();
        if (isset($id)) {
            $StageDocuments = StageDocument::where('id', $id)->first();
            if ($StageDocuments && $StageDocuments->type_of_bus) {
                $StageDocuments->type_of_bus = json_decode($StageDocuments->type_of_bus, true);
            }


            return view(
                'Bd_views.DocTypes.stage_doc_add_form',
                compact(
                    'StageDocuments',
                    'Documents',
                    'types_of_bus'
                )
            );
        } else {
            return view(
                'Bd_views.DocTypes.stage_doc_add_form',
                compact('Documents', 'types_of_bus')
            );
        }
    }
    public function bd_stage_doc_add(Request $request)
    {

        $id = $request->id;
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'stage' => 'required',
                'doc_type' => 'required',
                'mandatory' => 'required',
                'category_type' => 'required',
                'type_of_bus' => 'required',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            if (isset($id)) {
                StageDocument::where('id', $id)->update([
                    'stage' => $request->stage,
                    'doc_type' => $request->doc_type,
                    'mandatory' => $request->mandatory,
                    'category_type' => $request->category_type,
                    'type_of_bus' => json_encode($request->type_of_bus),
                    'updated_at' => now(),
                ]);
            } else {

                StageDocument::create([
                    'stage' => $request->stage,
                    'doc_type' => $request->doc_type,
                    'mandatory' => $request->mandatory,
                    'category_type' => $request->category_type,
                    'type_of_bus' => json_encode($request->type_of_bus),
                    'created_at' => now(),
                ]);
            }



            DB::commit();
            if (isset($id)) {
                Session::flash('success', 'Lead status  information updated successfully');
            } else {
                Session::flash('success', 'Lead status information saved successfully');
            }

            return redirect()->route('stage.doc.info');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving stage document: ' . $e->getMessage());

            Session::flash('error', 'An error occurred while saving the stage document');
            return redirect()->back()->with('error', 'Failed to save stage document');
        }
    }
    public function bd_stage_doc_data()
    {
        $StageDocuments = DB::table('stage_documents')->get();
        return dataTables::of($StageDocuments)

            ->editColumn('mandatory_1', function ($fn) {
                return $fn->mandatory == 'Y' ? 'Yes' : ($fn->mandatory == 'N' ? 'No' : 'N/A');
            })
            ->editColumn('doc_type', function ($fn) {
                $doc_type = DB::table('doc_types')->where('id', $fn->doc_type)->value('doc_type');
                return $doc_type ?? 'N/A';
            })
            ->editColumn('category', function ($fn) {

                return $fn->category_type == '1' ? 'Quotation' : ($fn->category_type == '2' ? 'Fac Offer' : 'N/A');
            })
            ->editColumn('busines_type', function ($fn) {
                $busTypes = json_decode($fn->type_of_bus, true);

                if (is_array($busTypes) && !empty($busTypes)) {
                    $names = DB::table('business_types')
                        ->whereIn('bus_type_id', $busTypes)
                        ->pluck('bus_type_name')
                        ->toArray();

                    return implode(', ', $names); // Join names with comma
                }

                return 'N/A';
            })

            ->addColumn('edit', function ($fn) {

                return '<a href="#" class="text-white update_stage_doc_type btn btn-sm btn-success rounded-pill" title="Update stage docs" data-id="' . $fn->id . '"> <i class="bx bx-refresh"></i>Edit</a>';
            })
            ->addColumn('delete', function ($fn) {

                return '<a href="#" class="text-white delete btn btn-sm btn-danger rounded-pill" title="Delete stage doc" data-id="' . $fn->id . '"> <i class="bx bx-trash"></i>Delete</a>';
            })
            ->rawColumns(['edit', 'delete'])
            ->make(true);
    }
    public function delete_stage_doc(Request $request)
    {
        $id = $request->id;
        try {
            DB::beginTransaction();
            $StageDocument = StageDocument::where('id', $id)->first();
            $StageDocument->delete();
            DB::commit();
            Session::flash('success', 'Stage document deleted successfully');
            return redirect()->route('stage.doc.type.info');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Failed to delete stage document');
            return redirect()->back()->with('error', 'Failed to delete stage document');
        }
    }

    // bd documents types
    public function bd_doc_type_info(Request $request)
    {
        return view('Bd_views.DocTypes.doc_type_info');
    }
    public function doc_type_form(Request $request)
    {
        $id = $request->id;

        if (isset($id)) {
            $Documents = DocType::where('id', $id)->first();


            return view(
                'Bd_views.DocTypes.doc_type_add_form',
                compact(
                    'Documents',
                )
            );
        } else {
            return view(
                'Bd_views.DocTypes.doc_type_add_form'
            );
        }
    }
    public function bd_doc_type_add(Request $request)
    {


        $id = $request->id;
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'doc_type' => 'required',
                'description' => 'required',
                'bus_type' => 'required',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            if (isset($id)) {
                DocType::where('id', $id)->update([
                    'doc_type' => $request->doc_type,
                    'description' => $request->description,
                    'checkbox_doc' => $request->checkbox_doc,
                    'attachment_file' => $request->attachment_file ?? '',
                    'bus_type' => $request->bus_type ?? '',
                    'updated_at' => now(),
                ]);
            } else {


                $file = $request->cedant_file;
                $mimetype = null;
                $Filename = null;

                if (!is_null($file)) {

                    if ($file->isValid()) {
                        $uploadsPath = 'uploads/cedant_docs';
                        if (!file_exists($uploadsPath)) {
                            mkdir($uploadsPath, 0777, true);
                        }
                    } else {
                        return redirect()->back()->with('error', 'Invalid file upload');
                    }

                    $mimetype = $file->getClientMimeType();
                    $fileContent = file_get_contents($file);
                    $encodedFileContent = base64_encode($fileContent);

                    $originalNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                    $Filename = mt_rand() . '_' . $originalNameWithoutExtension . '.' . $file->getClientOriginalExtension();
                    $generatedFilePath = $uploadsPath . '/' . $Filename;

                    // $file->move($uploadsPath, $Filename);
                    $file->move($uploadsPath, $Filename);
                }
                DocType::create([
                    'doc_type' => $request->doc_type,
                    'description' => $request->description,
                    'attachment_file' => $request->attachment_file ?? '',
                    'bus_type' => $request->bus_type ?? '',
                    'checkbox_doc' => $request->checkbox_doc,
                    'mimetype' => $mimetype,
                    'file_name' => $Filename,
                    'created_at' => now(),
                ]);
            }



            DB::commit();
            if (isset($id)) {
                Session::flash('success', 'bd  doc updated successfully');
            } else {
                Session::flash('success', 'bd doc saved successfully');
            }

            return redirect()->route('doc.type.info');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving  document: ' . $e->getMessage());

            Session::flash('error', 'An error occurred while saving the  document');
            return redirect()->back()->with('error', 'Failed to save document');
        }
    }
    public function bd_doc_type_data()
    {
        $doc_types = DB::table('doc_types')->get();
        return dataTables::of($doc_types)
            ->addColumn('edit', function ($fn) {

                return '<a href="#" class="text-white update_doc_type btn btn-sm btn-success rounded-pill" title="Update stage docs" data-id="' . $fn->id . '"> <i class="bx bx-refresh"></i>Edit</a>';
            })
            ->addColumn('delete', function ($fn) {

                return '<a href="#" class="text-white delete btn btn-sm btn-danger rounded-pill" title="Delete  doc type" data-id="' . $fn->id . '"> <i class="bx bx-trash"></i>Delete</a>';
            })
            ->rawColumns(['edit', 'delete'])
            ->make(true);
    }
    public function delete_doc_type(Request $request)
    {
        $id = $request->id;
        try {
            DB::beginTransaction();
            $doc_type = DocType::where('id', $id)->first();
            $doc_type->delete();
            DB::commit();
            Session::flash('success', 'document deleted successfully');
            return redirect()->route('doc.type.info');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Failed to delete document');
            return redirect()->back()->with('error', 'Failed to delete document');
        }
    }


    // checklist operation
    public function operationchecklist_info(Request $request)
    {
        return view('Bd_views.TreatyOperationChecklist.treaty_operation_info');
    }
    public function operationchecklist_form(Request $request)
    {
        $id = $request->id;

        if (isset($id)) {
            $OperationChecklist = OperationChecklist::where('id', $id)->first();


            return view(
                'Bd_views.TreatyOperationChecklist.treaty_operation_add_form',
                compact(
                    'OperationChecklist',
                )
            );
        } else {
            return view(
                'Bd_views.TreatyOperationChecklist.treaty_operation_add_form'
            );
        }
    }
    public function operationchecklist_add(Request $request)
    {


        $id = $request->id;
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            if (isset($id)) {
                OperationChecklist::where('id', $id)->update([
                    'name' => $request->name,
                    'updated_at' => now(),
                ]);
            } else {

                OperationChecklist::create([
                    'name' => $request->name,
                    'created_by' => Auth::user()->user_name,
                    'created_at' => now(),
                ]);
            }



            DB::commit();
            if (isset($id)) {
                Session::flash('success', 'bd treaty operation updated successfully');
            } else {
                Session::flash('success', 'bd operation saved successfully');
            }

            return redirect()->route('operationchecklist.info');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving  document: ' . $e->getMessage());

            Session::flash('error', 'An error occurred while saving');
            return redirect()->back()->with('error', 'Failed to save');
        }
    }
    public function operationchecklist_data()
    {
        $doc_types = DB::table('treaty_operation_checklists')->get();
        return dataTables::of($doc_types)
            ->addColumn('edit', function ($fn) {

                return '<a href="#" class="text-white update_doc_type btn btn-sm btn-success rounded-pill" title="Update stage docs" data-id="' . $fn->id . '"> <i class="bx bx-refresh"></i>Edit</a>';
            })
            ->addColumn('delete', function ($fn) {

                return '<a href="#" class="text-white delete btn btn-sm btn-danger rounded-pill" title="Delete  doc type" data-id="' . $fn->id . '"> <i class="bx bx-trash"></i>Delete</a>';
            })
            ->rawColumns(['edit', 'delete'])
            ->make(true);
    }
    public function delete_operationchecklist(Request $request)
    {
        $id = $request->id;
        try {
            DB::beginTransaction();
            $doc_type = OperationChecklist::where('id', $id)->first();
            $doc_type->delete();
            DB::commit();
            Session::flash('success', 'operation checklist deleted successfully');
            return redirect()->route('doc.type.info');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Failed to delete operation checklist');
            return redirect()->back()->with('error', 'Failed to delete operation checklist');
        }
    }
}
