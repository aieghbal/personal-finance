<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $stats = [
            'total_balance' => (float) $user->accounts()->sum('balance'),
            'monthly_expense' => (float) $user->transactions()
                ->whereHas('category', fn($q) => $q->where('type', 'expense'))
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->sum('amount'),
            'monthly_income' => (float) $user->transactions()
                ->whereHas('category', fn($q) => $q->where('type', 'income'))
                ->whereMonth('date', $currentMonth)
                ->whereYear('date', $currentYear)
                ->sum('amount'),
        ];

        // تغییر استراتژی نمودار: گروه‌بندی بر اساس نوع (Income/Expense)
        // اینطوری فقط دو تکه بزرگ (سبز و قرمز) خواهی داشت
        $chartData = $user->transactions()
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->with('category')
            ->get()
            ->groupBy('category.type')
            ->map(function ($items, $type) {
                return [
                    'label' => $type === 'income' ? 'کل درآمدها' : 'کل هزینه‌ها',
                    'value' => (float) $items->sum('amount'),
                    'type'  => $type,
                ];
            })->values();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
        ]);
    }
}
