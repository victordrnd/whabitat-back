<?php
namespace App\Services;
use GuzzleHttp\Client as Http;

class ReservationsService {
    
    private $http;
    private $user;
    private $password;
    private $api_url;

    public function __construct(){
        $this->http = new Http();
        $this->user = env('ZODOMUS_USER');
        $this->password= env('ZODOMUS_PASSWORD');
        $this->api_url = env('ZODUMUS_API_URL');
    }
    public function getFuturesReservations($channel_id, $property_id){
        $response = $this->http->request('GET', $this->api_url.'/reservations-summary',[
            'auth' => [$this->user, $this->password]
        ]);
        return $response->getBody();
    }
}