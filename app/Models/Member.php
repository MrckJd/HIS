<?php

namespace App\Models;

use App\Filament\AvatarProvider\UiAvatarsProvider;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Member extends Model
{
    use HasUlids;

    protected $fillable = [
        'household_id',
        'avatar',
        'code',
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

    public static function booted():void
    {
        static::creating(function ($model) {
            if (is_null($model->code)) {
                do{
                    $generatedCode = fake()->bothify('????####');
                } while (static::where('code', $generatedCode)->exists());

                $model->code = $generatedCode;
            }
        });

        static::deleting(function ($model) {
            if ($model->avatar) {
                Storage::disk('public')->delete($model->avatar);
            }
        });
    }

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function qrCode(): Attribute
    {
        return Attribute::make(
            get: fn () => QrCode::size(45)->generate($this->code ? $this->code : ' No QR')
        );
    }

    public function avatarUrl() : Attribute
    {
        return Attribute::make(
            fn ():string => $this->avatar ?? (new UiAvatarsProvider)->get($this)
        );
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->surname . ', ' .
                $this->first_name . ' ' .
                ($this->middle_name ? $this->middle_name . ' ' : '') .
                ($this->suffix ? $this->suffix : ''))
        );
    }

    public function memberServices(): HasMany
    {
        return $this->hasMany(MemberServices::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'member_services');
    }

}
