<?php

namespace App\Domain\QuoteBundle;

final class StepEngine
{
    public const FINAL = 'final';

    public static function makeDefault(): self
    {
        return new self();
    }

    public function nextStep(QuoteBundleData $d): string
    {
        if ($s = $this->firstMissing(StepRegistry::common(), $d)) {
            return $s;
        }

        if ($s = $this->firstMissing(StepRegistry::profile(), $d)) {
            return $s;
        }

        if ($s = $this->firstMissing(StepRegistry::auto(), $d)) {
            return $s;
        }

        if ($s = $this->firstMissing(StepRegistry::habitation(), $d)) {
            return $s;
        }

        return self::FINAL;
    }

    public function needsHydration(string $step): array
    {
        // ⚠️ adapte les clés selon tes steps réels dans StepRegistry::auto()
        return match ($step) {
            'auto_model' => ['auto_models_for_brand'],
            default => [],
        };
    }

    private function firstMissing(array $registry, QuoteBundleData $d): ?string
    {
        foreach ($registry as $step => $meta) {
            if ($this->skipStep($step, $d)) {
                continue;
            }

            $bucketName = $meta['bucket'] ?? null;
            if (!$bucketName || !property_exists($d, $bucketName)) {
                // Sécurité: si registry mal défini, on ignore le step plutôt que de casser le flow
                continue;
            }

            $bucket = $d->{$bucketName} ?? [];
            if (!is_array($bucket)) {
                $bucket = [];
            }

            $required = $meta['required'] ?? null;
            if (!$required) {
                continue;
            }

            if (is_array($required)) {
                foreach ($required as $k) {
                    if (!array_key_exists($k, $bucket) || $bucket[$k] === '' || $bucket[$k] === null) {
                        return $step;
                    }
                }
            } else {
                if (!array_key_exists($required, $bucket) || $bucket[$required] === '' || $bucket[$required] === null) {
                    return $step;
                }
            }
        }

        return null;
    }

    private function skipStep(string $step, QuoteBundleData $d): bool
    {
        $hab = $d->habitation ?? [];

        $living = $hab['living_there'] ?? null;       // yes|no
        $ptype  = $hab['property_type'] ?? null;      // maison|condo|appartement

        // ✅ move_in_date seulement si living_there = no
        // Donc: si living_there = yes => on SKIP move_in_date
        if ($step === 'hab_move_in_date' && $living === 'yes') {
            return true;
        }

        // ✅ units_in_building seulement si property_type != maison
        if ($step === 'hab_units_in_building' && $ptype === 'maison') {
            return true;
        }

        // ✅ marketing_email seulement si consent_marketing = accept
        if ($step === 'hab_marketing_email' && (($hab['consent_marketing'] ?? null) !== 'accept')) {
            return true;
        }

        return false;
    }
}
