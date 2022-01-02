<?php

namespace App\Services\DataImport\Drivers\Contracts;

interface Filter
{
    public static function handle(array &$record) : bool;
}
