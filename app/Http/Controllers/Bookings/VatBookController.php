<?php

namespace App\Http\Controllers\Bookings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking\AtcSessionBooking;

class VatBookController extends Controller
{
    /**
     * Required Parameters for all Requests:
     * Local_URL => our own url
     * Local_ID => our local ID of the booking
     * EU_ID => vatbook booking id. Obviously only for update and delete requests.
     *
     * FOR TESTING WE CAN USE
     * TEST=1 as parameter to prevent saving!!!!!
     *
     * BASED UPON:
     * http://support.vroute.net/viewtopic.php?f=3&t=47
     */

    /**
     * VATBOOK Base URL.
     *
     * @var string
     */
    protected $vatbookBaseUrl;

    /**
     * Event Base URL.
     */
    protected $eventBaseUrl;

    /**
     * HTTP client used by this class.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Testing Flag
     * Set to true for default to prevent saving to vatbook in case of errors.
     *
     * @var bool
     */
    protected $testing = true;

    public function __construct()
    {
        parent::__construct();

        // // See https://stackoverflow.com/questions/30959210/set-default-query-string-in-guzzle-6 for details
        // // on this approach regarding the TEST parameter added to test bookings
        // $handler = \GuzzleHttp\HandlerStack::create();

        // // Add TEST parameter if we are only testing
        // if ($this->testing) {
        //     $handler->push(\GuzzleHttp\Middleware::mapRequest(function (\Psr\Http\Message\RequestInterface $request) {
        //         return $request->withUri(\GuzzleHttp\Psr7\Uri::withQueryValue($request->getUri(), 'TEST', '1'));
        //     }));
        // }

        // $this->client = new \GuzzleHttp\Client([
        //     'base_uri' => $this->vatbookBaseUrl,
        //     'handler' => $handler,
        // ]);
    }

    /**
     * Send VatBook Insert Request
     * http://vatbook.euroutepro.com/atc/insert.php?Local_URL=&Local_ID=&b_day=&b_month=&b_year=&Controller=&Position=&sTime=&eTime=&T=&E=&voice=.
     *
     * Local_URL - as in common section
     * Local_ID - as in common section
     * b_day - day of month on which the ATC session will start
     * b_month - month part of the start date
     * b_year - year part of the start date (4 digits)
     * Controller - booking user's full name
     * cid - Controller VID (not required but recommended)
     * Position - callsign on which the service will be provided (up to 11 chars)
     * sTime - start time in UTC, 4 digits, no separator between hours and minutes
     * eTime - end time in UTC, as above. If eTime < sTime, then it means booked session ends on the next day
     * T - 0 for normal session, 1 for training (display applications show these differently)
     * E - 0 for normal session, 1 if it is part of event. For events, additional parameter E_URL is required and it specifies a URL where the user will be redirected to
     * voice - 0 for text, 1 for voice, 2 for unknown
     *
     * @param AtcSessionBooking $booking [description]
     *
     * @return [type] [description]
     */
    public function sendInsertRequest(AtcSessionBooking $booking)
    {
        // Send request to VatBook
        $res = $this->client->get($this->vatbookBaseUrl.'insert.asp', [
            'query' => [
                'Local_URL' => 'noredir',
                'Local_ID' => $booking->id,
                'b_day' => $booking->starts_at->format('d'),
                'b_month' => $booking->starts_at->format('m'),
                'b_year' => $booking->starts_at->format('Y'),
                'Controller' => \Illuminate\Support\Str::ascii($booking->controller->id),
                'cid' => $booking->controller->id,
                'Position' => $booking->station->ident,
                'sTime' => $booking->starts_at->format('Hi'),
                'eTime' => $booking->ends_at->format('Hi'),
                'T' => $booking->training ? 1 : 0,
                'E' => $booking->event ? 1 : 0,
                'E_URL' => $this->eventBaseUrl,
                'voice' => $booking->voice ? 1 : 0,
            ],
        ]);
        if (200 == $res->getStatusCode()) {
            $body = (string) $res->getBody(); // Cast the returned stream to a string we can work with
            $body = trim($body);

            // We need to find the EU_ID...
            $partials = explode(PHP_EOL, $body);
            /*
             * when an error occured!!!
             * The returned data ALLWAYS IS AN ARRAY OF LENGTH 4
             */
            if (4 != count($partials)) {
                return false;
            }

            // Response Pattern is:
            // array:4 [
            //   0 => "action=insert"
            //   1 => "Local_ID=7"
            //   2 => "EU_ID=384661917"
            //   3 => "Event_ID=0"
            // ]
            // so take [2] and parse the id
            $euid = explode('=', $partials[2])[1];

            $booking->vatbook_id = $euid;
            $booking->save();

            return true;
        } else {
            $booking->delete(); // Unable to book!!!!
            return false;
        }
    }

    /**
     * Send VatBook Update request to
     * http://vatbook.euroutepro.com/atc/update.php?Local_URL=&Local_ID=&EU_ID=&b_day=&b_month=&b_year=&Controller=&Position=&sTime=&eTime=&T=&E=&voice=.
     *
     * Local_URL - as in common section
     * Local_ID - as in common section
     * EU_ID - numeric ID of a booking previously made
     * b_day - day of month on which the ATC session will start
     * b_month - month part of the start date
     * b_year - year part of the start date (4 digits)
     * Controller - booking user's full name
     * cid - Controller VID (not required but recommended)
     * Position - callsign on which the service will be provided (up to 11 chars)
     * sTime - start time in UTC, 4 digits, no separator between hours and minutes
     * eTime - end time in UTC, as above. If eTime < sTime, then it means booked session ends on the next day
     * T - 0 for normal session, 1 for training (display applications show these differently)
     * E - 0 for normal session, 1 if it is part of event. For events, additional parameter E_URL is required and it specifies a URL where the user will be redirected to
     * voice - 0 for text, 1 for voice, 2 for unknown
     *
     * @param AtcSessionBooking $booking [description]
     *
     * @return [type] [description]
     */
    public function sendUpdateRequest(AtcSessionBooking $booking)
    {
        $res = $this->client->get($this->vatbookBaseUrl.'update.asp', [
            'query' => [
                'Local_URL' => 'noredir',
                'Local_ID' => $booking->id,
                'EU_ID' => $booking->vatbook_id,
                'b_day' => $booking->starts_at->format('d'),
                'b_month' => $booking->starts_at->format('m'),
                'b_year' => $booking->starts_at->format('Y'),
                'Controller' => \Illuminate\Support\Str::ascii($booking->controller->id),
                'cid' => $booking->controller->id,
                'Position' => $booking->station->ident,
                'sTime' => $booking->starts_at->format('Hi'),
                'eTime' => $booking->ends_at->format('Hi'),
                'T' => $booking->training ? 1 : 0,
                'E' => $booking->event ? 1 : 0,
                'E_URL' => $this->eventBaseUrl,
                'voice' => $booking->voice ? 1 : 0,
            ],
        ]);

        if (200 == $res->getStatusCode()) {
            $body = (string) $res->getBody(); // Cast the returned stream to a string we can work with
            $body = trim($body);

            // We need to find the EU_ID...
            $partials = explode(PHP_EOL, $body);

            /*
             * when an error occured!!!
             * The returned data ALWAYS IS AN ARRAY OF LENGTH 4
             */
            if (4 != count($partials)) {
                return false;
            }

            // Response Pattern is:
            // array:4 [
            //   0 => "action=insert"
            //   1 => "Local_ID=7"
            //   2 => "EU_ID=384661917"
            //   3 => "Event_ID=0"
            // ]
            // so take [2] and parse the id
            $euid = explode('=', $partials[2])[1];

            $booking->vatbook_id = $euid;
            $booking->save();

            return true;
        } else {
            return false;
        }
    }

    /**
     * Send VatBook Delete Request
     * http://vatbook.euroutepro.com/atc/delete.php?Local_URL=noredir&EU_ID=573682004&Local_ID=112.
     *
     * Local_URL - as in common section
     * Local_ID - as in common section
     * EU_ID - as in common section
     *
     * @param AtcSessionBooking $booking [description]
     *
     * @return [type] [description]
     */
    public function sendDeleteRequest(AtcSessionBooking $booking)
    {
        $res = $this->client->get($this->vatbookBaseUrl.'delete.asp', [
            'query' => [
                'Local_URL' => 'noredir',
                'Local_ID' => $booking->id,
                'EU_ID' => $booking->vatbook_id,
            ],
        ]);

        // $url = $this->vatbookBaseUrl.'delete.php?Local_URL=noredir&EU_ID='.$booking->vatbook_id.'&Local_ID='.$booking->id;
        // $res = $client->get($url);

        if (200 == $res->getStatusCode()) {
            $body = (string) $res->getBody(); // Cast the returned stream to a string we can work with
            $body = trim($body);

            // We need to find the EU_ID...
            $partials = explode(PHP_EOL, $body);

            /*
             * when an error occured!!!
             * The returned data ALLWAYS IS AN ARRAY OF LENGTH 4
             */
            if (4 != count($partials)) {
                return false;
            }

            // Response Pattern is:
            // array:4 [
            //   0 => "action=insert"
            //   1 => "Local_ID=7"
            //   2 => "EU_ID=384661917"
            //   3 => "Event_ID=0"
            // ]
            // so take [1] and parse the id
            $lclid = explode('=', $partials[1])[1];
            if ($lclid == $booking->id) {
                return true;
            }

            return false;
        } else {
            return false;
        }
    }
}
