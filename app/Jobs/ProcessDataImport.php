<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Facades\DataImport;

class ProcessDataImport implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected array $record;

    public function __construct(array $record)
    {
        $this->record = $record;
    }

    public int $uniqueFor = 3600;

    public function uniqueId() : string
    {
        return $this->record['id'].'.'.$this->record['last_position'];
    }

    public function handle(int $id) : void
    {
        DataImport::process($id);
    }
}
