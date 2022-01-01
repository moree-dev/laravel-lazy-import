<?php

namespace App\Services\DataImport\Drivers;

class Client extends Driver
{
    public function handle(array $data): bool
    {
        // TODO: save data to database.
        return true;
    }
}
