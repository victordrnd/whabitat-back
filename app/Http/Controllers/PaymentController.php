<?php

namespace App\Http\Controllers;

use App\Reservation;
use App\Services\MailerService;
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
    private $mailerService;
    public function __construct(
        PaymentService $paymentService,
        ReservationsService $reservationService,
        MailerService $mailerService
    ) {
        $this->paymentService = $paymentService;
        $this->reservationService = $reservationService;
        $this->mailerService = $mailerService;
    }

    public function createPaymentIntent(Request $request)
    {
        //Check if reservation available
        $response = null;
        $reservation = $request->reservation;
        //$this->reservationService->getDisabledDates($response, 1, 3, $reservation['range']['start']);
        $price = $this->reservationService->calculatePrice($reservation['range']['start'], $reservation['range']['end'],$reservation['adults'], $reservation['children']);
        try {
            $intent = \Stripe\SetupIntent::create([
                'usage' => 'off_session',
                'payment_method_types' => ['card'],
                'customer' => auth()->user()->stripe_id
            ]);
        } catch (Exception $e) {
            return $this->responseJson(500, $e->getMessage());
        }
        $arr = [
            'intent' => $intent,
            'amount' => $price
        ];
        return $this->responseJson(200, "L'intention de paiement a bien été créer", $arr);
    }



    public function confirmReservation(Request $request)
    {
        $reservation = $request->reservation;
        $intent = \Stripe\SetupIntent::retrieve($request->intent['id']);
        $response = null;
        if ($intent['status'] == "succeeded") {
            $res = $this->reservationService->createReservation($reservation, $intent);
            $this->paymentService->createPostHook($response, $res);
            if(config('app.env') == 'production')
                $this->mailerService->sendValidationEmail($res);
        }
        return $this->responseJson(200, 'La réservation a bien été enregistré', $res);
    }


    public function chargeHook($id, Request $req){
        try{
            $charge = $this->paymentService->charge($id);
        }catch(Exception $e){
            return response()->json(['error' => 'Une erreur est survenue'], 400);
        }
        return response()->json(compact('charge'), 200);
        
    }
}
