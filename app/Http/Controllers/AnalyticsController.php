<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class AnalyticsController extends Controller
{
    public function analytics()
    {
        // $cedants = Cedant::orderBy('premium_volume', 'desc')->take(10)->get();
        return view('analytics.analytics', ['cedants' => []]);
    }

    public function budgetTracker(Request $request)
    {
        // Default view is 'gwp' if not specified
        $view = $request->query('view', 'gwp');

        // Sector data
        $sectorData = [
            [
                'name' => 'Facultative',
                'gwp2023' => 734408043.84,
                'gwp2024' => 1090308893.15,
                'gwpChange' => 148.46,
                'gwpContribution' => 74.48,
                'comm2023' => 94164561.76,
                'comm2024' => 164086638.27,
                'commChange' => 174.26,
                'commContribution' => 81.70
            ],
            [
                'name' => 'Special Lines',
                'gwp2023' => 43479056.02,
                'gwp2024' => 122822230.48,
                'gwpChange' => 282.49,
                'gwpContribution' => 8.39,
                'comm2023' => 12198406.31,
                'comm2024' => 23055726.58,
                'commChange' => 189.01,
                'commContribution' => 11.48
            ],
            [
                'name' => 'Treaties',
                'gwp2023' => 97697339.91,
                'gwp2024' => 229639053.77,
                'gwpChange' => 235.05,
                'gwpContribution' => 15.69,
                'comm2023' => 3432704.44,
                'comm2024' => 7940948.20,
                'commChange' => 231.33,
                'commContribution' => 3.95
            ],
            [
                'name' => 'International',
                'gwp2023' => 19904950.20,
                'gwp2024' => 21221971.02,
                'gwpChange' => 106.62,
                'gwpContribution' => 1.45,
                'comm2023' => 4699837.70,
                'comm2024' => 5758618.55,
                'commChange' => 122.53,
                'commContribution' => 2.87
            ]
        ];

        // Calculate totals
        $totals = [
            'gwp2023' => 895489389.97,
            'gwp2024' => 1463992148,
            'gwpChange' => 163.49,
            'comm2023' => 114495510.19,
            'comm2024' => 200841931.60,
            'commChange' => 175.41
        ];

        // Define months and lines of business
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $lineOfBusiness = ['Facultative', 'Treaty', 'Specialty Lines', 'International Markets'];
        $monthlyData = [];

        foreach ($lineOfBusiness as $lob) {
            $monthlyData[$lob] = [];

            // Starting values and growth trends for each LoB
            $budgetBase = mt_rand(40, 60);
            $growthRate = mt_rand(5, 15) / 100; // 5-15% monthly growth
            $performanceVar = mt_rand(85, 115) / 100; // 85-115% performance variance

            foreach ($months as $index => $month) {
                // Calculate budget with growth trend
                $budget = round($budgetBase * (1 + $growthRate * $index), 1);

                // Calculate achieved with performance variance (different each month)
                $monthVar = $performanceVar * (mt_rand(90, 110) / 100); // Add monthly fluctuation
                $achieved = round($budget * $monthVar, 1);

                $monthlyData[$lob][$month] = [
                    'budget' => $budget,
                    'achieved' => $achieved
                ];
            }
        }

        // Prepare data for the view
        $data = [
            'months' => $months,
            'lineOfBusiness' => $lineOfBusiness,
            'monthlyData' => $monthlyData
        ];

        // Calculate summary metrics
        $totalBudget = 0;
        $totalAchieved = 0;

        foreach ($lineOfBusiness as $lob) {
            foreach ($months as $month) {
                $totalBudget += $monthlyData[$lob][$month]['budget'];
                $totalAchieved += $monthlyData[$lob][$month]['achieved'];
            }
        }

        $data['totalBudget'] = round($totalBudget, 1);
        $data['totalAchieved'] = round($totalAchieved, 1);
        $data['overallHitRate'] = round(($totalAchieved / $totalBudget) * 100, 1);
        $monthlyData1 = [
            ['name' => 'Jan', 'hitRate' => 85],
            ['name' => 'Feb', 'hitRate' => 75],
            ['name' => 'Mar', 'hitRate' => 90],
            ['name' => 'Apr', 'hitRate' => 80],
            ['name' => 'May', 'hitRate' => 800],
            // Add more data as needed
        ];


        return view('analytics.budget_tracker', [
            'view' => $view,
            'sectorData' => $sectorData,
            'totals' => $totals,
            'monthlyData1' => $monthlyData1,
            ...$data,

        ]);
    }

    public function facultativeReport()
    {
        return view('analytics.budget_tracker');
    }

    public function getBudegetAchievedGWPData()
    {
        // Option 1: Fetch from database
        // $monthlyData = GwpData::select('month', 'budgeted', 'achieved')
        //     ->whereYear('date', date('Y'))
        //     ->orderBy('date')
        //     ->get()
        //     ->toArray();

        // Option 2: Static data for testing
        $monthlyData = [
            ['month' => 'Jan', 'budgeted' => 400000, 'achieved' => 420000],
            ['month' => 'Feb', 'budgeted' => 450000, 'achieved' => 430000],
            ['month' => 'Mar', 'budgeted' => 480000, 'achieved' => 500000],
            ['month' => 'Apr', 'budgeted' => 460000, 'achieved' => 470000],
            ['month' => 'May', 'budgeted' => 510000, 'achieved' => 490000],
            ['month' => 'Jun', 'budgeted' => 490000, 'achieved' => 520000],
            ['month' => 'Jul', 'budgeted' => 530000, 'achieved' => 550000],
            ['month' => 'Aug', 'budgeted' => 520000, 'achieved' => 510000],
            ['month' => 'Sep', 'budgeted' => 540000, 'achieved' => 570000],
            ['month' => 'Oct', 'budgeted' => 550000, 'achieved' => 590000],
            ['month' => 'Nov', 'budgeted' => 580000, 'achieved' => 560000],
            ['month' => 'Dec', 'budgeted' => 600000, 'achieved' => 630000]
        ];

        return response()->json([
            'success' => true,
            'data' => $monthlyData
        ]);
    }

    public function getKpis(Request $request)
    {
        $kpis = [
            [
                'kpi' => 'Combined Ratio',
                'current_value' => '95.7%',
                'target' => '<100%',
                'status' => true
            ],
            [
                'kpi' => 'Loss Ratio',
                'current_value' => '62.3%',
                'target' => '<65%',
                'status' => true
            ],
            [
                'kpi' => 'Expense Ratio',
                'current_value' => '33.4%',
                'target' => '<35%',
                'status' => true
            ],
            [
                'kpi' => 'Return on Equity',
                'current_value' => '14.2%',
                'target' => '>12%',
                'status' => true
            ],
            [
                'kpi' => 'Treaty Renewal Rate',
                'current_value' => '87.5%',
                'target' => '>85%',
                'status' => true
            ],
            [
                'kpi' => 'Average Premium Growth',
                'current_value' => '+7.8%',
                'target' => '>5%',
                'status' => true
            ]
        ];
        return DataTables::of($kpis)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                // return $row->status == 1 ? '✓' : '✗';
                return '';
            })
            ->rawColumns(['status'])
            ->make(true);
    }

    public function coverAdministration()
    {
        $cedantData = [];
        return view('analytics.cover_administration', ['cedantData' => $cedantData]);
    }


    public function claimsAdministration()
    {
        return view('analytics.claims_administration');
    }


    public function debtors()
    {
        return view('analytics.debtors');
    }

    public function businessDevelopment()
    {
        // Data for 2025 (structured as in the original JavaScript)
        $data2024 = [
            [
                'date' => '31.01.2025',
                'month' => 'Jan',
                'wonRisks' => ['count' => null, 'percent' => null],
                'lostAccounts' => ['count' => null, 'percent' => null],
                'notQuoted' => ['count' => null, 'percent' => null],
                'reinsurersDeclined' => ['count' => null, 'percent' => null],
                'pendingConfirmation' => ['count' => null, 'percent' => null],
                'total' => null
            ],
            [
                'date' => '29.02.2025',
                'month' => 'Feb',
                'wonRisks' => ['count' => null, 'percent' => null],
                'lostAccounts' => ['count' => null, 'percent' => null],
                'notQuoted' => ['count' => null, 'percent' => null],
                'reinsurersDeclined' => ['count' => null, 'percent' => null],
                'pendingConfirmation' => ['count' => null, 'percent' => null],
                'total' => null
            ],
            [
                'date' => '31.03.2025',
                'month' => 'Mar',
                'wonRisks' => ['count' => 9, 'percent' => 14.5],
                'lostAccounts' => ['count' => 15, 'percent' => 24.2],
                'notQuoted' => ['count' => 14, 'percent' => 22.6],
                'reinsurersDeclined' => ['count' => 6, 'percent' => 9.7],
                'pendingConfirmation' => ['count' => 18, 'percent' => 29.0],
                'total' => 62
            ],
            [
                'date' => '30.04.2025',
                'month' => 'Apr',
                'wonRisks' => ['count' => null, 'percent' => null],
                'lostAccounts' => ['count' => null, 'percent' => null],
                'notQuoted' => ['count' => null, 'percent' => null],
                'reinsurersDeclined' => ['count' => null, 'percent' => null],
                'pendingConfirmation' => ['count' => null, 'percent' => null],
                'total' => null
            ],
            [
                'date' => '31.05.2025',
                'month' => 'May',
                'wonRisks' => ['count' => null, 'percent' => null],
                'lostAccounts' => ['count' => null, 'percent' => null],
                'notQuoted' => ['count' => null, 'percent' => null],
                'reinsurersDeclined' => ['count' => null, 'percent' => null],
                'pendingConfirmation' => ['count' => null, 'percent' => null],
                'total' => null
            ],
            [
                'date' => '30.06.2025',
                'month' => 'Jun',
                'wonRisks' => ['count' => 19, 'percent' => 14.1],
                'lostAccounts' => ['count' => 52, 'percent' => 38.5],
                'notQuoted' => ['count' => 24, 'percent' => 17.8],
                'reinsurersDeclined' => ['count' => 17, 'percent' => 12.6],
                'pendingConfirmation' => ['count' => 23, 'percent' => 17.0],
                'total' => 135
            ]
        ];

        // All months in 2025
        $allMonths2024 = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];

        // Calculate KPIs
        $latestTotalQuotes = $data2024[5]['total']; // June 2025
        $marchTotalQuotes = $data2024[2]['total']; // March 2025
        $latestTotalQuotesChange = number_format((($latestTotalQuotes - $marchTotalQuotes) / $marchTotalQuotes) * 100, 1);

        $latestWinRate = $data2024[5]['wonRisks']['percent'];
        $marchWinRate = $data2024[2]['wonRisks']['percent'];
        $latestWinRateChange = number_format($latestWinRate - $marchWinRate, 1);

        $latestLostRate = $data2024[5]['lostAccounts']['percent'];
        $marchLostRate = $data2024[2]['lostAccounts']['percent'];
        $latestLostRateChange = number_format($latestLostRate - $marchLostRate, 1);

        // Calculate average monthly quotes (for months with data only)
        $totalQuotes = 0;
        $monthsWithData = 0;
        foreach ($data2024 as $month) {
            if ($month['total'] !== null) {
                $totalQuotes += $month['total'];
                $monthsWithData++;
            }
        }
        $averageMonthlyQuotes = $monthsWithData > 0 ? number_format($totalQuotes / $monthsWithData, 1) : 0;

        return view('analytics.business_development', compact(
            'data2024',
            'allMonths2024',
            'latestTotalQuotes',
            'latestTotalQuotesChange',
            'latestWinRate',
            'latestWinRateChange',
            'latestLostRate',
            'latestLostRateChange',
            'averageMonthlyQuotes'
        ));
    }
}
