<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\UserMessageController;
use App\Http\Controllers\Admin\Api\MessageController;
use App\Http\Controllers\Admin\Api\UserController as AdminUserController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\SubscriberController;

// use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::post('/login', [LoginController::class, 'login']);



Route::middleware(['auth:sanctum', 'checkUserId'])->group(function () {
    Route::post('/logout/{user_id}', [LogoutController::class, 'logout']);
    Route::post('/upload/{user_id}', [UserController::class, 'upload']);
    Route::get('/files/{user_id}', [UserController::class, 'showFiles']);
    Route::delete('/files/{user_id}', [UserController::class, 'deleteFiles']);
    Route::get('/soft-delete-files/{user_id}', [UserController::class, 'showTrashFiles']);
    Route::post('/files/restore/{user_id}', [UserController::class, 'restoreFiles']);
    Route::get('/total-file-size/{user_id}', [UserController::class, 'getTotalFileSize']);
    Route::delete('/empty-trash/{user_id}', [UserController::class, 'emptyTrash']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/folders', [FolderController::class , 'getAllFolders']);
    Route::post('/folders', [FolderController::class , 'createFolder']);
    Route::put('/folder/{folder_id}/edit' , [FolderController::class , 'editFolder']);
    Route::get('/folders/{folder_id}/delete', [FolderController::class, 'deleteFolders']);
    Route::get('/folders/{folder_id}', [FolderController::class, 'showFolder']);
});


Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::post('/admin/logout/{user_id}', [LogoutController::class, 'logout']);
    Route::post('/admin/register', [AdminUserController::class, 'register']);
    Route::get('/admin/users', [AdminUserController::class, 'showAllUsers']);
    Route::post('/admin/user/{user_id}/upload', [AdminUserController::class, 'upload']);
    Route::get('/admin/user/{user_id}/files', [AdminUserController::class, 'showFiles']);
    Route::delete('/admin/user/{user_id}/files', [AdminUserController::class, 'deleteFiles']);
    Route::get('/admin/messages', [MessageController::class, 'index']);
    Route::get('/admin/messages/{id}', [MessageController::class, 'show']);
});

Route::post('/store-subscribers', [SubscriberController::class, 'storeSubscribersEmail']);

Route::post('/save-message', [UserMessageController::class, 'store']);
