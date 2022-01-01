<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataImportJobAborted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public array $record;

    public function __construct(array $record)
    {
        $this->record = $record;
    }

    public function broadcastOn() : PrivateChannel
    {
        return new PrivateChannel('data-import');
    }
}
