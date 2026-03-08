<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        Language::updateOrCreate(['code' => 'fr'], [
            'name' => 'Français',
            'is_active' => true,
            'is_default' => true,
            'sort_order' => 1,
        ]);

        Language::updateOrCreate(['code' => 'en'], [
            'name' => 'English',
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 2,
        ]);

        // Tu peux créer ES tout de suite mais désactivé si tu veux
        Language::updateOrCreate(['code' => 'es'], [
            'name' => 'Español',
            'is_active' => false,
            'is_default' => false,
            'sort_order' => 3,
        ]);

        Language::updateOrCreate(['code' => 'ht'], [
            'name' => 'Kreyòl Ayisyen',
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 4,
        ]);
    }
}
