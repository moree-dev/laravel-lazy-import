<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $file_path
 * @property string $driver
 * @property string $status enum pending|running|paused|aborted
 * @property int $last_position
 * @property Carbon $ran_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class DataImport extends Model
{
    protected $guarded = [];
}
