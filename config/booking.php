<?php

return [
    'atcBookingToken' => env('ATC_BOOKING_TOKEN'), // Booking token used by the atc-booking to access sensitive user data (names)
    'testing' => env('BOOKING_TESTING', true),
    'vatbookBaseUrl' => env('BOOKING_BASE_URL', 'http://vatbook.euroutepro.com/atc/'),
    'eventBaseUrl' => env('BOOKING_EVENT_BASE_URL', 'http://vatsim-germany.org'),
];
