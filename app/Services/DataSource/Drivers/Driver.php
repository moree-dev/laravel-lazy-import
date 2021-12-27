<?php

declare(strict_types=1);

namespace App\Services\DataSource\Drivers;

use App\Exceptions\DataSourceException;
use App\Services\DataSource\DataSourceResult;
use Illuminate\Support\Facades\Log;

abstract class Driver
{
    abstract public function read(string $path_to_file, int $offset, int $character_length) : DataSourceResult;

    /**
     * @throws DataSourceException
     */
    protected function readPart(string $path_to_file, int $offset, int $character_length) : string
    {
        try {
            $file = fopen($path_to_file, "r");
        } catch (\Exception $e) {
            Log::error("tried to read an unreadable file", ["file" => $path_to_file]);
            throw new DataSourceException(__("data_source.unreadable_file", ['file' => $path_to_file]), 103);
        }
        if ($file === false) {
            Log::error("tried to read an unreadable file", ["file" => $path_to_file]);
            throw new DataSourceException(__("data_source.unreadable_file", ['file' => $path_to_file]), 103);
        }
        return stream_get_contents($file, $character_length, $offset);
    }
}
