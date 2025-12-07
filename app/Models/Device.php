<?php

namespace App\Models;

use App\Enums\DeviceStatus;
use App\Enums\DeviceType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'type',
        'status',
        'mac_address',
        'ip_address',
        'is_online',
        'last_seen_at',
        'firmware_version',
        'settings',
        'power_consumption',
        'battery_level',
        'location',
        'icon',
        'order',
        'is_active',
        'room_id',
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
            'type' => DeviceType::class,
            'status' => DeviceStatus::class,
            'is_online' => 'boolean',
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
            'settings' => 'array',
            'power_consumption' => 'decimal:2',
            'battery_level' => 'integer',
            'order' => 'integer',
        ];
    }

    /**
     * Get the room that owns the device.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the user that owns the device.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
