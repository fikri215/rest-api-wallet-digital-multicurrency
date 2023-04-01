<?php

namespace App\Http\Controllers\API;

use App\Currency;
use App\CurrencyExchangeRate;
use App\User;
use App\Wallet;

use App\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WalletTransactionController extends Controller
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
    public function topup(Request $request)
    {
        try {
            $user = User::where('id', Auth::user()->id)->with('wallet')->first();
            $currency = Currency::where('id', $request->currency_id)->first();
            $currencyUser = $user->wallet->currency;
            $currentExchangeRate = CurrencyExchangeRate::where('currency_id', $currency->id)->first();
            $exchangeRate = $currentExchangeRate->exchange_rate_amount;

            // cek kondisi apakah currency waller user sama dengan currency topup
            if ($currencyUser->id == $currency->id) {
                $currentAmount = $request->amount;
            } else {
                $currentAmount = $request->amount * $exchangeRate;
            }

            DB::beginTransaction();
            $transaction = new WalletTransaction();
            $transaction->wallet_id = $user->wallet->id;
            $transaction->sender_id = Auth::user()->id;
            $transaction->reciever_id = Auth::user()->id;
            $transaction->amount = $request->amount;
            $transaction->transaction_type = 'topup';
            $transaction->currency_id = $request->currency_id;
            $transaction->payment_method = $request->payment_method;
            $transaction->current_exchange_rate = $exchangeRate;
            $transaction->save();

            $wallet = Wallet::find($transaction->wallet_id);
            $wallet->balance += $currentAmount;
            $wallet->save();

            DB::commit();
            $transactions['amount'] = $request->amount;
            $transactions['balance'] = $wallet->balance;
            

            return response()->json([
                'success' => true,
                'data' => $transactions
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    public function transfer(Request $request)
    {
        try {
            $sender = User::where('id', Auth::user()->id)->with('wallet')->first(); // mengambil data sender
            $reciever = User::where('id', $request->reciever_id)->first(); // mengambil data reciever
            $currency = Currency::where('id', $request->currency_id)->first(); // data kurs yang digunakan
            $currencySender = $sender->wallet->currency; //kurs sender
            $currencyReciever = $reciever->wallet->currency; //kurs reciever
            $currentExchangeRate = CurrencyExchangeRate::where('currency_id', $currency->id)->first(); // mengambil data exchange rate berdasarkan kurs yg direquest
            $exchangeRate = $currentExchangeRate->exchange_rate_amount;
            
            // cek kondisi apakah currency transfer sama dengan currency wallet sender lalu dikonversikan
            if ($currencySender->id == $currency->id) {
                $currentAmount = $request->amount;
            } else {
                $currentAmount = $request->amount * $exchangeRate;
            }
            // dd($currentAmount);
            if ($sender->wallet->balance >= $currentAmount) {
                DB::beginTransaction();
                
                //mengurangi balance wallet sender
                $wallet = Wallet::find($sender->wallet->id);
                $wallet->balance -= $currentAmount;
                $wallet->save();
    
                //membuat transaksi baru
                $transaction = new WalletTransaction();
                $transaction->wallet_id = $sender->wallet->id;
                $transaction->amount = $request->amount;
                $transaction->transaction_type = 'transfer';
                $transaction->sender_id = Auth::user()->id;
                $transaction->reciever_id = $request->reciever_id;
                $transaction->currency_id = $request->currency_id;
                $transaction->payment_method = 'wallet';
                $transaction->current_exchange_rate = $exchangeRate;
                $transaction->save();
 
                //cek apakah kurs transaksi sama dengan kurs wallet reciever lalu konversi
                $senderCurrency = Currency::where('id', $sender->wallet->currency->id)->first(); // data kurs yang digunakan
                if ($currencyReciever->id == $senderCurrency->id) {
                    $newAmount = $currentAmount;
                } else {
                    $newAmount = $currentAmount * $exchangeRate;
                }

                //menambahkan balance ke wallet reciever
                $recieverBalance = Wallet::find($reciever->wallet->id);
                $recieverBalance->balance += $newAmount;
                $recieverBalance->save();
    
                DB::commit();
                $transactions['amount'] = $request->amount;
                $transactions['balance'] = $wallet->balance;
    
                return response()->json([
                    'success' => true,
                    'data' => $transactions
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Your Balance is insufficient'
                ], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }

    }

}
