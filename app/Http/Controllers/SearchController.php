<?php

namespace App\Http\Controllers;

use App\Models\CoverDebit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function searchResults(Request $request)
    {
        $searchTerm = $request->input('query');
        if (!$searchTerm) {
            return response()->json(['error' => 'Search term is required'], 400);
        }

        $query = CoverDebit::where(DB::raw('LOWER(gl_updated_invoice_reference)'), 'LIKE', '%' . strtolower($searchTerm) . '%')
            ->orderBy('created_at', 'desc')
            ->select(['cover_no', 'id', 'document', 'endorsement_no', 'gl_updated_invoice_reference'])->take(10);

        $results = $query->get();
        return response()->json($results);
    }
}
