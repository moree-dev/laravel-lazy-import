<?php

namespace App\Providers;

use App\Events\DataImportProcessAborted;
use App\Events\DataImportPartProcessed;
use App\Events\DataImportProcessFailed;
use App\Events\DataImportProcessFinished;
use App\Events\DataImportProcessStarted;
use App\Events\NewDataImportDefined;
use App\Listeners\DefineNewDataImportJob;
use App\Listeners\DefineNextDataImportJob;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        NewDataImportDefined::class => [
            DefineNewDataImportJob::class
        ],
        DataImportProcessStarted::class => [
            //Send notification or something
        ],
        DataImportPartProcessed::class => [
            DefineNextDataImportJob::class
        ],
        DataImportProcessFinished::class => [
            //Send notification or something
        ],
        DataImportProcessFailed::class => [
            //Send notification or something
        ],
        DataImportProcessAborted::class => [
            //Send notification or something
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
