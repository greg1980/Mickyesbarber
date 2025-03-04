<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transformations', function (Blueprint $table) {
            if (!Schema::hasColumn('transformations', 'barber_id')) {
                $table->foreignId('barber_id')->after('id')->constrained('barbers')->cascadeOnDelete();
            }

            if (Schema::hasColumn('transformations', 'after_photo')) {
                $table->string('after_photo')->nullable()->change();
            }

            if (Schema::hasColumn('transformations', 'description')) {
                $table->text('description')->nullable()->change();
            }

            if (Schema::hasColumn('transformations', 'rating')) {
                $table->integer('rating')->nullable()->change();
            }

            if (Schema::hasColumn('transformations', 'review')) {
                $table->text('review')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('transformations', function (Blueprint $table) {
            if (Schema::hasColumn('transformations', 'barber_id')) {
                $table->dropForeign(['barber_id']);
                $table->dropColumn('barber_id');
            }
        });
    }
};

