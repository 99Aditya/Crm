<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomFieldController;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/', [AuthController::class, 'Auth'])->name('login.post');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/contact', [ContactController::class, 'index'])->name('contacts.list');
    Route::get('/get-contacts', [ContactController::class, 'getContact'])->name('contacts.get');
    Route::post('/contact', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/edit-contact/{id}', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::get('/delete-contact/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::post('/contacts/check-email', [ContactController::class, 'checkEmail'])->name('contacts.checkEmail');

    Route::post('/merge-contacts', [CustomFieldController::class, 'merge'])->name('contacts.merge');
    Route::post('/custom-fields', [CustomFieldController::class, 'store'])->name('custom_fields.store');
    Route::delete('/custom-fields/{customField}', [CustomFieldController::class, 'destroy'])->name('custom_fields.destroy');  
});