<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('atividades', 'fase_id')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF;');

            Schema::create('atividades_temp', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->text('descricao')->nullable();
                $table->foreignId('tarefa_id')->constrained()->cascadeOnDelete();
                $table->foreignId('fase_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });

            $records = DB::table('atividades')->get();

            foreach ($records as $record) {
                DB::table('atividades_temp')->insert((array) $record);
            }

            Schema::drop('atividades');
            Schema::rename('atividades_temp', 'atividades');

            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
            Schema::table('atividades', function (Blueprint $table) {
                $table->dropForeign(['fase_id']);
            });

            Schema::table('atividades', function (Blueprint $table) {
                $table->foreignId('fase_id')->nullable()->change();
            });

            Schema::table('atividades', function (Blueprint $table) {
                $table->foreign('fase_id')->references('id')->on('fases')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('atividades', 'fase_id')) {
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            if (DB::table('atividades')->whereNull('fase_id')->exists()) {
                throw new \RuntimeException('Nao e possivel reverter: existem atividades sem fase associada.');
            }

            DB::statement('PRAGMA foreign_keys=OFF;');

            Schema::create('atividades_temp', function (Blueprint $table) {
                $table->id();
                $table->string('nome');
                $table->text('descricao')->nullable();
                $table->foreignId('tarefa_id')->constrained()->cascadeOnDelete();
                $table->foreignId('fase_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });

            $records = DB::table('atividades')->get();

            foreach ($records as $record) {
                DB::table('atividades_temp')->insert((array) $record);
            }

            Schema::drop('atividades');
            Schema::rename('atividades_temp', 'atividades');

            DB::statement('PRAGMA foreign_keys=ON;');
        } else {
            Schema::table('atividades', function (Blueprint $table) {
                $table->dropForeign(['fase_id']);
            });

            Schema::table('atividades', function (Blueprint $table) {
                $table->foreignId('fase_id')->nullable(false)->change();
            });

            Schema::table('atividades', function (Blueprint $table) {
                $table->foreign('fase_id')->references('id')->on('fases')->cascadeOnDelete();
            });
        }
    }
};
