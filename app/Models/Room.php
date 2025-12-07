<?php

namespace App\Models;

use App\Enums\RoomIcon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'icon',
        'floor',
        'area',
        'color',
        'is_active',
        'order',
        'temperature',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'icon' => RoomIcon::class,
            'is_active' => 'boolean',
            'floor' => 'integer',
            'area' => 'decimal:2',
            'temperature' => 'decimal:2',
            'order' => 'integer',
        ];
    }

    /**
     * Get the user that owns the room.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the devices for the room.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
