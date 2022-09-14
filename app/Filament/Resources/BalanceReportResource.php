<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BalanceReportResource\Pages;
use App\Filament\Resources\BalanceReportResource\RelationManagers;
use App\Models\Account;
use App\Models\BalanceReport;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BalanceReportResource extends Resource
{
    protected static ?string $model = BalanceReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        DatePicker::make('report_date')
                                  ->format('Y-m-d'),
                    ]),
            ])
            ->columns(1);
    }

    public static function editForm(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        DatePicker::make('report_date')
                                  ->format('Y-m-d'),
                        Grid::make()
                            ->schema([
                                RichEditor::make('description'),
                            ])->columns(1),
                    ]),
                Repeater::make('account_list')
                        ->schema([
                            Select::make('account_id')
                                  ->options(
                                      Account::orderBy('order_column')->get()->pluck('name', 'id')
                                  )
                                  ->disabled(),
                            TextInput::make('previous_balance')
                                     ->prefix('Rp')
                                     ->numeric()
                                     ->mask(fn(TextInput\Mask $mask) => $mask
                                         ->numeric()
                                         ->decimalPlaces(0) // Set the number of digits after the decimal point.
                                         ->decimalSeparator(',') // Add a separator for decimal numbers.
                                         ->integer() // Disallow decimal numbers.
                                         ->mapToDecimalSeparator([',']) // Map additional characters to the decimal separator.
                                         ->normalizeZeros() // Append or remove zeros at the end of the number.
                                         ->padFractionalZeros() // Pad zeros at the end of the number to always maintain the maximum number of decimal places.
                                         ->thousandsSeparator('.') // Add a separator for thousands.
                                     )
                                     ->disabled(),
                            TextInput::make('balance')
                                     ->numeric()
                                     ->label('Current Balance')
                                     ->prefix('Rp')
                                     ->mask(fn(TextInput\Mask $mask) => $mask
                                         ->numeric()
                                         ->decimalPlaces(0) // Set the number of digits after the decimal point.
                                         ->decimalSeparator(',') // Add a separator for decimal numbers.
                                         ->integer() // Disallow decimal numbers.
                                         ->thousandsSeparator('.') // Add a separator for thousands.
                                     )
                                     ->default(0),
                        ])
                        ->columns(3)
                        ->disableItemCreation()
                        ->disableItemDeletion()
                        ->disableItemMovement(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('report_date')
                                         ->date('Y-m-d')
                                         ->sortable(),
                Tables\Columns\TextColumn::make('previous_balance')
                                         ->prefix('Rp')
                                         ->formatStateUsing(function ($state) {
                                             return number_format($state, 0, ',', '.');
                                         })
                                         ->toggleable(),
                Tables\Columns\TextColumn::make('delta_balance')
                                         ->label('Delta')
                                         ->prefix('Rp')
                                         ->formatStateUsing(function ($state) {
                                             return number_format($state, 0, ',', '.');
                                         })
                                         ->toggleable(),
                Tables\Columns\TextColumn::make('balance')
                                         ->label('Current Balance')
                                         ->prefix('Rp')
                                         ->formatStateUsing(function ($state) {
                                             return number_format($state, 0, ',', '.');
                                         }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('report_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBalanceReports::route('/'),
            'create' => Pages\CreateBalanceReport::route('/create'),
            'edit'   => Pages\EditBalanceReport::route('/{record}/edit'),
        ];
    }
}
