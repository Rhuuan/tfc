<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarefa_projeto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarefa_id')->constrained()->onDelete('cascade');
            $table->foreignId('projeto_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefa_projeto');
    }
};
