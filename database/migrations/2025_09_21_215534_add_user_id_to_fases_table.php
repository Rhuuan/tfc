<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToFasesTable extends Migration
{
    public function up()
    {
        Schema::table('fases', function (Blueprint $table) {
            $table->foreignId('user_id')->after('data')->constrained()->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('fases', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
