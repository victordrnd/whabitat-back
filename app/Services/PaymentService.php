<?php

namespace App\Services;

use App\Reservation;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Stripe;
class PaymentService
{

    private $http;

    public function __construct()
    {
        $this->http = new Client([
            'timeout' => 4,
            'headers'  => [
                'Content-Type' => 'application/json',
                'X-API-Key' => "fe5e0154b9c44c9e9158d4e853f6b5ab"
            ]
        ]);
        $this->api_url = "https://api.posthook.io/v1/hooks";
    }

    public function createPostHook(&$response, $reservation){
        $url = $this->api_url;
        $method = "POST";
        $date = Carbon::parse($reservation->arrival_date)->subDay(7);
        if($date > Carbon::now()){

            $body = [
                'path' => '/webhooks/payments/' . $reservation->id,
                "postAt" => Carbon::parse($reservation->arrival_date)->subDay(7)->format('Y-m-d\TH:i:s\Z'),
                'data' => [
                    'amount' => $reservation->amount
                    ]
                ];
                $this->handleRequest($response, $url, $method, $body);
        }else{
           $this->charge($reservation->id);
        }

    }



    public function charge($reservation_id){
        $reservation = Reservation::find($reservation_id);
        $setup_intent = \Stripe\SetupIntent::retrieve($reservation->setup_intent);

        $charge = \Stripe\PaymentIntent::create([
            'amount' => ceil($reservation->amount *100),
            'currency' => 'eur',
            'payment_method_types' => ['card'],
            'confirm' => true,
            'customer' => $reservation->guest->stripe_id,
            'payment_method' => $setup_intent->payment_method
          ]);
        return $charge;
    }




    public function handleRequest(&$response, $url, $method, $body = null)
    {
        $request = new \GuzzleHttp\Psr7\Request($method,  $url, ['timeout' => 4],json_encode($body));
        $promise = $this->http->sendAsync($request)->then(function ($response) {
            return json_decode($response->getBody()->getContents(), JSON_UNESCAPED_SLASHES);
        });
        $response = $promise->wait();
    }
}
