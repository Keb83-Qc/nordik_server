<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->foreignId('quote_type_id')
                  ->nullable()
                  ->after('type')
                  ->constrained('quote_types')
                  ->nullOnDelete();
        });

        // Migrer les données existantes : type (string) → quote_type_id (FK)
        DB::statement('
            UPDATE submissions s
            JOIN quote_types qt ON qt.slug = s.type
            SET s.quote_type_id = qt.id
            WHERE s.type IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['quote_type_id']);
            $table->dropColumn('quote_type_id');
        });
    }
};
