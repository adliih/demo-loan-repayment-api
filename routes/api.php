<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RepaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', [AuthController::class, "register"])->name('register');
    Route::post('login', [AuthController::class, "login"])->name('login');
});

Route::prefix('loans')->name('loans.')->middleware("auth:sanctum")->group(function () {
    Route::get('/', [LoanController::class, "index"])->name('index')->middleware(['can:isCustomer']);
    Route::post('/', [LoanController::class, "store"])->name('store')->middleware(['can:isCustomer']);

    Route::prefix('{id}')->group(function () {
        Route::post('/approve', [LoanController::class, "approve"])->name('approve')->middleware(['can:isAdmin']);

        Route::prefix('repayments')->name('repayments.')->group(function () {
            Route::post('/', [RepaymentController::class, "store"])->name('store')->middleware(['can:isCustomer']);
        });
    });
});