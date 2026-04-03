<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tx_ref')->unique();   // your internal ref
            $table->string('flw_tx_id')->nullable()->index(); // Flutterwave's numeric ID
            $table->string('flw_ref')->nullable();
            $table->enum('type', ['order_payment', 'wallet_topup'])->default('order_payment');
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('NGN');
            $table->enum('status', ['pending', 'successful', 'failed', 'cancelled'])
                ->default('pending');
            $table->json('meta')->nullable();     // store full FLW response for auditing
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
