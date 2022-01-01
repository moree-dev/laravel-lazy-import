<?php

namespace App\Listeners;

use App\Events\NewDataImportDefined;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DefineNewDataImportJob
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\NewDataImportDefined  $event
     * @return void
     */
    public function handle(NewDataImportDefined $event)
    {
        //
    }
}
