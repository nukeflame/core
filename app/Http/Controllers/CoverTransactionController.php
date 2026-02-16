<?php

namespace App\Http\Controllers;

use App\Models\CoverRegister;
use App\Models\Customer;
use App\Models\CustomerAccDet;
use App\Models\CoverDebit;
use App\Models\CoverRipart;
use App\Models\CoverReinProp;
use App\Models\DebitNote;
use App\Models\ReinclassPremtype;
use App\Models\TreatyDocument;
use App\Models\TreatyItemCode;
use App\Models\CoverPremtype;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CoverTransactionController extends Controller
{

    public function index(Request $request, $coverNo)
    {
        $cover = CoverRegister::where('cover_no', $coverNo)->whereIn('type_of_bus', ['TPR', 'TRP'])->firstOrFail();
        $customer = Customer::where('customer_id', $cover->customer_id)->first();

        $query = CustomerAccDet::query();

        $query->where('cover_no', $cover->cover_no);

        if ($request->filled('type_of_bus')) {
            $query->where('type_of_bus', $request->type_of_bus);
        }

        if ($request->filled('doc_type')) {
            $query->where('doc_type', $request->doc_type);
        }

        if ($request->filled('source_code')) {
            $query->where('source_code', $request->source_code);
        }

        if ($request->filled('account_year')) {
            $query->where('account_year', $request->account_year);
        }

        if ($request->filled('account_month')) {
            $query->where('account_month', $request->account_month);
        }

        $accounts = $query->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        $statsQuery = CustomerAccDet::where('cover_no', $coverNo);

        $stats = [
            'total_records' => (clone $statsQuery)->count(),
            'total_debits' => (clone $statsQuery)->where('dr_cr', 'D')->sum('local_nett_amount'),
            'total_credits' => (clone $statsQuery)->where('dr_cr', 'C')->sum('local_nett_amount'),
            'total_unallocated' => (clone $statsQuery)->sum('unallocated_amount'),
        ];

        $transactions = CoverDebit::where('endorsement_no', $cover->endorsement_no)
            ->orderBy('created_at', 'desc')
            ->get();

        $lastInstallment = CoverDebit::where('endorsement_no', $cover->endorsement_no)
            ->max('installment') ?? 0;
        $nextInstallment = $lastInstallment + 1;

        $installmentAmount = 0;
        if ($cover->no_of_installments > 0 && $cover->gross_premium > 0) {
            $installmentAmount = $cover->gross_premium / $cover->no_of_installments;
        }

        $endorsementNarration = $this->getEndorsementNarration($cover);
        $itemCodes = $this->getItemCodes();
        $classGroups = $this->getClassGroups();
        $businessClasses = $this->getBusinessClasses();
        $treatyClasses = $this->getTreatyClasses($cover->endorsement_no);

        $actionable = $cover->status !== 'CANCELLED' && $cover->status !== 'EXPIRED';
        $isTransaction = true; //$transactions->count() > 0;

        return view('cover.transactions.cover_transaction_home', [
            'endorsementNo' => $cover->endorsement_no,
            'cover' => $cover,
            'transactions' => $transactions,
            'endorsementNarration' => $endorsementNarration,
            'actionable' => $actionable,
            'isTransaction' => $isTransaction,
            'customer' => $customer,
            'nextInstallment' => $nextInstallment,
            'installmentAmount' => $installmentAmount,
            'accounts' => $accounts,
            'stats' => $stats,
            'itemCodes' => $itemCodes,
            'classGroups' => $classGroups,
            'businessClasses' => $businessClasses,
            'treatyClasses' => $treatyClasses,
            'taxRates' => TaxRate::getAllCurrentRates(),
        ]);
    }


    protected function getItemCodes(): array
    {
        return Cache::remember('treaty_item_codes', 3600, function () {
            return TreatyItemCode::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['item_code', 'description', 'item_type'])
                ->mapWithKeys(fn($item) => [
                    $item->item_code => [
                        'description' => $item->description,
                        'type' => $item->item_type,
                    ]
                ])
                ->toArray();
        });
    }

    protected function getClassGroups(): array
    {
        return Cache::remember('reins_class_groups', 3600, function () {
            $data = [];
            $reinclass = DB::table('reinsclasses')->get();

            foreach ($reinclass as $rein) {
                $data[] = [
                    'group_name' => $rein->class_name,
                    'group_code' => $rein->class_code,
                ];
            }

            return $data;
        });
    }

    protected function getBusinessClasses(): array
    {
        return Cache::remember('business_classes', 3600, function () {
            $data = [];
            $reinclasses = ReinclassPremtype::with('classGroup')->get();

            foreach ($reinclasses as $class) {
                $groupCode = $class->classGroup->class_code ?? null;
                $code = $class->premtype_code;
                $name = $class->premtype_name;

                if ($groupCode) {
                    $data[$groupCode][$code] = $name;
                }
            }

            return $data;
        });
    }

    public function quarterlyFigures(Request $request, $coverNo, $refNo)
    {
        $cover = CoverRegister::where('endorsement_no', $request->endorsementNo)->firstOrFail();
        $customer = Customer::with('primaryContact')->where('customer_id', $cover->customer_id)->first();
        $decodedRefNo = urldecode((string) $refNo);

        $selectedTransaction = CustomerAccDet::where('cover_no', $cover->cover_no)
            ->where('endorsement_no', $request->endorsementNo)
            ->where('reference', $decodedRefNo)
            ->orderByDesc('created_at')
            ->first();

        $currentQuarter = $selectedTransaction?->quarter;
        $currentYear = $selectedTransaction?->account_year;
        $currentQuarterDisplay = ($currentQuarter && $currentYear)
            ? strtoupper((string) $currentQuarter) . ' - ' . $currentYear
            : ('Q' . now()->quarter . ' - ' . now()->year);

        $transactions = CoverDebit::where('endorsement_no', $request->endorsementNo)
            ->orderBy('installment', 'asc')
            ->get();

        $lastInstallment = $transactions->max('installment') ?? 0;
        $nextInstallment = $lastInstallment + 1;

        $totalDebited = $transactions->sum('gross');
        $remainingAmount = ($cover->gross_premium ?? 0) - $totalDebited;

        $endorsementNarration = $this->getEndorsementNarration($cover);

        $actionable = $cover->status !== 'CANCELLED'
            && $cover->status !== 'EXPIRED'
            && $remainingAmount > 0;

        $documents = TreatyDocument::where(['cover_no' => $cover->cover_no])->get();

        $isTransaction = $transactions->count() > 0;
        $totalDocuments = $documents->count();
        $totalDebitItems = DebitNote::where([
            'cover_no' => $cover->cover_no,
            'endorsement_no' => $cover->endorsement_no
        ])
            ->withCount('items')
            ->get()
            ->sum('items_count');

        $totalReinsurers = CoverRipart::where(['cover_no' => $cover->cover_no, 'endorsement_no' =>  $cover->endorsement_no])->count();
        $cedantTreatyCapacity = (float) CoverReinProp::where('cover_no', $cover->cover_no)
            ->where('endorsement_no', $cover->endorsement_no)
            ->sum('treaty_amount');

        if ($cedantTreatyCapacity <= 0) {
            $cedantTreatyCapacity = (float) ($cover->effective_sum_insured ?? $cover->total_sum_insured ?? $cover->sum_insured ?? $cover->treaty_capacity ?? 0);
        }

        $cedantGrossPremium = (float) CoverRipart::where([
            'cover_no' => $cover->cover_no,
            'endorsement_no' => $cover->endorsement_no,
        ])->sum('total_premium');
        $cedantClaimAmount = (float) CoverRipart::where([
            'cover_no' => $cover->cover_no,
            'endorsement_no' => $cover->endorsement_no,
        ])->sum('claim_amt');
        $cedantIsCoverNote = $cedantClaimAmount > $cedantGrossPremium;

        return view('cover.transactions.cover_transaction_debit', [
            'endorsementNo' => $request->endorsementNo,
            'cover' => $cover,
            'transactions' => $transactions,
            'endorsementNarration' => $endorsementNarration,
            'actionable' => $actionable,
            'isTransaction' => $isTransaction,
            'customer' => $customer,
            'nextInstallment' => $nextInstallment,
            'remainingAmount' => $remainingAmount,
            'totalDebited' => $totalDebited,
            'totalDocuments' => $totalDocuments,
            'totalDebitItems' => $totalDebitItems,
            'totalReinsurers' => $totalReinsurers,
            'cedantTreatyCapacity' => $cedantTreatyCapacity,
            'cedantIsCoverNote' => $cedantIsCoverNote,
            'currentQuarterDisplay' => $currentQuarterDisplay,
        ]);
    }

    public function profitCommission(Request $request, $coverNo, $refNo)
    {
        $cover = CoverRegister::where('endorsement_no', $request->endorsementNo)->firstOrFail();
        $customer = Customer::where('customer_id', $cover->customer_id)->first();

        // Fetch profit commission transactions
        $profitCommissions = CustomerAccDet::where('endorsement_no', $request->endorsementNo)
            ->where('entry_type_descr', 'LIKE', '%Profit Commission%')
            ->orderBy('created_date', 'desc')
            ->get();

        $endorsementNarration = $this->getEndorsementNarration($cover);
        $actionable = $cover->status !== 'CANCELLED' && $cover->status !== 'EXPIRED';

        return view('cover.transactions.cover_transaction_profit_commission', [
            'endorsementNo' => $request->endorsementNo,
            'cover' => $cover,
            'customer' => $customer,
            'profitCommissions' => $profitCommissions,
            'endorsementNarration' => $endorsementNarration,
            'actionable' => $actionable,
        ]);
    }

    private function getEndorsementNarration($cover): array
    {
        $narration = [];

        switch ($cover->transaction_type) {
            case 'NEW':
                $narration = [
                    'type' => 'New Business',
                    'description' => 'New policy cover registration',
                    'icon' => 'fa-plus-circle',
                    'color' => 'success'
                ];
                break;
            case 'REN':
                $narration = [
                    'type' => 'Renewal',
                    'description' => 'Policy renewal',
                    'icon' => 'fa-sync',
                    'color' => 'info'
                ];
                break;
            case 'END':
                $narration = [
                    'type' => 'Endorsement',
                    'description' => 'Policy endorsement/amendment',
                    'icon' => 'fa-edit',
                    'color' => 'warning'
                ];
                break;
            case 'CAN':
                $narration = [
                    'type' => 'Cancellation',
                    'description' => 'Policy cancellation',
                    'icon' => 'fa-times-circle',
                    'color' => 'danger'
                ];
                break;
            default:
                $narration = [
                    'type' => $cover->transaction_type ?? 'Unknown',
                    'description' => 'Transaction',
                    'icon' => 'fa-file',
                    'color' => 'secondary'
                ];
        }

        return $narration;
    }

    public function storeQuarterlyFigures(Request $request)
    {
        // $validated = $request->validate([
        //     'treaty_id' => 'required|exists:treaties,id',
        //     'transaction_id' => 'required|exists:treaty_transactions,id',
        //     'quarter' => 'required|in:Q1,Q2,Q3,Q4',
        //     'year' => 'required|integer|min:2000|max:2100',
        //     'gross_premium' => 'required|numeric|min:0',
        //     'return_premium' => 'nullable|numeric|min:0',
        //     'net_premium' => 'nullable|numeric',
        //     'commission_rate' => 'nullable|numeric|min:0|max:100',
        //     'commission_amount' => 'nullable|numeric|min:0',
        //     'brokerage_rate' => 'nullable|numeric|min:0|max:100',
        //     'claims_paid' => 'nullable|numeric|min:0',
        //     'claims_outstanding' => 'nullable|numeric|min:0',
        //     'remarks' => 'nullable|string|max:500',
        // ]);

        // $quarterlyFigure = QuarterlyFigure::create($validated);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Quarterly figures created successfully',
        //     'data' => $quarterlyFigure
        // ]);

        return null;
    }

    public function storeProfitCommission(Request $request)
    {
        // $validated = $request->validate([
        //     'treaty_id' => 'required|exists:treaties,id',
        //     'transaction_id' => 'required|exists:treaty_transactions,id',
        //     'from_date' => 'required|date',
        //     'to_date' => 'required|date|after_or_equal:from_date',
        //     'premium_income' => 'required|numeric|min:0',
        //     'portfolio_premium' => 'nullable|numeric|min:0',
        //     'claims_paid' => 'nullable|numeric|min:0',
        //     'claims_outstanding' => 'nullable|numeric|min:0',
        //     'portfolio_claims' => 'nullable|numeric|min:0',
        //     'commission_paid' => 'nullable|numeric|min:0',
        //     'management_expenses_rate' => 'nullable|numeric|min:0|max:100',
        //     'reserve_rate' => 'nullable|numeric|min:0|max:100',
        //     'profit_commission_rate' => 'required|numeric|min:0|max:100',
        //     'profit_commission_amount' => 'nullable|numeric|min:0',
        //     'deficit_bf' => 'nullable|numeric|min:0',
        //     'deficit_cf' => 'nullable|numeric|min:0',
        //     'remarks' => 'nullable|string|max:500',
        // ]);

        // $profitCommission = ProfitCommission::create($validated);

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Profit commission added successfully',
        //     'data' => $profitCommission
        // ]);
        return null;
    }

    public function adjustCommission(Request $request)
    {
        // ... (existing commented out code)
        return null;
    }

    protected function getTreatyClasses($endorsement_no)
    {
        return CoverPremtype::join('treaty_types', 'cover_premtypes.treaty', '=', 'treaty_types.treaty_code')
            ->where('cover_premtypes.endorsement_no', $endorsement_no)
            ->get([
                'cover_premtypes.premtype_name as class_name',
                'cover_premtypes.comm_rate as commission',
                'cover_premtypes.premtype_code as class_code',
            ]);
    }
}
