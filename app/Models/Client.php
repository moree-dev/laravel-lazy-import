<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Class Client Model
 * @property int $id
 * @property string $name
 * @property ?string $address
 * @property bool $checked
 * @property ?string $description
 * @property ?string $interest
 * @property ?Carbon $date_of_birth
 * @property string $email
 * @property string $account
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Client extends Model
{
    protected $guarded = [];

    public function creditCard() : HasOne
    {
        return $this->hasOne(CreditCard::class);
    }
}
