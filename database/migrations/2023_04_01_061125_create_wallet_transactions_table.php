<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('amount');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('reciever_id');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('reciever_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('transaction_type', ['topup', 'transfer', 'received']);
            $table->enum('payment_method', ['qris', 'wallet', 'virtual account']);
            $table->integer('current_exchange_rate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
}
