<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceReport extends Model
{
    protected $fillable = [
        'report_date',
        'description',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    protected $appends = [
        'account_list',
    ];

    public function accounts()
    {
        return $this->hasMany(BalanceReportAccount::class, 'balance_report_id');
    }

    public function previousReport()
    {
        return BalanceReport::latest('report_date')
                            ->where('id', '!=', $this->id)
                            ->whereDate('report_date', '<=', $this->report_date)
                            ->first();
    }

    public function getBalanceAttribute()
    {
        return $this->accounts->sum('balance');
    }

    public function getPreviousBalanceAttribute()
    {
        $previousReport = $this->previousReport();

        if (! $previousReport) {
            return 0;
        }

        return $previousReport->balance;
    }

    public function getDeltaBalanceAttribute()
    {
        return $this->balance - $this->previous_balance;
    }

    public function getAccountListAttribute()
    {
        $previousReport = $this->previousReport();

        return BalanceReportAccount::query()
                                   ->where('balance_report_id', $this->id)
                                   ->get()
                                   ->map(function ($accountReport) use ($previousReport) {
                                       if (! $accountReport->account->is_active) {
                                           return false;
                                       }

                                       $previousBalance = optional(
                                               optional(
                                                   optional($previousReport)->accounts
                                               )->firstWhere('account_id', $accountReport->account_id)
                                           )->balance ?? 0;

                                       return [
                                           'account_id'       => $accountReport->account_id,
                                           'previous_balance' => $previousBalance,
                                           'balance'          => optional(
                                                   $this->accounts->firstWhere('account_id', $accountReport->account_id)
                                               )->balance ?? $previousBalance,
                                       ];
                                   })
                                   ->reject(function ($row) {
                                       return $row === false;
                                   });
    }
}
