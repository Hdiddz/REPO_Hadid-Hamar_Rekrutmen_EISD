<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\SkillController as AdminSkillController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Employer\ApplicationController as EmployerApplicationController;
use App\Http\Controllers\Employer\DashboardController as EmployerDashboardController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobController;
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
    Route::view('/chat', 'chat.index')->name('chat.index');

    Route::middleware('role:jobseeker')->group(function (): void {
        Route::post('/lowongan/{job}/lamar', [JobApplicationController::class, 'store'])->name('applications.store');
        Route::get('/riwayat-lamaran', [JobApplicationController::class, 'index'])->name('applications.index');
        Route::redirect('/lamaran', '/riwayat-lamaran')->name('applications.alias');
        Route::get('/profil', [ProfileController::class, 'show'])->name('profile.index');
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
        Route::get('/pelamar/{application}/resume', [EmployerApplicationController::class, 'download'])->name('applications.resume');
    });

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/lowongan', [AdminJobController::class, 'index'])->name('jobs.index');
        Route::get('/lowongan/{job}', [AdminJobController::class, 'show'])->name('jobs.show');
        Route::patch('/lowongan/{job}/status', [AdminJobController::class, 'updateStatus'])->name('jobs.status');
        Route::get('/master-data', [AdminCategoryController::class, 'index'])->name('master');
        Route::resource('categories', AdminCategoryController::class)->only(['store', 'update', 'destroy']);
        Route::resource('skills', AdminSkillController::class)->only(['store', 'update', 'destroy']);
        Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
        Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::get('/pelamar/{application}/resume', [EmployerApplicationController::class, 'download'])->name('applications.resume');
    });
});
