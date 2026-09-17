<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedView extends Model
{
    /** @use HasFactory<\Database\Factories\SavedViewFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'filters', 'is_shared'];

    protected function casts(): array
    {
        return ['filters' => 'array', 'is_shared' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
