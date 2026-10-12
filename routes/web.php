<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

// ----------------------------------------------------------------
// PUBLIC ROUTE (Smart Landing Page)
// ----------------------------------------------------------------
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard');
        }
        return redirect()->route('bookings.index');
    }
    return view('welcome');
});

// ----------------------------------------------------------------
// GUEST ROUTES (Login & Register)
// ----------------------------------------------------------------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


// ----------------------------------------------------------------
// PROTECTED ROUTES (Must be logged in to access anything below)
// ----------------------------------------------------------------
Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // ----------------------------------------------------------------
    // STAFF & ADMIN ROUTES (Management & Modifications)
    // MOVED ABOVE THE SHARED CATALOG TO FIX THE WILDCARD 404 TRAP
    // ----------------------------------------------------------------
    Route::middleware('staff')->group(function () {
        // Staff/Admin retain full create/edit/delete powers
        Route::resource('courts', CourtController::class)->except(['index', 'show']);
        Route::resource('equipment', EquipmentController::class)->except(['index', 'show']);
        Route::resource('announcements', AnnouncementController::class)->except(['index', 'show']);
        
        // Users module remains strictly locked to Staff/Admin
        Route::resource('users', UserController::class)->except(['show']); 
    });

    // ----------------------------------------------------------------
    // SHARED CATALOG ROUTES (Everyone can READ, including Athletes)
    // ----------------------------------------------------------------
    Route::resource('courts', CourtController::class)->only(['index', 'show']);
    Route::resource('equipment', EquipmentController::class)->only(['index', 'show']);
    Route::resource('announcements', AnnouncementController::class)->only(['index', 'show']);

    // ----------------------------------------------------------------
    // ADMIN ONLY ROUTES (Strict Dashboard access)
    // ----------------------------------------------------------------
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/report', [DashboardController::class, 'report'])->name('dashboard.report');
    });

    // ----------------------------------------------------------------
    // BOOKINGS (Shared access with internal controller logic)
    // ----------------------------------------------------------------
    Route::get('/bookings/calendar', [BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::get('/bookings/{booking}/print', [BookingController::class, 'print'])->name('bookings.print');
    Route::resource('bookings', BookingController::class);

});
