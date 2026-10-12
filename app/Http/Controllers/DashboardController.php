<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Court;
use App\Models\User;
use App\Models\Equipment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Existing System Stats
        $totalBookings = Booking::count();
        $totalCourts = Court::count();
        $availableCourts = Court::where('status', 'available')->count();
        $activeUsers = User::where('role', 'athlete')->count();

        // 2. NEW: Pending Approvals Alert Data
        $pendingApprovals = Booking::where('status', 'pending')->count();
        $pendingBookings = Booking::with(['user', 'court'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->get();

        // 4. Revenue Timeframes (Only counting 'confirmed' paid bookings)
        $dailyRevenue = Booking::where('status', 'confirmed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_price');

        $weeklyRevenue = Booking::where('status', 'confirmed')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('total_price');

        $monthlyRevenue = Booking::where('status', 'confirmed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');

        $totalRevenue = Booking::where('status', 'confirmed')->sum('total_price');

        // 5. Revenue Breakdown by SPORT
        $revenueByFacility = Court::leftJoin('bookings', function ($join) {
                $join->on('courts.id', '=', 'bookings.court_id')
                     ->where('bookings.status', '=', 'confirmed');
            })
            ->selectRaw('
                courts.sport_type as name,
                COUNT(bookings.id) as booking_count,
                COUNT(DISTINCT bookings.user_id) as unique_athletes,
                COALESCE(SUM(bookings.total_price), 0) as total_sales
            ')
            ->groupBy('courts.sport_type')
            ->orderByDesc('total_sales')
            ->get();

        // 6. NEW: Equipment Rental Breakdown for Dashboard
        $equipmentStats = DB::table('equipment')
            ->leftJoin('booking_equipment', 'equipment.id', '=', 'booking_equipment.equipment_id')
            ->leftJoin('bookings', 'booking_equipment.booking_id', '=', 'bookings.id')
            ->selectRaw('
                equipment.name,
                COALESCE(SUM(CASE WHEN bookings.status = "confirmed" THEN booking_equipment.quantity ELSE 0 END), 0) as total_rented,
                COALESCE(SUM(CASE WHEN bookings.status = "confirmed" THEN booking_equipment.quantity * booking_equipment.price ELSE 0 END), 0) as total_revenue
            ')
            ->groupBy('equipment.id', 'equipment.name')
            ->orderByDesc('total_revenue')
            ->get();

        // 7. Recent Reservations for the table
        $recentBookings = Booking::with(['user', 'court'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBookings', 'availableCourts', 'totalCourts', 'activeUsers',
            'pendingApprovals', 'pendingBookings',
            'dailyRevenue', 'weeklyRevenue', 'monthlyRevenue', 'totalRevenue',
            'revenueByFacility', 'equipmentStats', 'recentBookings'
        ));
    }

    public function report()
    {
        // 1. Revenue Timeframes
        $dailyRevenue = Booking::where('status', 'confirmed')->whereDate('created_at', Carbon::today())->sum('total_price');
        $weeklyRevenue = Booking::where('status', 'confirmed')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_price');
        $monthlyRevenue = Booking::where('status', 'confirmed')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('total_price');
        $totalRevenue = Booking::where('status', 'confirmed')->sum('total_price');

        // 2. Smart Analytics & Insights
        $totalConfirmedBookings = Booking::where('status', 'confirmed')->count();
        $uniqueAthletes = Booking::where('status', 'confirmed')->distinct('user_id')->count('user_id');

        // 3. Complete Sport/Activity Breakdown
        $revenueByFacility = Court::leftJoin('bookings', function ($join) {
                $join->on('courts.id', '=', 'bookings.court_id')
                     ->where('bookings.status', '=', 'confirmed');
            })
            ->selectRaw('
                courts.sport_type as name,
                COUNT(bookings.id) as booking_count,
                COUNT(DISTINCT bookings.user_id) as unique_athletes,
                COALESCE(SUM(bookings.total_price), 0) as total_sales
            ')
            ->groupBy('courts.sport_type')
            ->orderByDesc('total_sales')
            ->get();

        // 4. Identify the Top Performing Sport
        $topCourt = $revenueByFacility->first();

        // 5. Equipment Rental Breakdown
        $equipmentStats = DB::table('equipment')
            ->leftJoin('booking_equipment', 'equipment.id', '=', 'booking_equipment.equipment_id')
            ->leftJoin('bookings', 'booking_equipment.booking_id', '=', 'bookings.id')
            ->selectRaw('
                equipment.name,
                COALESCE(SUM(CASE WHEN bookings.status = "confirmed" THEN booking_equipment.quantity ELSE 0 END), 0) as total_rented,
                COALESCE(SUM(CASE WHEN bookings.status = "confirmed" THEN booking_equipment.quantity * booking_equipment.price ELSE 0 END), 0) as total_revenue
            ')
            ->groupBy('equipment.id', 'equipment.name')
            ->orderByDesc('total_revenue')
            ->get();

        return view('dashboard.report', compact(
            'dailyRevenue', 'weeklyRevenue', 'monthlyRevenue', 'totalRevenue',
            'totalConfirmedBookings', 'uniqueAthletes', 'revenueByFacility', 'topCourt', 'equipmentStats'
        ));
    }
}
