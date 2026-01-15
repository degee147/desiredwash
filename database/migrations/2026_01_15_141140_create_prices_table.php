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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('category')->nullable(); // clothing, bedding, accessories, etc.
            $table->string('service_type')->nullable(); // wash_press, dry_clean, etc.
            $table->decimal('regular_price', 10, 2);
            $table->decimal('express_price', 10, 2)->nullable();
            $table->string('icon_class')->nullable(); // CSS icon class
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
