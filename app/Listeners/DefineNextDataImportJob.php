<?php

namespace App\Listeners;

use App\Events\DataImportJobProcessed;
use App\Jobs\ProcessDataImport;

class DefineNextDataImportJob
{
    public function handle(DataImportJobProcessed $event)
    {
        ProcessDataImport::dispatchIf($event->finished===false, $event->record['id']);
    }
}
