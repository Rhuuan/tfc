<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recomendacao_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recomendacao_id')->constrained('recomendacoes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // Usamos string para ampla compatibilidade entre bancos
            $table->string('value', 8); // 'like' | 'dislike'
            $table->timestamps();

            $table->unique(['recomendacao_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recomendacao_feedbacks');
    }
};
