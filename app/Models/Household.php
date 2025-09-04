<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->user_id = request()->user()->id;
        });
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class)
            ->where(function($query){
                $query->whereNull('is_leader')
                      ->orWhere('is_leader', false);
            });
    }

    public function listmembers(): HasMany
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
