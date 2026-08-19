<?php

use App\Http\Controllers\Admin\AiProviderController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseLessonController;
use App\Http\Controllers\Admin\CourseSectionController;
use App\Http\Controllers\Admin\CustomPageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FreeCampaignController;
use App\Http\Controllers\Admin\LeaderboardController;
use App\Http\Controllers\Admin\MentorController;
use App\Http\Controllers\Admin\NotificationBroadcastController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PlanController;
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
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('authors', AuthorController::class)->except(['show']);
        Route::resource('publications', PublicationController::class)->except(['show']);
        Route::resource('mentors', MentorController::class)->except(['show']);
        Route::resource('books', BookController::class)->except(['show']);

        Route::resource('courses', CourseController::class)->except(['show']);
        Route::post('courses/{course}/sections', [CourseSectionController::class, 'store'])->name('courses.sections.store');
        Route::put('courses/{course}/sections/{section}', [CourseSectionController::class, 'update'])->name('courses.sections.update');
        Route::delete('courses/{course}/sections/{section}', [CourseSectionController::class, 'destroy'])->name('courses.sections.destroy');
        Route::post('courses/{course}/sections/{section}/lessons', [CourseLessonController::class, 'store'])->name('courses.lessons.store');
        Route::put('courses/{course}/sections/{section}/lessons/{lesson}', [CourseLessonController::class, 'update'])->name('courses.lessons.update');
        Route::delete('courses/{course}/sections/{section}/lessons/{lesson}', [CourseLessonController::class, 'destroy'])->name('courses.lessons.destroy');

        Route::resource('plans', PlanController::class)->except(['show']);

        Route::resource('coupons', CouponController::class)->except(['show']);
        Route::resource('free-campaigns', FreeCampaignController::class)->except(['show']);

        Route::resource('ai-providers', AiProviderController::class)->except(['show']);
        Route::post('ai-providers/{aiProvider}/test', [AiProviderController::class, 'test'])->name('ai-providers.test');

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

        Route::resource('pages', CustomPageController::class)->except(['show']);

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
