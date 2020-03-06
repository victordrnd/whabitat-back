<?php

namespace App\Services;

use GuzzleHttp\Client as Http;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Reservation;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Client;

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
        for ($i = 0; $i < 12; $i++) {
            if ($i == 0) {
                $dateFrom = $today->format('Y-m-d');
            } else {

                $dateFrom = $today->addMonths(1)->firstOfMonth()->format('Y-m-d');
            }
            $dateTo = Carbon::parse($dateFrom)->endOfMonth()->addDay()->format('Y-m-d');
            $url = $this->api_url . '/availability?channelId=' . $channel_id . '&propertyId=' . $property_id . '&dateFrom=' . $dateFrom . '&dateTo=' . $dateTo;
            $method = "GET";

            $request = new \GuzzleHttp\Psr7\Request($method,  $url, [
                'Authorization' => 'Basic ' . $this->credentials,
                'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            ]);
            $promises[] = $this->http->sendAsync($request);
            //$this->handleRequest($response, $url, $method);
        }
        $eachPromise = new EachPromise($promises, [
            // how many concurrency we are use
            'concurrency' => 12,
            'fulfilled' => function ($response) use (&$availabilities) {
                $response = json_decode($response->getBody()->getContents(), JSON_UNESCAPED_SLASHES);
                foreach ($response['rooms'] as $room) {
                    if ($room['id'] == "301") {
                        foreach ($room['dates'] as $date) {
                            if ($date['availability'] == 0) {
                                $availabilities[] = $date['date'];
                            }
                        }
                    }
                }
            },
            'rejected' => function ($reason) {
                // handle promise rejected here
            }
        ]);
        $eachPromise->promise()->wait();
        return $availabilities;
    }


    public function createReservation($reservation)
    {
        $start = Carbon::parse($reservation['range']['start'])->format('Y-m-d');
        $end = Carbon::parse($reservation['range']['end'])->format('Y-m-d');
        $res = Reservation::create([
            'arrival_date' => $start,
            'departure_date' => $end,
            'vacanciers' => $reservation['nb'],
            'guest_id' => auth()->user()->id,
            'property_id' => 1
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


    public function cancelReservation($reservation){
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



    public function calculatePrice($start_at, $end_at, $nb_vacanciers)
    {
        $start_at = Carbon::parse($start_at);
        $end_at = Carbon::parse($end_at);
        $nights = $end_at->diffInDays($start_at) - 1;
        return $nights * 242;
        //return array($nights,$start_at,$end_at);
    }
}
