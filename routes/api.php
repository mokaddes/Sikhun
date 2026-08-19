<?php

use App\Http\Controllers\Api\AiChatController;
use App\Http\Controllers\Api\AccessController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EssayController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\FlashcardController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReaderController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API — v1 (unversioned URLs today; nothing stops a future /v2 prefix
| living alongside this file without touching these routes).
| Every response uses the {success, data, message, meta} envelope from
| Api\BaseApiController. Auth endpoints are unauthenticated; everything
| else requires a Sanctum bearer token from POST /api/auth/login.
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::get('pages/{slug}', [PageController::class, 'show']);

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    Route::get('library', [LibraryController::class, 'index']);
    Route::get('library/{book:slug}', [LibraryController::class, 'show']);
    Route::post('library/{book}/purchase', [LibraryController::class, 'purchase']);

    Route::get('reader/{book}/page/{page}/url', [ReaderController::class, 'pageUrl']);

    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{course:slug}', [CourseController::class, 'show']);
    Route::post('courses/{course}/enroll', [CourseController::class, 'enroll']);
    Route::get('courses/{course}/sections/{section}/lessons/{lesson}', [CourseController::class, 'lesson']);
    Route::post('courses/{course}/sections/{section}/lessons/{lesson}/complete', [CourseController::class, 'completeLesson']);

    Route::get('wallet', [WalletController::class, 'index']);
    Route::get('wallet/transactions', [WalletController::class, 'transactions']);
    Route::post('wallet/recharge', [WalletController::class, 'recharge']);

    Route::get('subscriptions/plans', [SubscriptionController::class, 'plans']);
    Route::get('subscriptions/active', [SubscriptionController::class, 'active']);
    Route::post('subscriptions/purchase', [SubscriptionController::class, 'purchase']);

    Route::get('leaderboard', [LeaderboardController::class, 'index']);
    Route::get('leaderboard/my-rank', [LeaderboardController::class, 'myRank']);

    Route::get('referrals', [ReferralController::class, 'index']);
    Route::get('referrals/stats', [ReferralController::class, 'stats']);

    Route::get('access/status', [AccessController::class, 'status']);
    Route::post('access/redeem', [AccessController::class, 'redeem']);

    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('profile/password', [ProfileController::class, 'updatePassword']);
    Route::put('profile/theme', [ProfileController::class, 'updateTheme']);
    Route::put('profile/notifications', [ProfileController::class, 'updateNotifications']);
    Route::post('profile/fcm-token', [ProfileController::class, 'registerFcmToken']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllRead']);

    Route::get('support/conversation', [SupportController::class, 'show']);
    Route::post('support/message', [SupportController::class, 'send']);

    // AI features get a tighter throttle layered on top of the general one.
    Route::middleware('throttle:api-ai')->group(function () {
        Route::get('ai/chat/sessions', [AiChatController::class, 'index']);
        Route::post('ai/chat/sessions', [AiChatController::class, 'store']);
        Route::get('ai/chat/sessions/{session}', [AiChatController::class, 'show']);
        Route::delete('ai/chat/sessions/{session}', [AiChatController::class, 'destroy']);
        Route::post('ai/chat/sessions/{session}/message', [AiChatController::class, 'stream']);

        Route::get('exams', [ExamController::class, 'index']);
        Route::post('exams', [ExamController::class, 'store']);
        Route::get('exams/{exam}', [ExamController::class, 'show']);
        Route::post('exams/{exam}/complete', [ExamController::class, 'complete']);
        Route::get('exams/{exam}/result', [ExamController::class, 'result']);

        Route::get('flashcards', [FlashcardController::class, 'index']);
        Route::post('flashcards', [FlashcardController::class, 'store']);
        Route::get('flashcards/{set}', [FlashcardController::class, 'show']);
        Route::post('flashcards/{set}/review/{flashcard}', [FlashcardController::class, 'review']);

        Route::get('essays', [EssayController::class, 'index']);
        Route::post('essays', [EssayController::class, 'store']);
        Route::get('essays/{essay}', [EssayController::class, 'show']);

        Route::get('schedules', [ScheduleController::class, 'index']);
        Route::post('schedules', [ScheduleController::class, 'store']);
        Route::get('schedules/{schedule}', [ScheduleController::class, 'show']);
        Route::post('schedules/{schedule}/progress', [ScheduleController::class, 'progress']);
    });
});

// Signed, rate-limited, Sanctum-authed book page image — the API twin of
// the web guard's /reader/{book}/page/{page} route from Phase 3.
Route::middleware(['auth:sanctum', 'signed', 'throttle:reader-pages'])
    ->get('reader/{book}/page/{page}', [ReaderController::class, 'servePage'])
    ->name('api.reader.page');
