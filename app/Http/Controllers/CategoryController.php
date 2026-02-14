<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    // نمایش لیست دسته‌بندی‌ها
    public function index()
    {
        return Inertia::render('Categories/Index', [
            'categories' => auth()->user()->categories()->latest()->get()
        ]);
    }

    // ذخیره دسته‌بندی جدید
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense', // فقط ورودی یا خروجی
        ]);

        auth()->user()->categories()->create($validated);

        return redirect()->back()->with('message', 'دسته‌بندی با موفقیت ساخته شد.');
    }

    // حذف دسته‌بندی
    public function destroy(Category $category)
    {
        if ($category->user_id !== auth()->id()) abort(403);

        $category->delete();
        return redirect()->back();
    }
}
