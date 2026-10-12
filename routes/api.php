<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CoachController;
use App\Http\Controllers\Api\CourtController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\MembershipController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\UserController;

Route::name('api.')->group(function () {
// // Keep API route names separate from the similarly named web routes.
// Route::name('api.')->group(function () {
//     Route::post('/login', [AuthController::class, 'login'])->name('login');
//     Route::post('/register', [AuthController::class, 'register'])->name('register');

//     // Legacy read endpoints protected by the configured API access-token middleware.
//     Route::get('/user', [UserController::class, 'index'])->middleware('preventAccess')->name('legacy.user.index');
//     Route::get('/court', [CourtController::class, 'index'])->middleware('preventAccess')->name('legacy.court.index');
//     Route::get('/booking', [BookingController::class, 'index'])->middleware('preventAccess')->name('legacy.booking.index');

//     // Token-based logout remains available for Sanctum-authenticated clients.
//     Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('logout');

    // CRUD endpoints use X-API-TOKEN plus the configured Origin check.
    Route::middleware('preventAccess')->group(function () {
        // Courts
        Route::get('/courts', [CourtController::class, 'index'])->name('courts.index');
        Route::post('/courts', [CourtController::class, 'store'])->name('courts.store');
        Route::get('/courts/{id}', [CourtController::class, 'find'])->name('courts.show');
        Route::match(['put', 'patch'], '/courts/{id}', [CourtController::class, 'update'])->name('courts.update');
        Route::delete('/courts/{id}', [CourtController::class, 'delete'])->name('courts.destroy');

        // Equipment
        Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
        Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
        Route::get('/equipment/{id}', [EquipmentController::class, 'find'])->name('equipment.show');
        Route::match(['put', 'patch'], '/equipment/{id}', [EquipmentController::class, 'update'])->name('equipment.update');
        Route::delete('/equipment/{id}', [EquipmentController::class, 'delete'])->name('equipment.destroy');

        // Announcements
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/announcements/{id}', [AnnouncementController::class, 'find'])->name('announcements.show');
        Route::match(['put', 'patch'], '/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{id}', [AnnouncementController::class, 'delete'])->name('announcements.destroy');

        // Bookings
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{id}', [BookingController::class, 'find'])->name('bookings.show');
        Route::match(['put', 'patch'], '/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
        Route::delete('/bookings/{id}', [BookingController::class, 'delete'])->name('bookings.destroy');

        // Coaches
        Route::get('/coaches', [CoachController::class, 'index'])->name('coaches.index');
        Route::post('/coaches', [CoachController::class, 'store'])->name('coaches.store');
        Route::get('/coaches/{id}', [CoachController::class, 'find'])->name('coaches.show');
        Route::match(['put', 'patch'], '/coaches/{id}', [CoachController::class, 'update'])->name('coaches.update');
        Route::delete('/coaches/{id}', [CoachController::class, 'delete'])->name('coaches.destroy');

        // Memberships
        Route::get('/memberships', [MembershipController::class, 'index'])->name('memberships.index');
        Route::post('/memberships', [MembershipController::class, 'store'])->name('memberships.store');
        Route::get('/memberships/{id}', [MembershipController::class, 'find'])->name('memberships.show');
        Route::match(['put', 'patch'], '/memberships/{id}', [MembershipController::class, 'update'])->name('memberships.update');
        Route::delete('/memberships/{id}', [MembershipController::class, 'delete'])->name('memberships.destroy');

        // Sales
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/sales/{id}', [SaleController::class, 'find'])->name('sales.show');
        Route::match(['put', 'patch'], '/sales/{id}', [SaleController::class, 'update'])->name('sales.update');
        Route::delete('/sales/{id}', [SaleController::class, 'delete'])->name('sales.destroy');

        // Users (protected by the same API key and Origin checks as the other CRUD endpoints)
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'find'])->name('users.show');
        Route::match(['put', 'patch'], '/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'delete'])->name('users.destroy');
    });
});
