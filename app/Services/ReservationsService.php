<?php

namespace App\Services;

use GuzzleHttp\Client as Http;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Reservation;
use App\Tarif;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ReservationsService
{

    private $http;
    private $user;
    private $password;
    private $api_url;
    private $credentials;

    public function __construct()
    {
        //$this->http = new Http();
        $this->http = new Client(['timeout' => 2]);
        $this->user = env('ZODOMUS_USER');
        $this->password = env('ZODOMUS_PASSWORD');
        $this->credentials = base64_encode($this->user . ':' . $this->password);
        $this->api_url = env('ZODUMUS_API_URL');
    }

    //return  disabled dates
    public function getDisabledDates(&$response, $channel_id, $property_id, $dateFrom)
    {
        $today = Carbon::now();
        $availabilities = [];
        //Enlever les 7 jours suivants;
        $period = CarbonPeriod::between(Carbon::now(), Carbon::now()->addDays(7));
        foreach ($period as $date) {
            $availabilities[] = $date->format('Y-m-d');
        }
        for ($i = 0; $i < 12; $i++) {
            if ($i == 0) {
                $dateFrom = $today->format('Y-m-d');
            } else {

                $dateFrom = $today->addMonths(1)->firstOfMonth()->format('Y-m-d');
            }
            $dateTo = Carbon::parse($dateFrom)->endOfMonth()->format('Y-m-d');
            $url = $this->api_url . '/availability?channelId=' . $channel_id . '&propertyId=' . $property_id . '&dateFrom=' . $dateFrom . '&dateTo=' . $dateTo;
            $method = "GET";

            $request = new \GuzzleHttp\Psr7\Request($method,  $url, [
                'Authorization' => 'Basic ' . $this->credentials,
                'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
                'timeout' => 6000, // Response timeout
            ]);
            $promises[] = $request;
            //$this->handleRequest($response, $url, $method);
        }
        $eachPromise = new Pool($this->http, $promises, [
            'options' => ['timeout' => 15],
            // how many concurrency we are use
            'concurrency' => 12,
            'fulfilled' => function (ResponseInterface $response) use (&$availabilities) {
                $response = json_decode($response->getBody()->getContents(), JSON_UNESCAPED_SLASHES);
                foreach ($response['rooms'] as $room) {
                    if ($room['id'] == "301") {
                        //dd($response);
                        foreach ($room['dates'] as $date) {
                            if ($date['availability'] == 0) {
                                $availabilities[] = $date['date'];
                            }
                        }
                    }
                }
            },
            'rejected' => function ($reason) {
                dd($reason);
                // handle promise rejected here
            }
        ]);
        $eachPromise->promise()->wait();
        return $availabilities;
    }


    public function createReservation($reservation, $intent = null)
    {
        $start = Carbon::parse($reservation['range']['start'])->format('Y-m-d');
        $end = Carbon::parse($reservation['range']['end'])->format('Y-m-d');
        $res = Reservation::create([
            'arrival_date' => $start,
            'departure_date' => $end,
            'adults' => $reservation['adults'],
            'children' => $reservation['children'],
            'guest_id' => auth()->user()->id,
            'property_id' => 1,
            'setup_intent' => $intent['id'],
            'amount' => $this->calculatePrice($reservation['range']['start'], $reservation['range']['end'], $reservation['adults'], $reservation['children'])
        ]);

        $body = [
            'channelId' => 1,
            'propertyId' => 3,
            'roomId' => 301,
            'dateFrom' => $start,
            'dateTo' => $end,
            'availabilty' => 0
        ];
        $url = $this->api_url . '/availability';
        $method = "POST";
        $this->handleRequest($response, $url, $method, $body);
        return $res;
    }


    public function cancelReservation($reservation)
    {
        $start = $reservation->arrival_date;
        $end = $reservation->departure_date;
        // $body = [

        //]
    }



    public function handleRequest(&$response, $url, $method, $body = null)
    {
        $request = new \GuzzleHttp\Psr7\Request($method,  $url, [
            'Authorization' => 'Basic ' . $this->credentials,
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
        ], json_encode($body));

        $promise = $this->http->sendAsync($request)->then(function ($response) {
            return json_decode($response->getBody()->getContents(), JSON_UNESCAPED_SLASHES);
        });
        $response = $promise->wait();
    }



    public function calculatePrice($start_at, $end_at, $adults, $children)
    {
        $start_at = Carbon::parse($start_at);
        $end_at = Carbon::parse($end_at);
        $period = CarbonPeriod::create($start_at, $end_at);
        $tarifs = Tarif::where('start', '<=', $start_at)->where('end', '>=', $end_at)->get();
        $nights = $end_at->diffInDays($start_at);
        $tarif_applicable = count($tarifs) > 1 ? $tarifs[1] : $tarifs[0];
        if ($nights >= 3) {

            if (($adults + $children) <= 9) {
                $tarif = $tarif_applicable->amount * $nights;
            } elseif (($adults + $children) == 10) {
                $tarif = ($tarif_applicable->amount * $nights) + 20;
            } else {
                $tarif = ($tarif_applicable->amount * $nights) + 40;
            }
        } elseif ($nights == 2) {
            $tarif = ($tarif_applicable->two_night_amount * $nights);
        } elseif ($nights <= 1) {
            $tarif = ($tarif_applicable->single_night_amount * $nights);
        }


        $taxes = ($tarif/($adults + $children)) *0.03;
        $taxes = $taxes > 2.30 ? 2.30 : $taxes;
        $taxes = $taxes + $taxes * 0.15 + $taxes * 0.10;
        

        return $tarif + ($taxes * $adults);
    }
}
