<?php

namespace App\Filament\Conseiller\Widgets;

use Filament\Widgets\Widget;

class LatestProceduresWidget extends Widget
{
    protected static string $view = 'filament.conseiller.widgets.latest-procedures';
    protected int|string|array $columnSpan = 2;
}
