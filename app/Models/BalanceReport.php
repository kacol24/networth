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

    protected function previousReport()
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

        return Account::orderBy('order_column')->get()->map(function ($account) use ($previousReport) {
            return [
                'account_id'       => $account->id,
                'previous_balance' => optional(
                    $previousReport->accounts->firstWhere('account_id', $account->id)
                )->balance,
                'balance'          => optional($this->accounts->firstWhere('account_id', $account->id))->balance,
            ];
        });
    }
}
