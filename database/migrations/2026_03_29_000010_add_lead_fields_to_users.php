<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Active/désactive la réception de leads pour ce conseiller
            if (! Schema::hasColumn('users', 'accepts_leads')) {
                $table->boolean('accepts_leads')->default(false)->after('advisor_code');
            }

            // Poids dans la rotation : 1 = 1 lead par cycle, 2 = 2 leads par cycle, etc.
            if (! Schema::hasColumn('users', 'lead_weight')) {
                $table->unsignedTinyInteger('lead_weight')->default(1)->after('accepts_leads');
            }

            // Compteur du cycle en cours (remis à 0 quand tout le monde atteint son quota)
            if (! Schema::hasColumn('users', 'leads_received_cycle')) {
                $table->unsignedInteger('leads_received_cycle')->default(0)->after('lead_weight');
            }

            // Horodatage du dernier lead reçu (pour le tie-break dans la rotation)
            if (! Schema::hasColumn('users', 'last_lead_received_at')) {
                $table->timestamp('last_lead_received_at')->nullable()->after('leads_received_cycle');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'accepts_leads',
                'lead_weight',
                'leads_received_cycle',
                'last_lead_received_at',
            ]);
        });
    }
};
