<?php

namespace App\Domain\QuoteBundle\Enums;

enum YearsInsured: string
{
    case ZERO = '0';
    case ONE_TWO = '1_2';
    case THREE_FIVE = '3_5';
    case SIX_TEN = '6_10';
    case ELEVEN_PLUS = '11_plus';

    public static function fromLoose(string $v): self
    {
        $v = trim($v);

        return match ($v) {
            '0', 'ZERO' => self::ZERO,
            '1_2', '1-2', '1 à 2', '1 a 2' => self::ONE_TWO,
            '3_5', '3-5', '3 à 5', '3 a 5' => self::THREE_FIVE,
            '6_10', '6-10', '6 à 10', '6 a 10' => self::SIX_TEN,
            '11_plus', '11+', '11 et plus', '11 ans et plus' => self::ELEVEN_PLUS,
            default => self::ELEVEN_PLUS, // safe fallback
        };
    }
}
