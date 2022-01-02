<?php

namespace App\Services\DataImport;

use App\Models\DataImport;

class DataImportEloquent implements DataImportRepository
{
    public function create(array $data) : ?array
    {
        return DataImport::create($data)?->toArray();
    }

    public function update(int $id, array $data) : ?array
    {
        $model = DataImport::find($id);
        if ($model) {
            $model->update($data, ['id' => $id]);
            return $model->toArray();
        }
        return null;
    }

    public function getById(int $id) : ?array
    {
        return DataImport::find($id)?->toArray();
    }

    public function delete(int $id) : bool
    {
        return DataImport::find($id)->delete();
    }
}
