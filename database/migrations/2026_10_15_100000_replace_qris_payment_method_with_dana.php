<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('payment_method', 'QRIS')
            ->update(['payment_method' => 'DANA']);
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('payment_method', 'DANA')
            ->update(['payment_method' => 'QRIS']);
    }
};
