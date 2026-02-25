<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CoverType;
use Illuminate\Http\Request;
use App\Models\CustomerTypes;
use App\Models\CustomerAccDet;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Jobs\ClearCedantDataJob;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function customer_info(Request $request)
    {
        return view('customer.customer_info', [
            'user' => $request->user(),
        ]);
    }

    public function cedant_info(Request $request)
    {
        return view('customer.cedant_info', [
            'user' => $request->user(),
        ]);
    }

    public function reinsurer_info(Request $request)
    {
        return view('customer.reinsurer_info', [
            'user' => $request->user(),
        ]);
    }

    public function insured_info(Request $request)
    {
        return view('customer.insured_info', [
            'user' => $request->user(),
        ]);
    }

    public function TreatyPropEnquiry(Request $request)
    {
        return $this->treatyEnquiryView($request, ['TPR'], 'Treaty Proportional');
    }

    public function TreatyNonPropEnquiry(Request $request)
    {
        return $this->treatyEnquiryView($request, ['TNP'], 'Treaty Non Proportional');
    }

    public function TreatyFACPropEnquiry(Request $request)
    {
        return $this->treatyEnquiryView($request, ['FPR'], 'Facultative Proportional');
    }

    public function TreatyFACNonPropEnquiry(Request $request)
    {
        return $this->treatyEnquiryView($request, ['FNP'], 'Facultative Non Proportional');
    }

    private function treatyEnquiryView(Request $request, array $typeOfBus, string $treatyName)
    {
        return view('customer.treatyenquiry', [
            'user' => $request->user(),
            'type_of_bus' => $typeOfBus,
            'treaty_name' => $treatyName,
        ]);
    }

    public function TypeOfBusCoverDatatable(Request $request)
    {
        $typeOfBus = $request->input('type_of_bus', []);

        if (empty($typeOfBus)) {
            return response()->json(['data' => []]);
        }

        $covers = $this->customerService->getCoversByBusinessType($typeOfBus);

        return DataTables::of($covers)
            ->editColumn('cover_no', fn($row) => $row->cover_no)
            ->editColumn('endorsement_no', fn($row) => $row->endorsement_no ?? '—')
            ->editColumn('transaction_type', function ($row) {
                return match ($row->transaction_type) {
                    'NEW' => 'New',
                    'REN' => 'Renewal',
                    'EXT' => 'Endorsement',
                    'CNC' => 'Cancellation',
                    'NIL' => 'NIL Endorsement',
                    'RFN' => 'Refund',
                    default => $row->transaction_type ?? '—',
                };
            })
            ->editColumn('cover_type', fn($row) => $this->getCoverTypeName($row->cover_type))
            ->editColumn('class_desc', fn($row) => $this->getClassDescription($row))
            ->editColumn('cover_from', fn($row) => !empty($row->cover_from) ? formatDate($row->cover_from) : '—')
            ->editColumn('cover_to', fn($row) => !empty($row->cover_to) ? formatDate($row->cover_to) : '—')
            ->addColumn('status_verification', function ($row) {
                return match ($row->verified) {
                    'A' => '<span class="badge bg-success-gradient">Approved</span>',
                    'P' => '<span class="badge bg-warning-gradient">Pending</span>',
                    'R' => '<span class="badge bg-danger-gradient">Rejected</span>',
                    default => '<span class="badge bg-secondary">Unknown</span>',
                };
            })
            ->addColumn('status_badge', function ($row) {
                if (($row->cancelled ?? 'N') === 'Y') {
                    return '<span class="badge bg-danger-gradient">Cancelled</span>';
                }

                return ($row->status ?? '') === 'A'
                    ? '<span class="badge bg-success-gradient">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
            })
            ->rawColumns(['status_verification', 'status_badge'])
            ->make(true);
    }

    public function getCustomerData()
    {
        $customers = Customer::select([
            'customer_id',
            'name',
            'tax_no',
            'registration_no',
            'email',
            'website',
            'customer_type'
        ]);

        return DataTables::eloquent($customers)
            ->addColumn('debited_covers', fn($customer) => $this->getDebitedCoversCount($customer->customer_id))
            ->addColumn('customer_type_name', fn($customer) => $this->formatCustomerTypes($customer->customer_type))
            ->addColumn('actions', fn($customer) => $this->renderCustomerActions($customer))
            ->rawColumns(['customer_type_name', 'actions', 'debited_covers'])
            ->make(true);
    }

    public function getCedantData()
    {
        $cedantTypeIds = CustomerTypes::query()
            ->where(function ($query) {
                $query->whereRaw('LOWER(slug) = ?', ['cedant'])
                    ->orWhereRaw('LOWER(code) = ?', ['cedant'])
                    ->orWhereRaw('LOWER(type_name) = ?', ['cedant']);
            })
            ->pluck('type_id')
            ->filter()
            ->map(fn($id) => (string) $id)
            ->values();

        $customers = Customer::query()
            ->when($cedantTypeIds->isEmpty(), function ($query) {
                $query->whereRaw('1 = 0');
            })
            ->when($cedantTypeIds->isNotEmpty(), function ($query) use ($cedantTypeIds) {
                $query->where(function ($customerQuery) use ($cedantTypeIds) {
                    foreach ($cedantTypeIds as $typeId) {
                        $customerQuery->orWhereJsonContains('customer_type', [(string) $typeId]);

                        if (is_numeric($typeId)) {
                            $customerQuery->orWhereJsonContains('customer_type', [(int) $typeId]);
                        }
                    }
                });
            })
            ->select([
                'customer_id',
                'name',
                'tax_no',
                'registration_no',
                'email',
                'website',
                'customer_type'
            ]);

        return DataTables::eloquent($customers)
            ->addColumn('debited_covers', fn($customer) => $this->getDebitedCoversCount($customer->customer_id))
            ->addColumn('customer_type_name', fn($customer) => $this->formatCustomerTypes($customer->customer_type))
            ->addColumn('process', fn($customer) => $this->renderCedantProcessButton($customer))
            ->rawColumns(['customer_type_name', 'process', 'debited_covers'])
            ->make(true);
    }

    public function getReinsurerData()
    {
        $customers = Customer::join('customer_types', 'customer_types.type_id', '=', 'customers.customer_type')
            ->where('customer_types.code', 'REINCO')
            ->select([
                'customers.customer_id',
                'customers.name',
                'customers.tax_no',
                'customers.registration_no',
                'customers.email',
                'customers.website',
                'customers.customer_type',
                'customer_types.type_name as customer_type_name',
                'customer_types.code as customer_type_slug'
            ]);

        return DataTables::eloquent($customers)
            ->addColumn('process', fn($customer) => $this->renderProcessButton($customer->customer_id))
            ->rawColumns(['process'])
            ->make(true);
    }

    public function getInsuredData()
    {
        $customers = Customer::join('customer_types', 'customer_types.type_id', '=', 'customers.customer_type')
            ->where('customer_types.code', 'INSURED')
            ->select([
                'customers.customer_id',
                'customers.name',
                'customers.tax_no',
                'customers.registration_no',
                'customers.email',
                'customers.website',
                'customers.customer_type',
                'customer_types.type_name as customer_type_name',
                'customer_types.code as customer_type_slug'
            ]);

        return DataTables::eloquent($customers)
            ->addColumn('process', fn($customer) => $this->renderProcessButton($customer->customer_id))
            ->rawColumns(['process'])
            ->make(true);
    }

    public function StatementDatatable(Request $request)
    {
        $customerId = $request->input('customer_id');

        $statements = CustomerAccDet::where('customer_id', $customerId)->get();

        return DataTables::of($statements)
            ->editColumn('doc_type', fn($row) => $row->doc_type)
            ->editColumn('cover_no', fn($row) => $row->cover_no)
            ->editColumn('endorsement_no', fn($row) => $row->endorsement_no)
            ->editColumn('reference', fn($row) => $row->reference)
            ->editColumn('entry_type_descr', fn($row) => $row->entry_type_descr)
            ->editColumn('local_nett_amount', fn($row) => number_format($row->local_nett_amount, 2))
            ->editColumn('unallocated_amount', fn($row) => number_format($row->unallocated_amount, 2))
            ->editColumn('created_date', fn($row) => formatDate($row->created_date))
            ->make(true);
    }

    public function CustomerAddForm(Request $request)
    {
        $data = $this->getCustomerFormData();
        $customerId = $request->integer('customer_id');

        if ($customerId) {
            $data['customer'] = Customer::with([
                'primaryContact',
                'contacts' => function ($query) {
                    $query->orderByDesc('is_primary')->orderBy('order')->orderBy('id');
                },
            ])->findOrFail($customerId, [
                'customer_id',
                'name',
                'tax_no',
                'registration_no',
                'email',
                'website',
                'customer_type',
                'postal_address',
                'city',
                'agency_rate',
                'financial_rate',
                'telephone',
                'street',
                'country_iso',
                'identity_number',
                'identity_number_type'
            ]);
        }

        return view('customer.customer_add_form', $data);
    }

    public function storeCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partnerName' => 'required|string|max:255',
            'customerType' => 'required|array|min:1',
            'customerType.*' => 'required|exists:customer_types,type_id',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'incorporationNo' => 'required|string|max:255',
            'taxNo' => 'required|string|max:255',
            'identityType' => 'required|string|max:50',
            'identityNo' => 'required|string|max:50',
            'website' => 'nullable|string|max:255',
            'regulatorLicenseNo' => 'nullable|string|max:100',
            'licensingAuthority' => 'nullable|string|max:255',
            'licensingTerritory' => 'nullable|string|max:100',
            'amlDetails' => 'nullable|string|max:1000',
            'country' => 'required|string|size:3|exists:countries,country_iso',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postalCode' => 'required|string|max:20',
            'financialRating' => 'nullable|string|max:10',
            'agencyRating' => 'nullable|string|max:10',
            'contacts' => 'required|array|min:1',
            'contacts.*.id' => 'nullable|integer|exists:customer_contacts,id',
            'contacts.*.department' => 'nullable|string|in:executive,underwriting,claims,sales,marketing,finance,technical,operations,legal,hr,other',
            'contacts.0.name' => 'required|string|max:255',
            'contacts.0.position' => 'required|string|max:100',
            'contacts.0.mobile' => 'required|string|max:20',
            'contacts.0.email' => 'required|email|max:255',
        ], [
            'partnerName.required' => 'Legal/Trading Name is required.',
            'customerType.required' => 'Entity Type is required.',
            'email.required' => 'Primary Email Address is required.',
            'telephone.required' => 'Primary Telephone is required.',
            'incorporationNo.required' => 'Registration/Incorporation Number is required.',
            'taxNo.required' => 'Tax Identification Number is required.',
            'identityType.required' => 'Identity Document Type is required.',
            'identityNo.required' => 'Identity Document Number is required.',
            'country.required' => 'Country is required.',
            'street.required' => 'Street Address is required.',
            'city.required' => 'City/Town is required.',
            'postalCode.required' => 'Postal/ZIP Code is required.',
            'contacts.0.name.required' => 'Primary Contact Name is required.',
            'contacts.0.position.required' => 'Primary Contact Position is required.',
            'contacts.0.mobile.required' => 'Primary Contact Mobile Number is required.',
            'contacts.0.email.required' => 'Primary Contact Email is required.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => 'Please fill all required fields.',
                'errors' => $validator->errors(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'status' => 201,
            'message' => 'Customer information saved successfully',
        ], 201);
    }

    public function customerEdit(int $customerId)
    {
        $data = $this->getCustomerFormData();

        $baseColumns = [
            'customer_id',
            'name',
            'tax_no',
            'registration_no',
            'email',
            'website',
            'customer_type',
            'postal_address',
            'city',
            'agency_rate',
            'financial_rate',
            'telephone',
            'street',
            'country_iso',
            'identity_number',
            'identity_number_type'
        ];

        $optionalColumns = [
            'state',
            'security_rating',
            'rating_agency',
            'rating_date',
            'regulator_license_no',
            'licensing_authority',
            'licensing_territory',
            'aml_details',
            'insured_type',
            'industry_occupation',
            'date_of_birth_incorporation'
        ];

        $selectedColumns = $baseColumns;
        foreach ($optionalColumns as $column) {
            if (Schema::hasColumn('customers', $column)) {
                $selectedColumns[] = $column;
            }
        }

        $data['customer'] = Customer::with([
            'primaryContact',
            'contacts' => function ($query) {
                $query->orderByDesc('is_primary')->orderBy('order')->orderBy('id');
            },
        ])->findOrFail($customerId, $selectedColumns);

        return view('customer.customer_add_form', $data);
    }

    public function customerUpdate(Request $request, int $customerId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'partnerName' => 'required|string|max:255',
            'customerType' => 'required|array|min:1',
            'customerType.*' => 'required|exists:customer_types,type_id',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'incorporationNo' => 'required|string|max:255',
            'taxNo' => 'required|string|max:255',
            'identityType' => 'required|string|max:50',
            'identityNo' => 'required|string|max:50',
            'website' => 'nullable|string|max:255',
            'securityRating' => 'nullable|string|max:50',
            'ratingAgency' => 'nullable|string|max:100',
            'ratingDate' => 'nullable|date',
            'regulatorLicenseNo' => 'nullable|string|max:100',
            'licensingAuthority' => 'nullable|string|max:255',
            'licensingTerritory' => 'nullable|string|max:100',
            'amlDetails' => 'nullable|string|max:1000',
            'insuredType' => 'nullable|string|max:50',
            'industryOccupation' => 'nullable|string|max:255',
            'dateOfBirthIncorporation' => 'nullable|date',
            'country' => 'required|string|size:3|exists:countries,country_iso',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postalCode' => 'required|string|max:20',
            'financialRating' => 'nullable|string|max:10',
            'agencyRating' => 'nullable|string|max:10',
            'contacts' => 'required|array|min:1',
            'contacts.*.id' => 'nullable|integer|exists:customer_contacts,id',
            'contacts.*.department' => 'nullable|string|in:executive,underwriting,claims,sales,marketing,finance,technical,operations,legal,hr,other',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.position' => 'required|string|max:100',
            'contacts.*.mobile' => 'required|string|max:20',
            'contacts.*.email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => 'Please fill all required fields.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $customer = $this->customerService->updateCustomer($customerId, $validator->validated());

            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => 'Customer updated successfully',
                'data' => [
                    'customer_id' => $customer->customer_id,
                    'name' => $customer->name,
                ],
                'redirect_url' => route('customer.info'),
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'An error occurred while updating customer information',
            ], 500);
        }
    }

    public function CustomerDtl(Request $request)
    {
        $customerId = $request->input('customer_id');

        $customer = Customer::where('customer_id', $customerId)
            ->firstOrFail([
                'customer_id',
                'name',
                'postal_address',
                'postal_town',
                'city',
                'email',
                'telephone',
                'country_iso',
                'customer_type'
            ]);

        $country = Country::where('country_iso', $customer->country_iso)
            ->first(['country_iso', 'country_name']);

        return view('customer.customer_dtl', [
            'country' => $country,
            'customer' => $customer
        ]);
    }

    public function clearCedantData(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:customers,customer_id',
            ]);

            ClearCedantDataJob::dispatchSync((int) $request->input('id'));

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Customer covers cleared successfully'
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to queue cedant data clearing'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteCustomerData(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:customers,customer_id',
            ]);

            ClearCedantDataJob::dispatchSync((int) $request->input('id'), true);

            return response()->json([
                'status' => Response::HTTP_CREATED,
                'message' => 'Customer and related data deleted successfully'
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to delete customer data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getStatistics(): JsonResponse
    {
        try {
            $stats = Cache::remember('customer_statistics', 300, function () {
                return $this->customerService->getStatistics();
            });

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'total_cedants' => 0,
                'total_change' => 0,
                'active_covers' => 0,
                'covers_change' => 0,
                'unique_types' => 0,
                'types_breakdown' => [],
                'recent_activity' => 0,
                'last_update' => null,
                'sparkline_data' => null,
                'error' => 'Failed to fetch statistics'
            ], 500);
        }
    }

    private function getCoverTypeName(int $typeId): string
    {
        return Cache::remember("cover_type_{$typeId}", 3600, function () use ($typeId) {
            $coverType = CoverType::where('type_id', $typeId)->first();
            return $coverType ? $coverType->type_name : 'Unknown';
        });
    }

    private function getClassDescription(object $row): string
    {
        if (in_array($row->type_of_bus, ['FPR', 'FNP'])) {
            $class = Cache::remember("class_{$row->class_code}", 3600, function () use ($row) {
                return Classes::where('class_code', $row->class_code)->first();
            });

            return $class
                ? 'FACULTATIVE - ' . $class->class_name
                : 'FACULTATIVE - Unknown Class';
        }

        return match ($row->type_of_bus) {
            'TPR' => 'TREATY - PROPORTIONAL',
            'TNP' => 'TREATY - NON PROPORTIONAL',
            default => 'Unknown Type'
        };
    }

    private function getDebitedCoversCount(int $customerId): int
    {
        return Cache::remember("debited_covers_{$customerId}", 300, function () use ($customerId) {
            return DB::table('cover_register')
                ->where('customer_id', $customerId)
                ->count();
        });
    }

    private function formatCustomerTypes($customerTypes): string
    {
        if (empty($customerTypes)) {
            return "<b class='dashes'>—</b>";
        }

        $types = CustomerTypes::whereIn('type_id', (array)$customerTypes)
            ->pluck('type_name')
            ->map(fn($name) => trim($name))
            ->toArray();

        return !empty($types) ? implode(', ', $types) : "<b class='dashes'>—</b>";
    }

    private function renderCustomerActions(Customer $customer): string
    {
        $editBtn = sprintf(
            '<button class="edit_customer btn btn-sm btn-primary me-1" data-id="%s" title="Edit customer">
                <i class="bx bx-edit"></i> Edit
            </button>',
            $customer->customer_id
        );

        $deleteBtn = sprintf(
            '<button class="remove_process_customer btn btn-sm btn-danger"
                data-name="%s"
                data-cedant-id="%s"
                title="Delete customer and related data">
                <i class="bx bx-trash"></i> Delete
            </button>',
            htmlspecialchars($customer->name),
            $customer->customer_id
        );

        return $editBtn . $deleteBtn;
    }

    private function renderCedantProcessButton(Customer $customer): string
    {
        return sprintf(
            '<button class="process_customer btn btn-sm btn-primary"
                onclick="processCustomer(\'%s\')"
                title="Process customer">
                <i class="bx bx-send"></i> Process
            </button>',
            $customer->customer_id
        );
    }

    private function renderProcessButton(int $customerId): string
    {
        return sprintf(
            '<button class="process_customer btn btn-sm btn-primary"
                onclick="processCustomer(\'%s\')"
                title="Process">
                <i class="bx bx-send"></i> Process
            </button>',
            $customerId
        );
    }

    private function getCustomerFormData(): array
    {
        return Cache::remember('customer_form_data', 3600, function () {
            return [
                'type_of_cust' => CustomerTypes::select(['type_id', 'type_name', 'code', 'slug'])->get(),
                'countries' => Country::select(['country_iso', 'country_name'])->orderBy('country_name')->get(),
                'partnersId' => DB::table('partner_identification')
                    ->select(['identification_type', 'issued_by', 'issue_date', 'description'])
                    ->get(),
            ];
        });
    }
}
