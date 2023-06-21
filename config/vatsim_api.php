<?php

return [

    /**
     * Base authentication url
     * The home of VATSIM API Interface
     */
    'base' => env('VATSIM_API_BASE', 'https://api.vatsim.net/api'),

    'booking_base' => env('VATSIM_BOOKING_BASE', 'https://atc-bookings.vatsim.net/api'),
    'booking_key' => env('VATSIM_BOOKING_KEY'),
];
