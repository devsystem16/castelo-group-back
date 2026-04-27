<?php

use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\AffiliateController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\PropertyController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{property}', [PropertyController::class, 'show']);

Route::post('/contacts', [ContactController::class, 'store']);

Route::post('/affiliates/register', [AffiliateController::class, 'register']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Properties management (admin)
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{property}', [PropertyController::class, 'update']);
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy']);
    Route::post('/properties/{property}/media', [PropertyController::class, 'uploadMedia']);
    Route::delete('/properties/{property}/media/{media}', [PropertyController::class, 'deleteMedia']);

    // Affiliate dashboard
    Route::get('/affiliates/dashboard', [AffiliateController::class, 'dashboard']);
    Route::get('/affiliates/commissions', [AffiliateController::class, 'commissions']);

    // Admin routes
    Route::middleware('can:admin')->prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);
        Route::get('/affiliates', [AdminController::class, 'affiliates']);
        Route::put('/affiliates/{affiliate}/status', [AdminController::class, 'updateAffiliateStatus']);
        Route::get('/sales', [AdminController::class, 'sales'])->missing(fn() => response()->json(['data' => []]));
        Route::post('/sales', [AdminController::class, 'createSale']);
        Route::get('/commissions', [AdminController::class, 'commissions']);
        Route::put('/commissions/{commission}/approve', [AdminController::class, 'approveCommission']);
        Route::put('/commissions/{commission}/paid', [AdminController::class, 'markCommissionPaid']);
        Route::get('/contacts', [ContactController::class, 'index']);
        Route::put('/contacts/{contact}/read', [ContactController::class, 'markRead']);
    });
});
