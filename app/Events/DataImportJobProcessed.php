<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataImportJobProcessed
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public array $record;
    public bool $finished;

    public function __construct(array $record, bool $finished)
    {
        $this->record = $record;
        $this->finished = $finished;
    }

    public function broadcastOn() : PrivateChannel
    {
        return new PrivateChannel('data-import');
    }
}
