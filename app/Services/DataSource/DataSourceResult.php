<?php

declare(strict_types=1);

namespace App\Services\DataSource;

use Illuminate\Contracts\Support\Arrayable;

class DataSourceResult implements Arrayable
{
    protected array $data;

    protected int $position;

    public function __construct(array $data, int $position)
    {
        $this->data = $data;
        $this->position = $position;
    }

    public function toArray() : array
    {
        return $this->data;
    }

    public function getPosition() : int
    {
        return $this->position;
    }
}
