<?php

namespace App\Filament\Resources\BalanceReportResource\Pages;

use App\Filament\Resources\BalanceReportResource;
use App\Models\Account;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBalanceReport extends CreateRecord
{
    protected static string $resource = BalanceReportResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $balanceReport = parent::handleRecordCreation($data);

        $accounts = Account::orderBy('order_column')->get();
        $balanceReport->accounts()->createMany($accounts->map(function ($account) {
            return [
                'account_id' => $account->id,
                'balance'    => 0,
            ];
        }));

        return $balanceReport;
    }
}
