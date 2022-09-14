<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'order_column',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function balanceReportAccounts()
    {
        return $this->hasMany(BalanceReportAccount::class);
    }

    public function getBalanceAttribute()
    {
        $report = BalanceReport::orderBy('report_date', 'desc')->latest()->first();

        return optional(optional(optional($report)->accounts)->firstWhere('account_id', $this->id))->balance ?? 0;
    }

    public function getLastUpdatedAttribute()
    {
        return optional(BalanceReport::latest('report_date')->first())->report_date ?? '';
    }
}
