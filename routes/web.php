<?php

use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Student\AccessController;
use App\Http\Controllers\Student\AiChatController;
use App\Http\Controllers\Student\Auth\StudentAuthController;
use App\Http\Controllers\Student\BookshelfController;
use App\Http\Controllers\Student\CourseController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\EssayController;
use App\Http\Controllers\Student\ExamController;
use App\Http\Controllers\Student\FlashcardController;
use App\Http\Controllers\Student\LeaderboardController;
use App\Http\Controllers\Student\LibraryController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\ReaderController;
use App\Http\Controllers\Student\ReferralController;
use App\Http\Controllers\Student\ScheduleController;
use App\Http\Controllers\Student\SubscriptionController;
use App\Http\Controllers\Student\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/contact', [\App\Http\Controllers\Public\ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [\App\Http\Controllers\Public\ContactController::class, 'submit'])->name('contact.submit');

// Library & Courses browsing is intentionally public (not behind auth:web) —
// these pages need to be crawlable and indexable by search engines per the
// SEO requirements; only purchase/enroll/read actions require a login.
Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
Route::get('/library/{book:slug}', [LibraryController::class, 'show'])->name('library.show');
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/courses/{course:slug}/sections/{section}/lessons/{lesson}', [CourseController::class, 'lesson'])->name('courses.lesson');
Route::get('/p/{page:slug}', [PageController::class, 'show'])->name('pages.show');

Route::post('/language/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['bn', 'en'], true), 404);
    session(['locale' => $locale]);

    return back();
})->name('language.switch');

Route::get('/support/conversation', [\App\Http\Controllers\Public\SupportController::class, 'show'])->name('support.show');
Route::post('/support/message', [\App\Http\Controllers\Public\SupportController::class, 'send'])->name('support.send');

/*
|--------------------------------------------------------------------------
| Student guest routes (web guard)
|--------------------------------------------------------------------------
*/
Route::middleware('guest:web')->group(function () {
    Route::get('/register', [StudentAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [StudentAuthController::class, 'register']);
    Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [StudentAuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Student authenticated routes (web guard)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', 'student.active'])->group(function () {
    Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Library (browse/detail are public — see below; purchase requires login)
    Route::post('/library/{book}/purchase', [LibraryController::class, 'purchase'])->name('library.purchase');

    // Bookshelf
    Route::get('/bookshelf', [BookshelfController::class, 'index'])->name('bookshelf.index');

    // Courses (browse/detail/free-preview lessons are public; enroll/complete require login)
    Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{course}/sections/{section}/lessons/{lesson}/complete', [CourseController::class, 'completeLesson'])->name('courses.lesson.complete');

    // Reader
    Route::get('/library/{book}/read', [ReaderController::class, 'show'])->name('reader.show');
    Route::get('/library/{book}/read/page/{page}/url', [ReaderController::class, 'pageUrl'])->name('reader.page-url');

    // Wallet
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/recharge', [WalletController::class, 'recharge'])->name('wallet.recharge');

    // Subscription
    Route::get('/plans', [SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::post('/plans/purchase', [SubscriptionController::class, 'purchase'])->name('subscription.purchase');

    // AI Chat
    Route::get('/ai/chat', [AiChatController::class, 'index'])->name('ai-chat.index');
    Route::get('/ai/chat/{session}', [AiChatController::class, 'show'])->name('ai-chat.show');
    Route::delete('/ai/chat/{session}', [AiChatController::class, 'destroy'])->name('ai-chat.destroy');
    Route::get('/ai/chat/{session}/stream', [AiChatController::class, 'stream'])->name('ai-chat.stream');
    Route::middleware('ai.access')->group(function () {
        Route::post('/ai/chat', [AiChatController::class, 'create'])->name('ai-chat.create');
    });

    // AI Exam Engine
    Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/create', [ExamController::class, 'createForm'])->name('exams.create-form');
    Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
    Route::get('/exams/{exam}/status', [ExamController::class, 'status'])->name('exams.status');
    Route::post('/exams/{exam}/complete', [ExamController::class, 'complete'])->name('exams.complete');
    Route::get('/exams/{exam}/result', [ExamController::class, 'result'])->name('exams.result');
    Route::get('/exams/{exam}/pdf', [ExamController::class, 'pdf'])->name('exams.pdf');
    Route::middleware('ai.access')->group(function () {
        Route::post('/exams', [ExamController::class, 'create'])->name('exams.create');
    });

    // AI Flashcard Generator
    Route::get('/flashcards', [FlashcardController::class, 'index'])->name('flashcards.index');
    Route::get('/flashcards/create', [FlashcardController::class, 'createForm'])->name('flashcards.create-form');
    Route::get('/flashcards/{set}', [FlashcardController::class, 'show'])->name('flashcards.show');
    Route::delete('/flashcards/{set}', [FlashcardController::class, 'destroy'])->name('flashcards.destroy');
    Route::post('/flashcards/{set}/cards/{flashcard}/review', [FlashcardController::class, 'review'])->name('flashcards.review');
    Route::get('/flashcards/{set}/pdf', [FlashcardController::class, 'pdf'])->name('flashcards.pdf');
    Route::middleware('ai.access')->group(function () {
        Route::post('/flashcards', [FlashcardController::class, 'create'])->name('flashcards.create');
    });

    // AI Essay Grader
    Route::get('/essays', [EssayController::class, 'index'])->name('essays.index');
    Route::get('/essays/create', [EssayController::class, 'create'])->name('essays.create-form');
    Route::get('/essays/{essay}', [EssayController::class, 'show'])->name('essays.show');
    Route::get('/essays/{essay}/status', [EssayController::class, 'status'])->name('essays.status');
    Route::middleware('ai.access')->group(function () {
        Route::post('/essays', [EssayController::class, 'store'])->name('essays.store');
    });

    // Study Schedule Maker
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create-form');
    Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
    Route::get('/schedules/{schedule}/status', [ScheduleController::class, 'status'])->name('schedules.status');
    Route::post('/schedules/{schedule}/progress', [ScheduleController::class, 'markProgress'])->name('schedules.progress');
    Route::get('/schedules/{schedule}/pdf', [ScheduleController::class, 'pdf'])->name('schedules.pdf');
    Route::middleware('ai.access')->group(function () {
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    });

    // Leaderboard
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Referrals
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');

    // Notifications (bell dropdown)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Profile / Settings (theme sync, leaderboard opt-out, notification preferences)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/theme', [ProfileController::class, 'updateTheme'])->name('profile.theme');
    Route::put('/profile/leaderboard-opt-out', [ProfileController::class, 'updateLeaderboardOptOut'])->name('profile.leaderboard-opt-out');
    Route::put('/profile/notification-preferences', [ProfileController::class, 'updateNotificationPreferences'])->name('profile.notification-preferences');

    // Coupons / free-access status
    Route::get('/access', [AccessController::class, 'index'])->name('access.index');
    Route::post('/access/redeem', [AccessController::class, 'redeem'])->name('access.redeem');
});

/*
|--------------------------------------------------------------------------
| ZiniPay gateway callbacks — ZiniPay redirects the browser here (GET) after
| the hosted checkout, so these sit outside the web-guard group (the
| student's session may have expired). Order ownership is re-verified by
| order_number/invoice_id inside the controller, then the invoice is checked
| again server-side against ZiniPay before anything is fulfilled.
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/wallet/gateway/success', [WalletController::class, 'gatewaySuccess'])->name('wallet.gateway.success');
Route::match(['get', 'post'], '/wallet/gateway/fail', [WalletController::class, 'gatewayFail'])->name('wallet.gateway.fail');
Route::match(['get', 'post'], '/wallet/gateway/cancel', [WalletController::class, 'gatewayCancel'])->name('wallet.gateway.cancel');

/*
|--------------------------------------------------------------------------
| Signed, rate-limited book page image — the ONLY route that ever serves
| rendered PDF content. Reached exclusively via a URL minted by
| ReaderController::pageUrl(), never linked to directly.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:web', 'signed', 'throttle:reader-pages'])
    ->get('/reader/{book}/page/{page}', [ReaderController::class, 'servePage'])
    ->name('reader.page');
