<?php

namespace App\Services\DataImport\Drivers;

use App\Exceptions\DataImportInvalidDataException;

abstract class Driver
{
    /**
     * @throws DataImportInvalidDataException
     */
    abstract public function handle(array $data) : bool;
}
