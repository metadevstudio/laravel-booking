<?php
declare(strict_types=1);

namespace MetaDevStudio\LaravelBooking;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\OpeningHours\OpeningHours;

/**
 * Trait HasBookings
 * @package MetaDevStudio\LaravelBooking
 */
trait HasBookings
{
    /**
     * @return MorphMany
     */
    public function bookings(): MorphMany
    {
        return $this->morphMany(config('booking.model'), 'visitable');
    }

    /**
     * @return MorphMany
     */
    public function confirmedBookings(): MorphMany
    {
        return $this->bookings()->where('status', config('booking.status.confirmed', 'confirmed'));
    }

    /**
     * @return MorphMany
     */
    public function pendingBookings(): MorphMany
    {
        return $this->bookings()->where('status', config('bookings.status.pending', 'pending'));
    }

    /**
     * @return MorphMany
     */
    public function cancelledBookings(): MorphMany
    {
        return $this->bookings()->where('status', config('bookings.status.cancelled', 'cancelled'));
    }

    /**
     * @return MorphMany
     */
    public function currentBookings(): MorphMany
    {
        return $this->bookings()
            ->where('period_start', '<=', now())
            ->where('period_end', '>', now())
            ->where('status', '=', 'confirmed');
    }

    /**
     * @return MorphMany
     */
    public function pastBookings(): MorphMany
    {
        return $this->bookings()
            ->where('period_end', '<=', now());
    }

    /**
     * Checks if period is available for booking
     * @param $period array = Range to be checked, e.g: ['09:00-12:00', '13:00-18:00']
     * @return bool
     */
    public function periodIsAvailable(array $period): bool
    {
        if ($this->hasOpeningHours()) {
            $bookings = $this->bookings()->get(['period_start', 'period_end']);
            $exceptions = [];

            $x = 0;
            foreach ($bookings as $booking) {
                $exceptions[date('Y-m-d', strtotime($booking['period_start']))] = date('H:i:s', strtotime($booking['period_start'])) . "-" . date('H:i:s', strtotime($booking['period_end']));
            }

            $openingHours = OpeningHours::create([
                'monday' => [$this->attributes['monday_start_at'] - $this->attributes['monday_end_at']],
                'tuesday' => [$this->attributes['tuesday_start_at'] - $this->attributes['tuesday_end_at']],
                'wednesday' => [$this->attributes['wednesday_start_at'] - $this->attributes['wednesday_end_at']],
                'thursday' => [$this->attributes['thursday_start_at'] - $this->attributes['thursday_end_at']],
                'friday' => [$this->attributes['friday_start_at'] - $this->attributes['friday_end_at']],
                'saturday' => [$this->attributes['saturday_start_at'] - $this->attributes['saturday_end_at']],
                'sunday' => [$this->attributes['sunday_start_at'] - $this->attributes['sunday_end_at']],
                'exceptions' => [
                    $exceptions
                ]
            ]);
        } else {
            return false;
        }
    }

    /**
     * Does the model have day_open_star and day_open_end columns?
     * @return bool
     */
    public function hasOpeningHours(): bool
    {
        return true;
    }
}