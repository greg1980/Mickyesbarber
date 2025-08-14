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
        Schema::table('business_hours', function (Blueprint $table) {
            // Change open_time and close_time from timestamp to time
            $table->time('open_time')->nullable()->change();
            $table->time('close_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_hours', function (Blueprint $table) {
            // Revert back to timestamp if needed
            $table->timestamp('open_time')->nullable()->change();
            $table->timestamp('close_time')->nullable()->change();
        });
    }
};
