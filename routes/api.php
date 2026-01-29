<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\APIWITrackProjectController;
use App\Http\Controllers\API\APIWITrackPOController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Remove the api prefix since it's already handled by RouteServiceProvider
Route::post('api/get_wiTrack_project_details', [APIWITrackProjectController::class, 'get_wiTrack_project_details']);
Route::post('api/notify_wiTrack_project_cancelled', [APIWITrackProjectController::class, 'notify_wiTrack_project_cancelled']);
Route::post('api/get_wiTrack_po_details', [APIWITrackPOController::class, 'get_wiTrack_po_details']);

Route::post('api/sendProjectCompleteMsgToWitrack', [APIWITrackProjectController::class, 'sendProjectCompleteMsgToWitrack'])->name('api.send-project-complete');

Route::post('api/sendProjectFullCompleteMsgToWitrack', [APIWITrackProjectController::class, 'sendProjectFullCompleteMsgToWitrack'])->name('api.send-project-full-complete');

Route::post('api/sendProjectPartialCompleteMsgToWitrack', [APIWITrackProjectController::class, 'sendProjectPartialCompleteMsgToWitrack'])->name('api.send-project-partial-complete');

Route::post('/api/reject-order-pro-eng', [APIWITrackProjectController::class, 'orderRejectByProductionEng'])->name('api.reject-project');