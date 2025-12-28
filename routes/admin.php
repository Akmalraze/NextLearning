<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SubjectController;

Auth::routes(['register' => false]);

// Role-specific dashboard routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index')->middleware('role:Admin');
    Route::get('/teacher', [AdminController::class, 'index'])->name('teacher.index')->middleware('role:Teacher');
    Route::get('/student', [AdminController::class, 'index'])->name('student.index')->middleware('role:Student');

    // Generic dashboard route (redirects based on role)
    Route::get('/dashboard', [AdminController::class, 'redirectToDashboard'])->name('dashboard');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'active', 'role:Admin']], function () {

    // profile
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

    // Subject-Teacher Assignments (actions only, view integrated into show page)
    Route::post('classes/{id}/assign-teacher', [ClassController::class, 'assignTeacher'])->name('classes.assign-teacher');
    Route::delete('assignments/{assignmentId}', [ClassController::class, 'unassignTeacher'])->name('classes.unassign-teacher');

    // Subject Management
    Route::resource('subjects', SubjectController::class);

    // Roles & Permissions
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
});
