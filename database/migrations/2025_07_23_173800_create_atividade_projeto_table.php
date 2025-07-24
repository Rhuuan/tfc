<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atividade_projeto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atividade_id')->constrained()->onDelete('cascade');
            $table->foreignId('projeto_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atividade_projeto');
    }
};
