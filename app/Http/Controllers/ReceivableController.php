<?php

namespace App\Http\Controllers;

use DB;
use View;
use DataTables;

use Carbon\Carbon;
use App\Models\Country;
use App\Models\ARCustomer;
use Illuminate\Http\Request;

use App\Models\CustomerTypes;
use App\Models\ARCustomerGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReceivableController extends Controller
{

    function getCustomers(Request $request) {
        return view::make('finance.ar.customers_list');
    }

    function getCustomerData(Request $request){
        $customers = ARCustomer::select(['customer_group','customer_id', 'customer_name', 'pin_number', 'registration_no', 'email','status']);
        return Datatables::Of($customers)
    
            -> addColumn('group_descr', function ($cust) {
                $grp = ARCustomerGroup::where('group_id', $cust -> customer_group) -> first();
                return $grp -> group_description;
            })
            ->addColumn('action', function ($cust) {
                if($cust->status=='A'){
                    $disable="disabled";
                }else {
                    $disable=" ";
                }
                return '<button type="button" class="btn btn-outline-primary btn-sm" id="edit_cust" '.' '.$disable.'>Edit</button>';
            })
            ->make(true);
    }

    public function CustomerAddForm()
    {
        $custGrps = ARCustomerGroup::select(['group_id','group_description'])->where('lob_group','<>','Y')->get();
        $countries = Country::select(['country_iso','country_name'])->get();
        return view('finance.ar.customer_add_form',[
            'custGrps'=>$custGrps,
            'countries'=>$countries
         ]);
    }

    function CustomerAddData(Request $request){
        // dd($request);
        try {
            $firstThreeCharacters = substr($request->name, 0, 3);
            $count = ARCustomer::whereRaw("LEFT(customer_name, 3) = '" . $firstThreeCharacters . "'")->count();
            // Validation rules
    $validator =  Validator::make($request->all(),[
        'name'=>'required|string|max:255',
        'street' => 'required|string|max:255',
        'city' => 'required|string|max:255',
        'postal_address' => 'required|string|max:10',
        'country' => 'required|string|max:255',
        'customer_reg_no' => 'required|string|max:255',
        'customer_tax_no' => 'required|string|max:255',
        'startdate' => 'required|date',
        'email' => 'required|email|max:255',
        // 'financial_rate' => 'required|string|max:255',
        // 'agency_rate' => 'required|string|max:255',
        // 'website' => 'nullable|url|max:255',
        'telephone' => 'nullable|string|max:20',
    ]);

    $count = str_pad($count+1, 7, '0', STR_PAD_LEFT);

    if($validator){
       // If the validation passes, you can proceed to store the data in the database or perform other actions
       ARCustomer::create([
        // personal details
        'customer_name' => $request->name,
        'customer_id'=>$firstThreeCharacters.$count,
        'customer_group'=>$request->type_of_cust,
        'postal_street' => $request->street,
        'postal_city' => $request->city,
        'postal_address' => $request->postal_address,
        'country_iso' => $request->country_iso,
        'registration_no' => $request->customer_reg_no,
        'pin_number' => $request->customer_tax_no,
        'registration_date' => $request->startdate,
        'email' => $request->email,
        'industry' => $request->industry,
        'source_of_income' => $request->source_of_income,
        // 'website' => $request->website,
        'tax_group' => substr($request->tax_group,0,5),
        // contact details
        'telephone' => $request->telephone,
        'contact_name' => $request->contact_name,
        'contact_email' => $request->contact_email,
        'contact_position' => $request->contact_position,
        'contact_mobile_no' => $request->contact_mobile_no,
        // bank details
        'bank_code' => $request->bank_code,
        'bank_branch_code' => $request->bank_branch_code,
        'bank_account_no' => $request->bank_acc_no,
        'bank_account_name' => $request->bank_acc_name,
        // others
        'status' => 'A',
        'created_by' => Auth::user()->user_name,
        'created_at' => Carbon::now(),
       ]
    );

        // Redirect or return a response as needed
        return redirect('/finance/ar/customers')->with('success', 'Customer information saved successfully');
    }
    else{
        Session::flash('error', 'some field are missing');
        return [
            'code' => -1,
            'msg' => $validator->errors(),
        ];
    }
    } catch (\Throwable $e) {
        throw $e;
        // dd($e);
    }
    }

    //
}
