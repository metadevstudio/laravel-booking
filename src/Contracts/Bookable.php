<?php
declare(strict_types=1);

namespace MetaDevStudio\LaravelBooking\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Bookable
{
    public function bookings(): MorphMany;

    public function confirmedBookings(): MorphMany;

    public function pendingBookings(): MorphMany;

    public function cancelledBookings(): MorphMany;

    public function currentBookings(): MorphMany;

    public function pastBookings(): MorphMany;
}