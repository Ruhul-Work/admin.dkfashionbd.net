<?php
use App\Http\Controllers\backend\AuthController;
use App\Http\Controllers\CoreController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {

    // Redirect root to dashboard 
    Route::get('/', function () {
        return redirect()->route('backend.dashboard');
    });

    // Redirect /login to backend login
    Route::get('/login', function () {
        return redirect()->route('backend.login');
    })->name('login');

});

// ==== Backend (admin) ====
Route::prefix('backend')
    ->name('backend.')
    ->middleware(['web', 'firewall'])
    ->group(function () {

        // Guest-only routes
        Route::middleware('guest')->group(function () {
            Route::get('login', [AuthController::class, 'showLogin'])->name('login');
            Route::post('login', [AuthController::class, 'login'])->name('login.action');

            Route::get('register', [AuthController::class, 'showRegister'])->name('register');
            Route::post('register', [AuthController::class, 'register'])->name('register.action');
        });

        // Auth-only routes
        Route::middleware('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('dashboard', [CoreController::class, 'home'])->name('dashboard');
        });

    });
