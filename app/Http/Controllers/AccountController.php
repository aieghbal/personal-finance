<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    // نمایش لیست حساب‌ها
    public function index()
    {
        return Inertia::render('Accounts/Index', [
            'accounts' => auth()->user()->accounts()->latest()->get()
        ]);
    }

    // ذخیره حساب جدید
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
        ]);

        auth()->user()->accounts()->create($validated);

        return redirect()->back()->with('message', 'حساب با موفقیت ایجاد شد.');
    }

    // حذف حساب
    public function destroy(Account $account)
    {
        // چک کن که حساب متعلق به خود کاربر باشد
        if ($account->user_id !== auth()->id()) abort(403);

        $account->delete();
        return redirect()->back();
    }
}
