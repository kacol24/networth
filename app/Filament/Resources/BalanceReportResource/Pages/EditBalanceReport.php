<?php

namespace App\Filament\Resources\BalanceReportResource\Pages;

use App\Filament\Resources\BalanceReportResource;
use Filament\Pages\Actions;
use Filament\Resources\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBalanceReport extends EditRecord
{
    protected static string $resource = BalanceReportResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function form(Form $form): Form
    {
        return static::getResource()::editForm($form);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $balanceReport = parent::handleRecordUpdate($record, $data);

        foreach ($data['account_list'] as $account) {
            $balanceReport->accounts()->updateOrCreate([
                'account_id' => $account['account_id'],
            ], [
                'balance' => $account['balance'],
            ]);
        }

        return $balanceReport;
    }
}
