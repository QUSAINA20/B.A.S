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
        $search = $request->input('search');
        $query = User_Message::orderBy('latest' , 'desc');

        if ($search) {
            $query->where(function($q) use($search){
                    $q->Where("name", "like" , "%".$search."%")
                    ->orWhere("company_name", "like" , "%".$search."%")
                    ->orWhere("service", "like" , "%".$search."%");
                });
        }

        $messages = $query::paginate(5);
        return $this->sendResponse(MessageResource::collection($messages), 'Messages retrives successfully.');
    }

    public function show($id)
    {
        $messages = User_Message::find($id);
        return $this->sendResponse(MessageResource::collection($messages), 'Message retrived successfully.');
    }

}
