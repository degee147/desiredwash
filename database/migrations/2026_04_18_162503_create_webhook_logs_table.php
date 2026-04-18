<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('flutterwave');   // extensible for future providers
            $table->string('event')->nullable();                 // e.g. charge.completed
            $table->string('tx_ref')->nullable()->index();       // for quick dedup lookups
            $table->json('payload');                             // raw body saved immediately
            $table->string('status')->default('pending');        // pending | processing | processed | failed
            $table->text('error')->nullable();                   // last error message if failed
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
