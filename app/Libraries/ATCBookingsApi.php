<?php

namespace App\Libraries;

use App\Models\Booking\AtcSessionBooking;
use GuzzleHttp\Client;

class ATCBookingsApi
{
    # https://atc-bookings.vatsim.net/api-doc

    /**
     * If the booking is added successfully the vatsimbooking_id gets set and $booking is saved to the database.
     */
    public static function createAndSaveBooking(AtcSessionBooking $booking): array
    {
        $type = 'booking';
        if ($booking->event) {
            $type = 'event';
        }
        if ($booking->training) {
            $type = 'mentoring';
        }
        //if ($booking->exam) {
        //    $type = 'exam';
        //}

        $booking->loadMissing('station');

        $res = self::send('POST', 'booking', [
            'callsign' => $booking->station->ident,
            'cid' => $booking->controller_id,
            'type' => $type,
            'start' => $booking->starts_at->toDateTimeString(),
            'end' => $booking->ends_at->toDateTimeString(),
        ]);

        if ($res['code'] == 422) {
            return [
                'ok' => false,
                'message' => 'Station already booked!'
            ];
        }
        if ($res['code'] != 201) {
            $booking->vatbook_id = null;
            $booking->save();
            return [
                'ok' => false,
                'message' => 'Error in synchronisation!'
            ];
        }
        $booking->vatbook_id = $res['data']->id;
        $booking->save();
        return [
            'ok' => true,
            'message' => 'Booked.'
        ];
    }

    /**
     * This trys to edit the booking in VATSIM API, if booking is found but can't be updated the
     * $booking data will not be saved, so you can reset it from the database.
     */
    public static function editBooking(AtcSessionBooking $booking): array
    {
        if (!$booking->vatbook_id) {
            return self::createAndSaveBooking($booking);
        }
        $type = 'booking';
        if ($booking->event) {
            $type = 'event';
        }
        if ($booking->training) {
            $type = 'mentoring';
        }
        //if ($booking->exam) {
        //    $type = 'exam';
        //}

        $booking->loadMissing('station');

        $res = self::send('PUT', "booking/{$booking->vatbook_id}", [
            'callsign' => $booking->station->ident,
            'cid' => $booking->controller_id,
            'type' => $type,
            'start' => $booking->starts_at->toDateTimeString(),
            'end' => $booking->ends_at->toDateTimeString(),
        ]);

        if ($res['code'] == 404) {
            $booking->vatbook_id = null;
            $booking->save();
            return [
                'ok' => false,
                'message' => 'Booking updated but VATSIM sync failed.'
            ];
        }
        if ($res['code'] == 422) {
            $booking->refresh();
            return [
                'ok' => false,
                'message' => 'Station already booked!'
            ];
        }
        if ($res['code'] != 200) {
            $booking->refresh();
            return [
                'ok' => false,
                'message' => 'Error in synchronisation!'
            ];
        }
        $booking->save();
        return [
            'ok' => true,
            'message' => 'Booking updated!'
        ];

    }

    public static function deleteBooking(AtcSessionBooking $booking): array
    {
        if (!$booking->vatbook_id) {
            $booking->delete();
            return [
                'ok' => true,
                'message' => 'Local booking deleted!'
            ];
        }

        $res = self::send('DELETE', "booking/{$booking->vatbook_id}", []);

        if ($res['code'] == 404) {
            $booking->delete();
            return [
                'ok' => true,
                'message' => 'Local booking deleted!'
            ];
        }
        if ($res['code'] != 204) {
            return [
                'ok' => false,
                'message' => 'Error in synchronisation!'
            ];
        }
        $booking->delete();
        return [
            'ok' => true,
            'message' => 'Booking deleted!'
        ];
    }


    private static function send($method, $endpoint, $form_params): array
    {
        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . config('vatsim_api.booking_key'),
            ],
            'connect_timeout' => 25,
        ]);

        $url = config('vatsim_api.booking_base') . '/' . $endpoint;

        $res = $client->request($method,$url , ['form_params' => $form_params, 'http_errors' => false]);
        return ['code' => $res->getStatusCode(), 'data' => json_decode($res->getBody())];
    }
}
