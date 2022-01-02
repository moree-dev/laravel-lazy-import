<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DataImportProcessFailed
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function broadcastOn() : PrivateChannel
    {
        return new PrivateChannel('data-import');
    }
}
