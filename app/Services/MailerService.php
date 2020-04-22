<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Carbon\Carbon;
use App\Reservation;

class MailerService
{
    public function sendValidationEmail(Reservation $reservation){
        $mail = new PHPMailer(true);
			try {
				// Server settings
	    	$mail->SMTPDebug = 0;                                	// Enable verbose debug output
				$mail->isSMTP();                                     	// Set mailer to use SMTP
				$mail->Host = 'smtp.ionos.fr';												// Specify main and backup SMTP servers
				$mail->SMTPAuth = true;                              	// Enable SMTP authentication
				$mail->Username = 'reservations@whabitat.fr';             // SMTP username
				$mail->Password = env('MAIL_PASSWORD');
				$mail->SMTPSecure = 'tls';
				$mail->Port = 	587;
                $mail->Priority = 3;
                $mail->DKIM_domain = 'whabitat.fr';
                $mail->DKIM_private = '/app/dkim.private';
                $mail->DKIM_selector = 'mail';
                $mail->DKIM_passphrase = '';
                $mail->DKIM_identity = 'reservations@whabitat.fr';
                $mail->AddCustomHeader("X-MSMail-Priority: High");
                $mail->AddCustomHeader("Importance: High");  
				$mail->setFrom('reservations@whabitat.fr', 'Whabitat');
				$mail->addAddress($reservation->guest->email, $reservation->guest->firstname." ".$reservation->guest->lastname);
				$mail->addReplyTo('contact@whabitat.fr', 'Whabitat');
                $mail->addBCC('contact@whabitat.fr');
                $mail->addBCC('vic20016@gmail.com');
				// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');	// Optional name
				$mail->isHTML(true);
				$mail->Subject = "Confirmation de votre réservation";
				$mail->Body    = view('mail', compact('reservation'));
                $mail->send();
                return true;
			} catch (Exception $e) {
				dd($e->getMessage());
			}
    }
    
}
