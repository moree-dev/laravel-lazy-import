<?php

namespace App\Services\DataImport\Drivers\Filters;

use App\Services\DataImport\Drivers\Client;
use App\Services\DataImport\Drivers\Contracts\Filter;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;

class FilterClientByAge implements Filter
{
    protected static int $min;
    protected static int $max;
    protected static bool $allowUnknown;
    protected static bool $settingsRetrieved = false;

    public static function handle(array &$record): bool
    {
        self::retrieveSettings();
        $record['date_of_birth'] = self::parseDate($record['date_of_birth']);
        if (self::$min || self::$max) {
            if (is_null($record['date_of_birth'])) {
                return self::$allowUnknown;
            } else {
                $age = (int) Carbon::createFromFormat('Y-m-d', $record['date_of_birth'])
                    ->diff(Carbon::now())
                    ->format('%y');
                return (($age >= self::$min || !self::$min) && ($age <= self::$max || !self::$max));
            }
        }
        return true;
    }

    protected static function retrieveSettings()
    {
        if (!self::$settingsRetrieved) {
            self::$settingsRetrieved = true;
            self::$min = (int) config('data_import.drivers.'.Client::class.'.min_client_age', false);
            self::$max = (int) config('data_import.drivers.'.Client::class.'.max_client_age', false);
            self::$allowUnknown = config('data_import.drivers.'.Client::class.'.allow_unknown_age', true);
        }
    }

    protected static function parseDate(string|null $str) : string|null
    {
        if (is_null($str)) {
            return null;
        } else {
            try {
                return Carbon::parse($str)->format('Y-m-d');
            } catch (InvalidFormatException $exception) {
                return Carbon::createFromFormat('d/m/Y', $str)->format('Y-m-d');
            }
        }
    }
}
