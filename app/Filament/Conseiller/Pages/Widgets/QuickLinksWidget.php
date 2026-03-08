<?php

namespace App\Filament\Conseiller\Widgets;

use Filament\Widgets\Widget;

class QuickLinksWidget extends Widget
{
    protected static string $view = 'filament.conseiller.widgets.quick-links';
    protected int|string|array $columnSpan = 1;
}
