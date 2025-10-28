<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('atividade_tarefa')) {
            Schema::create('atividade_tarefa', function (Blueprint $table) {
                $table->id();
                $table->foreignId('atividade_id')->constrained()->cascadeOnDelete();
                $table->foreignId('tarefa_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['atividade_id', 'tarefa_id']);
            });
        }

        if (Schema::hasColumn('atividades', 'tarefa_id')) {
            $links = DB::table('atividades')
                ->whereNotNull('tarefa_id')
                ->select('id as atividade_id', 'tarefa_id')
                ->get();

            foreach ($links as $link) {
                DB::table('atividade_tarefa')->updateOrInsert(
                    [
                        'atividade_id' => $link->atividade_id,
                        'tarefa_id' => $link->tarefa_id,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }

            Schema::table('atividades', function (Blueprint $table) {
                $table->dropConstrainedForeignId('tarefa_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('atividades', 'tarefa_id')) {
            Schema::table('atividades', function (Blueprint $table) {
                $table->foreignId('tarefa_id')->nullable()->after('descricao')->constrained('tarefas')->cascadeOnDelete();
            });

            $links = DB::table('atividade_tarefa')
                ->select('atividade_id', 'tarefa_id')
                ->orderBy('created_at')
                ->get();

            foreach ($links as $link) {
                $current = DB::table('atividades')
                    ->where('id', $link->atividade_id)
                    ->value('tarefa_id');

                if ($current === null) {
                    DB::table('atividades')
                        ->where('id', $link->atividade_id)
                        ->update(['tarefa_id' => $link->tarefa_id]);
                }
            }
        }

        Schema::dropIfExists('atividade_tarefa');
    }
};
