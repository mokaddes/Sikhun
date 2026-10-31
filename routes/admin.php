<?php

use App\Http\Controllers\Admin\AiLogController;
use App\Http\Controllers\Admin\AiProviderController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\Auth\AdminPasswordResetController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BlogBlocklistController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BlogSourceController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BookUploadController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseLessonController;
use App\Http\Controllers\Admin\CourseSectionController;
use App\Http\Controllers\Admin\CourseVideoUploadController;
use App\Http\Controllers\Admin\CustomPageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FreeCampaignController;
use App\Http\Controllers\Admin\LeaderboardController;
use App\Http\Controllers\Admin\MentorController;
use App\Http\Controllers\Admin\NotificationBroadcastController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PublicationController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\SupportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin routes — completely separate guard from students.
| Prefixed /admin, named admin.*
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);

        // Admin password reset uses the separate `admins` broker and table.
        Route::get('/forgot-password', [AdminPasswordResetController::class, 'showRequestForm'])->name('password.request');
        Route::post('/forgot-password', [AdminPasswordResetController::class, 'sendResetLink'])
            ->middleware('throttle:auth')->name('password.email');
        Route::get('/reset-password/{token}', [AdminPasswordResetController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password', [AdminPasswordResetController::class, 'reset'])
            ->middleware('throttle:auth')->name('password.store');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('authors', AuthorController::class)->except(['show']);
        Route::resource('publications', PublicationController::class)->except(['show']);
        Route::resource('mentors', MentorController::class)->except(['show']);
        Route::resource('books', BookController::class);
        Route::get('books/{book}/pdf', [BookController::class, 'pdfUpload'])->name('books.pdf-upload');
        Route::post('books/{book}/pdf', [BookController::class, 'storePdf'])->name('books.pdf-store');
        Route::post('books/upload-chunk', [BookUploadController::class, 'uploadChunk'])->name('books.upload-chunk');
        Route::post('books/merge-chunks', [BookUploadController::class, 'mergeChunks'])->name('books.merge-chunks');
        Route::get('books/merge-chunks/status/{upload_id}', [BookUploadController::class, 'mergeStatus'])
            ->name('books.merge-status')
            ->where('upload_id', '[A-Za-z0-9-]+');
        Route::post('books/{book}/retry-processing', [BookController::class, 'retryProcessing'])->name('books.retry-processing');

        Route::resource('courses', CourseController::class)->except(['show']);
        Route::post('courses/{course}/sections', [CourseSectionController::class, 'store'])->name('courses.sections.store');
        Route::put('courses/{course}/sections/{section}', [CourseSectionController::class, 'update'])->name('courses.sections.update');
        Route::delete('courses/{course}/sections/{section}', [CourseSectionController::class, 'destroy'])->name('courses.sections.destroy');
        Route::post('courses/{course}/sections/{section}/lessons', [CourseLessonController::class, 'store'])->name('courses.lessons.store');
        Route::put('courses/{course}/sections/{section}/lessons/{lesson}', [CourseLessonController::class, 'update'])->name('courses.lessons.update');
        Route::delete('courses/{course}/sections/{section}/lessons/{lesson}', [CourseLessonController::class, 'destroy'])->name('courses.lessons.destroy');
        // Chunked lesson-video upload (mirrors the book PDF flow).
        Route::post('courses/video/chunk', [CourseVideoUploadController::class, 'uploadChunk'])->name('courses.video.chunk');
        Route::post('courses/video/merge', [CourseVideoUploadController::class, 'mergeChunks'])->name('courses.video.merge');

        Route::resource('plans', PlanController::class)->except(['show']);

        Route::resource('coupons', CouponController::class)->except(['show']);
        Route::resource('free-campaigns', FreeCampaignController::class)->except(['show']);

        Route::resource('ai-providers', AiProviderController::class)->except(['show']);
        Route::post('ai-providers/{aiProvider}/test', [AiProviderController::class, 'test'])->name('ai-providers.test');

        Route::get('ai-logs', [AiLogController::class, 'index'])->name('ai-logs.index');
        Route::get('ai-logs/{aiLog}', [AiLogController::class, 'show'])->name('ai-logs.show');
        Route::post('ai-logs/clear', [AiLogController::class, 'clear'])->name('ai-logs.clear');

        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
        Route::patch('students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');
        Route::post('students/{student}/wallet-adjust', [StudentController::class, 'adjustWallet'])->name('students.wallet-adjust');
        Route::post('students/{student}/assign-subscription', [StudentController::class, 'assignSubscription'])->name('students.assign-subscription');
        Route::post('students/{student}/grant-access', [StudentController::class, 'grantAccess'])->name('students.grant-access');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');

        Route::get('settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SiteSettingController::class, 'update'])->name('settings.update');

        // Admin account: profile information + password
        Route::get('profile', [ProfileController::class, 'settings'])->name('profile.settings');
        Route::put('profile/info', [ProfileController::class, 'updateProfile'])->name('profile.info');
        Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::resource('pages', CustomPageController::class)->except(['show']);

        // Blog module (Posts, Categories, Scraper Sources, Blocklist)
        Route::post('blog/bulk', [BlogPostController::class, 'bulkAction'])->name('blog.bulk');
        Route::post('blog/{blog}/toggle-publish', [BlogPostController::class, 'togglePublish'])->name('blog.toggle-publish');
        Route::post('blog/{blog}/block', [BlogPostController::class, 'block'])->name('blog.block');
        Route::resource('blog', BlogPostController::class)->except(['show']);

        Route::resource('blog-categories', BlogCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::post('blog-sources/run-all', [BlogSourceController::class, 'runAll'])->name('blog-sources.run-all');
        Route::post('blog-sources/{blogSource}/run', [BlogSourceController::class, 'runNow'])->name('blog-sources.run');
        Route::resource('blog-sources', BlogSourceController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::resource('blog-blocklist', BlogBlocklistController::class)->only(['index', 'store', 'destroy'])
            ->parameters(['blog-blocklist' => 'blocklist']);

        Route::get('referrals', [ReferralController::class, 'index'])->name('referrals.index');

        Route::get('leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');
        Route::delete('leaderboard/{entry}', [LeaderboardController::class, 'destroy'])->name('leaderboard.destroy');

        Route::get('notifications', [NotificationBroadcastController::class, 'index'])->name('notifications.index');
        Route::get('notifications/create', [NotificationBroadcastController::class, 'create'])->name('notifications.create-form');
        Route::post('notifications', [NotificationBroadcastController::class, 'store'])->name('notifications.store');

        Route::get('support', [SupportController::class, 'index'])->name('support.index');
        Route::get('support/{conversation}', [SupportController::class, 'show'])->name('support.show');
        Route::post('support/{conversation}/reply', [SupportController::class, 'reply'])->name('support.reply');
        Route::post('support/{conversation}/toggle-bot', [SupportController::class, 'toggleBot'])->name('support.toggle-bot');
        Route::post('support/{conversation}/close', [SupportController::class, 'close'])->name('support.close');
    });
});
