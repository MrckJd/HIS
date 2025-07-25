<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    use HasUlids;

    protected $fillable = [
        'household_id',
        'role',
        'first_name',
        'middle_name',
        'surname',
        'suffix',
        'birth_date',
        'gender',
        'precinct_no',
        'cluster_no',
        'is_leader',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function getFullNameAttribute(): string
    {
    return trim($this->first_name . ' ' .
               ($this->middle_name ? $this->middle_name . ' ' : '') .
               $this->surname . ' ' .
               ($this->suffix ? $this->suffix : ''));
    }


}
