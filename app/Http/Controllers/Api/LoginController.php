<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $validator['email'], 'password' => $validator['password']])) {
            $user = Auth::user();
            $accessToken = $user->createToken('authToken')->accessToken;
            return response(['user' => $user, 'access_token' => $accessToken]);
        } else {
            return response(['error' => 'Unauthorised'], 401);
        }
    }
}
