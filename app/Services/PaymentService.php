<?php

namespace App\Services;

use App\Reservation;
use GuzzleHttp\Client;
use Carbon\Carbon;
class PaymentService
{

    private $http;

    public function __construct()
    {
        $this->http = new Client(['timeout' => 2]);
        $this->api_url = "https://api.posthook.io/v1/hooks";
        $this->credentials = env('POSTHOOK_API_KEY');
    }

    public function createPostHook(&$response, $reservation)
    {
            $url = $this->api_url;
            $method = "POST";
            $request = new \GuzzleHttp\Psr7\Request($method,  $url, [
                'headers'  => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-API-Key' => $this->credentials
                ],
            ]);
            $body = [
                'path' => '/webhooks/payments/'.$reservation->id,
                "postAt" => Carbon::parse($reservation->arrival_date)->subDay(7)->format('Y-m-d\TH:i:s'),
                'data' => [
                    "amount" => $reservation->amount
                ]
            ];
            $this->handleRequest($response, $url, $method, $body);
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
}
