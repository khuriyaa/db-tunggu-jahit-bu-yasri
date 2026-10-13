<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\OwnerReportController;
use App\Http\Controllers\PaymentSettingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/payment-qr', [PaymentSettingController::class, 'showQr'])
        ->middleware('role:Admin,Customer')
        ->name('payment.qr');

    Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])
        ->middleware('role:Owner')
        ->name('owner.dashboard');

    Route::get('/owner/reports', [OwnerReportController::class, 'index'])
        ->middleware('role:Owner')
        ->name('owner.reports');

    Route::middleware('role:Customer')->group(function () {
        Route::get('/my-orders/create', [DashboardController::class, 'createCustomerOrder'])
            ->name('customer.orders.create');
        Route::get('/my-orders', [DashboardController::class, 'customerOrders'])
            ->name('customer.orders.index');
        Route::get('/my-payments', [DashboardController::class, 'customerPayments'])
            ->name('customer.payments.index');
        Route::patch('/my-orders/{order}/payment', [OrderController::class, 'updateCustomerPayment'])
            ->name('customer.payments.update');
    });

    Route::middleware('role:Admin,Staff')->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('orders.status');
    });

    Route::middleware('role:Admin')->group(function () {
        Route::get('/admin/payment-settings', [PaymentSettingController::class, 'edit'])
            ->name('admin.payment-settings.edit');
        Route::post('/admin/payment-settings', [PaymentSettingController::class, 'update'])
            ->name('admin.payment-settings.update');
        Route::delete('/admin/payment-settings', [PaymentSettingController::class, 'destroy'])
            ->name('admin.payment-settings.destroy');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
        Route::get('/users/manage/edit', [UserController::class, 'editIndex'])->name('users.manage.edit');
        Route::get('/users/manage/delete', [UserController::class, 'deleteIndex'])->name('users.manage.delete');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::post('/my-orders', [OrderController::class, 'storeForCustomer'])
        ->middleware('role:Customer')
        ->name('customer.orders.store');

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('role:Admin')
        ->name('users.index');
});
