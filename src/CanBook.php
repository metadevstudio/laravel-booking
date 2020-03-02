<?php
declare(strict_types=1);

namespace MetaDevStudio\LaravelBooking;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use MetaDevStudio\LaravelBooking\Contracts\Bookable;

/**
 * Trait CanBook
 * @package MetaDevStudio\LaravelBooking
 */
trait CanBook
{

    /**
     * @param Bookable $bookable
     * @param string $period_start
     * @param string $period_end
     * @param string $status
     * @return mixed
     */
    public function newBooking(Bookable $bookable, string $period_start, string $period_end, $status = 'confirmed')
    {
        $bookingModel = config('booking.model');

        $booking = new $bookingModel(
            [
                'reserved_id' => $this->getKey(),
                'reserved_type' => get_class(),
                'period_start' => $period_start ?? now()->addDays(1),
                'period_end' => $period_end ?? now()->addDays(1)->addMinutes(60),
                'status' => $status
            ]
        );

        $bookable->bookings()->save($booking);

        return $booking;
    }

    /**
     * @return MorphMany
     */
    public function bookings(): MorphMany
    {
        return $this->morphMany(config('booking.model'), 'reserved');
    }

    /**
     * @param Visitable $visitable
     * @return bool
     */
    public function hasBookingsOn(Visitable $visitable): bool
    {
        return $this->bookings()
            ->where([
                'bookable_id' => $visitable->primaryId(),
                'bookable_type' => get_class($visitable),
            ])
            ->exists();
    }

}
