<?php

namespace App\Filament\Widgets;

use App\Models\Listing;
use App\Models\Transaction;
use Carbon\Carbon;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;


class StatsOverview extends BaseWidget
{
    private function getPrecentage(int $form, int $to)
    {
        return $to - $form / ($to + $form / 2) * 100;
    }
    protected function getStats(): array
    {
        $newListing = Listing::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->count();
        $transaction = Transaction::whereStatus('approved')->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        $prevTransaction = Transaction::whereStatus('approved')->whereMonth('created_at', Carbon::now()->subMonth()->month)->whereYear('created_at', Carbon::now()->subMonth()->year);
        $transactionPrecentage = $this->getPrecentage($prevTransaction->count(), $transaction->count());
        $revenuePrecentage = $this->getPrecentage($prevTransaction->sum('total_price'), $transaction->sum('total_price'));
        return [
            Stat::make('New Listing of the Month', $newListing),
            Stat::make('Transaction of the Month', $transaction->count())
                ->description(
                    $transactionPrecentage > 0 ? "{$transactionPrecentage}% increased" : "{$transactionPrecentage}% decreased"
                )
                ->color($transactionPrecentage > 0 ? 'success' : 'danger'),
            Stat::make('Revenue of the Month', Number::currency($transaction->sum('total_price'), 'USD'))
                ->description(
                    $revenuePrecentage > 0 ? "{$revenuePrecentage}% increased" : "{$revenuePrecentage}% decreased"
                )
                ->color($revenuePrecentage > 0 ? 'success' : 'danger'),
        ];
    }
}
