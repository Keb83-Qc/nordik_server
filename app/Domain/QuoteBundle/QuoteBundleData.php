<?php

namespace App\Domain\QuoteBundle;

use App\Domain\QuoteBundle\Enums\AcceptRefuse;
use App\Domain\QuoteBundle\Enums\YesNo;
use App\Domain\QuoteBundle\Enums\YearsInsured;

final class QuoteBundleData
{
    public function __construct(
        public array $common = [],
        public array $auto = [],
        public array $habitation = [],
        public array $meta = [],
    ) {}

    public static function fromArray(array $data): self
    {
        $dto = new self(
            common: $data['common'] ?? [],
            auto: $data['auto'] ?? [],
            habitation: $data['habitation'] ?? [],
            meta: $data['meta'] ?? [],
        );

        $dto->normalize();
        $dto->cleanupConditionals();

        return $dto;
    }

    public function toArray(): array
    {
        return [
            'common' => $this->common,
            'auto' => $this->auto,
            'habitation' => $this->habitation,
            'meta' => $this->meta,
        ];
    }

    public function set(string $bucket, string $key, mixed $value): void
    {
        $this->{$bucket}[$key] = $value;

        $this->normalize();
        $this->cleanupConditionals();
    }

    public function setMany(string $bucket, array $pairs): void
    {
        foreach ($pairs as $k => $v) {
            $this->{$bucket}[$k] = $v;
        }

        $this->normalize();
        $this->cleanupConditionals();
    }

    public function normalize(): void
    {
        foreach (['living_there', 'electric_baseboard', 'supp_heating', 'has_ia_products', 'marketing_email', 'consent_credit'] as $k) {
            if (isset($this->habitation[$k])) {
                $this->habitation[$k] = YesNo::fromLoose((string)$this->habitation[$k])->value;
            }
        }

        foreach (['consent_profile', 'consent_marketing'] as $k) {
            if (isset($this->habitation[$k])) {
                $this->habitation[$k] = AcceptRefuse::fromLoose((string)$this->habitation[$k])->value;
            }
        }

        if (isset($this->habitation['years_insured'])) {
            $this->habitation['years_insured'] = YearsInsured::fromLoose((string)$this->habitation['years_insured'])->value;
        }

        if (isset($this->auto['usage'])) {
            $u = strtolower(trim((string)$this->auto['usage']));
            $this->auto['usage'] = ($u === 'commercial') ? 'commercial' : 'personnel';
        }
    }

    public function cleanupConditionals(): void
    {
        // ✅ move_in_date seulement si living_there = no
        if (($this->habitation['living_there'] ?? null) === 'yes') {
            unset($this->habitation['move_in_date']);
        }

        if (($this->habitation['property_type'] ?? null) === 'maison') {
            unset($this->habitation['units_in_building']);
        }

        if (($this->habitation['consent_marketing'] ?? null) !== 'accept') {
            unset($this->habitation['marketing_email']);
        }
    }
}
