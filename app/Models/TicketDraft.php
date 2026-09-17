<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketDraft extends Model
{
    /** @use HasFactory<\Database\Factories\TicketDraftFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'payload', 'saved_at'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'saved_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
