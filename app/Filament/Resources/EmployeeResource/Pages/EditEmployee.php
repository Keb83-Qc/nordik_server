<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use App\Filament\Pages\BaseEditRecord;

class EditEmployee extends BaseEditRecord
{
    protected static string $resource = EmployeeResource::class;
}
