<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'slug' => ['fr' => 'assurance', 'en' => 'insurance', 'es' => 'seguros'],
                'name' => ['fr' => 'Assurance', 'en' => 'Insurance', 'es' => 'Seguros'],
                'sort_order' => 10,
            ],
            [
                'slug' => ['fr' => 'epargne', 'en' => 'savings', 'es' => 'ahorros'],
                'name' => ['fr' => 'Épargne', 'en' => 'Savings', 'es' => 'Ahorros'],
                'sort_order' => 20,
            ],
            [
                'slug' => ['fr' => 'pret', 'en' => 'loans', 'es' => 'prestamos'],
                'name' => ['fr' => 'Prêt', 'en' => 'Loans', 'es' => 'Préstamos'],
                'sort_order' => 30,
            ],
            [
                'slug' => ['fr' => 'hypotheque', 'en' => 'mortgage', 'es' => 'hipoteca'],
                'name' => ['fr' => 'Hypothèque', 'en' => 'Mortgage', 'es' => 'Hipoteca'],
                'sort_order' => 40,
            ],
        ];

        foreach ($data as $row) {
            ServiceCategory::updateOrCreate(
                ['slug->fr' => $row['slug']['fr']],
                [
                    'slug' => $row['slug'],
                    'name' => $row['name'],
                    'sort_order' => $row['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
