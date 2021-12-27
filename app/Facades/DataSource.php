<?php

namespace App\Facades;

use App\Exceptions\DataSourceException;
use App\Services\DataSource\DataSourceResult;
use Illuminate\Support\Facades\Facade;

/**
 * @method static DataSourceResult|DataSourceException read(string $path_to_file, string $format, int $offset = 0)
 */
class DataSource extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\DataSource\DataSource::class;
    }
}
