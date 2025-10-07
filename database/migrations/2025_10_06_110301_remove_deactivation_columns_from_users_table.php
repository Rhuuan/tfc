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
        Schema::table('users', function (Blueprint $table) {
            // Verificar se as colunas existem antes de tentar removê-las
            if (Schema::hasColumn('users', 'deactivated_at')) {
                $table->dropColumn('deactivated_at');
            }
            if (Schema::hasColumn('users', 'reactivation_token')) {
                $table->dropColumn('reactivation_token');
            }
            if (Schema::hasColumn('users', 'reactivation_token_expires')) {
                $table->dropColumn('reactivation_token_expires');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Recriar as colunas caso seja necessário reverter
            $table->timestamp('deactivated_at')->nullable();
            $table->string('reactivation_token')->nullable();
            $table->timestamp('reactivation_token_expires')->nullable();
        });
    }
};
