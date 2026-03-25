<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ajouter colonne temporaire JSON
        Schema::table('chat_steps', function (Blueprint $table) {
            $table->json('question_multilang')->nullable()->after('chat_type');
        });

        // 2. Convertir le texte existant en JSON { fr: "...", en: "", es: "", ht: "" }
        DB::table('chat_steps')->orderBy('id')->each(function ($row) {
            DB::table('chat_steps')
                ->where('id', $row->id)
                ->update([
                    'question_multilang' => json_encode([
                        'fr' => $row->question ?? '',
                        'en' => '',
                        'es' => '',
                        'ht' => '',
                    ], JSON_UNESCAPED_UNICODE),
                ]);
        });

        // 3. Supprimer l'ancienne colonne texte
        Schema::table('chat_steps', function (Blueprint $table) {
            $table->dropColumn('question');
        });

        // 4. Renommer la nouvelle colonne
        Schema::table('chat_steps', function (Blueprint $table) {
            $table->renameColumn('question_multilang', 'question');
        });
    }

    public function down(): void
    {
        Schema::table('chat_steps', function (Blueprint $table) {
            $table->text('question_text')->nullable()->after('chat_type');
        });

        DB::table('chat_steps')->orderBy('id')->each(function ($row) {
            $q = json_decode($row->question, true);
            DB::table('chat_steps')
                ->where('id', $row->id)
                ->update(['question_text' => $q['fr'] ?? '']);
        });

        Schema::table('chat_steps', function (Blueprint $table) {
            $table->dropColumn('question');
        });

        Schema::table('chat_steps', function (Blueprint $table) {
            $table->renameColumn('question_text', 'question');
        });
    }
};
