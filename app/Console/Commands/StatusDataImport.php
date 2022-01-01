<?php

namespace App\Console\Commands;

use App\Facades\DataImport;
use Illuminate\Console\Command;

class StatusDataImport extends Command
{
    /**
     * @var string
     */
    protected $signature = 'data-import:status {id}';

    /**
     * @var string
     */
    protected $description = 'Get the status of a data import process';

    public function handle() : void
    {
        try {
            $id = (int) $this->argument('id');
            $record = DataImport::get($id);
            $this->info("status: {$record['status']}\nposition: {$record['last_position']}\nlast_run: {$record['ran_at']}\nfile:{$record['file_path']}");
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
