<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\PatronMiddleware;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\CashPointController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\ClotureController;

Route::post('login', [
    AuthController::class,
    'login',
]);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('mon-espace', [
        AuthController::class,
        'espaceAgent',
    ]);

    Route::post('mon-espace/transactions', [
        AuthController::class,
        'transactionAgent',
    ]);

    Route::post('mon-espace/clotures', [
        AuthController::class,
        'clotureAgent',
    ]);

    Route::post('logout', [
        AuthController::class,
        'logout',
    ]);

    Route::middleware(
        PatronMiddleware::class
    )->group(function () {

        Route::get('agents', [
            AgentController::class,
            'index',
        ]);

        Route::post('agents', [
            AgentController::class,
            'store',
        ]);

        Route::get('cash-points', [
            CashPointController::class,
            'index',
        ]);

        Route::post('cash-points', [
            CashPointController::class,
            'store',
        ]);

        Route::get('transactions', [
            TransactionController::class,
            'index',
        ]);

        Route::post('transactions', [
            TransactionController::class,
            'store',
        ]);

        Route::get('clotures', [
            ClotureController::class,
            'index',
        ]);

        Route::post('clotures', [
            ClotureController::class,
            'store',
        ]);
    });
});