<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'parcels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ic',
        'recipient_name',
        'sender_name',
        'courier_id',
        'serial_number',
        'tracking_number',
        'parcel_size',
        'amount',
        'cod_id',
        'cod_amount',
        'notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'cod_amount' => 'decimal:2',
        'cod_id' => 'boolean',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the courier that owns the parcel.
     */
    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    /**
     * Check if parcel is pending/unclaimed.
     */
    public function isPending(): bool
    {
        return $this->status === 1;
    }

    /**
     * Check if parcel is claimed.
     */
    public function isClaimed(): bool
    {
        return $this->status === 2;
    }

    /**
     * Calculate parcel amount based on size.
     */
    public static function calculateAmount(string $size): float
    {
        return match ($size) {
            'Kecil' => 0.00,
            'Sederhana' => 1.00,
            'Besar' => 2.00,
            default => 0.00,
        };
    }

    /**
     * Get total amount (parcel size + COD).
     */
    public function getTotalAmountAttribute(): float
    {
        if ($this->isClaimed()) {
            return 0.00;
        }
        return $this->amount + $this->cod_amount;
    }

    /**
     * Scope a query to only include pending parcels.
     */
    public function scopePending($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to only include claimed parcels.
     */
    public function scopeClaimed($query)
    {
        return $query->where('status', 2);
    }

    /**
     * Scope a query to filter by IC.
     */
    public function scopeByIc($query, $ic)
    {
        return $query->where('ic', $ic);
    }

    /**
     * Scope a query to filter parcels with recipients (registered users/students).
     */
    public function scopeWithRecipient($query)
    {
        return $query->whereNotNull('ic');
    }

    /**
     * Scope a query to filter parcels without recipients (walk-ins).
     */
    public function scopeWithoutRecipient($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('recipient_name')
              ->orWhere(function ($subQ) {
                  $subQ->whereNull('recipient_name')
                       ->whereNull('ic');
              });
        });
    }
}
