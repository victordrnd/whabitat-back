<?php

namespace App\Http\Controllers;

use App\Reservation;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Stripe;
use App\Services\ReservationsService;
use  Carbon\Carbon;

class PaymentController extends Controller
{

    private $paymentService;
    private $reservationService;
    public function __construct(
        PaymentService $paymentService,
        ReservationsService $reservationService
    ) {
        $this->paymentService = $paymentService;
        $this->reservationService = $reservationService;
    }

    public function createPaymentIntent(Request $request)
    {
        //Check if reservation available
        $response = null;
        $reservation = $request->reservation;
        //$this->reservationService->getDisabledDates($response, 1, 3, $reservation['range']['start']);
        $price = $this->reservationService->calculatePrice($reservation['range']['start'], $reservation['range']['end'], $reservation['nb']);
        try {
            $intent = \Stripe\SetupIntent::create([
                'usage' => 'off_session',
                'payment_method_types' => ['card'],
                'customer' => auth()->user()->stripe_id
            ]);
        } catch (Exception $e) {
            return $this->responseJson(500, $e->getMessage());
        }
        return $this->responseJson(200, "L'intention de paiement a bien été créer", $intent);
    }



    public function confirmReservation(Request $request)
    {
        $reservation = $request->reservation;
        $intent = \Stripe\SetupIntent::retrieve($request->intent['id']);
        $response = null;
        if ($intent['status'] == "succeeded") {
            $res = $this->reservationService->createReservation($reservation, $intent);
            $this->paymentService->createPostHook($response, $res);
        }
        return $this->responseJson(200, 'La réservation a bien été enregistré', $res);
    }
}
