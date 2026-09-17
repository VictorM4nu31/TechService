<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'name',
        'brand',
        'model',
        'serial_number',
        'type',
    ];

    public function scopeVisibleToUser(Builder $query, User $user): Builder
    {
        return $query->when(
            ! $user->hasAnyRole(['Admin', 'Agente']),
            fn (Builder $q) => $q->where('user_id', $user->id),
        );
    }

    public function isVisibleTo(User $user): bool
    {
        if ($user->hasAnyRole(['Admin', 'Agente'])) {
            return true;
        }

        return $this->user_id === $user->id;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }
}
