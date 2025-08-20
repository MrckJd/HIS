<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Household extends Model
{
    use HasUlids;

    protected $fillable = [
        'municipality',
        'baranggay',
        'purok',
        'address',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class)
            ->orderBy('is_leader', 'desc');
    }

    public function leader(): HasOne
    {
        return $this->hasOne(Member::class)
               ->where('is_leader', true);
    }

    public function getLeaderNameAttribute(): string
    {
        $leader = $this->leader;

        if (!$leader) {
            return '(No Leader)';
        }

        return trim($leader->first_name . ' ' .
                    ($leader->middle_name ? $leader->middle_name . ' ' : '') .
                    $leader->surname . ' ' .
                    ($leader->suffix ? $leader->suffix : ''));
    }
}
