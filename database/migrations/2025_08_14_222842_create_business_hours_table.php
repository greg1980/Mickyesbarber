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
        Schema::create('business_hours', function (Blueprint $table) {
            $table->id();
            $table->string('day_of_week'); // monday, tuesday, etc.
            $table->time('open_time')->nullable(); // 10:00:00
            $table->time('close_time')->nullable(); // 18:00:00
            $table->boolean('is_open')->default(true); // true for open, false for closed
            $table->string('display_text')->nullable(); // "10:00 am - 6:00 pm" or "Closed"
            $table->integer('sort_order')->default(0); // for ordering days
            $table->timestamps();

            // Ensure unique day entries
            $table->unique('day_of_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
};
