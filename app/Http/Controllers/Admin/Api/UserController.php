<?php

namespace App\Http\Controllers\Admin\Api;

use App\Events\FilesUploadedEvent;
use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'fullname' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9',
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'fullname' => $request->fullname,
            'service' => $request->service,
            'phone_number' => $request->phone_number,
            'company_name' => $request->company_name,
            'position' => $request->position,
            'password' => Hash::make($request->password),
        ]);
        $folder = new Folder();
        $folder->name = 'BAS';
        $folder->user_id = $user->id;
        $folder->save();
        // event(new Registered($user));
        $accessToken = $user->createToken('authToken')->plainTextToken;
        return response(['user' => $user, 'access_token' => $accessToken]);
    }
    public function showAllUsers()
    {
        $users = User::where('is_admin', 0)->orderBy('created_at', 'desc')->paginate(10);

        if ($users->isEmpty()) {
            return response()->json(['users' => []]);
        }

        return response()->json(['users' => $users]);
    }

    public function upload(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $validator = Validator::make($request->all(), [
                'files' => 'required|array',
                'files.*' => 'required|file',
            ]);

            $files = $request->file('files');

            $folder = Folder::where('user_id', $user->id)->where('name', 'BAS')->first();

            if (!$folder) {
                return response()->json(['error' => 'Folder not found'], 404);
            }

            $urls = collect($files)->map(function ($file) use ($user, $folder) {
                $media = $folder->addMedia($file)->toMediaCollection('documents');
                $media->copy($user, 'documents');
                return asset($media->getUrl());;
            });

            event(new FilesUploadedEvent($user, $urls));

            return response()->json(['urls' => $urls]);
        }
    }

    public function showFiles($id)
    {
        $user = User::with('media')->find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $files = $user->getMedia('documents');

            if ($files->isEmpty()) {
                return response()->json(['message' => 'No files', 'files' => [], 'user' => $user]);
            }

            $fileData = $files->map(function ($file) {
                return [
                    'id' => $file->id,
                    'url' => asset($file->getUrl())
                ];
            });

            return response()->json(['user' => $user, 'files' => $fileData]);
        }
    }


    public function deleteFiles(Request $request, $id)
    {

        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $files = $request->input('files');

            foreach ($files as $file) {
                $media = $user->media()->findOrFail($file);
                $media->delete();
            }

            return response()->json(['message' => 'Files deleted successfully']);
        }
    }
}
