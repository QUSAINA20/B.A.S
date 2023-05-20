<?php

namespace App\Http\Controllers\Admin\Api;

use App\Models\User_Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Http\Controllers\Api\BaseController;

class MessageController extends BaseController
{
    public function index(Request $request)
    {
        $messages = User_Message::orderBy('latest', 'desc')
            ->paginate(5);
        return $this->sendResponse(MessageResource::collection($messages), 'Messages retrives successfully.');
    }

    public function show($id)
    {
        $messages = User_Message::find($id);
        return $this->sendResponse(MessageResource::collection($messages), 'Message retrived successfully.');
    }

    public function search($any)
    {
        return User_Message::where("name", "like", "%" . $any . "%")->orWhere("service", "like", "%" . $any . "%")->orWhere("company_name", "like", "%" . $any . "%")->paginate(2);
    }
}
