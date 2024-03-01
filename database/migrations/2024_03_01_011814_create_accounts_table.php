<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('account_number')->unique();
            $table->decimal('debit', 8, 2)->default(0);
            $table->decimal('credit', 8, 2)->default(0);
            $table->decimal('balance', 8, 2)->default(0);
            $table->string('status')->default('active');
            $table->string('type')->default('savings');
            $table->string('currency')->default('USD');
            $table->string('country')->default('USA');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
