<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
    ];

    public function memberServices(): HasMany
    {
        return $this->hasMany(MemberServices::class);
    }
}
