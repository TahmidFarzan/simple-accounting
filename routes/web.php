<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InternalUser\UserController;
use App\Http\Controllers\InternalUser\DashboardController;


Route::get('/', function () {
    return redirect('/login');
});

Route::get('/home', function () {
    return redirect('/dashboard');
});

Route::group(['middleware' => 'prevent.back.history'],function(){
    Auth::routes(['register' => false,'verify' => true,]);

    // Dashboard.
    Route::prefix('dashboard')->name('dashboard.')->group(function(){
        Route::get('/', [DashboardController::class, 'index'])->name('index');
    });

    //User
    Route::prefix('user')->name('user.')->group(function(){
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::get('edit/{slug}', [UserController::class, 'edit'])->name('edit');
        Route::get('details/{slug}', [UserController::class, 'details'])->name('details');

        Route::post('save', [UserController::class, 'save'])->name('save');
        Route::delete('trash/{slug}', [UserController::class, 'trash'])->name('trash');
        Route::patch('update/{slug}', [UserController::class, 'update'])->name('update');
        Route::patch('restore/{slug}', [UserController::class, 'restore'])->name('restore');
    });
});
