<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DataTables;
use View;
use DB;

use App\Models\COA_Config;
use App\Models\Currency;
use App\Models\ARCustomerGroup;

class SettingsReceivableController extends Controller
{

function getCustomerGroups(Request $request) {
    return view::make('settings.finance.ar_customer_grp');
}

function getCustomerGroupsData(Request $request){
    $customer_grps = ARCustomerGroup::all();
    return Datatables::Of($customer_grps)

        -> addColumn('currency_descr', function ($grp) {
            $currency = Currency::where('currency_code', $grp -> default_currency) -> first();
            return $currency -> currency_name;
        })

        -> addColumn('tax_descr', function ($grp) {
            $tax = DB::table('tax_groups') -> where('group_id', $grp -> tax_category) -> first();
            return $tax -> group_description;
        })

        -> addColumn('glh_descr', function ($grp) {
            $glhd = trim($grp -> control_account);
            $glh = COA_Config::where('segment_code', 'COD') 
                    -> whereRaw("trim(account_number) = '" . $glhd . "' ") -> first();
            if ($glh) {
                return $glh -> description;
            }else {
                return ' ';
            }
            
        })
        ->make(true);
}
    //END
}
