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
            // Remover as 3 colunas relacionadas à desativação/reativação
            $table->dropColumn([
                'deactivated_at',
                'reactivation_token', 
                'reactivation_token_expires'
            ]);
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
