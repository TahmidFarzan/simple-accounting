<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InternalUser\UserController;
use App\Http\Controllers\InternalUser\ExtraController;
use App\Http\Controllers\InternalUser\SettingController;
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

    Route::prefix('setting')->name('setting.')->group(function(){
        Route::get('/', [SettingController::class, 'index'])->name('index');

        Route::prefix('business-setting')->name('business.setting.')->group(function(){
            Route::get('/', [SettingController::class, 'businessSettingIndex'])->name('index');
            Route::get('details/{slug}', [SettingController::class, 'businessSettingDetails'])->name('details');
            Route::get('edit/{slug}', [SettingController::class, 'businessSettingEdit'])->name('edit');
            Route::patch('update/{slug}', [SettingController::class, 'businessSettingUpdate'])->name('update');
        });

        Route::prefix('activity-log-setting')->name('activity.log.setting.')->group(function(){
            Route::get('/', [SettingController::class, 'activityLogSettingIndex'])->name('index');
            Route::get('details/{slug}', [SettingController::class, 'activityLogSettingDetails'])->name('details');
            Route::get('edit/{slug}', [SettingController::class, 'activityLogSettingEdit'])->name('edit');
            Route::patch('update/{slug}', [SettingController::class, 'activityLogSettingUpdate'])->name('update');
        });

        Route::prefix('authentication-log-setting')->name('authentication.log.setting.')->group(function(){
            Route::get('/', [SettingController::class, 'authenticationLogSettingIndex'])->name('index');
            Route::get('details/{slug}', [SettingController::class, 'authenticationLogSettingDetails'])->name('details');
            Route::get('edit/{slug}', [SettingController::class, 'authenticationLogSettingEdit'])->name('edit');
            Route::patch('update/{slug}', [SettingController::class, 'authenticationLogSettingUpdate'])->name('update');
        });
    });

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

    // Extra model.
    Route::prefix('user-permission')->name('user.permission.')->group(function(){
        Route::get('/', [ExtraController::class, 'userPermissionIndex'])->name('index');
        Route::get('details/{slug}', [ExtraController::class, 'userPermissionDetails'])->name('details');
    });

    // User permission group
    Route::prefix('user-permission-group')->name('user.permission.group.')->group(function(){
        Route::get('/', [ExtraController::class, 'userPermissionGroupIndex'])->name('index');
        Route::get('create', [ExtraController::class, 'userPermissionGroupCreate'])->name('create');
        Route::get('edit/{slug}', [ExtraController::class, 'userPermissionGroupEdit'])->name('edit');
        Route::get('details/{slug}', [ExtraController::class, 'userPermissionGroupDetails'])->name('details');

        Route::post('save', [ExtraController::class, 'userPermissionGroupSave'])->name('save');
        Route::patch('update/{slug}', [ExtraController::class, 'userPermissionGroupUpdate'])->name('update');
        Route::patch('delete/{slug}', [ExtraController::class, 'userPermissionGroupDelete'])->name('delete');
    });
});
