<?php

use App\Http\Controllers\Api\AccessController;
use App\Http\Controllers\Api\AiChatController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\BookshelfController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EssayController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\FlashcardController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\BkashWebhookController;
use App\Http\Controllers\Api\MhpayWebhookController;
use App\Http\Controllers\Api\NagadWebhookController;
use App\Http\Controllers\Api\MistakeReviewController;
use App\Http\Controllers\Api\MyBookController;
use App\Http\Controllers\Api\MyBookReaderController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OnboardingController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PublicContentController;
use App\Http\Controllers\Api\QuestionBankController;
use App\Http\Controllers\Api\QuestionBankPracticeController;
use App\Http\Controllers\Api\ReaderController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API — v1 (Full Android & Mobile Parity with Web Routes)
| Every response uses the {success, data, message, meta} envelope from
| Api\BaseApiController.
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Auth & Password Reset (Unauthenticated)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:register');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:auth');
    Route::get('config', [AuthController::class, 'config']);
    Route::post('otp/send', [AuthController::class, 'sendOtp'])->middleware('throttle:auth');
    Route::post('otp/login', [AuthController::class, 'otpLogin'])->middleware('throttle:auth');
    Route::post('otp/register', [AuthController::class, 'otpRegister'])->middleware('throttle:register');
    Route::post('oauth/{provider}/exchange', [\App\Http\Controllers\Student\Auth\SocialAuthController::class, 'apiExchange'])
        ->middleware('throttle:auth');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:auth');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:auth');
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('api.verification.verify');
});

/*
|--------------------------------------------------------------------------
| Payment Webhooks (Server-to-Server, Unauthenticated)
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], 'mhpay/webhook', [MhpayWebhookController::class, 'handle'])->name('mhpay.webhook');
Route::match(['get', 'post'], 'bkash/webhook', [BkashWebhookController::class, 'handle'])->name('api.webhooks.bkash');
Route::match(['get', 'post'], 'nagad/webhook', [NagadWebhookController::class, 'handle'])->name('api.webhooks.nagad');

/*
|--------------------------------------------------------------------------
| Public Content & Portals (Accessible with or without Bearer token)
|--------------------------------------------------------------------------
*/
Route::get('settings', [SettingController::class, 'index']);
Route::post('ai/chat', [AiChatController::class, 'ask']);

Route::middleware('throttle:api')->group(function () {
    Route::get('home', [PublicContentController::class, 'home']);
    Route::post('contact', [PublicContentController::class, 'contact'])->middleware('throttle:contact');
    Route::get('pages/{slug}', [PageController::class, 'show']);
    Route::get('plans', [SubscriptionController::class, 'plans']);

    // Blog
    Route::get('blog', [PublicContentController::class, 'blogIndex']);
    Route::get('blog/category/{category:slug}', [PublicContentController::class, 'blogCategory']);
    Route::get('blog/{post:slug}', [PublicContentController::class, 'blogShow']);

    // Blogs (mobile/plural alias)
    Route::get('blogs', [BlogController::class, 'index']);
    Route::get('blogs/categories', [BlogController::class, 'categories']);
    Route::get('blogs/tags', [BlogController::class, 'tags']);
    Route::get('blogs/{post:slug}', [BlogController::class, 'show']);

    // Job News Portal
    Route::get('job-news', [PublicContentController::class, 'jobIndex']);
    Route::get('job-news/{slug}', [PublicContentController::class, 'jobShow']);

    // University Admission Notices Portal
    Route::get('admission-notice', [PublicContentController::class, 'admissionIndex']);
    Route::get('admission-notice/{slug}', [PublicContentController::class, 'admissionShow']);

    // Public Library & Courses catalog browsing (uses auth('sanctum')->user() when token is present)
    Route::get('library', [LibraryController::class, 'index']);
    Route::get('library/{book:slug}', [LibraryController::class, 'show']);
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{course:slug}', [CourseController::class, 'show']);

    // Question bank public filters & browse
    Route::get('question-bank', [QuestionBankController::class, 'index']);
    Route::get('question-bank/filters', [QuestionBankController::class, 'filters']);
    Route::get('question-bank/{question}', [QuestionBankController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Student API Routes (Sanctum Bearer Token)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Auth & Session
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/email/verification-notification', [AuthController::class, 'sendVerification'])->middleware('throttle:6,1');

    // Dashboard & Onboarding
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('onboarding', [DashboardController::class, 'onboarding']);
    Route::post('onboarding/step', [DashboardController::class, 'saveOnboardingStep']);
    Route::post('onboarding/skip', [DashboardController::class, 'skipOnboarding']);

    // Library, Purchases & Bookshelf
    Route::post('library/{book}/purchase', [LibraryController::class, 'purchase']);
    Route::post('library/{book}/shelf', [LibraryController::class, 'addToShelf']);
    Route::get('library/{book}/chapters', [LibraryController::class, 'chapters']);
    Route::get('library/{book}/access', [LibraryController::class, 'access']);
    Route::post('library/{book}/chapters/purchase', [LibraryController::class, 'purchaseChapters']);
    Route::post('library/{book}/chapters/{chapter}/purchase', [LibraryController::class, 'purchaseChapter']);

    Route::get('bookshelf', [BookshelfController::class, 'index']);
    Route::post('bookshelf/{book}', [LibraryController::class, 'addToShelf']);
    Route::delete('bookshelf/{book}', [LibraryController::class, 'removeFromShelf']);

    // My Books (Student's own uploaded PDFs & Reader)
    Route::get('my-books', [MyBookController::class, 'index']);
    Route::post('my-books', [MyBookController::class, 'store']);
    Route::delete('my-books/{myBook}', [MyBookController::class, 'destroy']);
    Route::get('my-books/{myBook}/read', [MyBookController::class, 'show']);
    Route::get('my-books/{myBook}/read/page/{page}/url', [MyBookController::class, 'pageUrl']);
    Route::post('my-books/{myBook}/read/progress', [MyBookController::class, 'saveProgress']);
    Route::post('my-books/{myBook}/read/chat', [MyBookController::class, 'chat'])->middleware('throttle:api-ai');

    // Catalog Book Reader
    Route::get('reader/{book}', [ReaderController::class, 'show']);
    Route::get('reader/{book}/page/{page}/url', [ReaderController::class, 'pageUrl']);
    Route::post('reader/{book}/progress', [ReaderController::class, 'saveProgress']);
    Route::post('reader/{book}/chat', [ReaderController::class, 'chat'])->middleware('throttle:api-ai');

    // Courses
    Route::post('courses/{course}/enroll', [CourseController::class, 'enroll']);
    Route::get('courses/{course}/sections/{section}/lessons/{lesson}', [CourseController::class, 'lesson']);
    Route::get('courses/{course:slug}/sections/{section}/lessons/{lesson}/video', [CourseController::class, 'streamVideo']);
    Route::get('courses/{course:slug}/sections/{section}/lessons/{lesson}/download', [CourseController::class, 'downloadAttachment']);
    Route::post('courses/{course}/sections/{section}/lessons/{lesson}/complete', [CourseController::class, 'completeLesson']);

    // Wallet & Invoices
    Route::get('wallet', [WalletController::class, 'index']);
    Route::get('wallet/transactions', [WalletController::class, 'transactions']);
    Route::post('wallet/recharge', [WalletController::class, 'recharge']);
    Route::post('wallet/orders/{order}/verify', [WalletController::class, 'verifyOrder']);

    Route::get('invoices', [InvoiceController::class, 'index']);
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show']);
    Route::post('invoices/{invoice}/pay', [InvoiceController::class, 'pay']);

    // Subscriptions (General, Admission/Job Bundles & Custom Plan Builder)
    Route::get('subscriptions/plans', [SubscriptionController::class, 'plans']);
    Route::get('subscriptions/active', [SubscriptionController::class, 'active']);
    Route::post('subscriptions/purchase', [SubscriptionController::class, 'purchase']);
    Route::post('subscriptions/custom/purchase', [SubscriptionController::class, 'purchaseCustom']);

    // Question Bank Practice & Mistake Review
    Route::get('question-bank', [QuestionBankController::class, 'index']);
    Route::post('question-bank/{id}/ai-explain', [QuestionBankController::class, 'aiExplain'])->middleware('throttle:api-ai');
    Route::post('question-bank/generate-test', [QuestionBankController::class, 'generateTest'])->middleware('throttle:api-ai');

    Route::get('mistakes', [MistakeReviewController::class, 'index']);
    Route::post('mistakes/practice', [MistakeReviewController::class, 'practice']);
    Route::post('mistakes/reset', [MistakeReviewController::class, 'reset']);

    // Leaderboard, Referrals & Free Access Coupons
    Route::get('leaderboard', [LeaderboardController::class, 'index']);
    Route::get('leaderboard/my-rank', [LeaderboardController::class, 'myRank']);

    Route::get('referrals', [ReferralController::class, 'index']);
    Route::get('referrals/stats', [ReferralController::class, 'stats']);

    Route::get('access/status', [AccessController::class, 'status']);
    Route::post('access/redeem', [AccessController::class, 'redeem']);

    // Profile & Settings
    Route::get('profile', [ProfileController::class, 'show']);
    Route::match(['put', 'post'], 'profile', [ProfileController::class, 'update']);
    Route::put('profile/password', [ProfileController::class, 'updatePassword']);
    Route::put('profile/theme', [ProfileController::class, 'updateTheme']);
    Route::put('profile/leaderboard-opt-out', [ProfileController::class, 'updateLeaderboardOptOut']);
    Route::put('profile/notifications', [ProfileController::class, 'updateNotifications']);
    Route::post('profile/fcm-token', [ProfileController::class, 'registerFcmToken']);
    Route::delete('profile/fcm-token', [ProfileController::class, 'unregisterFcmToken']);
    Route::get('profile/export', [ProfileController::class, 'exportData']);
    Route::delete('profile/account', [ProfileController::class, 'deleteAccount']);

    // Onboarding
    Route::get('onboarding', [OnboardingController::class, 'show']);
    Route::post('onboarding/step', [OnboardingController::class, 'saveStep']);
    Route::post('onboarding/skip', [OnboardingController::class, 'skip']);

    // MyBook Reader
    Route::get('my-books/{myBook}/reader', [MyBookReaderController::class, 'show']);
    Route::get('my-books/{myBook}/reader/page-url/{page}', [MyBookReaderController::class, 'pageUrl']);
    Route::post('my-books/{myBook}/reader/progress', [MyBookReaderController::class, 'saveProgress']);
    Route::get('my-books/{myBook}/reader/page/{page}', [MyBookReaderController::class, 'servePage'])
        ->middleware('signed')
        ->name('api.my-books.reader.serve-page');
    Route::post('my-books/{myBook}/reader/chat', [MyBookReaderController::class, 'chat'])
        ->middleware('throttle:api-ai');

    // Question Bank Practice
    Route::get('question-bank-practice', [QuestionBankPracticeController::class, 'index']);
    Route::post('question-bank-practice/{question}/ask-ai', [QuestionBankPracticeController::class, 'askAi'])
        ->middleware('throttle:api-ai');
    Route::post('question-bank-practice/start-exam', [QuestionBankPracticeController::class, 'startExam']);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::put('notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);
    Route::delete('notifications', [NotificationController::class, 'destroyAll']);

    // Support Bot, Tickets & Feedback
    Route::get('support/conversation', [SupportController::class, 'show']);
    Route::post('support/message', [SupportController::class, 'send']);
    Route::post('support/conversation/ticket', [SupportController::class, 'handoff']);

    Route::get('support/tickets', [TicketController::class, 'index']);
    Route::post('support/tickets', [TicketController::class, 'store']);
    Route::get('support/tickets/{ticket}', [TicketController::class, 'show']);
    Route::post('support/tickets/{ticket}/messages', [TicketController::class, 'reply']);

    Route::get('feedback', [FeedbackController::class, 'index']);
    Route::post('feedback', [FeedbackController::class, 'store']);

    // AI Studio Tools (Chat, Exams, Flashcards, Essays, Schedules)
    Route::middleware('throttle:api-ai')->group(function () {
        // AI Chat
        Route::get('ai/chat/sessions', [AiChatController::class, 'index']);
        Route::post('ai/chat/sessions', [AiChatController::class, 'store']);
        Route::get('ai/chat/sessions/{session}', [AiChatController::class, 'show']);
        Route::patch('ai/chat/sessions/{session}', [AiChatController::class, 'rename']);
        Route::patch('ai/chat/sessions/{session}/book', [AiChatController::class, 'attachBook']);
        Route::delete('ai/chat/sessions/{session}', [AiChatController::class, 'destroy']);
        Route::post('ai/chat/sessions/{session}/message', [AiChatController::class, 'stream']);

        // AI Exam Engine
        Route::get('exams', [ExamController::class, 'index']);
        Route::get('exams/create-data', [ExamController::class, 'createData']);
        Route::post('exams', [ExamController::class, 'store']);
        Route::get('exams/{exam}', [ExamController::class, 'show']);
        Route::get('exams/{exam}/status', [ExamController::class, 'status']);
        Route::post('exams/{exam}/retry', [ExamController::class, 'retry']);
        Route::post('exams/{exam}/complete', [ExamController::class, 'complete']);
        Route::get('exams/{exam}/result', [ExamController::class, 'result']);
        Route::post('exams/{exam}/questions/{question}/explanation', [ExamController::class, 'explanation']);
        Route::get('exams/{exam}/pdf', [ExamController::class, 'pdf']);

        // AI Flashcards
        Route::get('flashcards', [FlashcardController::class, 'index']);
        Route::get('flashcards/create-data', [FlashcardController::class, 'createData']);
        Route::post('flashcards', [FlashcardController::class, 'store']);
        Route::get('flashcards/{set}', [FlashcardController::class, 'show']);
        Route::post('flashcards/{set}/retry', [FlashcardController::class, 'retry']);
        Route::delete('flashcards/{set}', [FlashcardController::class, 'destroy']);
        Route::post('flashcards/{set}/review/{flashcard}', [FlashcardController::class, 'review']);
        Route::get('flashcards/{set}/pdf', [FlashcardController::class, 'pdf']);

        // AI Essay Grader
        Route::get('essays', [EssayController::class, 'index']);
        Route::post('essays', [EssayController::class, 'store']);
        Route::get('essays/{essay}', [EssayController::class, 'show']);
        Route::get('essays/{essay}/status', [EssayController::class, 'status']);
        Route::post('essays/{essay}/retry', [EssayController::class, 'retry']);

        // Study Schedule Maker
        Route::get('schedules', [ScheduleController::class, 'index']);
        Route::post('schedules', [ScheduleController::class, 'store']);
        Route::get('schedules/{schedule}', [ScheduleController::class, 'show']);
        Route::get('schedules/{schedule}/status', [ScheduleController::class, 'status']);
        Route::post('schedules/{schedule}/retry', [ScheduleController::class, 'retry']);
        Route::post('schedules/{schedule}/progress', [ScheduleController::class, 'progress']);
        Route::get('schedules/{schedule}/pdf', [ScheduleController::class, 'pdf']);
    });
});

// Signed, rate-limited, Sanctum-authed book page images (Catalog & MyBook)
Route::middleware(['auth:sanctum', 'signed', 'throttle:reader-pages'])->group(function () {
    Route::get('reader/{book}/page/{page}', [ReaderController::class, 'servePage'])->name('api.reader.page');
    Route::get('reader/my-book/{myBook}/page/{page}', [MyBookController::class, 'servePage'])->name('api.my-books.reader.page');
});
