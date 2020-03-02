<?php

namespace MetaDevStudio\LaravelBooking;

use Illuminate\Support\ServiceProvider;

class BookingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $configFile = __DIR__ . '/../config/booking.php';

        $this->mergeConfigFrom($configFile, 'booking');

        $this->publishes([
            $configFile => config_path('booking.php'),
        ], 'config');

        if (!class_exists('CreateBookingsTable')) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../database/migrations/create_bookings_table.stub' =>
                    database_path("migrations/{$timestamp}_create_bookings_table.php")
            ], 'migrations');
        }
    }
}
