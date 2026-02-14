<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'category_id', 'account_id', 'amount', 'description', 'date'];

// کست کردن تاریخ برای کار راحت‌تر در لاراول ۱۲
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function account() {
        return $this->belongsTo(Account::class);
    }
}
