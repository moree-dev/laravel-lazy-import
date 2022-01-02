<?php

namespace App\Console\Commands;

use App\Facades\DataImport;
use Illuminate\Console\Command;

class AbortDataImport extends Command
{
    /**
     * @var string
     */
    protected $signature = 'data-import:abort {id}';

    /**
     * @var string
     */
    protected $description = 'Abort a data import process';

    public function handle() : void
    {
        try {
            $id = (int) $this->argument('id');
            DataImport::abort($id);
            $this->info("data import process #{$id} has been aborted successfully!");
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
