<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PublicServiceCategory;
use App\Models\PublicServiceCategoryTranslation;
use App\Models\PublicService;
use App\Models\PublicServiceTranslation;

class PublicServicesSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Catégories + leurs traductions (slug par langue)
        $categories = [
            'assurance' => [
                'sort_order' => 10,
                'translations' => [
                    'fr' => ['name' => 'Assurance',   'slug' => 'assurance'],
                    'en' => ['name' => 'Insurance',   'slug' => 'insurance'],
                    'es' => ['name' => 'Seguros',     'slug' => 'seguros'],
                    'ht' => ['name' => 'Asirans',     'slug' => 'asirans'], // adapte si tu veux
                ],
            ],
            'epargne' => [
                'sort_order' => 20,
                'translations' => [
                    'fr' => ['name' => 'Épargne',     'slug' => 'epargne'],
                    'en' => ['name' => 'Savings',     'slug' => 'savings'],
                    'es' => ['name' => 'Ahorro',      'slug' => 'ahorro'],
                    'ht' => ['name' => 'Ekonomi',     'slug' => 'ekonomi'],
                ],
            ],
            'pret' => [
                'sort_order' => 30,
                'translations' => [
                    'fr' => ['name' => 'Prêt',        'slug' => 'pret'],
                    'en' => ['name' => 'Loan',        'slug' => 'loan'],
                    'es' => ['name' => 'Préstamo',    'slug' => 'prestamo'],
                    'ht' => ['name' => 'Pre',         'slug' => 'pre'],
                ],
            ],
            'hypotheque' => [
                'sort_order' => 40,
                'translations' => [
                    'fr' => ['name' => 'Hypothèque',  'slug' => 'hypotheque'],
                    'en' => ['name' => 'Mortgage',    'slug' => 'mortgage'],
                    'es' => ['name' => 'Hipoteca',    'slug' => 'hipoteca'],
                    'ht' => ['name' => 'Ipotèk',      'slug' => 'ipotek'],
                ],
            ],
        ];

        // ✅ Services de base (code = nom de ta blade)
        // IMPORTANT: code doit correspondre à resources/views/services/{categorie}/{code}.blade.php
        $services = [
            'assurance' => [
                ['code' => 'assurance-vie', 'sort_order' => 10, 'translations' => [
                    'fr' => ['title' => 'Assurance vie', 'slug' => 'assurance-vie'],
                    'en' => ['title' => 'Life insurance', 'slug' => 'life-insurance'],
                    'es' => ['title' => 'Seguro de vida', 'slug' => 'seguro-de-vida'],
                    'ht' => ['title' => 'Asirans lavi', 'slug' => 'asirans-lavi'],
                ]],
                ['code' => 'assurance-maladie-grave', 'sort_order' => 20, 'translations' => [
                    'fr' => ['title' => 'Assurance maladie grave', 'slug' => 'assurance-maladie-grave'],
                    'en' => ['title' => 'Critical illness insurance', 'slug' => 'critical-illness-insurance'],
                    'es' => ['title' => 'Seguro de enfermedades graves', 'slug' => 'seguro-de-enfermedades-graves'],
                    'ht' => ['title' => 'Asirans maladi grav', 'slug' => 'asirans-maladi-grav'],
                ]],
                ['code' => 'assurance-invalidite', 'sort_order' => 30, 'translations' => [
                    'fr' => ['title' => 'Assurance invalidité', 'slug' => 'assurance-invalidite'],
                    'en' => ['title' => 'Disability insurance', 'slug' => 'disability-insurance'],
                    'es' => ['title' => 'Seguro de invalidez', 'slug' => 'seguro-de-invalidez'],
                    'ht' => ['title' => 'Asirans andikap', 'slug' => 'asirans-andikap'],
                ]],
                ['code' => 'assurance-complementaire-dentaire', 'sort_order' => 40, 'translations' => [
                    'fr' => ['title' => 'Assurance complémentaire dentaire', 'slug' => 'assurance-complementaire-dentaire'],
                    'en' => ['title' => 'Dental insurance', 'slug' => 'dental-insurance'],
                    'es' => ['title' => 'Seguro dental complementario', 'slug' => 'seguro-dental-complementario'],
                    'ht' => ['title' => 'Asirans dan', 'slug' => 'asirans-dan'],
                ]],
                ['code' => 'assurance-dommages', 'sort_order' => 50, 'translations' => [
                    'fr' => ['title' => 'Assurance dommages', 'slug' => 'assurance-dommages'],
                    'en' => ['title' => 'Property & casualty', 'slug' => 'property-casualty'],
                    'es' => ['title' => 'Seguro de daños', 'slug' => 'seguro-de-danos'],
                    'ht' => ['title' => 'Asirans domaj', 'slug' => 'asirans-domaj'],
                ]],
                ['code' => 'assurance-responsabilite-professionnelle', 'sort_order' => 60, 'translations' => [
                    'fr' => ['title' => 'Responsabilité professionnelle', 'slug' => 'assurance-responsabilite-professionnelle'],
                    'en' => ['title' => 'Professional liability', 'slug' => 'professional-liability-insurance'],
                    'es' => ['title' => 'Responsabilidad profesional', 'slug' => 'responsabilidad-profesional'],
                    'ht' => ['title' => 'Responsablite pwofesyonel', 'slug' => 'responsablite-pwofesyonel'],
                ]],
                ['code' => 'assurance-commerciale', 'sort_order' => 70, 'translations' => [
                    'fr' => ['title' => 'Assurance commerciale', 'slug' => 'assurance-commerciale'],
                    'en' => ['title' => 'Commercial insurance', 'slug' => 'commercial-insurance'],
                    'es' => ['title' => 'Seguro comercial', 'slug' => 'seguro-comercial'],
                    'ht' => ['title' => 'Asirans komèsyal', 'slug' => 'asirans-komesyal'],
                ]],
                ['code' => 'assurance-voyage', 'sort_order' => 80, 'translations' => [
                    'fr' => ['title' => 'Assurance voyage', 'slug' => 'assurance-voyage'],
                    'en' => ['title' => 'Travel insurance', 'slug' => 'travel-insurance'],
                    'es' => ['title' => 'Seguro de viaje', 'slug' => 'seguro-de-viaje'],
                    'ht' => ['title' => 'Asirans vwayaj', 'slug' => 'asirans-vwayaj'],
                ]],
            ],

            'epargne' => [
                ['code' => 'reer', 'sort_order' => 10, 'translations' => [
                    'fr' => ['title' => 'REER', 'slug' => 'reer'],
                    'en' => ['title' => 'RRSP', 'slug' => 'rrsp'],
                    'es' => ['title' => 'RRSP', 'slug' => 'rrsp'],
                    'ht' => ['title' => 'REER', 'slug' => 'reer'],
                ]],
                ['code' => 'celi', 'sort_order' => 20, 'translations' => [
                    'fr' => ['title' => 'CELI', 'slug' => 'celi'],
                    'en' => ['title' => 'TFSA', 'slug' => 'tfsa'],
                    'es' => ['title' => 'TFSA', 'slug' => 'tfsa'],
                    'ht' => ['title' => 'CELI', 'slug' => 'celi'],
                ]],
                ['code' => 'celiapp', 'sort_order' => 30, 'translations' => [
                    'fr' => ['title' => 'CELIAPP', 'slug' => 'celiapp'],
                    'en' => ['title' => 'FHSA', 'slug' => 'fhsa'],
                    'es' => ['title' => 'FHSA', 'slug' => 'fhsa'],
                    'ht' => ['title' => 'CELIAPP', 'slug' => 'celiapp'],
                ]],
                ['code' => 'reei', 'sort_order' => 40, 'translations' => [
                    'fr' => ['title' => 'REEI', 'slug' => 'reei'],
                    'en' => ['title' => 'RDSP', 'slug' => 'rdsp'],
                    'es' => ['title' => 'RDSP', 'slug' => 'rdsp'],
                    'ht' => ['title' => 'REEI', 'slug' => 'reei'],
                ]],
                ['code' => 'rene', 'sort_order' => 50, 'translations' => [
                    'fr' => ['title' => 'RENE', 'slug' => 'rene'],
                    'en' => ['title' => 'NRSP', 'slug' => 'nrsp'],
                    'es' => ['title' => 'NRSP', 'slug' => 'nrsp'],
                    'ht' => ['title' => 'RENE', 'slug' => 'rene'],
                ]],
                ['code' => 'cri', 'sort_order' => 60, 'translations' => [
                    'fr' => ['title' => 'CRI', 'slug' => 'cri'],
                    'en' => ['title' => 'LIRA', 'slug' => 'lira'],
                    'es' => ['title' => 'LIRA', 'slug' => 'lira'],
                    'ht' => ['title' => 'CRI', 'slug' => 'cri'],
                ]],
                ['code' => 'frv', 'sort_order' => 70, 'translations' => [
                    'fr' => ['title' => 'FRV', 'slug' => 'frv'],
                    'en' => ['title' => 'LIF', 'slug' => 'lif'],
                    'es' => ['title' => 'LIF', 'slug' => 'lif'],
                    'ht' => ['title' => 'FRV', 'slug' => 'frv'],
                ]],
                ['code' => 'ferr', 'sort_order' => 80, 'translations' => [
                    'fr' => ['title' => 'FERR', 'slug' => 'ferr'],
                    'en' => ['title' => 'RRIF', 'slug' => 'rrif'],
                    'es' => ['title' => 'RRIF', 'slug' => 'rrif'],
                    'ht' => ['title' => 'FERR', 'slug' => 'ferr'],
                ]],
                ['code' => 'reee', 'sort_order' => 90, 'translations' => [
                    'fr' => ['title' => 'REEE', 'slug' => 'reee'],
                    'en' => ['title' => 'RESP', 'slug' => 'resp'],
                    'es' => ['title' => 'RESP', 'slug' => 'resp'],
                    'ht' => ['title' => 'REEE', 'slug' => 'reee'],
                ]],
            ],

            'pret' => [
                ['code' => 'pret-reer', 'sort_order' => 10, 'translations' => [
                    'fr' => ['title' => 'Prêt REER', 'slug' => 'pret-reer'],
                    'en' => ['title' => 'RRSP loan', 'slug' => 'rrsp-loan'],
                    'es' => ['title' => 'Préstamo RRSP', 'slug' => 'prestamo-rrsp'],
                    'ht' => ['title' => 'Pre REER', 'slug' => 'pre-reer'],
                ]],
                ['code' => 'pret-reee', 'sort_order' => 20, 'translations' => [
                    'fr' => ['title' => 'Prêt REEE', 'slug' => 'pret-reee'],
                    'en' => ['title' => 'RESP loan', 'slug' => 'resp-loan'],
                    'es' => ['title' => 'Préstamo RESP', 'slug' => 'prestamo-resp'],
                    'ht' => ['title' => 'Pre REEE', 'slug' => 'pre-reee'],
                ]],
                ['code' => 'pret-investissement', 'sort_order' => 30, 'translations' => [
                    'fr' => ['title' => 'Prêt investissement', 'slug' => 'pret-investissement'],
                    'en' => ['title' => 'Investment loan', 'slug' => 'investment-loan'],
                    'es' => ['title' => 'Préstamo de inversión', 'slug' => 'prestamo-de-inversion'],
                    'ht' => ['title' => 'Pre envestisman', 'slug' => 'pre-envestisman'],
                ]],
                ['code' => 'carte-de-credit', 'sort_order' => 40, 'translations' => [
                    'fr' => ['title' => 'Carte de crédit', 'slug' => 'carte-de-credit'],
                    'en' => ['title' => 'Credit card', 'slug' => 'credit-card'],
                    'es' => ['title' => 'Tarjeta de crédito', 'slug' => 'tarjeta-de-credito'],
                    'ht' => ['title' => 'Kat kredi', 'slug' => 'kat-kredi'],
                ]],
            ],

            'hypotheque' => [
                ['code' => 'programme-dachat-clef-en-main', 'sort_order' => 10, 'translations' => [
                    'fr' => ['title' => 'Programme d’achat clef en main', 'slug' => 'programme-dachat-clef-en-main'],
                    'en' => ['title' => 'Turnkey purchase program', 'slug' => 'turnkey-purchase-program'],
                    'es' => ['title' => 'Programa de compra llave en mano', 'slug' => 'programa-de-compra-llave-en-mano'],
                    'ht' => ['title' => 'Pwogram achte kle nan men', 'slug' => 'pwogram-achte-kle-nan-men'],
                ]],
                ['code' => 'pret-hypothecaire', 'sort_order' => 20, 'translations' => [
                    'fr' => ['title' => 'Prêt hypothécaire', 'slug' => 'pret-hypothecaire'],
                    'en' => ['title' => 'Mortgage', 'slug' => 'mortgage'],
                    'es' => ['title' => 'Hipoteca', 'slug' => 'hipoteca'],
                    'ht' => ['title' => 'Prè ipotèk', 'slug' => 'pre-ipotek'],
                ]],
            ],
        ];

        // -------------------------
        // 1) Create / update categories + translations
        // -------------------------
        $categoryIdsByCode = [];

        foreach ($categories as $code => $catData) {
            $cat = PublicServiceCategory::updateOrCreate(
                ['code' => $code],
                [
                    'is_active' => 1,
                    'sort_order' => $catData['sort_order'] ?? 0,
                ]
            );

            $categoryIdsByCode[$code] = $cat->id;

            foreach (($catData['translations'] ?? []) as $locale => $tr) {
                PublicServiceCategoryTranslation::updateOrCreate(
                    [
                        'public_service_category_id' => $cat->id,
                        'locale' => $locale,
                    ],
                    [
                        'name' => $tr['name'] ?? $code,
                        'slug' => $tr['slug'] ?? $code,
                    ]
                );
            }
        }

        // -------------------------
        // 2) Create / update services + translations
        // -------------------------
        foreach ($services as $catCode => $items) {
            $catId = $categoryIdsByCode[$catCode] ?? null;
            if (!$catId) {
                continue;
            }

            foreach ($items as $s) {
                $srv = PublicService::updateOrCreate(
                    [
                        'public_service_category_id' => $catId,
                        'code' => $s['code'],
                    ],
                    [
                        'is_active' => 1,
                        'sort_order' => $s['sort_order'] ?? 0,
                    ]
                );

                foreach (($s['translations'] ?? []) as $locale => $tr) {
                    PublicServiceTranslation::updateOrCreate(
                        [
                            'public_service_id' => $srv->id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $tr['title'] ?? $s['code'],
                            'slug' => $tr['slug'] ?? $s['code'],
                        ]
                    );
                }
            }
        }
    }
}
