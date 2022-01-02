<?php

namespace App\Listeners;

use App\Events\DataImportPartProcessed;
use App\Jobs\ProcessDataImport;

class DefineNextDataImportJob
{
    public function handle(DataImportPartProcessed $event)
    {
        if ($event->finished===false) {
            ProcessDataImport::dispatch($event->record);
        }
    }
}
