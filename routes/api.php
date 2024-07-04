<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\genres\GenresController;
use App\Http\Controllers\api\members\MembersController;
use App\Http\Controllers\api\books\BooksController;
use App\Http\Controllers\api\loans\LoansController;

// Register & login routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Genre route
Route::get('/genres/index', [GenresController::class, 'index']);

// Book index routes
Route::get('/books/index', [BooksController::class, 'index']);
Route::get('/books/index/{id}', [BooksController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // Logout route
    Route::post('logout', [AuthController::class, 'logout']);

    // Genres routes
    Route::post('/genres/create', [GenresController::class, 'create']);
    // If working with form-data
    // Route::post('/genres/edit/{id}', [GenresController::class, 'update']);

    // If working with JSON body
    Route::put('/genres/edit/{id}', [GenresController::class, 'update']);
    Route::delete('/genres/delete/{id}', [GenresController::class, 'delete']);

    // Memebers routes
    Route::get('/members/index', [MembersController::class, 'index']);
    Route::get('/members/index/{id}', [MembersController::class, 'show']);
    Route::post('/members/create', [MembersController::class, 'create']);
    // Route::post('/members/edit/{id}', [MembersController::class, 'update']);
    
    // If working with JSON body
    Route::put('/members/edit/{id}', [MembersController::class, 'update']);
    Route::delete('/members/delete/{id}', [MembersController::class, 'delete']);

    // Books routes
    Route::post('/books/create', [BooksController::class, 'create']);
    Route::put('/books/edit/{id}', [BooksController::class, 'update']);
    Route::delete('/books/delete/{id}', [BooksController::class, 'delete']);

    // Loans routes
    Route::post('/loans/create', [LoansController::class, 'create']);
    Route::get('/loans/index', [LoansController::class, 'index']);
    Route::put('/loans/{id}/return', [LoansController::class, 'return']);

});







