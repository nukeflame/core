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
use App\Models\QuoteScheduleHeader;
use App\Models\ReinsClass;
use App\Models\ScheduleHeader;
use App\Models\SlipTemplate;
use App\Models\Bd\StageDocument;
use App\Services\S3AttachmentHandler;
use App\Models\TypeOfSumInsured;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BdScheduleController extends Controller
{

    public function getSlipTemplates(Request $request)
    {
        $wording = BdScheduleData::where('type_of_bus', $request->treaty_type)->first();
        $classes = ReinsClass::where('status', 'A')->get();
        $trans_type = 'NEW';
        $classGroups = ClassGroup::get(['group_code', 'group_name']);
        $class = Classes::where('status', 'A')->get(['class_code', 'class_name', 'class_group_code', 'status']);
        $businessTypes = BusinessType::get(['bus_type_id', 'bus_type_name']);
        $scheduleHeaders = QuoteScheduleHeader::select('id', 'name', 'class', 'class_group', 'business_type')->orderBy('name')->get();

        return view('business_development.settings.slip_template_data', [
            'treaty_type' => $request->treaty_type,
            'wording' => $wording,
            'classes' => $classes,
            'trans_type' => $trans_type,
            'classGroups' => $classGroups,
            'class' => $class,
            'businessTypes' => $businessTypes,
            'scheduleHeaders' => $scheduleHeaders,
        ]);
    }

    public function getSlipTemplateHeaders(Request $request)
    {
        $classGroupCode = $request->input('class_group_code', $request->input('class_group', ''));
        $classCode = $request->input('class_code', $request->input('class', ''));
        $rawBusType = strtoupper(trim((string) $request->input('business_type', $request->input('bus_type', ''))));
        $headerKeyword = trim((string) $request->input('header_keyword', ''));

        $requestedScheduleHeaderIds = collect($request->input('schedule_header_ids', []))
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if (!$classGroupCode && !$classCode && !$rawBusType && !$headerKeyword && empty($requestedScheduleHeaderIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule headers, class group, class code, or business type is required.',
                'headers' => [],
            ]);
        }

        $query = SlipTemplate::where('status', 'A');

        $busType = match ($rawBusType) {
            'FAC', 'FACULTATIVE', 'FPR', 'FNP' => 'FAC',
            'TRT', 'TREATY', 'TPR', 'TNP' => 'TRT',
            default => null,
        };

        if ($classGroupCode) {
            $query->where('class_group_code', $classGroupCode);
        }

        if ($classCode) {
            $query->where('class_code', $classCode);
        }

        $scheduleHeaderIds = collect($requestedScheduleHeaderIds);
        if ($scheduleHeaderIds->isEmpty() && ($headerKeyword !== '' || $classCode || $classGroupCode || $busType)) {
            $headerQuery = QuoteScheduleHeader::query();

            if ($classCode) {
                $headerQuery->where('class', $classCode);
            }
            if ($classGroupCode) {
                $headerQuery->where('class_group', $classGroupCode);
            }
            if ($busType) {
                $headerQuery->where('business_type', $busType);
            }
            if ($headerKeyword !== '') {
                $headerQuery->where('name', 'like', "%{$headerKeyword}%");
            }

            $scheduleHeaderIds = $headerQuery->pluck('id');
        }

        $hasScheduleHeaderFilter = $scheduleHeaderIds->isNotEmpty();
        if ($hasScheduleHeaderFilter) {
            $schIds = $scheduleHeaderIds->values()->all();
            $query->whereHas('scheduleHeaders', function ($q) use ($schIds) {
                $q->whereIn('quote_schedule_headers.id', $schIds);
            });
        }

        $templatesQuery = $query
            ->with('scheduleHeaders:id,name,position,amount_field,sum_insured_type,business_type');

        $template = $templatesQuery
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'No slip template found for the selected class.',
                'headers' => [],
            ]);
        }

        $templateHeaders = $template->scheduleHeaders;

        if ($hasScheduleHeaderFilter) {
            $matchingHeader = $templateHeaders
                ->whereIn('id', $scheduleHeaderIds->values()->all())
                ->sortBy('position')
                ->first();
        } else {
            $matchingHeader = [];
        }

        $headers = collect($matchingHeader ? [$matchingHeader] : []);
        $wording = (string) ($template->wording ?? '');

        return response()->json([
            'success' => true,
            'template_count' => 1,
            'template' => [
                'id' => $template->id,
                'schedule_title' => $template->schedule_title,
                'class_group_code' => $template->class_group_code,
                'class_code' => $template->class_code,
                'wording' => $wording,
                'description' => $template->description,
            ],
            'wording' => $wording,
            'headers' => $headers->map(function ($h) {
                return [
                    'id' => $h->id,
                    'name' => $h->name,
                    'position' => $h->position,
                    'amount_field' => $h->amount_field,
                    'sum_insured_type' => $h->sum_insured_type,
                    'business_type' => $h->business_type,
                ];
            }),
        ]);
    }


    public function getScheduleHeaders(Request $request)
    {
        $classes = Classes::select(['class_name', 'class_code', 'class_group_code'])->get();
        $classGroups = ClassGroup::select(['group_code', 'group_name'])->get();
        $type_of_sum_insured = TypeOfSumInsured::select(['sum_insured_code', 'sum_insured_name'])->get();
        $quote_schedule_columns = Schema::getColumnListing('quote_schedule_headers');

        return view('business_development.settings.schedule_info', compact(
            'classes',
            'classGroups',
            'type_of_sum_insured',
            'quote_schedule_columns'
        ));
    }

    public function getRiskParticulars(Request $request)
    {
        $classes = Classes::select(['class_name', 'class_code'])->get();
        $classGroups = ClassGroup::select(['group_code', 'group_name'])->get();
        $type_of_sum_insured = TypeOfSumInsured::select(['sum_insured_code', 'sum_insured_name'])->get();

        return view('business_development.settings.risk_particulars_info', compact(
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
                'business_development.settings.schedule_header_add_form',
                compact(
                    'classes',
                    'classGroups',
                    'type_of_sum_insured',
                    'schedule'
                )
            );
        } else {
            return view(
                'business_development.settings.schedule_header_add_form',
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
        $requestedTable = (string) $request->input('source_table', '');
        $allowedTables = ['schedule_headers', 'quote_schedule_headers'];

        if (in_array($requestedTable, $allowedTables, true)) {
            $table = $requestedTable;
        } elseif ($id && Schema::hasTable('quote_schedule_headers') && DB::table('quote_schedule_headers')->where('id', $id)->exists()) {
            // Backward-compatible fallback for existing quote schedule header edits.
            $table = 'quote_schedule_headers';
        } else {
            $table = 'schedule_headers';
        }

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
        } catch (Exception $e) {
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

    public function bd_quote_schedule_header_data(Request $request)
    {
        $columns = Schema::getColumnListing('quote_schedule_headers');

        if (empty($columns)) {
            return dataTables::of(collect([]))->make(true);
        }

        $query = DB::table('quote_schedule_headers')->select($columns);

        if ($request->filled('filter_business_type') && in_array('business_type', $columns, true)) {
            $query->where('business_type', $request->input('filter_business_type'));
        }

        if ($request->filled('filter_class_group') && in_array('class_group', $columns, true)) {
            $query->where('class_group', $request->input('filter_class_group'));
        }

        if ($request->filled('filter_class_name') && in_array('class', $columns, true)) {
            $query->where('class', $request->input('filter_class_name'));
        }

        $rows = $query->get();

        return dataTables::of($rows)->make(true);
    }
    public function delete_schedule_header(Request $request)
    {
        $id = $request->id;
        try {
            DB::beginTransaction();
            DB::table('schedule_headers')->where('id', $id)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Schedule header deleted successfully']);
        } catch (Exception $e) {
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
        return view('business_development.settings.Bd_schedule_data_edit', [
            'bd_schedule_data' => $bd_schedule_data,
        ]);
    }
    public function bd_schedule_data_create(Request $request)
    {
        $class = Classes::all();
        return view('business_development.settings.Bd_schedule_data_create', [
            'class' => $class,
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

    public function slipTemplateDatatable(Request $request)
    {
        $query = DB::table('slip_templates');
        $columns = Schema::getColumnListing('slip_templates');
        $keyColumn = collect(['id', 'slip_id', 'clause_id'])->first(function ($column) use ($columns) {
            return in_array($column, $columns, true);
        });

        if ($request->filled('treaty_type')) {
            $treatyType = strtoupper(trim((string) $request->input('treaty_type')));
            $query->where(function ($q) use ($treatyType) {
                $q->where('type_of_bus', $treatyType)
                    ->orWhere('treaty_type', $treatyType)
                    ->orWhere('type_of_bus', 'like', '%"' . $treatyType . '"%')
                    ->orWhere('type_of_bus', 'like', '%' . $treatyType . '%');
            });
        }

        $rows = $query->get();
        $businessTypeMap = BusinessType::get(['bus_type_id', 'bus_type_name'])->keyBy('bus_type_id');

        $classMap = Classes::select(['class_code', 'class_name', 'class_group_code'])
            ->get()
            ->keyBy('class_code');
        $groupMap = ClassGroup::select(['group_code', 'group_name'])
            ->get()
            ->keyBy('group_code');

        $normalized = collect($rows)->map(function ($row, $index) use ($classMap, $groupMap, $keyColumn, $businessTypeMap) {
            $status = strtoupper((string) ($row->status ?? 'A'));
            $classCode = $row->class_code ?? $row->rein_class ?? $row->class ?? null;
            $classRecord = $classCode && isset($classMap[$classCode]) ? $classMap[$classCode] : null;
            $rawGroupCode = $row->class_group_code ?? null;
            $rawGroupName = $row->class_group ?? $row->class_group_name ?? $row->group_name ?? null;
            $groupCode = $rawGroupCode ?: ($classRecord->class_group_code ?? null);

            if (!$groupCode && $rawGroupName) {
                $matchedGroup = $groupMap->first(function ($group) use ($rawGroupName) {
                    return strtolower(trim((string) $group->group_name)) === strtolower(trim((string) $rawGroupName));
                });
                $groupCode = $matchedGroup->group_code ?? null;
            }

            $groupRecord = $groupCode && isset($groupMap[$groupCode]) ? $groupMap[$groupCode] : null;
            $recordKey = $keyColumn ? ($row->{$keyColumn} ?? null) : null;
            $rawTypeOfBus = $row->type_of_bus ?? null;
            $typeOfBusValues = [];

            if (is_array($rawTypeOfBus)) {
                $typeOfBusValues = $rawTypeOfBus;
            } elseif (is_string($rawTypeOfBus) && $rawTypeOfBus !== '') {
                $decoded = json_decode($rawTypeOfBus, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $typeOfBusValues = $decoded;
                } else {
                    $typeOfBusValues = array_map('trim', explode(',', $rawTypeOfBus));
                }
            }

            if (empty($typeOfBusValues) && !empty($row->treaty_type)) {
                $typeOfBusValues = [(string) $row->treaty_type];
            }

            $typeOfBusValues = collect($typeOfBusValues)
                ->map(fn($item) => strtoupper(trim((string) $item)))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $typeOfBusDisplay = collect($typeOfBusValues)->map(function ($id) use ($businessTypeMap) {
                return $businessTypeMap[$id]->bus_type_name ?? $id;
            })->implode(', ');

            // Fetch linked schedule headers for this slip template
            $slipId = $row->id ?? $row->slip_id ?? $row->clause_id ?? null;
            $linkedHeaders = [];
            $linkedHeaderIds = [];
            if ($slipId) {
                $slipModel = SlipTemplate::find($slipId);
                if ($slipModel) {
                    $linkedHeaders = $slipModel->scheduleHeaders->pluck('name')->toArray();
                    $linkedHeaderIds = $slipModel->scheduleHeaders->pluck('id')->toArray();
                }
            }

            return (object) [
                'id' => $row->id ?? $row->slip_id ?? $row->clause_id ?? ($index + 1),
                'record_key' => $recordKey,
                'schedule_title' => $row->schedule_title ?? $row->title ?? $row->clause_title ?? 'Policy Wording',
                'class_group_code' => $groupCode,
                'class_code' => $classCode,
                'class_group' => $groupRecord->group_name ?? $row->class_group ?? $row->class_group_name ?? $row->group_name ?? '-',
                'class_name' => $classRecord->class_name ?? $row->class_name ?? $row->class ?? '-',
                'description' => $row->description ?? '-',
                'wording' => $row->wording ?? $row->clause_wording ?? $row->details ?? '',
                'type_of_bus' => $typeOfBusDisplay,
                'type_of_bus_values' => $typeOfBusValues,
                'status' => in_array($status, ['A', 'ACTIVE'], true) ? 'A' : 'I',
                'updated_at' => $row->updated_at ?? null,
                'schedule_headers' => implode(', ', $linkedHeaders) ?: '-',
                'schedule_header_ids' => $linkedHeaderIds,
            ];
        });

        $lastUpdated = $normalized->pluck('updated_at')->filter()->sort()->last();

        $meta = [
            'total_templates' => $normalized->count(),
            'class_groups' => $normalized->pluck('class_group')->filter()->unique()->count(),
            'class_names' => $normalized->pluck('class_name')->filter()->unique()->count(),
            'business_types' => $normalized
                ->pluck('type_of_bus_values')
                ->flatten()
                ->filter()
                ->unique()
                ->count(),
            'active_templates' => $normalized->where('status', 'A')->count(),
            'inactive_templates' => $normalized->where('status', 'I')->count(),
            'last_updated' => $lastUpdated ? (string) $lastUpdated : null,
        ];

        return dataTables::of($normalized)
            ->addIndexColumn()
            ->addColumn('status_badge', function ($row) {
                if (($row->status ?? 'I') === 'A') {
                    return '<span class="badge bg-success-gradient">Active</span>';
                }
                return '<span class="badge bg-secondary-gradient">Inactive</span>';
            })
            ->addColumn('action', function ($row) {
                $id = (int) ($row->record_key ?? 0);
                $scheduleTitle = e((string) ($row->schedule_title ?? ''));
                $classGroupCode = e((string) ($row->class_group_code ?? ''));
                $classCode = e((string) ($row->class_code ?? ''));
                $classGroup = e((string) ($row->class_group ?? ''));
                $className = e((string) ($row->class_name ?? ''));
                $description = e((string) ($row->description ?? ''));
                $wording = e((string) ($row->wording ?? ''));
                $typeOfBus = e((string) ($row->type_of_bus ?? ''));
                $status = e((string) ($row->status ?? 'A'));

                if ($id <= 0) {
                    return '<span class="text-muted">N/A</span>';
                }

                return
                    '<button type="button" class="btn btn-outline-dark btn-sm action-btn edit-slip-template" ' .
                    'data-id="' . $id . '" ' .
                    'data-schedule-title="' . $scheduleTitle . '" ' .
                    'data-class-group-code="' . $classGroupCode . '" ' .
                    'data-class-code="' . $classCode . '" ' .
                    'data-class-group="' . $classGroup . '" ' .
                    'data-class-name="' . $className . '" ' .
                    'data-description="' . $description . '" ' .
                    'data-wording="' . $wording . '" ' .
                    'data-type-of-bus="' . $typeOfBus . '" ' .
                    'data-status="' . $status . '">Edit</button> ' .
                    '<button type="button" class="btn btn-outline-danger btn-sm action-btn remove-slip-template" ' .
                    'data-id="' . $id . '" ' .
                    'data-schedule-title="' . $scheduleTitle . '">Remove</button>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->with(['meta' => $meta])
            ->make(true);
    }

    public function storeSlipTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|min:1',
            'schedule_title' => 'required|string|max:150',
            'class_group_code' => 'nullable|string|max:20',
            'class_code' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
            'wording' => 'nullable|string',
            'status' => 'required|string|in:A,I',
            'type_of_bus' => 'nullable',
            'type_of_bus.*' => 'nullable|string|max:20',
            'schedule_header_ids' => 'required|array|min:1',
            'schedule_header_ids.*' => 'required|integer|exists:quote_schedule_headers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Schema::hasTable('slip_templates')) {
            return response()->json([
                'success' => false,
                'message' => 'Slip template table is not available.',
            ], 500);
        }

        $columns = Schema::getColumnListing('slip_templates');
        $keyColumn = collect(['id', 'slip_id', 'clause_id'])->first(function ($column) use ($columns) {
            return in_array($column, $columns, true);
        });
        $id = $request->input('id');
        $defaultTreatyType = 'FAC';
        $submittedTypeOfBus = $request->input('type_of_bus', []);
        if (!is_array($submittedTypeOfBus)) {
            $submittedTypeOfBus = [$submittedTypeOfBus];
        }

        $resolvedTypeOfBusValues = collect($submittedTypeOfBus)
            ->map(fn($item) => strtoupper(trim((string) $item)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $resolvedTreatyType = strtoupper(trim((string) ($request->input('treaty_type') ?: ($resolvedTypeOfBusValues[0] ?? $defaultTreatyType))));
        $resolvedTypeOfBusValues = !empty($resolvedTypeOfBusValues) ? $resolvedTypeOfBusValues : [$resolvedTreatyType];

        $classGroupCode = (string) $request->input('class_group_code', '');
        $classCode = (string) $request->input('class_code', '');

        $classGroupName = null;
        if ($classGroupCode !== '') {
            $classGroupName = ClassGroup::where('group_code', $classGroupCode)->value('group_name');
        }

        $className = null;
        if ($classCode !== '') {
            $className = Classes::where('class_code', $classCode)->value('class_name');
        }
        $actor = auth()->user()->user_name ?? auth()->user()->name ?? auth()->user()->email ?? 'system';

        $payload = [
            'schedule_title' => $request->input('schedule_title'),
            'class_group_code' => $classGroupCode !== '' ? $classGroupCode : null,
            'class_code' => $classCode !== '' ? $classCode : null,
            'rein_class' => $classCode !== '' ? $classCode : null,
            'class_group' => $classGroupName,
            'class_name' => $className,
            'description' => $request->input('description'),
            'wording' => $request->input('wording'),
            'status' => $request->input('status', 'A'),
            'type_of_bus' => json_encode($resolvedTypeOfBusValues),
            'treaty_type' => $resolvedTreatyType,
            'title' => $request->input('schedule_title'),
            'clause_title' => $request->input('schedule_title'),
            'updated_by' => $actor,
        ];

        if (!$id) {
            $payload['created_by'] = $actor;
        }

        if (in_array('updated_at', $columns, true)) {
            $payload['updated_at'] = now();
        }

        if (!$id && in_array('created_at', $columns, true)) {
            $payload['created_at'] = now();
        }

        $payload = collect($payload)
            ->filter(function ($value, $key) use ($columns) {
                return in_array($key, $columns, true);
            })
            ->all();

        DB::beginTransaction();
        try {
            if ($id && $keyColumn) {
                $exists = DB::table('slip_templates')->where($keyColumn, $id)->exists();
                if (!$exists) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Slip template not found.',
                    ], 404);
                }

                DB::table('slip_templates')->where($keyColumn, $id)->update($payload);
                $recordId = (int) $id;
                $message = 'Slip template updated successfully.';
            } else {
                if ($keyColumn === 'id') {
                    $recordId = (int) DB::table('slip_templates')->insertGetId($payload);
                } else {
                    DB::table('slip_templates')->insert($payload);
                    $recordId = 0;
                    if ($keyColumn) {
                        $recordId = (int) DB::table('slip_templates')->max($keyColumn);
                    }
                }
                $message = 'Slip template created successfully.';
            }

            // Sync schedule headers via pivot table
            if ($recordId > 0) {
                $slipTemplate = SlipTemplate::find($recordId);
                if ($slipTemplate) {
                    $headerIds = collect($request->input('schedule_header_ids', []))
                        ->filter()
                        ->map(fn($v) => (int) $v)
                        ->unique()
                        ->values()
                        ->all();
                    $slipTemplate->scheduleHeaders()->sync($headerIds);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'id' => $recordId,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Failed to save slip template.',
            ], 500);
        }
    }

    public function deleteSlipTemplate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $id = (int) $request->input('id');
        $columns = Schema::getColumnListing('slip_templates');
        $keyColumn = collect(['id', 'slip_id', 'clause_id'])->first(function ($column) use ($columns) {
            return in_array($column, $columns, true);
        });

        if (!$keyColumn) {
            return response()->json([
                'success' => false,
                'message' => 'Could not detect slip template key column.',
            ], 500);
        }

        DB::beginTransaction();
        try {
            // Detach schedule headers from pivot table before deleting
            $slipTemplate = SlipTemplate::find($id);
            if ($slipTemplate) {
                $slipTemplate->scheduleHeaders()->detach();
            }

            $deleted = DB::table('slip_templates')->where($keyColumn, $id)->delete();

            if (!$deleted) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Slip template not found.',
                ], 404);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Slip template removed successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Failed to remove slip template.',
            ], 500);
        }
    }

    public function bd_lead_status_info(Request $request)
    {
        return view('business_development.LeadStatus.Bd_lead_status_info');
    }

    public function bd_lead_status_add_form(Request $request)
    {
        $id = $request->id;
        if (isset($id)) {
            $LeadStatus = LeadStatus::where('lead_id', $id)->first();

            return view(
                'business_development.LeadStatus.lead_status_add_form',
                compact(
                    'LeadStatus'
                )
            );
        } else {
            return view(
                'business_development.LeadStatus.lead_status_add_form'
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



            return redirect()->route('lead.status.info');
        } catch (Exception $e) {
            DB::rollBack();

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

            return redirect()->route('bd.lead.status.info');
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to delete lead status');
        }
    }

    public function bd_stage_doc_info(Request $request)
    {
        $documents = DocType::orderBy('doc_type')->get(['id', 'doc_type']);
        $typesOfBus = BusinessType::orderBy('bus_type_name')->get(['bus_type_id', 'bus_type_name']);

        return view('business_development.doc_types.stage_doc_info', compact('documents', 'typesOfBus'));
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
                'business_development.doc_types.stage_doc_add_form',
                compact(
                    'StageDocuments',
                    'Documents',
                    'types_of_bus'
                )
            );
        } else {
            return view(
                'business_development.doc_types.stage_doc_add_form',
                compact('Documents', 'types_of_bus')
            );
        }
    }

    public function bd_stage_doc_add(Request $request)
    {

        $id = $request->id;
        try {
            DB::beginTransaction();

            $stageId = $this->resolveStageDocumentStageId($request->input('stage'));
            $request->merge([
                'stage' => $stageId ?? $request->input('stage'),
            ]);

            $validator = Validator::make($request->all(), [
                'stage' => ['required', 'integer', Rule::in([0, 1, 2, 3, 4])],
                'doc_type' => 'required',
                'mandatory' => 'required|in:Y,N',
                'category_type' => 'required',
                'type_of_bus' => 'required|array|min:1',
                'type_of_bus.*' => 'required',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            if (isset($id)) {
                StageDocument::where('id', $id)->update([
                    'stage' => $stageId,
                    'doc_type' => $request->doc_type,
                    'mandatory' => $request->mandatory,
                    'category_type' => $request->category_type,
                    'type_of_bus' => json_encode($request->type_of_bus),
                    'updated_at' => now(),
                ]);
            } else {

                StageDocument::create([
                    'stage' => $stageId,
                    'doc_type' => $request->doc_type,
                    'mandatory' => $request->mandatory,
                    'category_type' => $request->category_type,
                    'type_of_bus' => json_encode($request->type_of_bus),
                    'created_at' => now(),
                ]);
            }



            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => isset($id) ? 'Stage document updated successfully.' : 'Stage document saved successfully.',
                ]);
            }

            return redirect()->route('stage.doc.info');
        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Failed to save stage document', [
                'error' => $e->getMessage(),
                'id' => $id,
                'payload' => $request->except(['_token']),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to save stage document. ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to save stage document');
        }
    }

    public function bd_stage_doc_data()
    {
        $StageDocuments = DB::table('stage_documents')->orderByDesc('id');
        return dataTables::of($StageDocuments)
            ->addColumn('select_row', function ($fn) {
                return '<input type="checkbox" class="form-check-input stage-doc-row-checkbox" data-id="' . (int) $fn->id . '" aria-label="Select stage document">';
            })
            ->addIndexColumn()
            ->editColumn('stage', function ($fn) {
                $normalizedStage = $this->normalizeStageDocumentStage($fn->stage);
                if (!$normalizedStage) {
                    return 'N/A';
                }
                return $normalizedStage === 'all' ? 'All Stages' : ucfirst($normalizedStage);
            })
            ->editColumn('mandatory_1', function ($fn) {
                if ($fn->mandatory == 'Y') {
                    return '<span class="badge bg-success-transparent text-success">Yes</span>';
                }

                if ($fn->mandatory == 'N') {
                    return '<span class="badge bg-danger-transparent text-danger">No</span>';
                }

                return '<span class="badge bg-secondary-transparent text-secondary">N/A</span>';
            })
            ->editColumn('doc_type', function ($fn) {
                $doc_type = DB::table('doc_types')->where('id', $fn->doc_type)->value('doc_type');
                return $doc_type ?? 'N/A';
            })
            ->editColumn('category', function ($fn) {
                if ($fn->category_type == '1') {
                    return '<span class="badge bg-primary-transparent text-primary">Quotation</span>';
                }

                if ($fn->category_type == '2') {
                    return '<span class="badge bg-info-transparent text-info">Facultative Offer</span>';
                }

                return '<span class="badge bg-secondary-transparent text-secondary">N/A</span>';
            })
            ->editColumn('busines_type', function ($fn) {
                $busTypes = json_decode($fn->type_of_bus, true);

                if (is_array($busTypes) && !empty($busTypes)) {
                    $names = DB::table('business_types')
                        ->whereIn('bus_type_id', $busTypes)
                        ->pluck('bus_type_name')
                        ->toArray();

                    $palette = [
                        ['bg' => 'bg-primary-transparent', 'text' => 'text-primary'],
                        ['bg' => 'bg-success-transparent', 'text' => 'text-success'],
                        ['bg' => 'bg-info-transparent', 'text' => 'text-info'],
                        ['bg' => 'bg-warning-transparent', 'text' => 'text-warning'],
                        ['bg' => 'bg-danger-transparent', 'text' => 'text-danger'],
                        ['bg' => 'bg-secondary-transparent', 'text' => 'text-secondary'],
                    ];

                    $badges = array_map(function ($name) use ($palette) {
                        $color = $palette[crc32((string) $name) % count($palette)];
                        return '<span class="badge ' . $color['bg'] . ' ' . $color['text'] . ' me-1 mb-1">' . e($name) . '</span>';
                    }, $names);

                    return implode(' ', $badges);
                }

                return '<span class="badge bg-secondary-transparent text-secondary">N/A</span>';
            })
            ->addColumn('action', function ($fn) {
                $normalizedStage = $this->normalizeStageDocumentStage($fn->stage);
                $stageValue = match ($normalizedStage) {
                    'all' => 'all',
                    'lead' => 'lead',
                    'proposal' => 'proposal',
                    'negotiation' => 'negotiation',
                    'final' => 'final',
                    default => '',
                };

                if ($stageValue === '') {
                    $stageValue = match ((int) $fn->stage) {
                        0 => 'all',
                        1 => 'lead',
                        2 => 'proposal',
                        3 => 'negotiation',
                        4 => 'final',
                        default => '',
                    };
                }

                $typeOfBus = json_decode($fn->type_of_bus, true);
                $typeOfBus = is_array($typeOfBus) ? $typeOfBus : [];

                $editBtn = '<button type="button" class="btn btn-outline-dark btn-sm action-btn update_stage_doc_type" title="Update stage document" data-id="' . $fn->id . '" data-stage="' . e($stageValue) . '" data-doc-type="' . e($fn->doc_type) . '" data-mandatory="' . e($fn->mandatory) . '" data-category-type="' . e($fn->category_type) . '" data-type-of-bus="' . e(json_encode($typeOfBus)) . '">Edit</button>';
                $deleteBtn = '<button type="button" class="btn btn-outline-danger btn-sm action-btn remove_stage_doc_type" title="Delete stage document" data-id="' . $fn->id . '">Remove</button>';
                return '<div class="action-buttons">' . $editBtn . ' ' . $deleteBtn . '</div>';
            })
            ->rawColumns(['select_row', 'mandatory_1', 'category', 'busines_type', 'action'])
            ->make(true);
    }

    public function delete_stage_doc(Request $request)
    {
        $id = $request->id;
        try {
            DB::beginTransaction();
            $deleted = DB::table('stage_documents')->where('id', $id)->delete();
            DB::commit();

            if (!$deleted) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Stage document not found.',
                    ], 404);
                }

                return redirect()->back()->with('error', 'Stage document not found');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Stage document removed successfully.',
                ]);
            }

            return redirect()->route('stage.doc.info');
        } catch (Exception $e) {
            DB::rollback();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete stage document.',
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete stage document');
        }
    }

    public function delete_stage_doc_bulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $ids = collect($request->input('ids', []))
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        if (empty($ids)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No stage documents selected.',
            ], 422);
        }

        try {
            DB::beginTransaction();
            $deletedCount = DB::table('stage_documents')->whereIn('id', $ids)->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => $deletedCount . ' stage document(s) removed successfully.',
                'deleted_count' => $deletedCount,
            ]);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete selected stage documents.',
            ], 500);
        }
    }

    private function normalizeStageDocumentStage($stage): ?string
    {
        $value = strtolower(trim((string) $stage));

        return match ($value) {
            '0', 'all' => 'all',
            '1', 'lead' => 'lead',
            '2', 'proposal' => 'proposal',
            '3', 'negotiation' => 'negotiation',
            '4', 'final', 'final_stage' => 'final',
            default => null,
        };
    }

    private function resolveStageDocumentStageId($stage): ?int
    {
        $value = strtolower(trim((string) $stage));

        return match ($value) {
            '0', 'all' => 0,
            '1', 'lead' => 1,
            '2', 'proposal' => 2,
            '3', 'negotiation' => 3,
            '4', 'final', 'final_stage' => 4,
            default => null,
        };
    }

    public function bd_doc_type_info(Request $request)
    {
        return view('business_development.doc_types.doc_type_info', [
            'docTypeStats' => $this->getDocTypeStats(),
        ]);
    }

    private function getDocTypeStats(): array
    {
        return [
            'total' => DB::table('doc_types')->count(),
            'required' => DB::table('doc_types')->where('is_required', 'Y')->count(),
            'default' => DB::table('doc_types')->where('is_default', 'Y')->count(),
            'uploaded' => DB::table('doc_types')
                ->whereNotNull('file_name')
                ->where('file_name', '!=', '')
                ->count(),
        ];
    }

    public function doc_type_form(Request $request)
    {
        $id = $request->id;

        if (isset($id)) {
            $Documents = DocType::where('id', $id)->first();


            return view(
                'business_development.doc_types.doc_type_add_form',
                compact(
                    'Documents',
                )
            );
        } else {
            return view(
                'business_development.doc_types.doc_type_add_form'
            );
        }
    }

    public function bd_doc_type_add(Request $request)
    {
        $id = $request->id;
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:50',
                'doc_type' => 'required',
                'description' => 'required',
                'country' => 'required|string|max:100',
                'is_required' => 'required|in:Y,N',
                'is_default' => 'required|in:Y,N',
                'cedant_file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors' => $validator->errors(),
                    ], 422);
                }

                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $file = $request->file('cedant_file');
            $mimetype = null;
            $Filename = null;
            $s3UploadPath = 'uploads/cedant_docs';
            $s3AttachmentHandler = app(S3AttachmentHandler::class);

            if (!is_null($file)) {
                try {
                    $uploadResult = $s3AttachmentHandler->uploadUploadedFile($file, $s3UploadPath);
                    $mimetype = $uploadResult['mimetype'];
                    $Filename = $uploadResult['filename'];
                } catch (\InvalidArgumentException $e) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['message' => 'Invalid file upload.'], 422);
                    }
                    return redirect()->back()->with('error', 'Invalid file upload');
                }
            }

            if (isset($id)) {
                $existingDocType = DocType::where('id', $id)->first();
                if (!$existingDocType) {
                    throw new Exception('Document type not found.');
                }

                $payload = [
                    'code' => strtoupper(trim((string) $request->code)),
                    'doc_type' => $request->doc_type,
                    'description' => $request->description,
                    'country' => trim((string) $request->country),
                    'is_required' => $request->is_required ?? 'Y',
                    'is_default' => $request->is_default ?? 'Y',
                    'checkbox_doc' => $request->checkbox_doc ?? null,
                    'attachment_file' => $request->attachment_file ?? '',
                    'bus_type' => $request->bus_type ?? '',
                    'updated_at' => now(),
                ];

                if (!is_null($file)) {
                    if (!empty($existingDocType->file_name)) {
                        $oldFilePath = str_starts_with($existingDocType->file_name, 'uploads/')
                            ? $existingDocType->file_name
                            : $s3UploadPath . '/' . $existingDocType->file_name;
                        if (Storage::disk('s3')->exists($oldFilePath)) {
                            Storage::disk('s3')->delete($oldFilePath);
                        }
                    }

                    $payload['mimetype'] = $mimetype;
                    $payload['file_name'] = $Filename;
                }

                $existingDocType->update($payload);
            } else {
                DocType::create([
                    'code' => strtoupper(trim((string) $request->code)),
                    'doc_type' => $request->doc_type,
                    'description' => $request->description,
                    'country' => trim((string) $request->country),
                    'is_required' => $request->is_required ?? 'Y',
                    'is_default' => $request->is_default ?? 'Y',
                    'attachment_file' => $request->attachment_file ?? '',
                    'bus_type' => $request->bus_type ?? '',
                    'checkbox_doc' => $request->checkbox_doc ?? null,
                    'mimetype' => $mimetype,
                    'file_name' => $Filename,
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => isset($id) ? 'Document type updated successfully.' : 'Document type saved successfully.',
                ]);
            }

            return redirect()->route('doc.type.info');
        } catch (Exception $e) {
            DB::rollBack();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'Failed to save document.',
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to save document');
        }
    }

    public function bd_doc_type_data()
    {
        $doc_types = DB::table('doc_types')->get();

        return dataTables::of($doc_types)
            ->addIndexColumn()
            ->addColumn('code', function ($fn) {
                $code = $fn->code ?: 'N/A';
                return '<strong>' . e($code) . '</strong>';
            })
            ->addColumn('country', function ($fn) {
                return $fn->country ?: 'All';
            })
            ->addColumn('required_label', function ($fn) {
                $isRequired = ($fn->is_required ?? 'Y') === 'Y';
                if ($isRequired) {
                    return '<span class="badge bg-success-transparent text-success">Yes</span>';
                }
                return '<span class="badge bg-danger-transparent text-danger">No</span>';
            })
            ->addColumn('default_label', function ($fn) {
                $isDefault = ($fn->is_default ?? 'Y') === 'Y';
                if ($isDefault) {
                    return '<span class="badge bg-primary-transparent text-primary">Yes</span>';
                }
                return '<span class="badge bg-secondary-transparent text-secondary">No</span>';
            })
            ->addColumn('file_status', function ($fn) {
                if (!empty($fn->file_name)) {
                    $filePath = str_starts_with($fn->file_name, 'uploads/')
                        ? $fn->file_name
                        : 'uploads/cedant_docs/' . $fn->file_name;
                    $fileUrl = Storage::disk('s3')->url($filePath);

                    return '<span class="badge bg-success-transparent text-success">Uploaded</span> ' .
                        '<a href="' . e($fileUrl) . '" target="_blank" rel="noopener" title="View file" class="ms-1 text-primary">' .
                        '<i class="bx bx-show fs-5 align-middle"></i></a>';
                }
                return '<span class="badge bg-warning-transparent text-warning">Not Uploaded</span>';
            })
            ->addColumn('action', function ($fn) {
                $hasFile = !empty($fn->file_name);
                $buttonText = $hasFile ? 'View / Replace' : 'Upload';
                $filePath = $hasFile
                    ? (str_starts_with($fn->file_name, 'uploads/') ? $fn->file_name : 'uploads/cedant_docs/' . $fn->file_name)
                    : '';
                $fileUrl = $hasFile ? Storage::disk('s3')->url($filePath) : '';

                $editBtn = '<button type="button" class="btn btn-outline-dark btn-sm action-btn update_doc_type" title="Update document type" data-id="' . $fn->id . '" data-code="' . e($fn->code ?? '') . '" data-doc-type="' . e($fn->doc_type) . '" data-description="' . e($fn->description) . '" data-country="' . e($fn->country ?? 'All') . '" data-is-required="' . e($fn->is_required ?? 'Y') . '" data-is-default="' . e($fn->is_default ?? 'Y') . '" data-file-url="' . e($fileUrl) . '" data-file-status="' . e($hasFile ? 'Uploaded' : 'Not Uploaded') . '">' . e($buttonText) . '</button>';
                $deleteBtn = '<button type="button" class="btn btn-outline-danger btn-sm action-btn remove_doc_type" title="Delete document type" data-id="' . $fn->id . '">Remove</button>';
                return '<div class="action-buttons">' . $editBtn . ' ' . $deleteBtn . '</div>';
            })
            ->rawColumns(['code', 'required_label', 'default_label', 'file_status', 'action'])
            ->with(['stats' => $this->getDocTypeStats()])
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

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Document type removed successfully.',
                ]);
            }

            return redirect()->route('doc.type.info');
        } catch (Exception $e) {
            DB::rollback();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to delete document.',
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete document');
        }
    }

    public function operationchecklist_info(Request $request)
    {
        return view('business_development.TreatyOperationChecklist.treaty_operation_info');
    }

    public function operationchecklist_form(Request $request)
    {
        $id = $request->id;

        if (isset($id)) {
            $OperationChecklist = OperationChecklist::where('id', $id)->first();


            return view(
                'business_development.TreatyOperationChecklist.treaty_operation_add_form',
                compact(
                    'OperationChecklist',
                )
            );
        } else {
            return view(
                'business_development.TreatyOperationChecklist.treaty_operation_add_form'
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
                    'created_by' => auth()->user()->user_name,
                    'created_at' => now(),
                ]);
            }

            DB::commit();


            return redirect()->route('operationchecklist.info');
        } catch (Exception $e) {
            DB::rollBack();
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

            return redirect()->route('doc.type.info');
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to delete operation checklist');
        }
    }
}
