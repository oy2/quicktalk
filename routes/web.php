<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
})->middleware('auth');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ChatController -- For now these are in web not api because only the web app is using them -- thus web auth/middleware is used
Route::get('/users', [ChatController::class, 'users'])->name('users');
Route::get('/conversations', [ChatController::class, 'fetchConversations'])->name('fetch.conversations');
Route::get('/conversation/{conversation_id}', [ChatController::class, 'fetchConversation'])->name('fetch.conversation');
Route::post('/conversation', [ChatController::class, 'createConversation'])->name('create.conversation');
Route::post('/groupconversation', [ChatController::class, 'createConversationGroup'])->name('create.conversation.group');
Route::post('/message', [ChatController::class, 'sendMessage'])->name('send.message');
Route::get('/messages/{conversation_id}', [ChatController::class, 'fetchMessages'])->name('fetch.messages');


require __DIR__.'/auth.php';
