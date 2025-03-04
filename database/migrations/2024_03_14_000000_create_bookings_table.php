<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barber_id')->constrained()->cascadeOnDelete();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->decimal('service_price', 8, 2);
            $table->decimal('deposit_amount', 8, 2)->default(0);
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
