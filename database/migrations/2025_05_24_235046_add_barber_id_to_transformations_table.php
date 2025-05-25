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
        Schema::table('transformations', function (Blueprint $table) {
            $table->foreignId('barber_id')->nullable()->constrained('barbers')->onDelete('set null')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transformations', function (Blueprint $table) {
            $table->dropForeign(['barber_id']);
            $table->dropColumn('barber_id');
        });
    }
};
