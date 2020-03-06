<?php

namespace App\Http\Controllers;
use App\Services\ReservationsService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Reservation;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReservationController extends Controller
{
    private $reservationService;
    public function __construct()
    {
        $this->reservationService = new ReservationsService;
    }


    public function getDisabledDates(Request $request){
        $dateFrom = $request->dateFrom ? Carbon::parse($request->dateFrom)->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $availabilities = $this->reservationService->getDisabledDates($response, 1,3, $dateFrom);
        return $this->responseJson(200, 'Les réservations ont été retournés', $availabilities);
    }



    public function getMyLastReservation() {
        $reservation = Reservation::where('guest_id', auth()->user()->id)->latest()->first();
        return $this->responseJson(200, 'La dernière réservation a été retournée', $reservation);       
    }



    public function getMyReservations(){
        $reservations = Reservation::where('guest_id', auth()->user()->id)->orderBy('arrival_date', 'desc')->get();
        return $this->responseJson(200, 'Les réservation ont été retournée', $reservations);
    }


    public function cancelReservation($id){
        try{
            $reservation = Reservation::where('guest_id', auth()->user()->id)->where('id', $id)->find();
        }catch(ModelNotFoundException $e){
            return $this->responseJson(404, 'La reservation ne vous appartient pas');
        }
        $this->reservationService->cancelReservation($reservation);
        $reservation->delete();
        return $this->responseJson(200, 'La reservation a correctement été mis à jour');
    }


}
