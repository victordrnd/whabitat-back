<?php

namespace App\Http\Controllers;
use App\Services\ReservationsService;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    private $reservationService;
    public function __construct()
    {
        $this->reservationService = new ReservationsService;
    }
    public function getFutureReservations(){
        $response = null;
        $this->reservationService->getFuturesReservations($response, 1,1);
        //dd(gettype($response));
        // echo $response;
        // dd();
        return $this->responseJson(200, 'Les réservations ont été retournés', $response);
    }




}
