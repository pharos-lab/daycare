<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Daycare extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'director_id',
        'name',
        'address',
        'city',
        'postal_code',
        'country',
        'phone',
        'email',
        'capacity',
        'opening_hours',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'opening_hours' => 'array',
            'capacity' => 'integer',
        ];
    }

    /**
     * Get the director that owns the daycare.
     */
    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    /**
     * Get full address as string
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->postal_code} {$this->city}, {$this->country}";
    }

    /**
     * Check if daycare belongs to a specific director
     */
    public function belongsToDirector(int $directorId): bool
    {
        return $this->director_id === $directorId;
    }
}