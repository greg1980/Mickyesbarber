<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminFinanceController extends Controller
{
    public function index()
    {
        // Get current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Calculate key metrics
        $totalRevenue = $this->getTotalRevenue();
        $monthlyRevenue = $this->getMonthlyRevenue($currentMonth, $currentYear);
        $dailyRevenue = $this->getDailyRevenue();
        $revenueGrowth = $this->getRevenueGrowth();

        // Get chart data
        $monthlyRevenueChart = $this->getMonthlyRevenueChart();
        $dailyRevenueChart = $this->getDailyRevenueChart();

        return Inertia::render('Admin/Finances', [
            'stats' => [
                'totalRevenue' => $totalRevenue,
                'monthlyRevenue' => $monthlyRevenue,
                'dailyRevenue' => $dailyRevenue,
                'revenueGrowth' => $revenueGrowth,
            ],
            'charts' => [
                'monthlyRevenue' => $monthlyRevenueChart,
                'dailyRevenue' => $dailyRevenueChart,
            ]
        ]);
    }

    private function getTotalRevenue()
    {
        return Booking::where('payment_status', 'paid')
            ->sum('amount_paid');
    }

    private function getMonthlyRevenue($month, $year)
    {
        return Booking::where('payment_status', 'paid')
            ->whereMonth('booking_date', $month)
            ->whereYear('booking_date', $year)
            ->sum('amount_paid');
    }

    private function getDailyRevenue()
    {
        return Booking::where('payment_status', 'paid')
            ->whereDate('booking_date', Carbon::today())
            ->sum('amount_paid');
    }

    private function getRevenueGrowth()
    {
        $currentMonth = Carbon::now();
        $previousMonth = Carbon::now()->subMonth();

        $currentMonthRevenue = Booking::where('payment_status', 'paid')
            ->whereYear('booking_date', $currentMonth->year)
            ->whereMonth('booking_date', $currentMonth->month)
            ->sum('amount_paid');

        $previousMonthRevenue = Booking::where('payment_status', 'paid')
            ->whereYear('booking_date', $previousMonth->year)
            ->whereMonth('booking_date', $previousMonth->month)
            ->sum('amount_paid');

        if ($previousMonthRevenue == 0) {
            return $currentMonthRevenue > 0 ? 100 : 0;
        }

        return round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 2);
    }

    private function getMonthlyRevenueChart()
    {
        $months = [];
        $revenues = [];

        // Get last 12 months of data
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');
            $revenue = Booking::where('payment_status', 'paid')
                ->whereYear('booking_date', $date->year)
                ->whereMonth('booking_date', $date->month)
                ->sum('amount_paid');

            $months[] = $month;
            $revenues[] = (float) $revenue;
        }

        return [
            'labels' => $months,
            'data' => $revenues
        ];
    }

    private function getDailyRevenueChart()
    {
        $days = [];
        $revenues = [];

        // Get last 30 days of data
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $day = $date->format('M j');
            $revenue = Booking::where('payment_status', 'paid')
                ->whereDate('booking_date', $date->toDateString())
                ->sum('amount_paid');

            $days[] = $day;
            $revenues[] = (float) $revenue;
        }

        return [
            'labels' => $days,
            'data' => $revenues
        ];
    }

    // API endpoints for AJAX requests
    public function getMonthlyRevenueData()
    {
        return response()->json($this->getMonthlyRevenueChart());
    }

    public function getDailyRevenueData()
    {
        return response()->json($this->getDailyRevenueChart());
    }

    public function getRevenueStats()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return response()->json([
            'totalRevenue' => $this->getTotalRevenue(),
            'monthlyRevenue' => $this->getMonthlyRevenue($currentMonth, $currentYear),
            'dailyRevenue' => $this->getDailyRevenue(),
            'revenueGrowth' => $this->getRevenueGrowth(),
        ]);
    }

    // PDF Export Methods
    public function exportFullReport()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $data = [
            'title' => 'Complete Financial Dashboard Report',
            'date' => Carbon::now()->format('F j, Y'),
            'stats' => [
                'totalRevenue' => $this->getTotalRevenue(),
                'monthlyRevenue' => $this->getMonthlyRevenue($currentMonth, $currentYear),
                'dailyRevenue' => $this->getDailyRevenue(),
                'revenueGrowth' => $this->getRevenueGrowth(),
            ],
            'charts' => [
                'monthlyRevenue' => $this->getMonthlyRevenueChart(),
                'dailyRevenue' => $this->getDailyRevenueChart(),
            ],
            'additionalStats' => $this->getAdditionalStats(),
        ];

                $html = view('pdfs.financial-report', $data)->render();

        $options = [
            'page-size' => 'A4',
            'margin-top' => 10,
            'margin-right' => 10,
            'margin-bottom' => 10,
            'margin-left' => 10,
        ];

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        return $pdf->download('financial-dashboard-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function exportRevenueReport()
    {
        $data = [
            'title' => 'Revenue Report',
            'date' => Carbon::now()->format('F j, Y'),
            'totalRevenue' => $this->getTotalRevenue(),
            'revenueBreakdown' => $this->getRevenueBreakdown(),
        ];

        $html = view('pdfs.revenue-report', $data)->render();
        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        return $pdf->download('revenue-report-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function exportMonthlyReport()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $data = [
            'title' => 'Monthly Revenue Report',
            'date' => Carbon::now()->format('F j, Y'),
            'monthlyRevenue' => $this->getMonthlyRevenue($currentMonth, $currentYear),
            'chartData' => $this->getMonthlyRevenueChart(),
        ];

        $html = view('pdfs.monthly-report', $data)->render();
        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        return $pdf->download('monthly-revenue-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    public function exportDailyReport()
    {
        $data = [
            'title' => 'Daily Revenue Report',
            'date' => Carbon::now()->format('F j, Y'),
            'dailyRevenue' => $this->getDailyRevenue(),
            'chartData' => $this->getDailyRevenueChart(),
        ];

        $html = view('pdfs.daily-report', $data)->render();
        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');

        return $pdf->download('daily-revenue-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    private function getAdditionalStats()
    {
        $totalBookings = Booking::count();
        $paidBookings = Booking::where('payment_status', 'paid')->count();
        $pendingBookings = Booking::where('payment_status', 'pending')->count();
        $failedBookings = Booking::where('payment_status', 'failed')->count();

        $averageTransaction = $paidBookings > 0 ? $this->getTotalRevenue() / $paidBookings : 0;

        return [
            'totalBookings' => $totalBookings,
            'paidBookings' => $paidBookings,
            'pendingBookings' => $pendingBookings,
            'failedBookings' => $failedBookings,
            'averageTransaction' => $averageTransaction,
        ];
    }

    private function getRevenueBreakdown()
    {
        return [
            'thisWeek' => Booking::where('payment_status', 'paid')
                ->whereBetween('booking_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->sum('amount_paid'),
            'lastMonth' => Booking::where('payment_status', 'paid')
                ->whereMonth('booking_date', Carbon::now()->subMonth()->month)
                ->whereYear('booking_date', Carbon::now()->subMonth()->year)
                ->sum('amount_paid'),
            'yearToDate' => Booking::where('payment_status', 'paid')
                ->whereYear('booking_date', Carbon::now()->year)
                ->sum('amount_paid'),
        ];
    }
}
