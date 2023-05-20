<?php

namespace App\Http\Controllers\Api;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    public function storeSubscribersEmail(Request $request)
    {
            $validator = Validator::make($request->all(),[
                'email' => 'required|email|unique:subscribers',
            ]);
            if($validator->fails()){
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages()
                ],400);
            }else{
                $subscriber = Subscriber::create([
                    'email'=>$request->email,
                ]);
                if($subscriber){
                    return response()->json([
                        "message" => "You are Subscribed!",
                        "status" => 200,
                    ], 200);
                }else{
                    return response()->json([
                        "message" => "something went wrong!",
                        "status" => 500,
                    ], 500);
                }
                
            }
    }
}