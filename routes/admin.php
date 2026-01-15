<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\SectionTitleController;

Auth::routes(['register' => false]);

// Role-specific dashboard routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/educator', [AdminController::class, 'index'])->name('teacher.index')->middleware('role:Educator');
    Route::get('/learner', [AdminController::class, 'index'])->name('student.index')->middleware('role:Learner');

    // Generic dashboard route (redirects based on role)
    Route::get('/dashboard', [AdminController::class, 'redirectToDashboard'])->name('dashboard');
});

// Educator routes (previously admin routes) - keeping route names as teacher.* for backward compatibility
Route::group(['prefix' => 'educator', 'as' => 'teacher.', 'middleware' => ['auth', 'active', 'role:Educator']], function () {

    // Profile
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile-update', [ProfileController::class, 'update'])->name('profile.update')->middleware('password.confirm');
    Route::get('change-password', [ProfileController::class, 'password'])->name('password.index');
    Route::put('update-password', [ProfileController::class, 'updatePassword'])->name('password.update')->middleware('password.confirm');

    // User Management
    Route::resource('users', UserController::class);
    Route::get('users-bulk-create', [UserController::class, 'bulkCreate'])->name('users.bulk-create');
    Route::post('users-bulk-store', [UserController::class, 'bulkStore'])->name('users.bulk-store');
    Route::patch('user-toggle-status/{id}', [UserController::class, 'toggleStatus'])->name('user.toggleStatus');

    // Class Management
    Route::resource('classes', ClassController::class);
    Route::get('classes/{id}/enrollments', [ClassController::class, 'enrollments'])->name('classes.enrollments');
    Route::post('classes/{id}/enroll', [ClassController::class, 'enroll'])->name('classes.enroll');
    Route::delete('classes/{classId}/unenroll/{studentId}', [ClassController::class, 'unenroll'])->name('classes.unenroll');

    // Subject-Teacher Assignments
    Route::post('classes/{id}/assign-teacher', [ClassController::class, 'assignTeacher'])->name('classes.assign-teacher');
    Route::delete('assignments/{assignmentId}', [ClassController::class, 'unassignTeacher'])->name('classes.unassign-teacher');

    // Subject Management
    Route::resource('subjects', SubjectController::class);
    Route::patch('subjects/{id}/toggle-publish', [SubjectController::class, 'togglePublish'])->name('subjects.toggle-publish');

    // Section Title Management
    Route::get('section-titles/create/{subjectId}', [SectionTitleController::class, 'create'])->name('section-titles.create');
    Route::post('section-titles', [SectionTitleController::class, 'store'])->name('section-titles.store');
    Route::get('section-titles/{id}/edit', [SectionTitleController::class, 'edit'])->name('section-titles.edit');
    Route::put('section-titles/{id}', [SectionTitleController::class, 'update'])->name('section-titles.update');
    Route::delete('section-titles/{id}', [SectionTitleController::class, 'destroy'])->name('section-titles.destroy');
});
