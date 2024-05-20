<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BusineController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MipymeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkShiftController;
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

Route::get('/roles',[RoleController::class, 'index']);
Route::get('/roles/{id}',[RoleController::class, 'show']);
Route::post('/roles',[RoleController::class, 'store']);
Route::put('/roles/{id}',[RoleController::class, 'update']);
Route::patch('/roles/{id}',[RoleController::class, 'updatePartial']);
Route::delete('/roles/{id}',[RoleController::class, 'destroy']);

Route::get('/users',[UserController::class, 'index']);
Route::get('/users/{id}',[UserController::class, 'show']);
Route::post('/users',[UserController::class, 'store']);
Route::put('/users/{id}',[UserController::class, 'update']);
Route::patch('/users/{id}',[UserController::class, 'updatePartial']);
Route::delete('/users/{id}',[UserController::class, 'destroy']);

Route::get('/comments',[CommentController::class, 'index']);
Route::get('/comments/{id}',[CommentController::class, 'show']);
Route::post('/comments',[CommentController::class, 'store']);
Route::put('/comments/{id}',[CommentController::class, 'update']);
Route::patch('/comments/{id}',[CommentController::class, 'updatePartial']);
Route::delete('/comments/{id}',[CommentController::class, 'destroy']);

Route::get('/workshifts',[WorkShiftController::class, 'index']);
Route::get('/workshifts/{id}',[WorkShiftController::class, 'show']);
Route::post('/workshifts',[WorkShiftController::class, 'store']);
Route::put('/workshifts/{id}',[WorkShiftController::class, 'update']);
Route::patch('/workshifts/{id}',[WorkShiftController::class, 'updatePartial']);
Route::delete('/workshifts/{id}',[WorkShiftController::class, 'destroy']);