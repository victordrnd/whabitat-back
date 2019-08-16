<?php
namespace App\Services;
use GuzzleHttp\Client as Http;
use Carbon\Carbon;
class ReservationsService {
    
    private $http;
    private $user;
    private $password;
    private $api_url;
    private $credentials;

    public function __construct(){
        $this->http = new Http();
        $this->user = env('ZODOMUS_USER');
        $this->password= env('ZODOMUS_PASSWORD');
        $this->credentials = base64_encode($this->user.':'.$this->password);
        $this->api_url = env('ZODUMUS_API_URL');
    }

    
    public function getFuturesReservations(&$response,$channel_id, $property_id){
        $url = $this->api_url.'/reservations-summary?channelId='.$channel_id.'&propertyId='.$property_id;
        $method ="GET";
        $this->handleRequest($url, $method, $response);
    }



    public function handleRequest($url, $method, &$response){
        $request = new \GuzzleHttp\Psr7\Request($method,  $url, [
            'Authorization' => 'Basic '.$this->credentials,
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
        ]);
        
        $promise = $this->http->sendAsync($request)->then(function($response){
            return json_decode($response->getBody()->getContents(), JSON_UNESCAPED_SLASHES);
        });
        $response = $promise->wait();
    }



    public function calculatePrice($start_at, $end_at, $nb_vacanciers){
        $start_at = Carbon::parse($start_at);
        $end_at = Carbon::parse($end_at);
        $nights = $end_at->diffInDays($start_at) - 1;
        return $nights * 242;
        //return array($nights,$start_at,$end_at);
    }
}