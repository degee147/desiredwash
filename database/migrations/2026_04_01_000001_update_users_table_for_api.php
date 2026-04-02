<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->decimal('wallet_balance', 12, 2)->default(0)->after('phone');
            $table->string('auth_provider')->default('email')->after('wallet_balance'); // email | google | apple
            $table->string('avatar_url')->nullable()->after('auth_provider');
            $table->string('zone_id')->nullable()->after('avatar_url');
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'wallet_balance', 'auth_provider', 'avatar_url', 'zone_id']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
