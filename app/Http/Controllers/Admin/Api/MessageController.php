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
        $query = User_Message::orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where("name", "like", "%" . $search . "%")
                    ->orWhere("company_name", "like", "%" . $search . "%")
                    ->orWhere("service", "like", "%" . $search . "%");
            });
        }

        $messages = $query->paginate(5);

        if ($messages->isEmpty()) {
            return $this->sendResponse([], 'No messages found.');
        }

        return $this->sendResponse(MessageResource::collection($messages), 'Messages retrieved successfully.');
    }

    public function show($id)
    {
        $message = User_Message::findOrFail($id);

        return $this->sendResponse(MessageResource::collection([$message]), 'Message retrieved successfully.');
    }
}
