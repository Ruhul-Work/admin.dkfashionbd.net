<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_image',
        'offer_type',
        'start_date',
        'end_date',
        'announcement_start_at',
        'announcement_end_at',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'datetime', 
        'end_date' => 'datetime',
        'announcement_start_at' => 'datetime',
        'announcement_end_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    #[Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isAnnouncementVisibleNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        // Offer must be currently running
        if ($this->start_date && $this->start_date->gt($now)) {
            return false;
        }
        if ($this->end_date && $this->end_date->lte($now)) {
            return false;
        }

        // Announcement window can be controlled independently; if null, fall back to offer window.
        $startAt = $this->announcement_start_at ?? $this->start_date;
        $endAt = $this->announcement_end_at ?? $this->end_date;

        if ($startAt && $startAt->gt($now)) {
            return false;
        }
        if ($endAt && $endAt->lte($now)) {
            return false;
        }

        return true;
    }

    public function gift()
    {
        return $this->hasOne(OfferGift::class);
    }

    /**
     * For 'bundle' (Buy One Get Multiple) type — multiple rows in offer_gifts
     */
    public function giftItems()
    {
        return $this->hasMany(OfferGift::class)->orderBy('id');
    }

    public function shipping()
    {
        return $this->hasOne(OfferShipping::class);
    }

    public function discount()
    {
        return $this->hasOne(OfferDiscount::class);
    }

}
