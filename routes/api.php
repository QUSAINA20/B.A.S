<?php

use App\Http\Controllers\Admin\Api\UserController as AdminUserController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    Route::post('/upload', [UserController::class, 'upload']);
    Route::get('/files', [UserController::class, 'showFiles']);
    Route::delete('/files', [UserController::class, 'deleteFiles']);
    Route::get('/soft-delete-files', [UserController::class, 'showTrashFiles']);
    Route::post('/files/restore', [UserController::class, 'restoreFiles']);
    Route::get('/total-file-size', [UserController::class, 'getTotalFileSize']);
    Route::delete('/empty-trash', [UserController::class, 'emptyTrash']);
});

Route::post('/admin/register', [AdminUserController::class, 'register']);
Route::middleware('auth:api')->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'showAllUsers']);
    Route::post('/admin/user/{user}/upload', [AdminUserController::class, 'upload']);
    Route::get('/admin/user/{user}/files', [AdminUserController::class, 'showFiles']);
    Route::delete('/admin/user/{user}/files', [AdminUserController::class, 'deleteFiles']);
});
