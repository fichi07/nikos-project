<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    public static function canCreate(): bool
    {
        return false;
    }

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('listing.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_days')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('USD')
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $state): string => match ($state) {
                    'waiting' => 'gray',
                    'approved' => 'info',
                    'canceled' => 'danger'
                }),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'waiting' => 'waiting',
                    'approved' => 'approved',
                    'canceled' => 'canceled',
                ])
            ])
            ->actions([
                Action::make('approved')
                    ->button()
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Transaction $transaction) {
                        Transaction::find($transaction->id)->update(
                            ['status' => 'approved']
                        );
                        Notification::make()->success()->title('Transaction Approved')->body('Transaction has been Approved successfully')->icon('heroicon-o-check')->send();
                    })
                    ->hidden(fn(Transaction $transaction) => $transaction->status !== 'waiting')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
