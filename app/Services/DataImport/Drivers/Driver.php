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
        if ($filters) {
            $data = collect($data)->filter(function ($record) use ($filters) : bool {
                foreach ($filters as $filter) {
                    if ($filter::handle($record)===false) {
                        return false;
                    }
                }
                return true;
            })->toArray();
        }
        return $data;
    }
}
