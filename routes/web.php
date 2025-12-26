<?php

use App\Http\Controllers\LevelGradeForUser;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ReportController;

// Redirect root ke login jika belum auth, atau ke home jika sudah login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    }
    return redirect('/login');
});

// Ini akan melindungi SEMUA rute di dalamnya (Home, Timesheet, Profile, dll)
Route::middleware(['auth', 'verified', 'prevent-back-history'])->group(function () {

    // Home - berbeda per role
    Route::get('/home', function () {
        if (auth::user()->isManager()) {
            return view('home-manager');
        } elseif (auth::user()->isAdmin()) {
            return view('home-admin');
        }
        return view('home-karyawan', ['title' => 'Home Page']);
    })->name('home');

    Route::get('/timesheet', [TimesheetController::class, 'index'])->name('timesheet.index');
    Route::get('/timesheet/create', [TimesheetController::class, 'create'])->name('timesheet.create');
    Route::get('/timesheet/{timesheet}/edit', [TimesheetController::class, 'edit'])->name('timesheet.edit');
    Route::post('/timesheet/store-new', [TimesheetController::class, 'storeNew'])->name('timesheet.store-new');
    Route::post('/timesheet/{timesheet}/store', [TimesheetController::class, 'store'])->name('timesheet.store');
    Route::post('/timesheet/{timesheet}/submit', [TimesheetController::class, 'submit'])->name('timesheet.submit');
    Route::get('/timesheet/{timesheet}', [TimesheetController::class, 'show'])->name('timesheet.show');

    //  print pdf
    Route::get('/timesheet/{timesheet}/pdf', [TimesheetController::class, 'exportPdf'])
        ->name('timesheet.pdf');

    // Manager only routes
    Route::middleware(['manager'])->group(function () {
        Route::post('/timesheet/{timesheet}/approve', [TimesheetController::class, 'approve'])->name('timesheet.approve');
        Route::post('/timesheet/{timesheet}/reject', [TimesheetController::class, 'reject'])->name('timesheet.reject');
    });

    // User Management (Manager only)
    Route::middleware(['auth', 'manager'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::post('/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('deactivate');
        Route::post('/{user}/activate', [UserManagementController::class, 'activate'])->name('activate');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    });

    // User Management (Admin only)
    Route::middleware(['auth', 'admin'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::post('/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('deactivate');
        Route::post('/{user}/activate', [UserManagementController::class, 'activate'])->name('activate');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    });

    // monthly report by user
    Route::get('/report/monthly', [ReportController::class, 'index'])->name('report.monthly');

    Route::get('/helpdeskit', function () {
        return view('helpdeskit', ['title' => 'Help Desk IT']);
    });

    Route::get('/helpdeskhr', function () {
        return view('helpdeskhr', ['title' => 'Help Desk HR']);
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';