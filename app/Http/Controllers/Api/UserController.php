<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Folder;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media as ModelsMedia;

class UserController extends Controller
{

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

            $urls = collect($files)->map(function ($file) use ($user) {
                $media = $user->addMedia($file)->toMediaCollection('documents');
                return asset($media->getUrl());
            });

            return response()->json(['urls' => $urls]);
        }
    }
    public function showTrashFiles($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $documents = $user->getMedia('trash')->map(function ($file) {
                return [
                    'id' => $file->id,
                    'url' => asset($file->getUrl()),
                ];
            });
            if ($documents->isEmpty()) {
                return response()->json(['message' => 'No trashed files', 'documents' => []]);
            }

            return response()->json(['documents' => $documents]);
        }
    }

    public function getTotalFileSize($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $size = $user->getMedia('documents')->sum('size');
            $sizeInGB = $size / (1024 * 1024 * 1024);
            $sizeInGB = number_format($sizeInGB, 3);

            return response()->json(['total_file_size' => $sizeInGB . ' GB']);
        }
    }
    public function restoreFiles(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $files = $request->input('files');

            foreach ($files as $file) {
                $file = $user->getMedia('trash')->find($file);
                $file->move($user, 'documents');
            }

            return response()->json(['message' => 'Files restored successfully']);
        }
    }
    public function deleteFilesPermanently(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $files = $request->input('files');

            foreach ($files as $file) {
                $media = $user->media()
                    ->where('id', $file)
                    ->where('collection_name', 'trash')
                    ->first();

                if ($media) {
                    $media->delete();
                }
            }
            return response()->json(['message' => 'Selected files deleted permanently from trash']);
        }
    }
    public function emptyTrash($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $media = $user->media()
                ->where('collection_name', 'trash')
                ->get();
            foreach ($media as $file) {
                $file->delete();
            }
        }
        return response()->json(['message' => 'All files deleted permanently from trash']);
    }

    public function moveFilesToFolder(Request $request)
    {
        $folderId = $request->input('folder_id');
        $fileIds = $request->input('file_ids');

        $folder = Folder::find($folderId);
        if (!$folder) {
            return response()->json(['error' => 'Folder not found'], 404);
        }

        $user = User::find(Auth::user()->id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        foreach ($fileIds as $fileId) {
            $media = $user->getMedia('documents')->find($fileId);
            if ($media) {
                $media->copy($folder, 'documents');
            }
        }

        return response()->json(['message' => 'Files moved to folder successfully']);
    }

    public function showFiles($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $files = $user->getMedia('documents');

            if ($files->isEmpty()) {
                return response()->json(['message' => 'No files', 'files' => []]);
            }
        }
        $folders_info = $user->folders()->select('id', 'name')->get();
        $fileData = $files->map(function ($file) {
            return [
                'id' => $file->id,
                'url' => asset($file->getUrl())
            ];
        });
        return response()->json(['files' => $fileData, 'folders_info' => $folders_info]);
    }


    public function deleteFiles(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        } else {
            $files = $request->input('files');

            foreach ($files as $file) {
                $file = $user->getMedia('documents')->find($file);
                if ($file) {
                    $file->move($user, 'trash');
                    $file->delete();
                }
            }

            return response()->json(['message' => 'Files moved to trash']);
        }
    }
}
