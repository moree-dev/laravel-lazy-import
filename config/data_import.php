<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Data Validation Strategy
    |--------------------------------------------------------------------------
    |
    | This config variable controls the validation strategy of data import service.
    | If it set to true, It will stop the rest of import process and throws and
    | exception. Otherwise, the driver will skip the wrong record and continue
    | the rest of the process. But it will log the records with error.
    |
    */
    "strict_validation" => (int) ENV("DATA_IMPORT_STRICT_VALIDATION", false),

    /*
    |--------------------------------------------------------------------------------
    | Drivers configurations
    |--------------------------------------------------------------------------------
    |
    | Wrap the configurations of each driver in an array with driver's name as index.
    | If you want to filter and skip some records, regardless to 'strict_validation'
    | you can define filters of each driver. just wrap filters class names into
    | [driver].filters as index. But be aware that each filter should implement
    | App\DataImport\Drivers\Contracts\Filter
    |
    */
    "drivers" => [
        \App\Services\DataImport\Drivers\Client::class => [
            "filters" => [
                \App\Services\DataImport\Drivers\Filters\FilterClientByAge::class,
                \App\Services\DataImport\Drivers\Filters\FilterClientByCreditCardPattern::class
            ]
        ]
    ]
];
