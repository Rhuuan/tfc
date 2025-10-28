<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('fases', 'data')) {
            Schema::table('fases', function (Blueprint $table) {
                $table->dropColumn('data');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('fases', 'data')) {
            Schema::table('fases', function (Blueprint $table) {
                $table->date('data')->nullable();
            });
        }
    }
};
