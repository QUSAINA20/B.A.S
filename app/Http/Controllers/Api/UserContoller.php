<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

        $currentStorageUsage = $this->getTotalFileSize($user);
        $currentStorageUsageInGB = (float) str_replace(' GB', '', $currentStorageUsage);

        if (($totalSizeInGB + $currentStorageUsageInGB) > 1) {
            return response()->json(['error' => 'Storage limit exceeded. Maximum allowed storage is 1 GB.']);
        }

        $urls = collect($files)->map(function ($file) use ($user) {
            $media = $user->addMedia($file)->toMediaCollection('documents');
            return asset($media->getUrl());
        });

        return response()->json(['urls' => $urls]);
    }

    public function showFiles(Request $request)
    {
        $user = $request->user();

        $files = $user->getMedia('documents');
        $urls = $files->map(function ($file) {
            return asset($file->getUrl());
        });

        return response()->json(['urls' => $urls]);
    }

    public function deleteFiles(Request $request)
    {
        $user = $request->user();
        $files = $request->input('files');

        foreach ($files as $file) {
            $media = $user->media()->findOrFail($file);
            $media->delete();
        }

        return response()->json(['message' => 'Files deleted successfully']);
    }

    public function showSoftDeleteFiles(Request $request)
    {
        $user = $request->user();

        $documents = $user->getMedia('documents')->onlyTrashed();
        return response()->json([
            'documents' => $documents,
        ]);
    }

    public function getTotalFileSize(Request $request)
    {
        $user = $request->user();

        $size = $user->getMedia('documents')->sum('size');
        $sizeInGB = $size / (1024 * 1024 * 1024);
        $sizeInGB = number_format($sizeInGB, 2);

        return response()->json(['total_file_size' => $sizeInGB . ' GB']);
    }
}
