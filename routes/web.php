<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InternalUser\UserController;
use App\Http\Controllers\InternalUser\ExtraController;
use App\Http\Controllers\InternalUser\SettingController;
use App\Http\Controllers\InternalUser\DashboardController;
use App\Http\Controllers\InternalUser\ActivityLogController;
use App\Http\Controllers\InternalUser\ProjectContractController;
use App\Http\Controllers\InternalUser\AuthenticationLogController;
use App\Http\Controllers\InternalUser\ProjectContractClientController;
use App\Http\Controllers\InternalUser\ProjectContractJournalController;
use App\Http\Controllers\InternalUser\ProjectContractCategoryController;
use App\Http\Controllers\InternalUser\ProjectContractPaymentMethodController;

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
        Route::delete('delete/{slug}', [ExtraController::class, 'userPermissionGroupDelete'])->name('delete');
    });

    // Project contract
    Route::prefix('project-contract')->name('project.contract.')->group(function(){
        // Project contract
        Route::get('/', [ProjectContractController::class, 'index'])->name('index');
        Route::get('edit/{slug}', [ProjectContractController::class, 'edit'])->name('edit');
        Route::get('create', [ProjectContractController::class, 'create'])->name('create');
        Route::get('details/{slug}', [ProjectContractController::class, 'details'])->name('details');

        Route::post('save', [ProjectContractController::class, 'save'])->name('save');
        Route::patch('update/{slug}', [ProjectContractController::class, 'update'])->name('update');
        Route::delete('delete/{slug}', [ProjectContractController::class, 'delete'])->name('delete');
        Route::patch('change-status/{slug}', [ProjectContractController::class, 'changeStatus'])->name('change.status');
        Route::patch('change-receivable-status/{slug}', [ProjectContractController::class, 'changeReceivableStatus'])->name('change.receivable.status');

        // Journal
        Route::prefix('{pcSlug}/journal')->name('journal.')->group(function(){
            Route::get('/', [ProjectContractJournalController::class, 'index'])->name('index');
            Route::get('edit/{slug}', [ProjectContractJournalController::class, 'edit'])->name('edit');
            Route::get('create', [ProjectContractJournalController::class, 'create'])->name('create');
            Route::get('details/{slug}', [ProjectContractJournalController::class, 'details'])->name('details');

            Route::post('save', [ProjectContractJournalController::class, 'save'])->name('save');
            Route::patch('update/{slug}', [ProjectContractJournalController::class, 'update'])->name('update');
            Route::delete('delete/{slug}', [ProjectContractJournalController::class, 'delete'])->name('delete');
        });

        // Category
        Route::prefix('category')->name('category.')->group(function(){
            Route::get('/', [ProjectContractCategoryController::class, 'index'])->name('index');
            Route::get('edit/{slug}', [ProjectContractCategoryController::class, 'edit'])->name('edit');
            Route::get('create', [ProjectContractCategoryController::class, 'create'])->name('create');
            Route::get('details/{slug}', [ProjectContractCategoryController::class, 'details'])->name('details');

            Route::post('save', [ProjectContractCategoryController::class, 'save'])->name('save');
            Route::patch('update/{slug}', [ProjectContractCategoryController::class, 'update'])->name('update');
            Route::delete('trash/{slug}', [ProjectContractCategoryController::class, 'trash'])->name('trash');
            Route::patch('restore/{slug}', [ProjectContractCategoryController::class, 'restore'])->name('restore');
        });

        // Client
        Route::prefix('client')->name('client.')->group(function(){
            Route::get('/', [ProjectContractClientController::class, 'index'])->name('index');
            Route::get('edit/{slug}', [ProjectContractClientController::class, 'edit'])->name('edit');
            Route::get('create', [ProjectContractClientController::class, 'create'])->name('create');
            Route::get('details/{slug}', [ProjectContractClientController::class, 'details'])->name('details');

            Route::post('save', [ProjectContractClientController::class, 'save'])->name('save');
            Route::patch('update/{slug}', [ProjectContractClientController::class, 'update'])->name('update');
            Route::delete('trash/{slug}', [ProjectContractClientController::class, 'trash'])->name('trash');
            Route::patch('restore/{slug}', [ProjectContractClientController::class, 'restore'])->name('restore');
        });

        // Payment method
        Route::prefix('payment-method')->name('payment.method.')->group(function(){
            Route::get('/', [ProjectContractPaymentMethodController::class, 'index'])->name('index');
            Route::get('edit/{slug}', [ProjectContractPaymentMethodController::class, 'edit'])->name('edit');
            Route::get('create', [ProjectContractPaymentMethodController::class, 'create'])->name('create');
            Route::get('details/{slug}', [ProjectContractPaymentMethodController::class, 'details'])->name('details');

            Route::post('save', [ProjectContractPaymentMethodController::class, 'save'])->name('save');
            Route::patch('update/{slug}', [ProjectContractPaymentMethodController::class, 'update'])->name('update');
            Route::delete('trash/{slug}', [ProjectContractPaymentMethodController::class, 'trash'])->name('trash');
            Route::patch('restore/{slug}', [ProjectContractPaymentMethodController::class, 'restore'])->name('restore');
        });
    });
});
