<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WorkspaceEnhancementController;
use Illuminate\Support\Facades\Route;

// Public / Guest Routes
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Authenticated Workspace Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/ask-ai', [DashboardController::class, 'askAi'])->name('dashboard.ask-ai');

    // Projects & Project Wiki
    Route::resource('projects', ProjectController::class);
    Route::patch('/projects/{project}/wiki', [WorkspaceEnhancementController::class, 'updateProjectWiki'])->name('projects.wiki.update');

    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');

    // Files
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::get('/files/{projectFile}/download', [FileController::class, 'download'])->name('files.download');
    Route::delete('/files/{projectFile}', [FileController::class, 'destroy'])->name('files.destroy');

    // Chats
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::post('/chats', [ChatController::class, 'store'])->name('chats.store');
    Route::post('/chats/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('chats.messages.store');

    // Meetings, Transcript & AI Summary
    Route::get('/meetings', [MeetingController::class, 'index'])->name('meetings.index');
    Route::post('/meetings', [MeetingController::class, 'store'])->name('meetings.store');
    Route::get('/meetings/{meeting}', [WorkspaceEnhancementController::class, 'showMeeting'])->name('meetings.show');
    Route::post('/meetings/{meeting}/ai-summary', [WorkspaceEnhancementController::class, 'generateMeetingSummary'])->name('meetings.ai-summary');
    Route::delete('/meetings/{meeting}', [MeetingController::class, 'destroy'])->name('meetings.destroy');

    // Daily Status Updates
    Route::get('/daily-updates', [WorkspaceEnhancementController::class, 'dailyUpdates'])->name('daily-updates.index');
    Route::post('/daily-updates', [WorkspaceEnhancementController::class, 'storeDailyUpdate'])->name('daily-updates.store');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Profile & Member Directory
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/members/{user}', [WorkspaceEnhancementController::class, 'memberProfile'])->name('members.show');
});