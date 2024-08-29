<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;


class WaitingTransactions extends BaseWidget
{
    protected static ?int $sort = 3;
    public function table(Table $table): Table
    {

        return

            $table->query(
                Transaction::query()->whereStatus('waiting')
            )


            ->columns([

                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->weight(FontWeight::Bold)
                    ->extraAttributes(['class' => 'w-[2]'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('listing.title')
                    ->numeric()
                    ->extraAttributes(['class' => 'w-[2]'])
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
                }),

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

        ;
    }
}
