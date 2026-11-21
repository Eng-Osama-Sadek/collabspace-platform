<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\Api\DailyUpdateApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\FileApiController;
use App\Http\Controllers\Api\MeetingApiController;
use App\Http\Controllers\Api\NotificationApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Support\Facades\Route;

$registerRoutes = function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthApiController::class, 'register']);
        Route::post('login', [AuthApiController::class, 'login']);

        Route::middleware('api.token')->group(function () {
            Route::get('me', [AuthApiController::class, 'me']);
            Route::post('logout', [AuthApiController::class, 'logout']);
        });
    });

    Route::middleware('api.token')->group(function () {
        Route::get('dashboard', [DashboardApiController::class, 'index']);
        Route::post('dashboard/ask-ai', [DashboardApiController::class, 'askAi']);

        Route::apiResource('projects', ProjectApiController::class);
        Route::patch('projects/{project}/wiki', [ProjectApiController::class, 'updateWiki']);

        Route::apiResource('tasks', TaskApiController::class);
        Route::patch('tasks/{task}/status', [TaskApiController::class, 'updateStatus']);

        Route::apiResource('files', FileApiController::class)->except(['update']);

        Route::get('chats', [ChatApiController::class, 'index']);
        Route::post('chats', [ChatApiController::class, 'store']);
        Route::get('chats/{conversation}/messages', [ChatApiController::class, 'messages']);
        Route::post('chats/{conversation}/messages', [ChatApiController::class, 'sendMessage']);

        Route::apiResource('meetings', MeetingApiController::class);
        Route::post('meetings/{meeting}/ai-summary', [MeetingApiController::class, 'generateAiSummary']);

        Route::get('daily-updates', [DailyUpdateApiController::class, 'index']);
        Route::post('daily-updates', [DailyUpdateApiController::class, 'store']);

        Route::get('reports', [ReportApiController::class, 'index']);
        Route::post('reports', [ReportApiController::class, 'store']);

        Route::get('notifications', [NotificationApiController::class, 'index']);
        Route::patch('notifications/{notification}/read', [NotificationApiController::class, 'markAsRead']);
        Route::post('notifications/read-all', [NotificationApiController::class, 'markAllAsRead']);

        Route::get('profile', [ProfileApiController::class, 'show']);
        Route::match(['put', 'patch'], 'profile', [ProfileApiController::class, 'update']);
        Route::get('users', [ProfileApiController::class, 'users']);
    });
};

Route::prefix('v1')->group($registerRoutes);
$registerRoutes();
