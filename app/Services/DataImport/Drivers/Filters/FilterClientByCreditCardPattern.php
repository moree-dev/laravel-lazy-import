<?php

namespace App\Services\DataImport\Drivers\Filters;

use App\Services\DataImport\Drivers\Contracts\Filter;

class FilterClientByCreditCardPattern implements Filter
{
    public static function handle(array $record): bool
    {
        return true;
    }
}
