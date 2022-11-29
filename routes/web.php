<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InternalUser\UserController;
use App\Http\Controllers\InternalUser\DashboardController;
use App\Http\Controllers\InternalUser\ActivityLogController;
use App\Http\Controllers\InternalUser\AuthenticationLogController;

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

    // User
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

    // Activity log
    Route::prefix('activity-log')->name('activity.log.')->group(function(){
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('details/{id}', [ActivityLogController::class, 'details'])->name('details');
        Route::delete('delete/{id}', [ActivityLogController::class, 'delete'])->name('delete');
        Route::delete('delete-all-logs', [ActivityLogController::class, 'deleteAllLogs'])->name('delete.all.logs');
    });

    // Authentication log
    Route::prefix('authentication-log')->name('authentication.log.')->group(function(){
        Route::get('/', [AuthenticationLogController::class, 'index'])->name('index');
        Route::get('details/{id}', [AuthenticationLogController::class, 'details'])->name('details');
        Route::delete('delete/{id}', [AuthenticationLogController::class, 'delete'])->name('delete');
        Route::delete('delete-all-logs', [AuthenticationLogController::class, 'deleteAllLogs'])->name('delete.all.logs');
    });
});
