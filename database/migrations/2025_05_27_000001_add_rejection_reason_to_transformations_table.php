<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('transformations', function (Blueprint $table) {
            $table->string('rejection_reason')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('transformations', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
