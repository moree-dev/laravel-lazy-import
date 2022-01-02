<?php

namespace App\Services\DataImport\Drivers\Filters;

use App\Services\DataImport\Drivers\Client;
use App\Services\DataImport\Drivers\Contracts\Filter;
use Illuminate\Support\Facades\Log;

class FilterClientByCreditCardPattern implements Filter
{
    protected static string|null $pattern;
    protected static bool $settingsRetrieved = false;

    public static function handle(array &$record): bool
    {
        self::retrieveSettings();
        return !self::$pattern || preg_match(self::$pattern, $record['credit_card']['number']);
    }

    protected static function retrieveSettings()
    {
        if (!self::$settingsRetrieved) {
            self::$settingsRetrieved = true;
            self::$pattern = config('data_import.drivers.'.Client::class.'.card_number_pattern', null);
        }
    }
}
