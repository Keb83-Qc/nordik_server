<?php

namespace Database\Seeders;

use App\Models\AbfRecommendation;
use Illuminate\Database\Seeder;

class AbfRecommendationSeeder extends Seeder
{
    public function run(): void
    {
        AbfRecommendation::truncate();

        $data = [];
        $sort = 1;

        // ── DÉCÈS ─────────────────────────────────────────────────────
        foreach ([
            ['key' => 'temporaryLifeInsurance',  'title' => 'Souscrire une assurance vie temporaire',            'text' => "Il serait important d'avoir votre protection d'assurance vie temporaire afin de laisser la liquidité nécessaire à vos survivants, leur permettant de rembourser vos dettes et leur assurant un revenu adéquat pour maintenir leur niveau de vie."],
            ['key' => 'permanentLifeInsurance',  'title' => 'Souscrire une assurance vie permanente',            'text' => "Une base d'assurance vie permanente devrait être ajoutée à votre protection temporaire afin de laisser un montant en héritage et d'assumer vos derniers frais."],
            ['key' => 'mortgageInsurance',       'title' => "Réviser l'assurance prêt hypothécaire",             'text' => "Il serait important de réviser votre assurance prêt hypothécaire afin de vous assurer que la protection en place correspond à votre situation actuelle."],
            ['key' => 'childrenLifeInsurance',   'title' => 'Souscrire une assurance vie pour enfants',          'text' => "Souscrire une assurance vie pour vos enfants dès maintenant permet de garantir leur assurabilité future à des conditions avantageuses."],
            ['key' => 'reviewExistingContracts', 'title' => "Réviser les contrats d'assurance existants",        'text' => "Vos contrats d'assurance existants devraient être révisés afin de vous assurer que les protections en place correspondent toujours à vos besoins actuels."],
            ['key' => 'acceleratedPayments',     'title' => "Prévoir des paiements d'assurance accélérés",       'text' => "Il serait avantageux de prévoir des paiements accélérés pour votre assurance afin de réduire la durée des obligations financières."],
        ] as $item) {
            $data[] = array_merge($item, ['category' => 'deces', 'sort_order' => $sort++]);
        }

        // ── INVALIDITÉ ─────────────────────────────────────────────────
        $sort = 1;
        foreach ([
            ['key' => 'disabilityInsurance', 'title' => "Souscrire une assurance invalidité",            'text' => "Il est important d'avoir une protection d'assurance invalidité adéquate pour maintenir votre niveau de vie en cas d'incapacité à travailler."],
            ['key' => 'reviewCollective',    'title' => "Réviser la couverture collective",               'text' => "Votre couverture d'invalidité collective devrait être revue afin de s'assurer qu'elle répond adéquatement à vos besoins actuels."],
            ['key' => 'supplemental',        'title' => "Ajouter une protection complémentaire",         'text' => "Une assurance invalidité complémentaire serait recommandée pour combler l'écart entre votre couverture actuelle et vos besoins réels."],
        ] as $item) {
            $data[] = array_merge($item, ['category' => 'invalidite', 'sort_order' => $sort++]);
        }

        // ── MALADIE GRAVE ──────────────────────────────────────────────
        $sort = 1;
        foreach ([
            ['key' => 'criticalIllness', 'title' => "Souscrire une assurance maladie grave",  'text' => "Il serait important de souscrire une assurance maladie grave afin de disposer d'un capital lors d'un diagnostic d'une maladie grave couverte."],
            ['key' => 'returnOfPremium', 'title' => "Ajouter le remboursement de primes",     'text' => "L'ajout de l'avenant de remboursement de primes permettrait de récupérer les primes versées si aucune réclamation n'est effectuée."],
        ] as $item) {
            $data[] = array_merge($item, ['category' => 'maladie-grave', 'sort_order' => $sort++]);
        }

        // ── FONDS D'URGENCE ────────────────────────────────────────────
        $sort = 1;
        foreach ([
            ['key' => 'buildFund',           'title' => "Constituer un fonds d'urgence",           'text' => "Il est recommandé de constituer un fonds d'urgence représentant entre 3 et 6 mois de dépenses courantes afin de faire face à des imprévus sans recourir à l'endettement."],
            ['key' => 'highInterestSavings', 'title' => "Compte épargne à intérêt élevé",          'text' => "Placer votre fonds d'urgence dans un compte épargne à intérêt élevé permet de conserver la liquidité tout en optimisant le rendement de cette réserve."],
        ] as $item) {
            $data[] = array_merge($item, ['category' => 'fonds-urgence', 'sort_order' => $sort++]);
        }

        // ── RETRAITE ───────────────────────────────────────────────────
        $sort = 1;
        foreach ([
            ['key' => 'reer', 'title' => "Cotiser au REER",           'text' => "Il serait avantageux de maximiser vos cotisations au REER afin de réduire votre revenu imposable et d'accumuler un capital pour la retraite."],
            ['key' => 'celi', 'title' => "Cotiser au CELI",           'text' => "Le CELI constitue un excellent véhicule d'épargne-retraite puisque les revenus de placement qui y sont générés ne sont pas imposables."],
            ['key' => 'rrq',  'title' => "Optimiser la rente RRQ/RPC", 'text' => "Il serait important d'analyser le moment optimal pour commencer à recevoir votre rente du Régime de rentes du Québec afin d'en maximiser les prestations."],
        ] as $item) {
            $data[] = array_merge($item, ['category' => 'retraite', 'sort_order' => $sort++]);
        }

        // ── CONSEILS GÉNÉRAUX ──────────────────────────────────────────
        $sort = 1;
        foreach ([
            ['key' => 'rrq',           'title' => 'Régime des rentes du Québec (RRQ)', 'checked_by_default' => false, 'text' => "Il serait important d'analyser le moment optimal pour commencer à recevoir votre rente du Régime de rentes du Québec (RRQ) afin d'en maximiser les prestations selon votre situation personnelle et financière."],
            ['key' => 'compte-conj',   'title' => 'Compte conjoint',                   'checked_by_default' => false, 'text' => "L'ouverture d'un compte conjoint peut simplifier la gestion des finances familiales et faciliter l'accès aux fonds en cas de décès de l'un des conjoints."],
            ['key' => 'auto-hab',      'title' => 'Assurance auto et habitation',      'checked_by_default' => false, 'text' => "Il serait judicieux de réviser vos assurances auto et habitation afin de vous assurer que vos protections correspondent à votre situation actuelle et d'optimiser vos primes."],
            ['key' => 'budget',        'title' => 'Budget',                            'checked_by_default' => false, 'text' => "L'établissement d'un budget détaillé vous permettrait de mieux contrôler vos dépenses, d'identifier des occasions d'épargne et d'atteindre vos objectifs financiers plus rapidement."],
            ['key' => 'comptable',     'title' => 'Comptable',                         'checked_by_default' => false, 'text' => "Je vous recommande de consulter un comptable ou fiscaliste afin d'optimiser votre situation fiscale et de vous assurer que vous bénéficiez de tous les avantages fiscaux auxquels vous avez droit."],
            ['key' => 'testament',     'title' => 'Testament',                         'checked_by_default' => false, 'text' => "Je vous encourage à procéder à la rédaction ou à la mise à jour de votre testament afin de vous assurer que vos biens seront distribués selon vos volontés et de faciliter le règlement de votre succession."],
            ['key' => 'mandat',        'title' => 'Mandat de protection',              'checked_by_default' => false, 'text' => "Le mandat de protection (anciennement mandat en cas d'inaptitude) est un document essentiel qui désigne la personne qui s'occupera de vous et de vos biens si vous devenez inapte."],
            ['key' => 'personnes-res', 'title' => 'Personnes-ressources',              'checked_by_default' => false, 'text' => "Il est conseillé d'identifier et de documenter vos personnes-ressources (notaire, comptable, médecin, proche de confiance) afin que vos proches sachent qui contacter en cas de besoin."],
            ['key' => 'taux-hypo',     'title' => 'Taux hypothécaires',                'checked_by_default' => false, 'text' => "Il serait opportun d'analyser votre situation hypothécaire actuelle afin de déterminer si un refinancement ou une renégociation de votre taux pourrait vous faire réaliser des économies substantielles."],
            ['key' => 'beneficiaires', 'title' => 'Bénéficiaires',                     'checked_by_default' => false, 'text' => "Il est important de vérifier et mettre à jour les désignations de bénéficiaires sur vos contrats d'assurance vie et vos régimes d'épargne-retraite afin de vous assurer que les sommes seront versées aux personnes de votre choix."],
        ] as $item) {
            $data[] = array_merge($item, ['category' => 'conseils', 'sort_order' => $sort++, 'is_active' => true]);
        }

        foreach ($data as $row) {
            AbfRecommendation::create(array_merge([
                'is_active'          => true,
                'checked_by_default' => false,
            ], $row));
        }
    }
}
