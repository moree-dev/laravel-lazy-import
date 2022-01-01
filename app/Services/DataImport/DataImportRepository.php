<?php

namespace App\Services\DataImport;

interface DataImportRepository
{
    public function create(array $data): array;

    public function update(int $id, array $data): array;

    public function getById(int $id): array;

    public function delete(int $id): bool;
}
