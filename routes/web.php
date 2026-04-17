<?php

use App\Http\Controllers\AjaxTableController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;

use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/services', [HomeController::class, 'services'])->name('services');
Route::get('/prices', [HomeController::class, 'prices'])->name('prices');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');


// Price routes
// Route::get('/prices', [PriceController::class, 'index'])->name('prices.index');
// Route::get('/prices/{id}', [PriceController::class, 'show'])->name('prices.show');

// // If you want to add admin routes for managing prices
// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::resource('admin/prices', PriceController::class);
// });

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('dashboard')
        ->name('dashboard.')
        ->group(function () {
            // Route::get('/', [DashboardController::class, 'index'])->name('index');
            Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
            Route::get('/password', [DashboardController::class, 'password'])->name('password');
            Route::post('/profile/change-password', [DashboardController::class, 'changePassword'])->name('changePassword');
            Route::put('/update-profile', [DashboardController::class, 'updateProfile'])->name('updateProfile');
            Route::get('/referrals', [DashboardController::class, 'referrals'])->name('referrals');
            Route::get('/refresh', [DashboardController::class, 'refresh'])->name('refresh');
            Route::get('/resendmail', [DashboardController::class, 'resendmail'])->name('resendmail');


        });

    Route::prefix('tables')
        ->name('tables.')
        ->group(function () {
            Route::get('/jobs', [AjaxTableController::class, 'jobs'])->name('jobs');
            Route::get('/users/{userid}', [AjaxTableController::class, 'users'])->name('users');
        });



    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
