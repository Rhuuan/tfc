<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaseAtividadeTable extends Migration
{
    public function up()
    {
        Schema::create('atividade_fase', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fase_id')->constrained()->onDelete('cascade');
            $table->foreignId('atividade_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('atividade_fase');
    }
}
