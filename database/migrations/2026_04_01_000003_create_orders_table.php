<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('zone_id');
            $table->foreign('zone_id')->references('id')->on('zones');
            $table->string('zone_name');
            $table->text('address');
            $table->json('items'); // array of {service_id, service_name, quantity, unit_price}
            $table->date('scheduled_pickup_date');
            $table->string('scheduled_pickup_time');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('delivery_fee', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('payment_method'); // card | bank_transfer | wallet
            $table->string('payment_status')->default('pending'); // pending | success | failed
            $table->string('status')->default('pending'); // pending | confirmed | picked_up | washing | ready_for_delivery | delivered | cancelled
            $table->text('notes')->nullable();
            $table->string('payment_reference')->nullable(); // Flutterwave tx_ref
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
