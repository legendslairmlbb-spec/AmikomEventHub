<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Transaction::whereIn('status', ['settlement', 'success'])->sum('total_price');
        $ticketsSold = Transaction::whereIn('status', ['settlement', 'success'])->count();
        $activeEvents = Event::where('date', '>=', now())->count();
        $pendingOrders = Transaction::where('status', 'pending')->count();
        $recentTransactions = Transaction::with('event')->latest()->take(5)->get();

        // Data for charts (Last 7 days revenue)
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d M');
            
            $dailyRevenue = Transaction::whereIn('status', ['settlement', 'success'])
                ->whereDate('created_at', $date)
                ->sum('total_price');
            $chartData[] = $dailyRevenue;
        }

        return view('admin.dashboard', compact(
            'totalRevenue', 'ticketsSold', 'activeEvents', 
            'pendingOrders', 'recentTransactions', 'chartLabels', 'chartData'
        ));
    }
}
