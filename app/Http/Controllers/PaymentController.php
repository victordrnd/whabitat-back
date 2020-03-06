<?php

namespace App\Http\Controllers;

use App\Reservation;
use Exception;
use Illuminate\Http\Request;
use Stripe;
use App\Services\ReservationsService;
use  Carbon\Carbon;

class PaymentController extends Controller
{

    public function createPaymentIntent(Request $request){
        //Check if reservation available
        $response = null;
        $reservationService = new ReservationsService();
        $reservation = $request->reservation;
        $reservationService->getDisabledDates($response, 1,3,$reservation['range']['start']);
        $price = $reservationService->calculatePrice($reservation['range']['start'], $reservation['range']['end'], $reservation['nb']);
        try{
            $intent = \Stripe\PaymentIntent::create([
                'amount' => $price *100,
                'currency' => 'eur',
                'customer' => auth()->user()->stripe_id
            ]);
        }catch(Exception $e){
            return $this->responseJson(500, 'Un problème est survenue lors de la création du paiement');
        }
        return $this->responseJson(200, "L'intention de paiement a bien été créer", $intent);
    }



    public function confirmReservation(Request $request){
        $reservation = $request->reservation;
        $intent = \Stripe\PaymentIntent::retrieve($request->intent['id']);
        if($intent['status' ] =="succeeded"){
            $reservationService = new ReservationsService();
            $res = $reservationService->createReservation($reservation);
        }
        return $this->responseJson(200, 'La réservation a bien été enregistré', $res);
    }
}
