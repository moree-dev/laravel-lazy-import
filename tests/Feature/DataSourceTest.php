<?php

namespace Tests\Feature;

use App\Exceptions\DataSourceException;
use App\Facades\DataSource;
use App\Services\DataSource\DataSourceResult;
use Illuminate\Support\Str;
use Tests\TestCase;

class DataSourceTest extends TestCase
{
    public function test_non_existing_file()
    {
        $this->expectException(DataSourceException::class);
        $this->expectExceptionCode(101);
        DataSource::read(Str::random(20), 'json');
    }

    public function test_wrong_driver_file()
    {
        $this->expectException(DataSourceException::class);
        $this->expectExceptionCode(102);
        DataSource::read(storage_path('customers.json'), 'non-existing-driver');
    }

    public function test_json_driver_character_length_exception()
    {
        config(['data_source.character_length' => 1]);
        $this->expectException(DataSourceException::class);
        $this->expectExceptionCode(104);
        DataSource::read(storage_path('customers.json'), 'json');
    }

    public function test_json_driver_works_normally()
    {
        config(['data_source.character_length' => 2000]);
        $result = DataSource::read(storage_path('customers.json'), 'json');
        $result2 = DataSource::read(storage_path('customers.json'), 'json', $result->getPosition());
        $this->assertInstanceOf(DataSourceResult::class, $result);
        $this->assertIsArray($result->toArray());
        $this->assertArrayHasKey(0, $result->toArray());
        $this->assertNotEmpty($result->getPosition());
        $this->assertInstanceOf(DataSourceResult::class, $result2);
        $this->assertIsArray($result2->toArray());
        $this->assertArrayHasKey(0, $result2->toArray());
        $this->assertNotEmpty($result2->getPosition());
    }
}
