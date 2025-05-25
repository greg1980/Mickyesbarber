<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('transformations', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false)->after('after_photo');
        });
    }

    public function down()
    {
        Schema::table('transformations', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
