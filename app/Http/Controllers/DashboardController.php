<?php

namespace App\Http\Controllers;

use App\Enums\PermissionsLevel;
use App\Models\CoverDebit;
use App\Models\CoverRegister;
use App\Models\Todo;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        // Check if cookie exists for report sidebar
        if ($request->cookie('show_report_sidebar')) {
            return (new AnalyticsController)->analytics();
        }

        // Get current year and period
        $year = now()->year;
        $period = $request->get('period', 'ytd');

        // Get dashboard metrics using the service
        $metrics = $this->dashboardService->getMetricsForPeriod($period, $year);
        $businessMix = $this->dashboardService->getBusinessMix($year);
        $coverCounts = $this->dashboardService->getCoverCounts($year, now()->month);
        $recentActivity = $this->dashboardService->getRecentActivity(5);
        $avgCommRate = $this->dashboardService->getAverageCommissionRate($year);

        // Get todos
        $todos = Todo::where('user_id', Auth::id())->get();

        return view('dashboard', [
            // Legacy format for backward compatibility
            'totalCovers'        => $coverCounts['total'],
            'totalFacCovers'     => $coverCounts['fac'],
            'totalTPRCovers'     => $coverCounts['tpr'],
            'totalTNPCovers'     => $coverCounts['tnp'],
            'totalDebitedCovers' => $coverCounts['debited'],
            'todos'              => $todos,

            // New comprehensive data
            'metrics'            => $metrics,
            'businessMix'        => $businessMix,
            'coverCounts'        => $coverCounts,
            'recentActivity'     => $recentActivity,
            'avgCommRate'        => $avgCommRate,
            'currentPeriod'      => $period,
            'currentYear'        => $year,
        ]);
    }

    /**
     * AJAX endpoint for period-based data refresh
     */
    public function getMetrics(Request $request)
    {
        $period = $request->get('period', 'ytd');
        $year = $request->get('year', now()->year);

        $metrics = $this->dashboardService->getMetricsForPeriod($period, $year);
        $businessMix = $this->dashboardService->getBusinessMix($year);

        return response()->json([
            'success' => true,
            'metrics' => $metrics,
            'businessMix' => $businessMix,
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
        return DataTables::of(collect([]))
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
