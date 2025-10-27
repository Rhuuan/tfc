<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE atividades ALTER COLUMN fase_id DROP NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE atividades ALTER COLUMN fase_id SET NOT NULL');
    }
};
