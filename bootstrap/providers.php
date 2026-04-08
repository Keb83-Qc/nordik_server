<?php

return [
    App\Providers\AppServiceProvider::class,
    // App\Providers\HorizonServiceProvider::class, // Désactivé — Redis non disponible sur l'hébergement
    App\Providers\Filament\AdminPanelProvider::class,
    // App\Providers\Filament\ConseillerPanelProvider::class, // Désactivé — tout passe par /admin
];
