<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BusineController;
use App\Http\Controllers\CashOpeningController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MipymeController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\RegisterBusinesController;
use App\Http\Controllers\RegisterEmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnitQuantityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkShiftController;
use App\Models\Department;
use App\Models\UnitQuantity;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/registerBusines', [RegisterBusinesController::class, 'store']);
Route::post('/registerEmployee', [RegisterEmployeeController::class, 'store']);

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/logout', [AuthController::class, 'logout']);
});



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

Route::get('/days',[DayController::class, 'index']);
Route::get('/days/{id}',[DayController::class, 'show']);
Route::post('/days',[DayController::class, 'store']);
Route::put('/days/{id}',[DayController::class, 'update']);
Route::patch('/days/{id}',[DayController::class, 'updatePartial']);
Route::delete('/days/{id}',[DayController::class, 'destroy']);

Route::get('/contracts',[ContractController::class, 'index']);
Route::get('/contracts/{id}',[ContractController::class, 'show']);
Route::post('/contracts',[ContractController::class, 'store']);
Route::put('/contracts/{id}',[ContractController::class, 'update']);
Route::patch('/contracts/{id}',[ContractController::class, 'updatePartial']);
Route::delete('/contracts/{id}',[ContractController::class, 'destroy']);

Route::get('/cashOpenings',[CashOpeningController::class, 'index']);
Route::post('/cashOpenings',[CashOpeningController::class, 'store']);

Route::get('/companies',[CompanyController::class, 'index']);
Route::get('/companies/{id}',[CompanyController::class, 'show']);
Route::post('/companies',[CompanyController::class, 'store']);
Route::put('/companies/{id}',[CompanyController::class, 'update']);
Route::patch('/companies/{id}',[CompanyController::class, 'updatePartial']);
Route::delete('/companies/{id}',[CompanyController::class, 'destroy']);

Route::get('/providers',[ProviderController::class, 'index']);
Route::get('/providers/{id}',[ProviderController::class, 'show']);
Route::post('/providers',[ProviderController::class, 'store']);
Route::put('/providers/{id}',[ProviderController::class, 'update']);
Route::patch('/providers/{id}',[ProviderController::class, 'updatePartial']);
Route::delete('/providers/{id}',[ProviderController::class, 'destroy']);

Route::get('/categories',[CategoryController::class, 'index']);
Route::get('/categories/{id}',[CategoryController::class, 'show']);
Route::post('/categories',[CategoryController::class, 'store']);
Route::put('/categories/{id}',[CategoryController::class, 'update']);
Route::patch('/categories/{id}',[CategoryController::class, 'updatePartial']);
Route::delete('/categories/{id}',[CategoryController::class, 'destroy']);

Route::get('/departments',[DepartmentController::class, 'index']);
Route::get('/departments/{id}',[DepartmentController::class, 'show']);
Route::post('/departments',[DepartmentController::class, 'store']);
Route::put('/departments/{id}',[DepartmentController::class, 'update']);
Route::patch('/departments/{id}',[DepartmentController::class, 'updatePartial']);
Route::delete('/departments/{id}',[DepartmentController::class, 'destroy']);

Route::get('/units',[UnitQuantityController::class, 'index']);
Route::get('/units/{id}',[UnitQuantityController::class, 'show']);
Route::post('/units',[UnitQuantityController::class, 'store']);
Route::put('/units/{id}',[UnitQuantityController::class, 'update']);
Route::patch('/units/{id}',[UnitQuantityController::class, 'updatePartial']);
Route::delete('/units/{id}',[UnitQuantityController::class, 'destroy']);
