<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media as ModelsMedia;
use Spatie\MediaLibrary\Models\Media;

class UserController extends Controller
{

    public function upload(Request $request)
    {
        $user = $request->user();

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
            return response()->json(['error' => 'Storage limit exceeded. Maximum allowed storage is 1 GB.']);
        }

        $urls = collect($files)->map(function ($file) use ($user) {
            $media = $user->addMedia($file)->toMediaCollection('documents');
            return asset($media->getUrl());
        });

        return response()->json(['urls' => $urls]);
    }
    public function showTrashFiles(Request $request)
    {
        $user = $request->user();

        $documents = $user->getMedia('trash')->map(function ($file) {
            return [
                'id' => $file->id,
                'url' => asset($file->getUrl()),
            ];
        });

        return response()->json(['documents' => $documents]);
    }

    public function getTotalFileSize(Request $request)
    {
        $user = $request->user();

        $size = $user->getMedia('documents')->sum('size');
        $sizeInGB = $size / (1024 * 1024 * 1024);
        $sizeInGB = number_format($sizeInGB, 2);

        return response()->json(['total_file_size' => $sizeInGB . ' GB']);
    }
    public function restoreFiles(Request $request)
    {
        $user = $request->user();
        $files = $request->input('files');

        foreach ($files as $file) {
            $file = $user->getMedia('trash')->first();
            $file->move($user, 'documents');
        }

        return response()->json(['message' => 'Files restored successfully']);
    }
    public function emptyTrash(Request $request)
    {
        $user = $request->user();
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
