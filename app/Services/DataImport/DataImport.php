<?php

namespace App\Services\DataImport;

use App\Events\DataImportJobFailed;
use App\Events\DataImportJobFinished;
use App\Events\DataImportJobStarted;
use App\Events\NewDataImportDefined;
use App\Exceptions\DataImportException;
use App\Exceptions\DataImportInvalidDataException;
use App\Exceptions\DataSourceException;
use App\Facades\DataSource;
use App\Services\DataImport\Drivers\Driver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DataImport
{
    protected DataImportRepository $repository;

    public function __construct(DataImportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $path_to_file
     * @param string $driver
     * @throws DataImportException
     */
    public function define(string $path_to_file, string $driver) : void
    {
        if (!file_exists($path_to_file)) {
            Log::error("tried to read a non-existing file!", ["file" => $path_to_file]);
            throw new DataImportException(
                __("data_import.file_does_not_exist", ["file" => $path_to_file]),
                101
            );
        }

        $driver = $this->retrieveDriver($driver);

        $record = $this->repository->create([
            'file_path' => $path_to_file,
            'driver' => $driver::class
        ]);

        if ($record) {
            NewDataImportDefined::dispatch($record);
        } else {
            Log::error("a failure data import definition called!", ["file" => $path_to_file]);
            throw new DataImportException(
                __("data_import.failed_to_define_new_import", ["file" => $path_to_file, 'driver' => $driver]),
                103
            );
        }
    }

    /**
     * @param int $id
     * @throws DataImportException
     */
    public function handleStep(int $id)
    {
        $record = $this->repository->getById($id);
        if ($record) {
            try {
                $data = DataSource::read($record['file_path'], 'json');
                $driver = $this->retrieveDriver($record['driver']);
                $finished = $driver->handle($data->toArray());
                $this->updateRecordAfterRun($record, $data->getPosition());

                if ($finished) {
                    $this->dispatchFinished($record);
                } else {
                    $this->dispatchNextStep($record);
                }
            } catch (DataSourceException $exception) {
                DataImportJobFailed::dispatch($id);
                throw new DataImportException(
                    __('data_import.data_source_error', ['id' => $id, 'message' => $exception->getMessage()]),
                    ["id" => $id],
                    105
                );
            } catch (DataImportInvalidDataException $exception) {
                DataImportJobFailed::dispatch($id);
                throw new DataImportException(
                    __('data_import.invalid_data', ['id' => $id, 'message' => $exception->getMessage()]),
                    ["id" => $id],
                    106
                );
            } catch (\Throwable $exception) {
                DataImportJobFailed::dispatch($id);
                throw new DataImportException(
                    __('data_import.unknown_error', ['id' => $id, 'message' => $exception->getMessage()]),
                    107,
                );
            }
        } else {
            DataImportJobFailed::dispatch($id);
            Log::error("tried to retrieve a non-existing data import process!", ["id" => $id]);
            throw new DataImportException(__("data_import.process_does_not_exist", ["id" => $id]), 104);
        }
    }

    protected function updateRecordAfterRun(array $record, int $position) : void
    {
        if($record['status']==='pending')
            DataImportJobStarted::dispatch($record);

        $this->repository->update($record['id'], [
            'status' => 'running',
            'last_position' => $position,
            'ran_at' => Carbon::now()->toString()
        ]);
    }

    protected function dispatchNextStep(array $record) : void
    {

    }

    protected function dispatchFinished(array $record) : void
    {
        DataImportJobFinished::dispatch($record);

        $this->repository->update($record['id'], [
            'status' => 'finished',
            'ran_at' => Carbon::now()->toString()
        ]);
    }

    /**
     * @throws DataImportException
     */
    protected function retrieveDriver(string $driver) : Driver
    {
        $target_driver_class = "App\Services\DataImport\Drivers\\".Str::studly($driver);
        if (class_exists($target_driver_class)) {
            return new $target_driver_class();
        } else {
            Log::error("an unknown driver called!", ["driver" => $target_driver_class]);
            throw new DataImportException(
                __("data_import.driver_does_not_exist", ["driver" => $target_driver_class]),
                102
            );
        }
    }
}
