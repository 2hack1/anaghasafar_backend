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
            $mail->Subject = 'confirm - Your Trip';

            $mail->Body = '
            <p>Dear Traveler,</p>
            <p>Thank you for submitting your trip request with <strong>Anagha Safar</strong>.</p>
            <p>We have successfully received your application. Our travel experts are reviewing your preferences and will get back to you shortly with a personalized itinerary tailored to your needs.</p>
            <p>We appreciate your interest in planning your journey with us and look forward to creating a memorable travel experience for you.</p>
            <p>Warm regards,<br>
            <strong>Anagha Safar Team</strong></p>';

            $mail->AltBody ="Dear Traveler,\n\nThank you for submitting your trip request with Anagha Safar.\nWe have received your application and our team will contact you shortly with a personalized plan.\n\nWarm regards,\nAnagha Safar Team";
            $mail->send();

            return response()->json([
                'status' => 'success',
                'message' => 'Email has been sent!'
            ]);
        } catch (Exception $er) {
            dd($er);
        }
    }

    
     public function forgetUserPassSendEmail($email, $otp)
   {
    $mail = new PHPMailer(true);

      try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'anaghasolutions1@gmail.com'; // your Gmail
        $mail->Password   = 'shms mvcx raet trkj';   // Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom('your_email@gmail.com', 'AnaghaSafar');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body    = '
            <p>Dear User,</p>
            <p>You have requested to reset your password. Please use the following OTP to proceed:</p>
            <h2>' . $otp . '</h2>
            <p>This OTP is valid for 10 minutes.</p>
            <p>Do not share this OTP with anyone.</p>
            <p>Regards,<br><strong>AnaghaSafar Team</strong></p>
        ';

        $mail->AltBody = "Dear User,\n\nYour OTP for password reset is: $otp\n\nThis OTP is valid for 10 minutes.\nDo not share it with anyone.\n\nRegards,\nAnaghaSafar Team";

        $mail->send();

        return response()->json([
            'status' => 'success',
            'message' => 'OTP has been sent to your email!'
        ]);

    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
        ]);
      }
   }


   public function updatedPass($email, $newPassword)
   {
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'anaghasolutions1@gmail.com'; // your Gmail
        $mail->Password   = 'shms mvcx raet trkj'; // Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom('your_email@gmail.com', 'AnaghaSafar');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your Password Has Been Updated';
        $mail->Body    = '
            <p>Dear User,</p>
            <p>Your password has been updated successfully.</p>
            <p>Your new password is: <strong>' . $newPassword . '</strong></p>
            <p>If you did not perform this action, please contact support immediately.</p>
            <p>Regards,<br><strong>AnaghaSafar Team</strong></p>
        ';

        $mail->AltBody = "Dear User,\n\nYour password has been updated successfully.\nYour new password is: $newPassword\n\nIf you did not perform this action, please contact support immediately.\n\nRegards,\nAnaghaSafar Team";

        $mail->send();

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated and email sent!'
        ]);

    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
        ]);
    }
}



 public function roomNoSuccAdd($request)
{
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'anaghasolutions1@gmail.com'; // your Gmail
        $mail->Password   = 'shms mvcx raet trkj'; // Gmail app password
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom('your_email@gmail.com', 'AnaghaSafar');
        $mail->addAddress($request->email);

        // Subject
        $mail->isHTML(true);
        $mail->Subject = 'Your Room Has Been Successfully Assigned';

        // Professional HTML email body
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px;">
            <div style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="background: #007bff; padding: 15px; text-align: center; color: #ffffff;">
                    <h2 style="margin: 0;">Booking Confirmation</h2>
                </div>
                <div style="padding: 20px; color: #333333; font-size: 15px; line-height: 1.6;">
                    <p>Dear <strong>' . htmlspecialchars($request->user_name) . '</strong>,</p>
                    <p>We are delighted to confirm your booking with <strong>' . htmlspecialchars($request->hotel_name) . '</strong>.</p>

                    <h3 style="color:#007bff; margin-top: 20px;">Room Details</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #eee;">Room Number:</td>
                            <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>' . htmlspecialchars($request->room_no) . '</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border-bottom: 1px solid #eee;">Room Type:</td>
                            <td style="padding: 8px; border-bottom: 1px solid #eee;"><strong>' . htmlspecialchars($request->roomType) . '</strong></td>
                        </tr>
                    </table>

                    <p style="margin-top: 20px;">We look forward to hosting you and ensuring a comfortable stay.</p>
                    <p style="margin-top: 15px;">If you have any special requests or questions, please do not hesitate to contact us.</p>

                    <p style="margin-top: 25px;">Warm regards,<br>
                    <strong>AnaghaSafar Team</strong></p>
                </div>
                <div style="background: #f1f1f1; padding: 15px; text-align: center; font-size: 12px; color: #777;">
                    © ' . date("Y") . ' AnaghaSafar. All rights reserved.
                </div>
            </div>
        </div>
        ';

        // Plain text version
        $mail->AltBody = "Dear {$request->user_name},\n\n"
            . "We are delighted to confirm your booking with {$request->hotel_name}.\n\n"
            . "Room Number: {$request->room_no}\n"
            . "Room Type: {$request->roomType}\n\n"
            . "We look forward to hosting you and ensuring a comfortable stay.\n\n"
            . "Regards,\nAnaghaSafar Team";

        $mail->send();

        return response()->json([
            'status' => 'success',
            'message' => 'Professional booking confirmation email sent successfully!'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo
        ]);
    }
}

    
}
