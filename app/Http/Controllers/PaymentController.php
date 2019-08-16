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
        $reservationService->getFuturesReservations($response, 1,1);
        $reservation = $request->reservation;
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
        $start = Carbon::parse($reservation['range']['start'])->format('Y-m-d');
        $end = Carbon::parse($reservation['range']['end'])->format('Y-m-d');
        $res = Reservation::create([
            'arrival_date' => $start,
            'departure_date' => $end,
            'guest_id' => auth()->user()->id,
            'property_id' => 1
        ]);
        return $this->responseJson(200, 'La réservation a bien été enregistré', $res);
    }
}
