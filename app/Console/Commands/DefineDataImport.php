<?php

namespace App\Console\Commands;

use App\Facades\DataImport;
use Illuminate\Console\Command;

class DefineDataImport extends Command
{
    /**
     * @var string
     */
    protected $signature = 'data-import:define {storage_path} {driver}';

    /**
     * @var string
     */
    protected $description = 'Define new data import file';

    public function handle() : void
    {
        try {
            $file = storage_path($this->argument('storage_path'));
            $driver = $this->argument('driver');
            if(!file_exists($file))
                throw new \InvalidArgumentException(__('data_import.file_not_found', ['file' => $file]));

            DataImport::define($file, $driver);
            $this->info("data import process for import file {$file} using {$driver} driver has been defined successfully!");
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
