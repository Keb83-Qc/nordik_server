<?php

namespace App\Filament\Resources\SlideResource\Pages;

use App\Filament\Resources\SlideResource;
use Filament\Actions;
use App\Filament\Pages\BaseEditRecord;

class EditSlide extends BaseEditRecord
{
    // 1. Cette ligne est indispensable pour que le formulaire comprenne le JSON

    protected static string $resource = SlideResource::class;
}
