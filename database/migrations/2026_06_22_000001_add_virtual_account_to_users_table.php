<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('va_bank_name')->nullable()->after('wallet_balance');
            $table->string('va_account_number')->nullable()->after('va_bank_name');
            $table->string('va_account_name')->nullable()->after('va_account_number');
            $table->string('va_flw_ref')->nullable()->after('va_account_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['va_bank_name', 'va_account_number', 'va_account_name', 'va_flw_ref']);
        });
    }
};
