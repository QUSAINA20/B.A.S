<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\Api\UserMessageController;
use App\Http\Controllers\Admin\Api\MessageController;
use App\Http\Controllers\Admin\Api\UserController as AdminUserController;

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


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::post('/upload', [UserController::class, 'upload']);
    Route::get('/files', [UserController::class, 'showFiles']);
    Route::delete('/files', [UserController::class, 'deleteFiles']);
    Route::get('/soft-delete-files', [UserController::class, 'showTrashFiles']);
    Route::post('/files/restore', [UserController::class, 'restoreFiles']);
    Route::get('/total-file-size', [UserController::class, 'getTotalFileSize']);
    Route::delete('/empty-trash', [UserController::class, 'emptyTrash']);
});


Route::middleware(['auth:api', 'isAdmin'])->group(function () {
    Route::post('/admin/logout', [LogoutController::class, 'logout']);
    Route::post('/admin/register', [AdminUserController::class, 'register']);
    Route::get('/admin/users', [AdminUserController::class, 'showAllUsers']);
    Route::post('/admin/user/{user}/upload', [AdminUserController::class, 'upload']);
    Route::get('/admin/user/{user}/files', [AdminUserController::class, 'showFiles']);
    Route::delete('/admin/user/{user}/files', [AdminUserController::class, 'deleteFiles']);
    Route::get('/admin/showMessages',[MessageController::class,'index'])->name('showMessages');
    Route::get('/admin/getMessage/{id}',[MessageController::class,'show'])->name('getMessage');
    Route::get('/admin/search/{any}',[MessageController::class,'search'])->name('search');
});

Route::post('/store/subscribers', [SubscriberController::class, 'storeSubscribersEmail']);

Route::post('/SaveMessage' , [UserMessageController::class, 'store'])->name('SaveMessage');
