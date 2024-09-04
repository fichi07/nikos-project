<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Transaction\Store;
use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with('listing')->whereUserId(auth()->id())->paginate();
        return response()->json([
            'success' => true,
            'massage' => 'Get All My Transaction',
            'data' => $transactions
        ]);
    }

    private function _fullyBookChecker(Store $request)
    {
        $listing = Listing::find($request->listing_id);
        $runningTransactionCount = Transaction::whereListingId($listing->id)
            ->whereNot('status', 'canceled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [
                    $request->start_date,
                    $request->end_date
                ])->orWhereBetween('end_date', [
                    $request->start_date,
                    $request->end_date
                ])->orWhere(function ($subquery) use ($request) {
                    $subquery->where('start_date', '<', $request->start_date)
                        ->where('end_date', '>', $request->end_date);
                });
            })->count();
        if ($runningTransactionCount >= $listing->max_person) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'massage' => 'Listing is Fully Booked',

                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
        return true;
    }

    public function isAvailable(Store $request)
    {
        $this->_fullyBookChecker($request);

        return response()->json([
            'success' => true,
            'massage' => 'Listing is Ready to Book',
        ]);
    }

    public function Store(Store $request)
    {
        $this->_fullyBookChecker($request);

        $transaction = Transaction::create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'listing_id' => $request->listing_id,
            'user_id' => auth()->id()
        ]);

        $transaction->Listing;

        return response()->json([
            'success' => true,
            'massage' => 'Transaction Created',
            'data' => $transaction
        ]);
    }

    public function Show(Transaction $transaction): JsonResponse
    {

        if ($transaction->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'massage' => 'Unauthorized',

            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $transaction->Listing;
        return response()->json([
            'success' => true,
            'massage' => 'Get Detail Transaction',
            'data' => $transaction
        ]);
    }
}
