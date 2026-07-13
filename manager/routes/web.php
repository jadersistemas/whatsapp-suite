<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;

// Auth routes (excluded from ApiKey middleware via isAuthRoute check)
Route::get('/login', [AuthController::class, 'showForm'])->name('auth.apikey.form');
Route::post('/login', [AuthController::class, 'verify'])->name('auth.apikey.verify');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Protected routes
Route::get('/', [WhatsAppController::class, 'dashboard'])->name('dashboard');

// Instances
Route::prefix('instances')->name('whatsapp.')->group(function () {
    Route::get('/', [WhatsAppController::class, 'instances'])->name('instances');
    Route::get('/create', [WhatsAppController::class, 'createInstance'])->name('create');
    Route::post('/create', [WhatsAppController::class, 'storeInstance'])->name('store');
    Route::get('/{name}', [WhatsAppController::class, 'showInstance'])->name('show');
    Route::delete('/{name}', [WhatsAppController::class, 'deleteInstance'])->name('delete');
    Route::post('/{name}/logout', [WhatsAppController::class, 'logout'])->name('logout');
    Route::post('/{name}/connect/qr', [WhatsAppController::class, 'connectQr'])->name('connect.qr');
    Route::post('/{name}/connect/pairing', [WhatsAppController::class, 'connectPairing'])->name('connect.pairing');
    Route::get('/{name}/connection-state', [WhatsAppController::class, 'connectionState'])->name('connection-state');
    Route::put('/{name}/settings', [WhatsAppController::class, 'updateSettings'])->name('settings');
});

// Messages
Route::prefix('messages')->name('messages.')->group(function () {
    Route::get('/{instanceName}', [WhatsAppController::class, 'sendMessage'])->name('send');
    Route::post('/{instanceName}/text', [WhatsAppController::class, 'sendText'])->name('text');
    Route::post('/{instanceName}/link', [WhatsAppController::class, 'sendLink'])->name('link');
    Route::post('/{instanceName}/media', [WhatsAppController::class, 'sendMedia'])->name('media');
    Route::post('/{instanceName}/contact', [WhatsAppController::class, 'sendContact'])->name('contact');
    Route::post('/{instanceName}/location', [WhatsAppController::class, 'sendLocation'])->name('location');
    Route::post('/{instanceName}/reaction', [WhatsAppController::class, 'sendReaction'])->name('reaction');
});

// Webhooks
Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::get('/{instanceName}', [WhatsAppController::class, 'webhookSettings'])->name('settings');
    Route::put('/{instanceName}', [WhatsAppController::class, 'updateWebhook'])->name('update');
});

// API endpoints
Route::prefix('api')->name('api.')->group(function () {
    Route::post('/check-number/{instanceName}', [WhatsAppController::class, 'checkNumber'])->name('check-number');
});

// Settings
Route::get('/settings', [WhatsAppController::class, 'settings'])->name('settings');
