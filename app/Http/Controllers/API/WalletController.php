<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Wallet;
use App\Currency;

use Illuminate\Http\Request;
use App\CurrencyExchangeRate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getWallet()
    {
        try {
            $wallet = Wallet::where('user_id', Auth::user()->id)->first();

            $wallets['balance'] = $wallet->balance;
            $wallets['currency'] = $wallet->currency->name;

            return response()->json([
                'success' => true,
                'data' => $wallets
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function changeCurrency(Request $request)
    {
        try {
            $wallet = Wallet::where('user_id', Auth::user()->id)->first();
            $currentExchangeRate = CurrencyExchangeRate::where('currency_id', $request->currency_id)->first();
            $exchangeRate = $currentExchangeRate->exchange_rate_amount;
            
            //konversi kurs
            if ($wallet->currency->id == $request->currency_id) {
                $balance = $wallet->balance;
            } else {
                $balance = $wallet->balance / $exchangeRate;
            }
            
            $wallet->balance = $balance;
            $wallet->currency_id = $request->currency_id;
            $wallet->save();

            $wallets['balance'] = $wallet->balance;
            $wallets['currency'] = $wallet->currency->name;

            return response()->json([
                'success' => true,
                'data' => $wallets
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }
}
