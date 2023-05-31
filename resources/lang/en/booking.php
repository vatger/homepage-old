<?php

return array (
  'begin' => 'Begin',
  'bookings' => 'ATC Bookings',
  'delete' => 'Delete booking',
  'errors' =>
  array (
    'alreadyBooked' => 'This station is already booked during the selected time frame.',
    'toFarFuture'  => 'The selected time slot is too far in the future.',
    'failedVatbook' =>
    array (
      'insert' => 'Booking has been saved localy.
While sending it to VatBook there was an error.',
      'update' => 'Booking has been updated localy.
While sending it to VatBook there was an error.',
    ),
    'notController' => 'You are not the controller for that session.',
    'notEligable' => 'You must at least hold the S1 rating to be eligable to book any session.',
    'timeframeLimits' => 'The timeframe limits have not been met. A session must be at least 60 minutes in duration while not exceeding 24 hours.',
    'timeframePast' => 'Please ensure that the begin is not more than 2 hours in the past.',
    'timeframeSense' => 'Please make sure that the begin is prior to the end.',
  ),
  'filter' => 'Filter',
  'frequency' => 'Frequency',
  'make' =>
  array (
    'abort' => 'Abort',
    'editMode' => 'Edit ATC Booking',
    'guide' => 'To book or modify a booking you can either choose an airport and select a related station or you can just search that station.

Searching a station will disable the airport selection option and automatically set the station to the searched one.',
    'newMode' => 'Make ATC Booking',
    'save' => 'Save',
    'select' =>
    array (
      'aerodrome' => 'Choose Airport',
      'begin' => 'Begin ( Times in UTC ):',
      'end' => 'End ( Times in UTC ):',
      'event' => 'Is this booking related to an event?',
      'search' => 'Search ATC station',
      'station' => 'Choose station',
      'training' => 'Is this session an training?',
      'voice' => 'Is voice used?',
    ),
  ),
  'modify' => 'Modify booking',
  'mybookings' => 'My ATC Bookings',
  'next48hours' => 'Sessions booked within the next 48 hours.',
  'nobookings' => 'No bookings found!',
  'nobookings_actions' => 'To change this, go to the ATC Booking page and book your sessions.',
  'station' => 'ATS Station',
  'timeframe' => 'Timeframe',
);
