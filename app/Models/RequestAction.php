<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestAction extends Model
{
    use HasUlids;

    protected $fillable = [
        'action_type',
        'reason',
        'document',
        'status',
        'member_id',
        'user_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'json',
    ];

    public function member():BelongsTo
    {
        return $this->belongsTo(Member::class);

    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
