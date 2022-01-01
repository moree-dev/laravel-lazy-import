<?php

declare(strict_types=1);

namespace App\Services\DataSource;

use App\Exceptions\DataSourceException;
use App\Services\DataSource\Drivers\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DataSource
{
    /**
     * @throws DataSourceException
     */
    public function read(string $path_to_file, int $offset = 0) : DataSourceResult
    {
        if (!file_exists($path_to_file)) {
            Log::error("tried to read a non-existing file!", ["file" => $path_to_file]);
            throw new DataSourceException(__("data_source.file_does_not_exist", ["file" => $path_to_file]), 101);
        }

        $extension = pathinfo($path_to_file, PATHINFO_EXTENSION);

        $driver = $this->retrieveDriver($extension);
        $character_length = config("data_source.character_length");

        return $driver->read($path_to_file, $offset, $character_length);
    }

    /**
     * @throws DataSourceException
     */
    protected function retrieveDriver(string $format) : Driver
    {
        $target_driver_class = "App\Services\DataSource\Drivers\\".Str::studly($format);
        if (class_exists($target_driver_class)) {
            return new $target_driver_class();
        } else {
            Log::error("an unknown driver called!", ["driver" => $target_driver_class]);
            throw new DataSourceException(__("data_source.driver_does_not_exist", ["driver" => $target_driver_class]), 102);
        }
    }
}
