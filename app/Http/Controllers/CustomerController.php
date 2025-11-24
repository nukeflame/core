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
            ->editColumn('cover_type', fn($row) => $this->getCoverTypeName($row->cover_type))
            ->editColumn('class_desc', fn($row) => $this->getClassDescription($row))
            ->editColumn('cover_to', fn($row) => formatDate($row->cover_to))
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

    public function CustomerAddForm()
    {
        $data = $this->getCustomerFormData();

        return view('customer.customer_add_form', $data);
    }

    public function CustomerAddData(Request $request): JsonResponse
    {
        try {
            $validator = $this->validateCustomerData($request);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'status' => 422,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $customer = $this->customerService->createCustomer($request->all());

            if ($request->has('contacts')) {
                $this->customerService->createCustomerContacts($customer->id, $request->input('contacts'));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => 201,
                'message' => 'Customer information saved successfully',
                'data' => [
                    'customer_id' => $customer->customer_id,
                    'name' => $customer->name
                ]
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Customer creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'An error occurred while saving customer information',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function customerEdit(int $customerId)
    {
        $customer = Customer::findOrFail($customerId, [
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

        $data = $this->getCustomerFormData();
        $data['customer'] = $customer;

        return view('customer.customer_edit_form', $data);
    }

    public function customerUpdate(int $customerId): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Update functionality not yet implemented'
        ], 501);
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

            ClearCedantDataJob::dispatch($request->input('id'));

            return response()->json([
                'status' => Response::HTTP_ACCEPTED,
                'message' => 'Cedant data clearing has been queued for processing'
            ], Response::HTTP_ACCEPTED);
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
                title="Delete all covers">
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
                'type_of_cust' => CustomerTypes::select(['type_id', 'type_name', 'code'])->get(),
                'countries' => Country::select(['country_iso', 'country_name'])->orderBy('country_name')->get(),
                'partnersId' => DB::table('partner_identifications')
                    ->select(['identification_type', 'issued_by', 'issue_date', 'description'])
                    ->get(),
            ];
        });
    }

    private function validateCustomerData(Request $request)
    {
        return Validator::make($request->all(), [
            'partnerName' => 'required|string|max:255|unique:customers,name',
            'customerType' => 'required|array',
            'customerType.*' => 'exists:customer_types,type_id',
            'financialRating' => 'required|string|max:10',
            'agencyRating' => 'required|string|max:10',
            'email' => 'required|email|max:255|unique:customers,email',
            'street' => 'required|string|max:255',
            'taxNo' => 'required|string|max:255|unique:customers,tax_no',
            'incorporationNo' => 'required|string|max:255|unique:customers,registration_no',
            'website' => 'nullable|url|max:255',
            'city' => 'required|string|max:100',
            'identityType' => 'required|string|max:50',
            'identityNo' => 'required|string|max:50',
            'telephone' => 'nullable|string|max:20',
            'postalCode' => 'required|string|max:20',
            'country' => 'required|string|size:3|exists:countries,country_iso',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required_with:contacts|string|max:255',
            'contacts.*.position' => 'nullable|string|max:100',
            'contacts.*.mobile' => 'nullable|string|max:20',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.isPrimary' => 'nullable|boolean',
            'contacts.*.order' => 'nullable|integer|min:0|max:100',
        ], [
            'partnerName.unique' => 'A customer with this name already exists.',
            'email.unique' => 'This email address is already registered.',
            'taxNo.unique' => 'This tax number is already registered.',
            'incorporationNo.unique' => 'This incorporation number is already registered.',
            'customerType.*.exists' => 'One or more selected customer types are invalid.',
            'country.exists' => 'The selected country is invalid.',
        ]);
    }
}
