<?php

namespace Tests\Feature;

use App\Events\DataImportPartProcessed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Exceptions\DataImportException;
use App\Facades\DataImport;
use App\Jobs\ProcessDataImport;
use App\Services\DataImport\Drivers\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class DataImportTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_non_existing_file()
    {
        $this->expectException(DataImportException::class);
        $this->expectExceptionCode(101);
        DataImport::define(Str::random(20), 'unknown-driver');
    }

    public function test_wrong_driver_file()
    {
        $this->expectException(DataImportException::class);
        $this->expectExceptionCode(102);
        DataImport::define(storage_path('customers.json'), 'unknown-driver');
    }

    public function test_client_import_can_be_defined()
    {
        Bus::fake();
        DataImport::define(storage_path('customers.json'), Client::class);
        Bus::assertDispatched(ProcessDataImport::class);
    }

    public function test_client_import_can_be_processed()
    {
        Event::fake();
        $process = \App\Models\DataImport::create([
            'file_path' => storage_path('customers.json'),
            'driver' => Client::class,
            'last_position' => 0
        ]);
        DataImport::process($process->id);
        Event::assertDispatched(DataImportPartProcessed::class);
    }
}
