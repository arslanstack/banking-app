<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Transactions Table:
    // id (Primary Key)
    // account_id (Foreign Key referencing Accounts Table)
    // txn_date
    // txn_type (e.g., 'deposit', 'withdrawal', 'transfer')
    // amount
    // sender_account_id (Foreign Key referencing Accounts Table)
    // receiver_account_id (Foreign Key referencing Accounts Table)
    // post_balance
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts');
            $table->dateTime('txn_date');
            $table->string('tid')->unique();
            $table->tinyInteger('txn_type')->default(0)->comment('0: deposit, 1: withdrawal, 2: transfer');
            $table->decimal('amount', 8, 2);
            $table->foreignId('sender_account_id')->constrained('accounts');
            $table->foreignId('receiver_account_id')->constrained('accounts');
            $table->decimal('post_balance', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
