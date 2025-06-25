<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SampleEmail;
class MailController extends Controller
{
    //
    public function sendMail(Request $request){
        // $to_email = $request->input('email');
        $to_email = $request->input('email');
        $subject = 'Sample Mail from Laravel API';
        $content = 'This is a sample email sent from a Laravel API endpoint.';
        try {
            // dd("hellow owrd");
            // Mail::to($to_email)->send(new SampleEmail());

            // return response()->json(['message' => 'Email sent successfully'], 200);

            Mail::send([], [], function ($message) use ($to_email, $subject, $content) {
                $message->to($to_email)
                        ->subject($subject)
                        ->setBody($content, 'text/html');
            });

            return response()->json(['message' => 'Email sent successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send email: ' . $e->getMessage()], 500);
            //throw $th;
        }
    }
}
