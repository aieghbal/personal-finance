<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Morilog\Jalali\Jalalian;

class TransactionController extends Controller
{
    // نمایش لیست تراکنش‌ها
    public function index()
    {
        $transactions = auth()->user()->transactions()
            ->with(['account', 'category'])
            ->latest()
            ->get()
            ->map(function ($tr) {
                // اضافه کردن تاریخ شمسی به خروجی
                $tr->date_jalali = Jalalian::fromDateTime($tr->date)->format('Y/m/d');
                return $tr;
            });

        return Inertia::render('Transactions/Index', [
            'transactions' => $transactions,
            'accounts' => auth()->user()->accounts,
            'categories' => auth()->user()->categories,
        ]);
    }

    // ذخیره تراکنش جدید
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id'  => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $category = Category::findOrFail($validated['category_id']);
            $account = Account::findOrFail($validated['account_id']);

            // ثبت تراکنش
            auth()->user()->transactions()->create($validated);

            // به‌روزرسانی موجودی بر اساس نوع دسته‌بندی
            if ($category->type === 'income') {
                $account->increment('balance', $validated['amount']);
            } else {
                $account->decrement('balance', $validated['amount']);
            }
        });

        return back()->with('message', 'تراکنش با موفقیت ثبت شد');
    }

    // بروزرسانی تراکنش
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'account_id'  => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $transaction) {
            // ۱. خنثی کردن اثر تراکنش قبلی
            $oldAccount = Account::find($transaction->account_id);
            $oldCategory = Category::find($transaction->category_id);

            if ($oldCategory->type === 'income') {
                $oldAccount->decrement('balance', $transaction->amount);
            } else {
                $oldAccount->increment('balance', $transaction->amount);
            }

            // ۲. اعمال اثر تراکنش جدید
            $newAccount = Account::find($validated['account_id']);
            $newCategory = Category::find($validated['category_id']);

            if ($newCategory->type === 'income') {
                $newAccount->increment('balance', $validated['amount']);
            } else {
                $newAccount->decrement('balance', $validated['amount']);
            }

            // ۳. آپدیت دیتا
            $transaction->update($validated);
        });

        return back()->with('message', 'تراکنش بروزرسانی شد');
    }

    // حذف تراکنش
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) abort(403);

        DB::transaction(function () use ($transaction) {
            $account = $transaction->account;
            $category = $transaction->category;

            if ($category->type === 'income') {
                $account->decrement('balance', $transaction->amount);
            } else {
                $account->increment('balance', $transaction->amount);
            }

            $transaction->delete();
        });

        return back();
    }
}
