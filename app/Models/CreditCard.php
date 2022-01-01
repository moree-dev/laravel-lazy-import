<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class CreditCard Model
 * @property int $id
 * @property int $client_id
 * @property string $type
 * @property string $number
 * @property string $name
 * @property int $expiration_month
 * @property int $expiration_year
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CreditCard extends Model
{
    protected $guarded = [];

    public function client() : BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
