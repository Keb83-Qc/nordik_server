<?php

namespace App\Domain\QuoteBundle\Enums;

enum YesNo: string
{
    case YES = 'yes';
    case NO  = 'no';

    public static function fromLoose(string $v): self
    {
        $v = strtolower(trim($v));
        return ($v === 'yes' || $v === 'oui') ? self::YES : self::NO;
    }
}
