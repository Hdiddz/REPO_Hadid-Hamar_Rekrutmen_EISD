<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SkillController as AdminSkillController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Employer\ApplicationController as EmployerApplicationController;
use App\Http\Controllers\Employer\DashboardController as EmployerDashboardController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/lowongan', [JobController::class, 'index'])->name('jobs.index');
Route::get('/lowongan/{job}', [JobController::class, 'show'])->name('jobs.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/masuk', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/daftar', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');
    Route::get('/pengaturan', [ProfileController::class, 'settings'])->name('settings.index');
    Route::post('/pengaturan/avatar', [ProfileController::class, 'updateAvatar'])->name('settings.avatar.update');
    Route::delete('/pengaturan/avatar', [ProfileController::class, 'destroyAvatar'])->name('settings.avatar.destroy');
    Route::put('/pengaturan/username', [ProfileController::class, 'updateUsername'])->name('settings.username.update');
    Route::put('/pengaturan/kata-sandi', [ProfileController::class, 'updatePassword'])->name('settings.password.update');
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::redirect('/pesan', '/chat');
    Route::get('/chat/conversations', [ChatController::class, 'conversations'])->name('chat.conversations');
    Route::delete('/chat/conversations/{user}', [ChatController::class, 'removeConversation'])->name('chat.conversations.remove');
    Route::get('/chat/messages/{user}', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/messages/{user}', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::delete('/chat/messages/{message}', [ChatController::class, 'deleteMessage'])->name('chat.messages.delete');
    Route::delete('/chat/clear/{user}', [ChatController::class, 'clearChat'])->name('chat.clear');
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/{id}/baca', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifikasi/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifikasi/baca-semua', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.index');

    Route::middleware('role:jobseeker')->group(function (): void {
        Route::post('/lowongan/{job}/lapor', [JobReportController::class, 'store'])->name('jobs.report');
        Route::post('/lowongan/{job}/lamar', [JobApplicationController::class, 'store'])->name('applications.store');
        Route::get('/riwayat-lamaran', [JobApplicationController::class, 'index'])->name('applications.index');
        Route::redirect('/lamaran', '/riwayat-lamaran')->name('applications.alias');
        Route::post('/riwayat-lamaran/{application}/resign', [JobApplicationController::class, 'requestResignation'])->name('applications.resign');
        Route::delete('/riwayat-lamaran/{application}', [JobApplicationController::class, 'destroy'])->name('applications.destroy');
        Route::get('/riwayat-lamaran/{application}/resume/preview', [EmployerApplicationController::class, 'preview'])->name('applications.resume.preview');
    });

    Route::prefix('mitra')->name('employer.')->middleware('role:employer')->group(function (): void {
        Route::get('/dashboard', EmployerDashboardController::class)->name('dashboard');
        Route::resource('lowongan', EmployerJobController::class)
            ->parameters(['lowongan' => 'job'])
            ->names('jobs')
            ->except(['index', 'show']);
        Route::patch('/lowongan/{job}/status', [EmployerJobController::class, 'updateStatus'])->name('jobs.status');
        Route::get('/pelamar', [EmployerApplicationController::class, 'index'])->name('applications.index');
        Route::patch('/pelamar/{application}', [EmployerApplicationController::class, 'update'])->name('applications.update');
        Route::patch('/pelamar/{application}/resign-decision', [EmployerApplicationController::class, 'resignDecision'])->name('applications.resignDecision');
        Route::get('/pelamar/{application}/resume', [EmployerApplicationController::class, 'download'])->name('applications.resume');
        Route::get('/pelamar/{application}/resume/preview', [EmployerApplicationController::class, 'preview'])->name('applications.resume.preview');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/lowongan', [AdminJobController::class, 'index'])->name('jobs.index');
        Route::get('/lowongan/{job}', [AdminJobController::class, 'show'])->name('jobs.show');
        Route::get('/lowongan/{job}/edit', [AdminJobController::class, 'edit'])->name('jobs.edit');
        Route::put('/lowongan/{job}', [AdminJobController::class, 'update'])->name('jobs.update');
        Route::post('/lowongan/{job}/tutup', [AdminJobController::class, 'close'])->name('jobs.close');
        Route::post('/lowongan/{job}/buka', [AdminJobController::class, 'reopen'])->name('jobs.reopen');
        Route::delete('/lowongan/{job}', [AdminJobController::class, 'destroy'])->name('jobs.destroy');
        Route::patch('/lamaran/{application}/status', [AdminJobController::class, 'updateApplicationStatus'])->name('applications.status');
        Route::patch('/lowongan/{job}/status', [AdminJobController::class, 'updateStatus'])->name('jobs.status');
        Route::get('/laporan', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/{report}', [AdminReportController::class, 'show'])->name('reports.show');
        Route::patch('/laporan/{report}/tinjau', [AdminReportController::class, 'markReviewed'])->name('reports.review');
        Route::post('/laporan/{report}/tindak-lanjut', [AdminReportController::class, 'action'])->name('reports.action');
        Route::get('/master-data', [AdminCategoryController::class, 'index'])->name('master');
        Route::resource('categories', AdminCategoryController::class)->only(['store', 'update', 'destroy']);
        Route::resource('skills', AdminSkillController::class)->only(['store', 'update', 'destroy']);
        Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/pengguna/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::put('/pengguna/{user}/akun', [AdminUserController::class, 'updateAccount'])->name('users.account');
        Route::put('/pengguna/{user}/kata-sandi', [AdminUserController::class, 'updatePassword'])->name('users.password');
        Route::post('/pengguna/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
        Route::post('/pengguna/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');
        Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/pelamar/{application}/resume', [EmployerApplicationController::class, 'download'])->name('applications.resume');
        Route::get('/pelamar/{application}/resume/preview', [EmployerApplicationController::class, 'preview'])->name('applications.resume.preview');
    });
});
