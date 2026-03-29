<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 30)->unique();       // auto, habitation, bundle, commercial
            $table->json('label');                       // {fr: "Auto", en: "Auto", ...}
            $table->string('icon', 100)->nullable();     // heroicon-o-truck
            $table->string('color', 30)->nullable();     // info, success, warning, danger
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Données de base — remplacent les valeurs hardcodées dans le code
        DB::table('quote_types')->insert([
            [
                'slug'       => 'auto',
                'label'      => json_encode([
                    'fr' => 'Auto',
                    'en' => 'Auto',
                    'es' => 'Auto',
                    'ht' => 'Otomobil',
                ]),
                'icon'       => 'heroicon-o-truck',
                'color'      => 'info',
                'sort_order' => 1,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug'       => 'habitation',
                'label'      => json_encode([
                    'fr' => 'Habitation',
                    'en' => 'Home Insurance',
                    'es' => 'Vivienda',
                    'ht' => 'Kay',
                ]),
                'icon'       => 'heroicon-o-home',
                'color'      => 'success',
                'sort_order' => 2,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug'       => 'bundle',
                'label'      => json_encode([
                    'fr' => 'Bundle Auto + Habitation',
                    'en' => 'Bundle Auto + Home',
                    'es' => 'Paquete Auto + Vivienda',
                    'ht' => 'Pakèt Otomobil + Kay',
                ]),
                'icon'       => 'heroicon-o-squares-2x2',
                'color'      => 'warning',
                'sort_order' => 3,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug'       => 'commercial',
                'label'      => json_encode([
                    'fr' => 'Commercial',
                    'en' => 'Commercial',
                    'es' => 'Comercial',
                    'ht' => 'Komèsyal',
                ]),
                'icon'       => 'heroicon-o-building-office',
                'color'      => 'danger',
                'sort_order' => 4,
                'is_active'  => false, // Inactif pour l'instant
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_types');
    }
};
