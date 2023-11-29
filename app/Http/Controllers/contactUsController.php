<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Requests\sendMessageRequest;
use App\Models\ContactUses;


class contactUsController extends Controller
{

    public function sendMessage(sendMessageRequest $request)
    {
        $request->validated();

        $name = $request->input('name');
        $email = $request->input('email');
        $subject = $request->input('subject');
        $message = $request->input('message');

        //return response()->json(['success' => true, 'message' => 'Message sent successfully']);

        $created = ContactUses::create([

            "name"=> $name,
            "email"=> $email,
            "subject"=> $subject,
            "message"=> $message
            
        ]);

        return response()->json([
            
            "Message details"=>$created
        ],200);
    }
}
