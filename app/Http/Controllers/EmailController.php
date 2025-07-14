<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;

class EmailController extends Controller
{


    public function sendMail(Request $request)
    {


        $to_email = $request->input('email');
        $to_name = $request->input('name');

    //    dd($request->all());
        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'anaghasolutions1@gmail.com';
            $mail->Password   = 'shms mvcx raet trkj'; // 🔐 App password
            // $mail->Username   = 'kapilagrawal230@gmail.com';
            // $mail->Password   = 'zlhh zefq ckdh ubmv'; // 🔐 App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('kapilagrawal230@gmail.com', 'AnaghaSafar');
            $mail->addAddress($to_email, $to_name);
            // $mail->addReplyTo('kapilagrawal230@gmail.com', 'Kapil');


            $mail->isHTML(true);
            $mail->Subject = 'Thank You for Applying - Make Your Own Trip';

            $mail->Body = '
            <p>Dear Traveler,</p>
            <p>Thank you for submitting your trip request with <strong>Anagha Safar</strong>.</p>
            <p>We have successfully received your application. Our travel experts are reviewing your preferences and will get back to you shortly with a personalized itinerary tailored to your needs.</p>
            <p>We appreciate your interest in planning your journey with us and look forward to creating a memorable travel experience for you.</p>
            <p>Warm regards,<br>
            <strong>Anagha Safar Team</strong></p>';

            $mail->AltBody = "Dear Traveler,\n\nThank you for submitting your trip request with Anagha Safar.\nWe have received your application and our team will contact you shortly with a personalized plan.\n\nWarm regards,\nAnagha Safar Team";
               $mail->send();

            return response()->json([
                'status' => 'success',
                'message' => 'Email has been sent!'
            ]);
        } catch (Exception $er) {
            dd($er);
        }
    }


    public function  orderEmail(Request $request)
    {


        $to_email = $request->input('email');
        $to_name = $request->input('name');

    //    dd($request->all());
        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'anaghasolutions1@gmail.com';
            $mail->Password   = 'shms mvcx raet trkj'; // 🔐 App password
            // $mail->Username   = 'kapilagrawal230@gmail.com';
            // $mail->Password   = 'zlhh zefq ckdh ubmv'; // 🔐 App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('kapilagrawal230@gmail.com', 'AnaghaSafar');
            $mail->addAddress($to_email, $to_name);


            $mail->isHTML(true);
            $mail->Subject = 'Thank You for Applying - Make Your Own Trip';

            $mail->Body = '
            <p>Dear Traveler,</p>
            <p>Thank you for submitting your trip request with <strong>Anagha Safar</strong>.</p>
            <p>We have successfully received your application. Our travel experts are reviewing your preferences and will get back to you shortly with a personalized itinerary tailored to your needs.</p>
            <p>We appreciate your interest in planning your journey with us and look forward to creating a memorable travel experience for you.</p>
            <p>Warm regards,<br>
            <strong>Anagha Safar Team</strong></p>';

            $mail->AltBody = "Dear Traveler,\n\nThank you for submitting your trip request with Anagha Safar.\nWe have received your application and our team will contact you shortly with a personalized plan.\n\nWarm regards,\nAnagha Safar Team";
               $mail->send();

            return response()->json([
                'status' => 'success',
                'message' => 'Email has been sent!'
            ]);
        } catch (Exception $er) {
            dd($er);
        }








        // include base_path("email-project/index.php");
        // return response()->json(['status'=>'Email Triggered']);
    }
}
