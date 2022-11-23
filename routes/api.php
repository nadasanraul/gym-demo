<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceLineController;
use App\Http\Controllers\UserController;
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

Route::apiResource('invoices', InvoiceController::class);
Route::post('invoices/{invoice}/lines', [InvoiceLineController::class, 'store']);
Route::post('users/{user}/checkin', [UserController::class, 'checkin']);
