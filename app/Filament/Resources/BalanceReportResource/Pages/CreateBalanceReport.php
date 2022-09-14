<?php

namespace App\Filament\Resources\BalanceReportResource\Pages;

use App\Filament\Resources\BalanceReportResource;
use App\Models\Account;
use App\Models\BalanceReport;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBalanceReport extends CreateRecord
{
    protected static string $resource = BalanceReportResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        \DB::beginTransaction();
        $balanceReport = parent::handleRecordCreation($data);

        $accounts = Account::orderBy('order_column')->get();
        $balanceReport->accounts()->createMany(
            $accounts->map(function ($account) use ($data, $balanceReport) {
                $create = [
                    'account_id' => $account->id,
                ];

                if (! $account->is_active) {
                    $previousReport = $balanceReport->previousReport();
                    $previousUpdate = optional(
                        optional(
                            optional($previousReport)->accounts
                        )->firstWhere('account_id', $account->id)
                    )->updated_at;
                    $create['updated_at'] = $previousUpdate;
                }

                return $create;
            })
        );
        \DB::commit();

        return $balanceReport;
    }
}
