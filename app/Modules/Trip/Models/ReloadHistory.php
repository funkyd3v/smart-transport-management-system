<?php

declare(strict_types=1);

namespace App\Modules\Trip\Models;

use App\Modules\Driver\Models\Driver;
use App\Modules\Truck\Models\Truck;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReloadHistory extends Model
{
    public $timestamps = false;

    protected $table = 'reload_history';

    protected $fillable = [
        'trip_id',
        'truck_id',
        'driver_id',
        'reload_point',
        'reloaded_at',
        'note',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'reloaded_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function getReloadAmountAttribute(): ?float
    {
        $metadata = $this->decodeMetadata();

        if (! array_key_exists('reload_amount', $metadata) || $metadata['reload_amount'] === null || $metadata['reload_amount'] === '') {
            return null;
        }

        return (float) $metadata['reload_amount'];
    }

    public function getNoteTextAttribute(): ?string
    {
        $metadata = $this->decodeMetadata();

        if (array_key_exists('note', $metadata)) {
            $note = $metadata['note'];

            return filled($note) ? (string) $note : null;
        }

        return filled($this->attributes['note'] ?? null) ? (string) $this->attributes['note'] : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeMetadata(): array
    {
        $note = (string) ($this->attributes['note'] ?? '');

        if ($note === '') {
            return [];
        }

        $decoded = json_decode($note, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
