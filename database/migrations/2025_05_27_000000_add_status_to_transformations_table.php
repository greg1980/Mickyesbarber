<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('transformations', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('rating');
        });
    }

    public function down()
    {
        Schema::table('transformations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
