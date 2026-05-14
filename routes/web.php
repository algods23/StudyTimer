<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SessionHistoryController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TimerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/timer', [TimerController::class, 'index'])->name('timer.index');
    Route::post('/timer/sessions', [TimerController::class, 'store'])->name('timer.sessions.store');
    Route::resource('subjects', SubjectController::class)->except('show');
    Route::get('/sessions', SessionHistoryController::class)->name('sessions.index');
    Route::get('/goals', [GoalController::class, 'edit'])->name('goals.edit');
    Route::put('/goals', [GoalController::class, 'update'])->name('goals.update');
    Route::get('/analytics', AnalyticsController::class)->name('analytics.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
