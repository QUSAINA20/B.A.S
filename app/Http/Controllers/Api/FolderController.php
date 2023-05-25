<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Folder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FolderController extends Controller
{

    public function getAllFolders(){
            $folders = Folder::all()->where('user_id' , Auth::user()->id);
            return response()->json([$folders],200);
    }

    public function createFolder(Request $request){
            $user=User::find(Auth::user()->id);
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validator->fails()){
                return response()->json(['errors' => $validator->errors()], 422);
            }else{
                $folder = new Folder;
                $folder->name = $request->name;
                $user->folders()->save($folder);
                return response()->json([$folder ], 200);
            }
    }

    public function editFolder($id ,Request $request){
        $user = User::find(Auth::user()->id);
        if(!$user){
            return response()->json(['error' => 'User not found'], 404);
        }else{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }else{
                $folder = Folder::find($id);
                if(Auth::user()->id == $folder->user_id){
                    $folder->name = $request->name;
                    $folder->user_id = Auth::user()->id;
                    $folder->update();
                    return response()->json([$folder], 200);
                }else{
                    return response()->json(['message'=>"unauthorized"], 401);
                }
            }
        }
    }
    
    public function deleteFolders(Request $request){
            $user = User::find(Auth::user()->id);
            $folders = $request->input('folders');
            foreach ($folders as $folder) {
                if($folder){ 
                    $folder->move($user, 'trashFolders');
                    $folder->delete();
                }
            }
    }
}
