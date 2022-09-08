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
    ];

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
