<?php

namespace App\Services\DataImport;

use App\Events\DataImportProcessAborted;
use App\Events\DataImportProcessFailed;
use App\Events\DataImportProcessFinished;
use App\Events\DataImportPartProcessed;
use App\Events\DataImportProcessStarted;
use App\Events\NewDataImportDefined;
use App\Exceptions\DataImportException;
use App\Exceptions\DataImportInvalidDataException;
use App\Exceptions\DataSourceException;
use App\Facades\DataSource;
use App\Services\DataImport\Drivers\Driver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DataImport
{
    protected DataImportRepository $repository;

    public function __construct(DataImportRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
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
            'driver' => $driver::class,
            'last_position' => 0
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
     * @throws DataImportException
     */
    public function process(int $id) : void
    {
        $process = $this->repository->getById($id);
        if ($process) {
            if($process['status']==='aborted'){
                return;
            }

            try {
                $data = DataSource::read($process['file_path'], $process['last_position']);
                $driver = $this->retrieveDriver($process['driver']);
                $driver->handle($data->toArray());
                $this->handleNext($process,  $data->getPosition(), $data->isFinished());
            } catch (DataSourceException $exception) {
                DataImportProcessFailed::dispatch($id);
                throw new DataImportException(
                    __('data_import.data_source_error', ['id' => $id, 'message' => $exception->getMessage()]),
                    ["id" => $id],
                    105
                );
            } catch (DataImportInvalidDataException $exception) {
                DataImportProcessFailed::dispatch($id);
                throw new DataImportException(
                    __('data_import.invalid_data', ['id' => $id, 'message' => $exception->getMessage()]),
                    ["id" => $id],
                    106
                );
            } catch (\Throwable $exception) {
                DataImportProcessFailed::dispatch($id);
                throw new DataImportException(
                    __('data_import.unknown_error', ['id' => $id, 'message' => $exception->getMessage()]),
                    107,
                );
            }
        } else {
            DataImportProcessFailed::dispatch($id);
            Log::error("tried to retrieve a non-existing data import process!", ["id" => $id]);
            throw new DataImportException(__("data_import.process_does_not_exist", ["id" => $id]), 104);
        }
    }

    /**
     * @throws DataImportException
     */
    public function abort(int $id) : void
    {
        $process = $this->repository->getById($id);
        if ($process) {
            $this->repository->update($id, ['status' => 'aborted']);
            DataImportProcessAborted::dispatch($process);
        } else {
            Log::error("tried to abort a non-existing data import job!", ["id" => $id]);
            throw new DataImportException(
                __("data_import.process_does_not_exist", ["id" => $id]),
                104
            );
        }
    }

    /**
     * @throws DataImportException
     */
    public function get(int $id) : array
    {
        $process = $this->repository->getById($id);
        if (!$process) {
            Log::error("tried to abort a non-existing data import job!", ["id" => $id]);
            throw new DataImportException(
                __("data_import.process_does_not_exist", ["id" => $id]),
                104
            );
        }
        return $process;
    }

    protected function handleNext(array $process, int $position, bool $finished) : void
    {
        if ($process['status']==='pending') {
            DataImportProcessStarted::dispatch($process);
        }

        if ($finished) {
            $process = $this->repository->update($process['id'], [
                'status' => 'finished',
                'last_position' => $position,
                'ran_at' => Carbon::now()
            ]);
            DataImportProcessFinished::dispatch($process);
        } else {
            $process = $this->repository->update($process['id'], [
                'last_position' => $position,
                'ran_at' => Carbon::now()
            ]);
        }

        DataImportPartProcessed::dispatch($process, $finished);
    }

    protected function handleFinished(array $process) : void
    {
        DataImportProcessFinished::dispatch($process);

        $this->repository->update($process['id'], [
            'status' => 'finished',
            'ran_at' => Carbon::now()
        ]);
    }

    /**
     * @throws DataImportException
     */
    protected function retrieveDriver(string $driver) : Driver
    {
        if (class_exists($driver)) {
            return new $driver();
        } else {
            Log::error("an unknown driver called!", ["driver" => $driver]);
            throw new DataImportException(
                __("data_import.driver_does_not_exist", ["driver" => $driver]),
                102
            );
        }
    }
}
