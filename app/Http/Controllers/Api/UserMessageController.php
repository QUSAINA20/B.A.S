<?php

namespace App\Http\Controllers\Api;

use App\Models\User_Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use Illuminate\Support\Facades\Validator;

class UserMessageController extends BaseController
{
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required|string',
            'company_name' => 'required|string',
            'position' => 'required|string',
            'number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            'service' => 'required|string',
            'email' => 'required|email|unique:users',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $message = User_Message::create($input);
        return $this->sendResponse(new MessageResource($message), 'Message Sent successfully.');
    }
}
