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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Standard, Enterprise, Premium
            $table->string('subtitle'); // e.g., "50 Clothes Per Month"
            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable(); // For showing discounts
            $table->json('items'); // Array of package items
            $table->string('icon_class')->nullable(); // CSS icon class
            $table->integer('sort_order')->default(0); // For ordering packages
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
