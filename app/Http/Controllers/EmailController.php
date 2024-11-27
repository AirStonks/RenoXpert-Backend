<?php

namespace App\Http\Controllers;

use App\Mail\HelloEmail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function sendEmail()
    {
        /** 
         * Store a receiver email address to a variable.
         */
        $receiverEmailAddress = "ck.yongkang@belive.asia";

        /**
         * Import the Mail class at the top of this page,
         * and call the to() method for passing the 
         * receiver email address.
         * 
         * Also, call the send() method to incloude the
         * HelloEmail class that contains the email template.
         */
        try {
            Mail::to($receiverEmailAddress)->send(new HelloEmail());
            return "Email has been sent successfully.";
        } catch (Exception $e) {
            // Log the error or return the error message
            return "Oops! There was some error sending the email.";
        }
    }
}
