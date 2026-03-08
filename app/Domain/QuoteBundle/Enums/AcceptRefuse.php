<?php

namespace App\Domain\QuoteBundle\Enums;

enum AcceptRefuse: string
{
    case ACCEPT = 'accept';
    case REFUSE = 'refuse';

    public static function fromLoose(string $v): self
    {
        $v = strtolower(trim($v));
        return ($v === 'accept') ? self::ACCEPT : self::REFUSE;
    }
}
