<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceReportAccount extends Model
{
    protected $fillable = [
        'balance_report_id',
        'account_id',
        'balance',
    ];

    public function balanceReport()
    {
        return $this->belongsTo(BalanceReport::class, 'balance_report_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id')->withTrashed();
    }
}
