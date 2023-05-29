<?php

use App\Http\Controllers\Api\ThingspeakController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MeasuringAndStatusController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\GreenhouseController;

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
/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
//Public routes
Route::post('Greenhouse/login', [AuthController::class, 'login']);
Route::post('Greenhouse/register', [AuthController::class, 'register']);
Route::post('Greenhouse/submitForgotPassword', [AuthController::class, 'submitForgetPasswordForm']);
Route::post('Greenhouse/resetPassword/{token}', [AuthController::class, 'resetPassword']);

//Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('Greenhouse/logout', [AuthController::class, 'logout']);
    Route::get('Greenhouse/greenhouses', [GreenhouseController::class, 'getUserGreenhouses']);
    Route::get('Greenhouse/measuringStatus/{greenhouse}', [MeasuringAndStatusController::class, 'showLastData']);
    Route::get('Greenhouse/displayParametersToUser/{id}', [GreenhouseController::class, 'getDisplayedMeasurementStatuses']);
    Route::post('Greenhouse/writeConfigurations/{id}', [ThingspeakController::class, 'writeDataToThingSpeakConfiguration']);
    Route::get('Greenhouse/readConfigurations/{id}', [ThingspeakController::class, 'readDataFromThingSpeakConfiguration']);
    Route::put('Greenhouse/updateConfigurations/{id}', [ThingspeakController::class, 'updateConfiguration']);
    Route::put('Greenhouse/updateManagements/{id}', [ThingspeakController::class, 'updateManagement']);
    Route::put('Greenhouse/updateRemoteMode/{id}', [ThingspeakController::class, 'updateRemoteMode']);
    Route::get('Greenhouse/measurement_statuses_by_time/{greenhouse_id}', [MeasuringAndStatusController::class, 'getDisplayedMeasurementStatusesByTime']);
    Route::put('Greenhouse/updateUser/{id}', [AuthController::class, 'updateUserData']);
    Route::get('Greenhouse/readLastDataConfiguration/{id}', [ThingspeakController::class, 'readLastDataConfiguration']);
    Route::get('Greenhouse/user', [AuthController::class, 'getLoggedInUserId']);
    Route::get('Greenhouse/readLastDataConfigurationAccess/{id}', [GreenhouseController::class, 'getConfigurationCommandAccess']);
});

//Admin routes
Route::group(['middleware' => ['auth:sanctum', 'admin']], function () {
    Route::post('Greenhouse/admin/logout', [AuthController::class, 'adminLogout']);
    Route::get('Greenhouse/admin/users', [AdminController::class, 'allUsers']);
    Route::delete('Greenhouse/admin/deleteUser/{id}', [AdminController::class, 'deleteUser']);
    Route::post('Greenhouse/admin/createGreenhouse/{id}', [GreenhouseController::class, 'createGreenhouse']);
    Route::get('Greenhouse/admin/createdGreenhouses', [GreenhouseController::class, 'greenhouseCreatedByAdmin']);
    Route::delete('Greenhouse/admin/deleteGreenhouse/{id}', [GreenhouseController::class, 'deleteGreenhouse']);
    Route::put('Greenhouse/admin/updateGreenhouse/{id}', [GreenhouseController::class, 'updateGreenhouse']);
    Route::put('Greenhouse/admin/updateUser/{id}', [AdminController::class, 'updateUser']);
    Route::get('Greenhouse/admin/greenhouseWithMeasuringAndStatus', [GreenhouseController::class, 'showAllGreenhouseAndMeasuringAndStatus']);
    Route::put('Greenhouse/admin/assignParameters/{id}', [GreenhouseController::class, 'assignParameters']);
    Route::put('Greenhouse/admin/assignConfigurationCommand/{id}', [GreenhouseController::class, 'assignConfigurationCommand']);
    Route::put('Greenhouse/admin/assignManagementCommand/{id}', [GreenhouseController::class, 'assignManagementCommand']);
    Route::post('Greenhouse/admin/assignThingspeak/{id}', [GreenhouseController::class, 'assignThingspeak']);
    Route::put('Greenhouse/admin/updateThingspeak/{id}', [GreenhouseController::class, 'updateThingspeak']);
    Route::delete('Greenhouse/admin/deleteThingspeak/{id}', [GreenhouseController::class, 'deleteThingspeak']);
    Route::get('Greenhouse/admin/readThingspeak/{id}', [GreenhouseController::class, 'readThingspeak']);
});
