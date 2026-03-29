<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Portail : conseiller fixe optionnel
        Schema::table('quote_portals', function (Blueprint $table) {
            $table->string('advisor_code', 50)->nullable()->after('type')
                ->comment('Si défini, toutes les soumissions de ce portail vont à ce conseiller. Sinon : rotation.');
        });

        // Soumission : portail d'origine
        Schema::table('submissions', function (Blueprint $table) {
            $table->foreignId('portal_id')
                ->nullable()
                ->after('quote_type_id')
                ->constrained('quote_portals')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['portal_id']);
            $table->dropColumn('portal_id');
        });

        Schema::table('quote_portals', function (Blueprint $table) {
            $table->dropColumn('advisor_code');
        });
    }
};
