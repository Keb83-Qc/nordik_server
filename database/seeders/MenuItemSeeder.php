<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'key'        => 'home',
                'label'      => ['fr' => 'Accueil',      'en' => 'Home',       'es' => 'Inicio',    'ht' => 'Akèy'],
                'path'       => 'home',
                'type'       => 'link',
                'sort_order' => 1,
            ],
            [
                'key'        => 'about',
                'label'      => ['fr' => 'À Propos',     'en' => 'About',      'es' => 'Nosotros',  'ht' => 'Sou nou'],
                'path'       => 'about',
                'type'       => 'link',
                'sort_order' => 2,
            ],
            [
                'key'        => 'management',
                'label'      => ['fr' => 'Gestion',      'en' => 'Management', 'es' => 'Gestión',   'ht' => 'Jesyon'],
                'path'       => 'management',
                'type'       => 'link',
                'sort_order' => 3,
            ],
            [
                'key'        => 'services',
                'label'      => ['fr' => 'Services',     'en' => 'Services',   'es' => 'Servicios', 'ht' => 'Sèvis'],
                'path'       => 'services',
                'type'       => 'mega_services',
                'sort_order' => 4,
            ],
            [
                'key'        => 'team',
                'label'      => ['fr' => 'Notre Équipe', 'en' => 'Our Team',   'es' => 'Equipo',    'ht' => 'Ekip nou'],
                'path'       => 'construction',
                'type'       => 'link',
                'sort_order' => 5,
            ],
            [
                'key'        => 'careers',
                'label'      => ['fr' => 'Carrières',    'en' => 'Careers',    'es' => 'Carreras',  'ht' => 'Karyè'],
                'path'       => 'carrieres',
                'type'       => 'link',
                'sort_order' => 6,
            ],
            [
                'key'        => 'partners',
                'label'      => ['fr' => 'Partenaires',  'en' => 'Partners',   'es' => 'Socios',    'ht' => 'Patnè'],
                'path'       => 'partenaires',
                'type'       => 'link',
                'sort_order' => 7,
            ],
            [
                'key'        => 'contact',
                'label'      => ['fr' => 'Contact',      'en' => 'Contact',    'es' => 'Contacto',  'ht' => 'Kontakte'],
                'path'       => 'contact',
                'type'       => 'link',
                'sort_order' => 8,
            ],
            [
                'key'        => 'cta',
                'label'      => ['fr' => 'Obtenir un devis', 'en' => 'Get a Quote', 'es' => 'Obtener cotización', 'ht' => 'Jwenn yon devis'],
                'path'       => 'contact',
                'type'       => 'cta',
                'sort_order' => 9,
            ],
        ];

        foreach ($items as $data) {
            MenuItem::updateOrCreate(
                ['key' => $data['key']],
                $data + ['is_active' => true, 'target' => '_self']
            );
        }
    }
}
