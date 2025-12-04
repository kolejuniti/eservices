<?php

namespace App\Enums;

enum ParcelStatus: int
{
    case PENDING = 1;  // Parcel registered, waiting for collection
    case CLAIMED = 2;  // Parcel collected by recipient

    /**
     * Get the label for the status.
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::CLAIMED => 'Claimed',
        };
    }

    /**
     * Get the Malay label for the status.
     */
    public function labelMalay(): string
    {
        return match($this) {
            self::PENDING => 'Belum Dituntut',
            self::CLAIMED => 'Telah Dituntut',
        };
    }

    /**
     * Get the badge color class for the status.
     */
    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING => 'badge bg-warning',
            self::CLAIMED => 'badge bg-success',
        };
    }
}
