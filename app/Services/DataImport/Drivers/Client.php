<?php

namespace App\Services\DataImport\Drivers;

use App\Exceptions\DataImportInvalidDataException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Client extends Driver
{
    /**
     * @throws DataImportInvalidDataException
     */
    public function handle(array $data): bool
    {
        $data = $this->filterData($data);

        DB::beginTransaction();

        foreach ($data as $record) {
            $validated_record = $this->validate($record);
            if ($validated_record) {
                $this->insertRecord($validated_record);
            }
        }

        DB::commit();

        return true;
    }

    /**
     * @throws DataImportInvalidDataException
     */
    protected function validate(array $record) : array|bool
    {
        try {
            $result = Validator::make($record, [
                'name' => 'required|string',
                'address' => 'present|string|nullable',
                'checked' => 'required|boolean',
                'description' => 'present|string|nullable',
                'interest' => 'present|string|nullable',
                'date_of_birth' => 'present|date|nullable',
                'email' => 'required|email',
                'account' => 'required|string',
                'credit_card' => 'present|array|nullable',
                'credit_card.type' => 'required|string',
                'credit_card.number' => 'required|string',
                'credit_card.name' => 'required|string',
                'credit_card.expirationDate' => ["required", "regex:/^([0][0-9]|[1][0-2])\/[0-9]{2}$/i"],
            ])->validated();
        } catch (ValidationException $exception) {
            Log::error("data import incorrect data was found!", ["driver" => self::class, 'data' => $record, 'errors' => $exception->errors()]);
            if ($this->strictValidation()) {
                throw new DataImportInvalidDataException(__("data_import.invalid_record", ["message" => $exception->getMessage()]));
            } else {
                return false;
            }
        }
        return $result;
    }

    protected function insertRecord(array $record) : void
    {
        $client_id = DB::table('clients')->insertGetId([
            'name' => $record['name'],
            'address' => $record['address'],
            'checked' => $record['checked'],
            'description' => $record['description'],
            'interest' => $record['interest'],
            'date_of_birth' => Carbon::make($record['date_of_birth']),
            'email' => $record['email'],
            'account' => $record['account'],
        ]);
        DB::table('credit_cards')->insert([
            'client_id' => $client_id,
            'type' => $record['credit_card']['type'],
            'number' => $record['credit_card']['number'],
            'name' => $record['credit_card']['name'],
            'expiration_date' => $record['credit_card']['expirationDate'],
        ]);
    }


}
