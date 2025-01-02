<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/users', [ProfileController::class, 'users']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/chat-room/create/{user:id}', [ChatController::class, 'createChatRoom'])->name('create-chat-room');
    Route::get('/chat/{chat_room:name}/messages', [ChatController::class, 'fetchMessages']);
    Route::post('/chat/message', [ChatController::class, 'store']);
});

require __DIR__.'/auth.php';
