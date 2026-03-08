<?php

namespace App\Filament\Conseiller\Widgets;

use Filament\Widgets\Widget;

class UsefulDocumentsWidget extends Widget
{
    protected static string $view = 'filament.conseiller.widgets.useful-documents';
    protected int|string|array $columnSpan = 1;
}
