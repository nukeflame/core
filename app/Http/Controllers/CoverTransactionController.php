<?php

namespace App\Http\Controllers;

use App\Models\CoverRegister;
use App\Models\Customer;
use Illuminate\Http\Request;

class CoverTransactionController extends Controller
{
    /**
     * Display the main transactions page for a specific cover.
     *
     * @param string $coverNumber
     * @return \Illuminate\View\View
     */
    public function index($endorsementNo)
    {


        $cover = CoverRegister::where('endorsement_no', $endorsementNo)->firstOrFail();
        $customer = Customer::where('customer_id', $cover->customer_id)->first();




        // logger()->debug($customer);
        $transactions = [];
        $endorsementNarration = [];
        $actionable = true;
        $isTransaction  = true;

        return view('cover.transactions.cover_transaction_home', [
            'endorsementNo' => $endorsementNo,
            'cover' => $cover,
            'transactions' => $transactions,
            'endorsementNarration' => $endorsementNarration,
            'actionable' => $actionable,
            'isTransaction' => $isTransaction,
            'customer' => $customer
        ]);
    }

    /**
     * Display debit transactions page for a specific cover.
     *
     * @param string $coverNumber
     * @return \Illuminate\View\View
     */
    public function debit($endorsementNo)
    {
        $cover = CoverRegister::where('endorsement_no', $endorsementNo)->firstOrFail();
        $customer = Customer::where('customer_id', $cover->customer_id)->first();




        // logger()->debug($customer);
        $transactions = [];
        $endorsementNarration = [];
        $actionable = true;
        $isTransaction  = true;

        return view('cover.transactions.cover_transaction_debit', [
            'endorsementNo' => $endorsementNo,
            'cover' => $cover,
            'transactions' => $transactions,
            'endorsementNarration' => $endorsementNarration,
            'actionable' => $actionable,
            'isTransaction' => $isTransaction,
            'customer' => $customer
        ]);
    }

    /**
     * Display profit commission transactions page for a specific cover.
     *
     * @param string $coverNumber
     * @return \Illuminate\View\View
     */
    public function profitCommission($coverNumber)
    {
        // [Inference] This filters for profit commission type transactions
        // Fetch cover details and profit commission transactions
        // Example: $cover = Cover::where('cover_number', $coverNumber)->firstOrFail();
        // Example: $profitCommissions = Transaction::where('cover_number', $coverNumber)
        //              ->where('type', 'profit_commission')->get();

        // return view('cover.transactions.cover_transaction_profit_commission', [
        //     'coverNumber' => $coverNumber,
        //     // 'cover' => $cover,
        //     // 'profitCommissions' => $profitCommissions,
        // ]);
        return null;
    }
}
