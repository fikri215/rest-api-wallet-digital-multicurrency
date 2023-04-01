<?php

namespace App\Http\Controllers\API;

use App\CurrencyExchangeRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrencyExchangeRateController extends Controller
{
    public function setExchangeRate(Request $request)
    {
        try{
            $exchangeRate = new CurrencyExchangeRate();
            $exchangeRate->currency_id = $request->currency_id;
            $exchangeRate->exchange_rate_amount = $request->exchange_rate_amount;
            $exchangeRate->save();

            $currency['name'] = $exchangeRate->currency->name;
            $currency['exchange_rate_amount'] = $exchangeRate->exchange_rate_amount;

            return response()->json([
                'success' => true,
                'data' => $currency
                    ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function updateExchangeRate(Request $request)
    {
        try {
            $exchangeRate = CurrencyExchangeRate::where('currency_id', $request->currency_id)->first();
            // dd($exchangeRate);
            if (!$exchangeRate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Exchange Rate not found'
                ], 400);
            }
        
            $exchangeRate->currency_id = $request->currency_id;
            $exchangeRate->exchange_rate_amount = $request->exchange_rate_amount;
            $exchangeRate->save();

            return response()->json([
                'success' => true,
                'data' => $exchangeRate
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
