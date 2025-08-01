<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        return view('quote.quote_home', []);
    }
}
