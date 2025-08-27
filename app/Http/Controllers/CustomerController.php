<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Classes;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CoverType;
use Illuminate\Http\Request;
use App\Models\CustomerTypes;
use App\Models\CustomerAccDet;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\ClearCedantDataJob;
use App\Models\Bd\CustomerContact;
use App\Models\PartnerIdentification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
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
    function TreatyPropEnquiry(Request $request)
    {
        return view('customer.treatyenquiry', [
            'user' => $request->user(),
            'type_of_bus' => ['TPR'],
            'treaty_name' => 'Treaty Proportional',
        ]);
    }
    function TreatyNonPropEnquiry(Request $request)
    {
        return view('customer.treatyenquiry', [
            'user' => $request->user(),
            'type_of_bus' => ['TNP'],
            'treaty_name' => 'Treaty Non Proportional',
        ]);
    }
    function TreatyFACPropEnquiry(Request $request)
    {
        return view('customer.treatyenquiry', [
            'user' => $request->user(),
            'type_of_bus' => ['FPR'],
            'treaty_name' => 'Facultative Proportional',
        ]);
    }
    function TreatyFACNonPropEnquiry(Request $request)
    {
        return view('customer.treatyenquiry', [
            'user' => $request->user(),
            'type_of_bus' => ['FNP'],
            'treaty_name' => 'Facultative Non Proportional',
        ]);
    }
    function TypeOfBusCoverDatatable(Request $request)
    {
        $type_of_bus = $request->type_of_bus;
        $Array = implode(',', array_fill(0, count($type_of_bus), '?'));

        $rawQuery = "
            SELECT DISTINCT ON (cr.cover_no)
                cr.cover_no, cr.cover_type, cr.class_code, cr.cover_to, cr.created_at, cr.type_of_bus, c.name
            FROM cover_register cr
            INNER JOIN customers c ON cr.customer_id = c.customer_id
            WHERE cr.type_of_bus IN ($Array)
            ORDER BY cr.cover_no, cr.created_at DESC
        ";

        // Execute the query with the type_of_bus array values
        $results = DB::select($rawQuery, $type_of_bus);

        // Optionally convert the result to a collection of models
        $query = collect($results);
        // dd($query);
        return datatables::of($query)

            ->editColumn('cover_no', function ($fn) {
                return $fn->cover_no;
            })

            ->editColumn('cover_type', function ($fn) {
                $t = CoverType::where('type_id', $fn->cover_type)->first();
                return $t->type_name;
            })

            ->editColumn('class_desc', function ($fn) {
                if ($fn->type_of_bus == 'FPR' || $fn->type_of_bus == 'FNP') {
                    $class_desc = Classes::where('class_code', $fn->class_code)->first();
                    // $class_desc=$class_desc->class_name;
                    if ($class_desc) {
                        $class_desc = 'FACULTATIVE - ' . $class_desc->class_name;
                    } else {
                        $class_desc = 'Unknown Class';
                    }
                } elseif ($fn->type_of_bus == 'TPR') {

                    $class_desc = 'TREATY -  PROPORTIONAL';
                } elseif ($fn->type_of_bus == 'TNP') {
                    $class_desc = 'TREATY  - NON PROPORTIONAL';
                }
                return $class_desc;
            })

            ->editColumn('cover_to', function ($fn) {
                return formatDate($fn->cover_to);
            })
            ->make(true);
    }

    public function getCustomerData()
    {
        $customers = Customer::select(['customer_id', 'name', 'tax_no', 'registration_no', 'email', 'website', 'customer_type']);
        return DataTables::of($customers)
            ->addColumn('debited_covers', function ($fn) {
                $debitedCovers = DB::table('cover_register')
                    ->where('customer_id', $fn->customer_id)
                    ->count();

                return $debitedCovers;
            })
            ->addColumn('actions', function ($fn) {
                $btn = "";
                $btn .= '<button class="edit_customer btn btn-primary btn-sm-action datatable-btn me-2" data-id="' . $fn->customer_id . '">Edit <i class="bx bx-edit"></i></button>';
                $btn .= '<button class="remove_process_customer btn btn-danger btn-sm-action datatable-btn" data-name="' . $fn->name . '" data-cedant_id="' . $fn->customer_id . '">Delete <i class="bx bx-trash"></i></button>';
                return $btn;
            })
            ->addColumn('customer_type_name', function ($fn) {
                $customerTypes = CustomerTypes::whereIn('type_id', $fn->customer_type)->select(['type_id', 'type_name', 'code'])->get();
                $rr = collect($customerTypes)->map(function ($query) {
                    return ' ' . $query->type_name;
                });

                return count($rr) > 0 ? json_decode($rr->toJson(), true) : "<b class='dashes' style=''>_</b>";
            })
            ->rawColumns(['customer_type_name', 'actions', 'debited_covers'])
            ->make(true);
    }

    public function getCedantData()
    {
        $customers = Customer::select(['customer_id', 'name', 'tax_no', 'registration_no', 'email', 'website', 'customer_type']);
        return DataTables::of($customers)
            ->addColumn('debited_covers', function ($fn) {
                $debitedCovers = DB::table('cover_register')
                    ->where('customer_id', $fn->customer_id)
                    ->count();

                return $debitedCovers;
            })
            ->addColumn('process', function ($fn) {
                $btn = "";
                $btn .= '<button class="process_customer btn btn-primary btn-sm-action datatable-btn" onclick="processCustomer(`' . $fn->customer_id . '`)">Process <i class="bx bx-send"></i></button>';
                $btn .= " <button class='remove_process_customer btn btn-danger  datatable-btn' data-name='{$fn->name}' data-cedant_id='{$fn->customer_id}'><i class='bx bx-trash'></i> Clear all Covers</button>";
                return $btn;
            })
            ->addColumn('customer_type_name', function ($fn) {
                $customerTypes = CustomerTypes::whereIn('type_id', $fn->customer_type)->select(['type_id', 'type_name', 'code'])->get();
                $rr = collect($customerTypes)->map(function ($query) {
                    return ' ' . $query->type_name;
                });

                return count($rr) > 0 ? json_decode($rr->toJson(), true) : "<b class='dashes' style=''>_</b>";
            })
            ->rawColumns(['customer_type_name', 'process', 'debited_covers'])
            ->make(true);
    }

    public function customerEdit($customerId)
    {
        $customer = Customer::where('customer_id', $customerId)->firstOrFail([
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

        $customerTypes = CustomerTypes::select(['type_id', 'type_name', 'code'])->get();
        $countries = Country::select(['country_iso', 'country_name'])->get();
        $partnersId = PartnerIdentification::select(['identification_type', 'issued_by', 'issue_date', 'description'])->get();

        logger(json_encode($customer->load('contacts'), JSON_PRETTY_PRINT));


        return view('customer.customer_edit_form', [
            'type_of_cust' => $customerTypes,
            'countries' => $countries,
            'partnersId' => $partnersId,
            'customer' => $customer
        ]);
    }

    public function customerUpdate($customerId)
    {
        // $customer = Customer::where('customer_id', $customerId)->firstOrFail(['customer_id', 'name', 'tax_no', 'registration_no', 'email', 'website', 'customer_type']);

        // $customerTypes = CustomerTypes::select(['type_id', 'type_name', 'code'])->get();
        // $countries = Country::select(['country_iso', 'country_name'])->get();
        // $partnersId = PartnerIdentification::select(['identification_type', 'issued_by', 'issue_date', 'description'])->get();

        // logger(json_encode($customer, JSON_PRETTY_PRINT));


        // return view('customer.customer_edit_form', [
        //     'type_of_cust' => $customerTypes,
        //     'countries' => $countries,
        //     'partnersId' => $partnersId,
        //     'customer' => $customer
        // ]);

        return null;
    }

    public function getReinsurerData()
    {
        $customers = DB::select("
                SELECT a.customer_id, a.name, a.tax_no, a.registration_no, a.email, a.website,a.customer_type,
                    b.type_name as customer_type_name,b.code as customer_type_slug
                FROM customers a
                JOIN customer_types b ON b.type_id = a.customer_type
                where b.code='REINCO'
                ");
        return DataTables::of($customers)
            ->editColumn('process', function ($fn) {
                return '<button class="process_customer btn btn-primary  datatable-btn "onclick="processCustomer(`' . $fn->customer_id . '`)">Process</button>';
            })

            ->rawColumns(['process'])
            ->make(true);
    }

    public function getInsuredData()
    {
        $customers = DB::select("
                SELECT a.customer_id, a.name, a.tax_no, a.registration_no, a.email, a.website,a.customer_type,
                    b.type_name as customer_type_name,b.code as customer_type_slug
                FROM customers a
                JOIN customer_types b ON b.type_id = a.customer_type
                where b.code='INSURED'
                ");

        return DataTables::of($customers)
            ->editColumn('process', function ($fn) {
                return '<button class="process_customer btn btn-primary  datatable-btn "onclick="processCustomer(`' . $fn->customer_id . '`)">Process</button>';
            })

            ->rawColumns(['process'])
            ->make(true);
    }

    public function CustomerAddForm()
    {
        $customerTypes = CustomerTypes::select(['type_id', 'type_name', 'code'])->get();
        $countries = Country::select(['country_iso', 'country_name'])->get();
        $partnersId = PartnerIdentification::select(['identification_type', 'issued_by', 'issue_date', 'description'])->get();

        return view('customer.customer_add_form', [
            'type_of_cust' => $customerTypes,
            'countries' => $countries,
            'partnersId' => $partnersId,
        ]);
    }

    public function CustomerAddData(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'partnerName' => 'required|string|max:255|unique:customers,name',
                'customerType' => 'required|array',
                'financialRating' => 'required|string',
                'agencyRating' => 'required|string',
                'email' => 'required|email|max:255',
                'street' => 'required|string|max:255',
                'taxNo' => 'required|string|max:255',
                'incorporationNo' => 'required|string|max:255',
                'website' => 'nullable|url|max:255',
                'city' => 'required|string|max:255',
                'identityType' => 'required|string|max:50',
                'identityNo' => 'required|string|max:50',
                'telephone' => 'nullable|string|max:20',
                'postalCode' => 'required|string|max:10',
                'country' => 'required|string|max:3',
                'contacts' => 'nullable|array',
                'contacts.*.name' => 'nullable|string|max:255',
                'contacts.*.position' => 'nullable|string|max:255',
                'contacts.*.mobile' => 'nullable|string|max:20',
                'contacts.*.email' => 'nullable|email|max:255',
                'contacts.*.isPrimary' => 'nullable|boolean',
                'contacts.*.order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'code' => -1,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $customerType = is_array($request->customerType) ? $request->customerType : explode(',', $request->customerType);
            $customer = Customer::create([
                'name' => $request->partnerName,
                'customer_type' => $customerType,
                'street' => $request->street,
                'city' => $request->city,
                'postal_address' => $request->postalCode,
                'country_iso' => $request->country,
                'registration_no' => $request->incorporationNo,
                'tax_no' => $request->taxNo,
                'startdate' => null,
                'email' => $request->email,
                'financial_rate' => $request->financialRating,
                'agency_rate' => $request->agencyRating,
                'website' => $request->website,
                'telephone' => $request->telephone,
                'identity_number_type' => $request->identityType,
                'identity_number' => $request->identityNo,
                'status' => 'A',
                'created_by' => Auth::user()->user_name ?? 'system',
                'created_at' => Carbon::now(),
            ]);

            if ($request->has('contacts') && is_array($request->contacts)) {
                foreach ($request->contacts as $contactData) {
                    if (!empty($contactData['name']) || !empty($contactData['email']) || !empty($contactData['mobile'])) {
                        CustomerContact::create([
                            'customer_id' => $customer->id,
                            'contact_name' => $contactData['name'],
                            'contact_position' => $contactData['position'],
                            'contact_mobile_no' => $contactData['mobile'],
                            'contact_email' => $contactData['email'],
                            'is_primary' => $contactData['isPrimary'] ?? false,
                            'order' => $contactData['order'] ?? 0,
                            'created_at' => Carbon::now(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'code' => 1,
                'message' => 'Customer information saved successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'code' => -1,
                'message' => 'An error occurred while saving customer information',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function CustomerDtl(Request $request)
    {
        $customer = Customer::where('customer_id', $request->customer_id)->get(['customer_id', 'name', 'postal_address', 'postal_town', 'city', 'email', 'telephone', 'country_iso', 'customer_type'])[0];
        $country = Country::where('country_iso', $customer->country_iso)->first(['country_iso', 'country_name']);

        return view('customer.customer_dtl', [
            'country' => $country,
            'customer' => $customer
        ]);
    }

    public function StatementDatatable(Request $request)
    {
        $customer_id = $request->get('customer_id');
        $query = CustomerAccDet::query()->where('customer_id', $customer_id)->get();

        return datatables::of($query)
            ->editColumn('doc_type', function ($fn) {
                return $fn->doc_type;
            })
            ->editColumn('cover_no', function ($fn) {
                return $fn->cover_no;
            })
            ->editColumn('endorsement_no', function ($fn) {
                return $fn->endorsement_no;
            })
            ->editColumn('reference', function ($fn) {
                return $fn->reference;
            })
            ->editColumn('entry_type_descr', function ($fn) {
                return $fn->entry_type_descr;
            })
            ->editColumn('local_nett_amount', function ($fn) {
                return number_format($fn->local_nett_amount, 2);
            })
            ->editColumn('unallocated_amount', function ($fn) {
                return number_format($fn->unallocated_amount, 2);
            })
            ->editColumn('created_date', function ($fn) {
                return formatDate($fn->created_date);
            })
            ->make(true);
    }

    public function clearCedantData(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required',
            ]);

            ClearCedantDataJob::dispatch($request->id);

            return response()->json([
                'status' => Response::HTTP_ACCEPTED,
                'message' => 'Cedant data clearing has been queued'
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
}
