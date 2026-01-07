<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Child extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'daycare_id',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'emergency_contact',
        'enrollment_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
        'enrollment_date' => 'date',
    ];

    /**
     * Get the daycare that the child belongs to.
     */
    public function daycare(): BelongsTo
    {
        return $this->belongsTo(Daycare::class);
    }

    /**
     * Get the parents associated with the child.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'child_parent', 'child_id', 'parent_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    /**
     * Get the child's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the child's age in years.
     */
    public function getAgeAttribute(): int
    {
        return $this->birth_date->diffInYears(now());
    }

    /**
     * Get the child's age in months.
     */
    public function getAgeInMonthsAttribute(): int
    {
        return $this->birth_date->diffInMonths(now());
    }
}