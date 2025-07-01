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
}
