<?php

use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PriceController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AjaxTableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;


// ── Public routes ──────────────────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/prices', [HomeController::class, 'prices'])->name('prices');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');


// ── Authenticated routes ───────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('dashboard')
        ->name('dashboard.')
        ->group(function () {
            Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
            Route::get('/password', [DashboardController::class, 'password'])->name('password');
            Route::post('/profile/change-password', [DashboardController::class, 'changePassword'])->name('changePassword');
            Route::put('/update-profile', [DashboardController::class, 'updateProfile'])->name('updateProfile');
            Route::get('/referrals', [DashboardController::class, 'referrals'])->name('referrals');
            Route::get('/refresh', [DashboardController::class, 'refresh'])->name('refresh');
            Route::get('/resendmail', [DashboardController::class, 'resendmail'])->name('resendmail');
        });

    // Ajax DataTables — {userid} is auth()->id(), passed in from Blade
    Route::prefix('tables')
        ->name('tables.')
        ->group(function () {
            Route::get('/users/{userid}', [AjaxTableController::class, 'users'])->name('users');
            Route::get('/orders/{userid}', [AjaxTableController::class, 'orders'])->name('orders');
            Route::get('/transactions/{userid}', [AjaxTableController::class, 'transactions'])->name('transactions');
            Route::get('/notifications/{userid}', [AjaxTableController::class, 'notifications'])->name('notifications');
            Route::get('/prices/{userid}', [AjaxTableController::class, 'prices'])->name('prices');
            Route::get('/packages/{userid}', [AjaxTableController::class, 'packages'])->name('packages');
            Route::get('/services/{userid}', [AjaxTableController::class, 'services'])->name('services');
            Route::get('/jobs', [AjaxTableController::class, 'jobs'])->name('jobs');
        });


    // ── Admin / Support routes ─────────────────────────────────────────────────

    Route::prefix('admin')
        ->name('admin.')
        ->middleware([AdminMiddleware::class])
        ->group(function () {

            // Users
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/{user}', [UserController::class, 'show'])->name('show');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::put('/{user}', [UserController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
                Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggleStatus');
                Route::post('/{user}/fund', [UserController::class, 'fundUser'])->name('fundUser');
                Route::get('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('resetPassword');
            });

            // Orders
            Route::prefix('orders')->name('orders.')->group(function () {
                Route::get('/', [OrderController::class, 'index'])->name('index');
                Route::get('/{order}', [OrderController::class, 'show'])->name('show');
                Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('updateStatus');
            });

            // Transactions
            Route::prefix('transactions')->name('transactions.')->group(function () {
                Route::get('/', [TransactionController::class, 'index'])->name('index');
                Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
            });

            // Notifications
            Route::prefix('notifications')->name('notifications.')->group(function () {
                Route::get('/', [NotificationController::class, 'index'])->name('index');
                Route::get('/create', [NotificationController::class, 'create'])->name('create');
                Route::post('/', [NotificationController::class, 'store'])->name('store');
                Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
            });

            // Prices
            Route::prefix('prices')->name('prices.')->group(function () {
                Route::get('/', [PriceController::class, 'index'])->name('index');
                Route::get('/create', [PriceController::class, 'create'])->name('create');
                Route::post('/', [PriceController::class, 'store'])->name('store');
                Route::get('/{price}/edit', [PriceController::class, 'edit'])->name('edit');
                Route::put('/{price}', [PriceController::class, 'update'])->name('update');
                Route::delete('/{price}', [PriceController::class, 'destroy'])->name('destroy');
            });

            // Packages
            Route::prefix('packages')->name('packages.')->group(function () {
                Route::get('/', [PackageController::class, 'index'])->name('index');
                Route::get('/create', [PackageController::class, 'create'])->name('create');
                Route::post('/', [PackageController::class, 'store'])->name('store');
                Route::get('/{package}/edit', [PackageController::class, 'edit'])->name('edit');
                Route::put('/{package}', [PackageController::class, 'update'])->name('update');
                Route::delete('/{package}', [PackageController::class, 'destroy'])->name('destroy');
            });

            // Services
            Route::prefix('services')->name('services.')->group(function () {
                Route::get('/', [ServiceController::class, 'index'])->name('index');
                Route::get('/create', [ServiceController::class, 'create'])->name('create');
                Route::post('/', [ServiceController::class, 'store'])->name('store');
                Route::get('/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
                Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
                Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('destroy');
            });
        });
});

require __DIR__ . '/auth.php';
