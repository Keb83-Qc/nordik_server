<?php

namespace App\Domain\QuoteBundle\Enums;

enum PropertyType: string
{
    case MAISON = 'maison';
    case CONDO = 'condo';
    case APPARTEMENT = 'appartement';
}
