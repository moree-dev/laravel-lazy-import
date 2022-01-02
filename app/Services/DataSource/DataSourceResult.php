<?php

declare(strict_types=1);

namespace App\Services\DataSource;

use Illuminate\Contracts\Support\Arrayable;

class DataSourceResult implements Arrayable
{
    protected array $data;

    protected int $position;

    protected bool $isFinished;

    public function __construct(array $data, int $position, bool $is_finished = false)
    {
        $this->data = $data;
        $this->position = $position;
        $this->isFinished = $is_finished;
    }

    public function toArray() : array
    {
        return $this->data;
    }

    public function getPosition() : int
    {
        return $this->position;
    }

    public function isFinished() : bool
    {
        return $this->isFinished;
    }
}
