<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InternalUser\DashboardController;


Route::get('/', function () {
    return redirect('/login');
});

Route::get('/home', function () {
    return redirect('/dashboard');
});


    Auth::routes(['register' => false,'verify' => true,]);

    // Internal user route.
    Route::prefix('dashboard')->name('dashboard.')->group(function(){
        Route::get('/', [DashboardController::class, 'index'])->name('index');
    });

