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
        $body = $this->reservationService->getFuturesReservations(1,1);
        $this->responseJson(200, 'Les réservations ont été retournés', $body);
    }
}
