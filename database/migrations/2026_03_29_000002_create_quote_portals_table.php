<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_portals', function (Blueprint $table) {
            $table->id();

            // Identification
            $table->string('slug', 50)->unique();               // internal, partenaire-abc
            $table->string('name', 100);                        // Nordik, ABC Finance
            $table->enum('type', ['internal', 'partner'])
                  ->default('internal');

            // Branding (utilisé seulement pour les partenaires)
            $table->string('logo_path')->nullable();            // chemin vers le logo uploadé
            $table->string('primary_color', 10)
                  ->nullable()
                  ->default('#1a2e4a');                         // couleur principale
            $table->string('secondary_color', 10)
                  ->nullable()
                  ->default('#e8b84b');                         // couleur secondaire

            // Consentement multilingue
            $table->json('consent_title')->nullable();          // {fr: "Bienvenue", en: "Welcome", ...}
            $table->json('consent_text')->nullable();           // {fr: "<p>...</p>", en: "<p>...</p>", ...}

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_portals');
    }
};
