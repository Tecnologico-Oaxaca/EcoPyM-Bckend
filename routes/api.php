<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\BranchController;
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

Route::get('/branches',[BranchController::class, 'index']);
Route::get('/branches/{id}',[BranchController::class, 'show']);
Route::post('/branches',[BranchController::class, 'store']);
Route::put('/branches/{id}',[BranchController::class, 'update']);
Route::patch('/branches/{id}',[BranchController::class, 'updatePartial']);
Route::delete('/branches/{id}',[BranchController::class, 'destroy']);

Route::get('/areas',[AreaController::class, 'index']);
Route::get('/areas/{id}',[AreaController::class, 'show']);
Route::post('/areas',[AreaController::class, 'store']);
Route::put('/areas/{id}',[AreaController::class, 'update']);
Route::patch('/areas/{id}',[AreaController::class, 'updatePartial']);
Route::delete('/areas/{id}',[AreaController::class, 'destroy']);