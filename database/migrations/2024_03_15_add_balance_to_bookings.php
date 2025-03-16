<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('balance_amount', 8, 2)->default(0)->after('deposit_amount');
        });

        // Update existing bookings to set balance_amount
        DB::statement('UPDATE bookings SET balance_amount = service_price - deposit_amount WHERE service_price > deposit_amount');
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('balance_amount');
        });
    }
};
