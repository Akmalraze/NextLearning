<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\TeacherReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'active'])->controller(ModuleController::class)
    ->prefix('ManageModules')
    ->name('modules-')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/list', 'list')->name('list');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('{modules}/edit', 'edit')->name('edit');
        Route::put('{modules}', 'update')->name('update');
        Route::get('{modules}', 'view')->name('view');
        Route::delete('{modules}', 'destroy')->name('destroy');
    });


Route::middleware(['auth', 'active', 'role:Admin'])->controller(ClassController::class)
    ->prefix('ManageClasses')
    ->name('classes-')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('{classes}/edit', 'edit')->name('edit');
        Route::put('{classes}', 'update')->name('update');
        Route::get('{classes}', 'view')->name('view');
        Route::delete('{classes}', 'destroy')->name('destroy');
    });






Route::prefix('admin')->middleware(['auth', 'role:Admin'])->group(function () {
    // Admin Report page
    Route::get('/report', [AdminReportController::class, 'adminReport'])->name('admin.report');
    // Admin Report export (JSON/CSV)
    Route::get('/report/export', [AdminReportController::class, 'adminReportExport'])->name('admin.report.export');
});

Route::prefix('teacher')->middleware(['auth', 'role:Teacher'])->group(function () {
    // Teacher Report page
    Route::get('/report', [TeacherReportController::class, 'teacherReport'])->name('teacher.report');
    // Teacher Report export (CSV)
    Route::get('/report/export', [TeacherReportController::class, 'teacherReportExport'])->name('teacher.report.export');
});



Route::get('/subject', function () {
    return view('pages.ManageSubject.index');
})->name('subject');

// Assessments - accessible to Teacher and Student only
Route::middleware(['auth', 'active', 'role:Teacher|Student'])->group(function () {
    Route::resource('assessments', App\Http\Controllers\AssessmentController::class);
    
    // Questions for quiz assessments (Teacher)
    Route::post('assessments/{id}/questions', [App\Http\Controllers\AssessmentController::class, 'storeQuestion'])->name('assessments.questions.store');
    Route::delete('assessments/{id}/questions/{questionId}', [App\Http\Controllers\AssessmentController::class, 'deleteQuestion'])->name('assessments.questions.destroy');
    
    // Materials for test/homework assessments (Teacher)
    Route::post('assessments/{id}/materials', [App\Http\Controllers\AssessmentController::class, 'uploadMaterial'])->name('assessments.materials.store');
    Route::delete('assessments/{id}/materials/{materialId}', [App\Http\Controllers\AssessmentController::class, 'deleteMaterial'])->name('assessments.materials.destroy');

    // Student submissions
    Route::post('assessments/{id}/start-quiz', [App\Http\Controllers\AssessmentController::class, 'startQuiz'])->name('assessments.startQuiz');
    Route::post('assessments/{id}/submit-quiz', [App\Http\Controllers\AssessmentController::class, 'submitQuiz'])->name('assessments.submitQuiz');
    Route::post('assessments/{id}/submit-homework', [App\Http\Controllers\AssessmentController::class, 'submitHomework'])->name('assessments.submitHomework');
    Route::delete('assessments/{id}/remove-submission', [App\Http\Controllers\AssessmentController::class, 'removeSubmission'])->name('assessments.removeSubmission');
    
    // Teacher: View and grade student submissions
    Route::get('assessments/{id}/submissions', [App\Http\Controllers\AssessmentController::class, 'viewSubmissions'])->name('assessments.submissions');
    Route::post('assessments/{id}/submissions/{submissionId}/update-mark', [App\Http\Controllers\AssessmentController::class, 'updateSubmissionMark'])->name('assessments.submissions.updateMark');
});
