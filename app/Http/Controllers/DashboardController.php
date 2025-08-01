<?php

namespace App\Http\Controllers;

use App\Enums\PermissionsLevel;
use App\Models\CoverDebit;
use App\Models\CoverRegister;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalCovers    = $this->totalCovers();
        $totalFacCovers = $this->totalFacCovers();
        $totalDebitedCovers = $this->totalDebitedCovers();
        $totalTPRCovers = $this->totalTPRCovers();
        $totalTNPCovers = $this->totalTNPCovers();

        // Check if cookie exists
        if ($request->cookie('show_report_sidebar')) {
            return (new AnalyticsController)->analytics();
        }

        $todos = Todo::where('user_id', Auth::id())->get();

        return view('dashboard', [
            'totalCovers'        => $totalCovers,
            'totalFacCovers'     => $totalFacCovers,
            'totalTPRCovers'     => $totalTPRCovers,
            'totalTNPCovers'     => $totalTNPCovers,
            'totalDebitedCovers' => $totalDebitedCovers,
            'todos'              => $todos
        ]);
    }

    public function totalCovers(): array
    {
        $total = CoverRegister::where('account_year', now()->year)
            ->where('account_month', now()->month)
            ->where('cancelled', 'N')
            ->where('created_by', auth()->user()->user_name)
            ->count();

        return [
            'title' => 'Total Reg. Covers',
            'amount' => $total,
        ];
    }

    public function totalFacCovers()
    {
        $total = CoverRegister::where('account_year', now()->year)
            ->where('account_month', now()->month)
            ->where('cancelled', 'N')
            ->where('created_by', auth()->user()->user_name)
            ->whereIn('type_of_bus', ['FPR', 'FNP'])
            ->count();

        return [
            'title' => 'Fac. Non-Proportional',
            'amount' => $total,
        ];
    }

    public function totalDebitedCovers(): array
    {
        $total = CoverDebit::where('period_year', now()->year)
            ->where('period_month', now()->month)
            ->where('reversed', 'N')
            ->where('created_by', auth()->user()->user_name)
            ->count();

        return [
            'title' => 'Debited Covers',
            'amount' => $total,
        ];
    }

    public function totalTPRCovers(): array
    {
        $total = CoverRegister::where('account_month', now()->month)
            ->where('account_year', now()->year)
            ->where('cancelled', 'N')
            ->where('type_of_bus', 'TPR')
            ->count();

        return [
            'title' => 'Proportional Treaty',
            'amount' => $total,
        ];
    }

    public function totalTNPCovers(): array
    {
        $total = CoverRegister::where('account_month', now()->month)
            ->where('account_year', now()->year)
            ->where('cancelled', 'N')
            ->where('type_of_bus', 'TNP')
            ->count();

        return [
            'title' => 'Treaty Non-Prop.',
            'amount' => $total,
        ];
    }

    public function totalTNPCoversPremium(): array
    {
        $total = CoverRegister::where('account_month', now()->month)
            ->where('account_year', now()->year)
            ->where('cancelled', 'N')
            ->where('type_of_bus', 'TNP')
            ->sum('rein_premium');

        return [
            'title' => 'Non-Prop. Treaty Covers',
            'amount' => $total,
        ];
    }

    public function appointmentsDatatable()
    {
        return DataTables::of([])
            ->addColumn('status', function ($data) {
                return "";
            })
            ->addColumn('actions', function ($data) {
                return "";
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }
}
