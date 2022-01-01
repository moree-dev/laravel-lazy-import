<?php

namespace App\Listeners;

use App\Events\NewDataImportDefined;
use App\Jobs\ProcessDataImport;

class DefineNewDataImportJob
{
    public function handle(NewDataImportDefined $event)
    {
        ProcessDataImport::dispatch($event->record['id']);
    }
}
