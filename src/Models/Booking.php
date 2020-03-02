<?php

namespace MetaDevStudio\LaravelBooking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Booking
 * @package MetaDevStudio\LaravelBooking\Models
 */
class Booking extends Model
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return MorphTo
     */
    public function reserved(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUsers($query)
    {
        return $query->where('reserved_type', 'App\User');
    }
    /**
     * @param $query
     * @return mixed
     */
    public function scopePast($query)
    {
        return $query->where('period_end', '<', now());
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePending($query)
    {
        return $query->where('status', config('booking.status.pending'));
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeFuture($query)
    {
        return $query->where('period_start', '>', now());
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', config('booking.status.confirmed'));
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', config('booking.status.cancelled'));
    }

    /**
     * @return bool
     */
    public function isPast(): bool
    {
        return ($this->period_end < now());
    }

    /**
     * @return bool
     */
    public function isFuture(): bool
    {
        return ($this->period_start > now() && $this->period_end > now());
    }

    /**
     * @return bool
     */
    public function isCurrent(): bool
    {
        return ($this->period_start >= now() && $this->period_end > now() && $this->status == config('booking.status.confirmed'));
    }

    /**
     * @return bool
     */
    public function isCancelled(): bool
    {
        return ($this->status == config('booking.status.cancelled'));
    }
}
