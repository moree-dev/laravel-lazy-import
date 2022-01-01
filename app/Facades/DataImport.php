<?php

namespace App\Facades;

use App\Exceptions\DataImportException;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void|DataImportException define(string $path_to_file, string $driver)
 * @method static void|DataImportException process(int $id)
 */
class DataImport extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\DataImport\DataImport::class;
    }
}
