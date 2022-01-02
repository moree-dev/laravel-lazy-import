<?php

namespace App\Services\DataImport\Drivers;

use App\Exceptions\DataImportInvalidDataException;

abstract class Driver
{
    private bool $strictValidation;

    public function __construct()
    {
        $this->strictValidation = config('data_import.strict_validation', false);
    }

    /**
     * @throws DataImportInvalidDataException
     */
    abstract public function handle(array $data) : bool;

    protected function strictValidation() : bool
    {
        return $this->strictValidation;
    }

    protected function filterData(array $data) : array
    {
        $filters = (array) config('data_import.drivers.'.$this::class.'.filters', []);
        foreach ($data as $key => &$row) {
            foreach ($filters as $filter) {
                if ($filter::handle($row)===false) {
                    unset($data[$key]);
                }
            }
        }
        return array_values($data);
    }
}
