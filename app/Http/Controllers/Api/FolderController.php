<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Folder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FolderController extends Controller
{

    public function getAllFolders()
    {
        $folders = Folder::where('user_id', Auth::user()->id)->get();

        if ($folders->isEmpty()) {
            return response()->json(['folders' => []]);
        }

        return response()->json(['folders' => $folders]);
    }

    public function createFolder(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {
            $folder = new Folder;
            $folder->name = $request->name;
            $user->folders()->save($folder);
            return response()->json([$folder], 200);
        }
    }

    public function editFolder($id, Request $request)
    {
        $user = User::find(Auth::user()->id);
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {
            $folder = Folder::find($id);
            if (Auth::user()->id == $folder->user_id) {
                $folder->name = $request->name;
                $folder->user_id = Auth::user()->id;
                $folder->update();
                return response()->json([$folder], 200);
            } else {
                return response()->json(['message' => "unauthorized"], 401);
            }
        }
    }

    public function addFilesToFolder($id, Request $request)
    {
        $user = User::find(Auth::user()->id);
        $folder = Folder::find($id);
        $validator = Validator::make($request->all(), [
            'files' => 'required|array',
            'files.*' => 'required|file',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $files = $request->file('files');
        $totalSize = 0;

        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }
        $totalSizeInGB = $totalSize / (1024 * 1024 * 1024);

        $documentsSize = $user->getMedia('documents')->sum('size') / (1024 * 1024 * 1024);
        $trashSize = $user->getMedia('trash')->sum('size') / (1024 * 1024 * 1024);

        if (($totalSizeInGB + $documentsSize + $trashSize) > 1) {
            return response()->json(['error' => 'Storage limit exceeded. Maximum allowed storage (docs and trash) is 1 GB.']);
        }




        $urls = collect($files)->map(function ($file) use ($folder, $user) {
            $media = $folder->addMedia($file)->toMediaCollection('documents');
            $copiedMedia = $media->copy($user, 'documents');

            return $copiedMedia->getUrl();
        });


        return response()->json(['urls' => $urls]);
    }

    public function showFilesInFolder($id)
    {
        $user = User::find(Auth::user()->id);
        $folder = Folder::with('media')->find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } elseif (!$folder) {
            return response()->json(['error' => 'Folder not found'], 404);
        } else {
            $files = $folder->getMedia('documents');
            $folders_info = $user->folders()->select('id', 'name')->get();
            if ($files->isEmpty()) {
                return response()->json(['message' => 'No files', 'files' => [], 'folder-name' => $folder->name, 'folders-info' => $folders_info]);
            }

            $fileData = $files->map(function ($file) {
                return [
                    'id' => $file->id,
                    'url' => asset($file->getUrl()),
                    'created_at' => $file->created_at->toDateTimeString(),
                ];
            });

            return response()->json(['folder-name' => $folder->name, 'files' => $fileData, 'folders-info' => $folders_info]);
        }
    }


    public function deleteFolders(Request $request)
    {
        $user = User::find(Auth::user()->id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $folders = $request->input('folders');
            foreach ($folders as $folder) {
                $folder = Folder::find($folder);
                if ($folder) {
                    $files = $folder->getMedia('documents');
                    foreach ($files as $file) {
                        $file = $folder->getMedia('documents')->find($file);
                        $userFile = $user->getMedia('documents')->where('file_name', $file->file_name)->first();
                        if ($file) {
                            $file->move($user, 'trash');
                            $userFile->delete();
                            $file->delete();
                        }
                    }
                    $folder->delete();
                }
            }
            return response()->json(['message' => 'Folders moved to trash']);
        }
    }
    public function deleteFilesFromFolder(Request $request, $id)
    {
        $folder = Folder::find($id);
        if (!$folder) {
            return response()->json(['error' => 'Folder not found'], 404);
        } else {
            $files = $request->input('files');

            foreach ($files as $file) {
                $file = $folder->getMedia('documents')->find($file);
                if ($file) {
                    $file->delete();
                }
            }

            return response()->json(['message' => 'Files removed from folder']);
        }
    }
}
