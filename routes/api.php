<?php

use App\Http\Controllers\BusineController;
use App\Http\Controllers\MipymeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/busine',[BusineController::class, 'index']);
Route::get('/busine/{id}',[BusineController::class, 'show']);
Route::post('/busine',[BusineController::class, 'store']);
Route::put('/busine/{id}',[BusineController::class, 'update']);
Route::patch('/busine/{id}',[BusineController::class, 'updatePartial']);
Route::delete('/busine/{id}',[BusineController::class, 'destroy']);


Route::get('/mipymes',[MipymeController::class, 'index']);
Route::get('/mipymes/{id}',[MipymeController::class, 'show']);
Route::post('/mipymes',[MipymeController::class, 'store']);
Route::put('/mipymes/{id}',[MipymeController::class, 'update']);
Route::patch('/mipymes/{id}',[MipymeController::class, 'updatePartial']);
Route::delete('/mipymes/{id}',[MipymeController::class, 'destroy']);