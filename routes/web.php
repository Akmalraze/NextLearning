<?php

// use App\Http\Controllers\Admin\ClassController; // Commented out - route group is disabled
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MaterialController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

// Health check endpoint for ELB (no database required)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ], 200);
});

Route::get('/', function () {
    return view('welcome');
});

// Public course catalog
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{subject}', [CourseController::class, 'show'])->name('courses.show');
Route::post('/courses/{subject}/enroll', [CourseController::class, 'enroll'])
    ->middleware(['auth', 'active', 'role:Learner'])
    ->name('courses.enroll');

// My Courses (enrolled courses for learners)
Route::get('/my-courses', [CourseController::class, 'myCourses'])
    ->middleware(['auth', 'active', 'role:Learner'])
    ->name('courses.my-courses');

// Unenroll from a course
Route::post('/courses/{subject}/unenroll', [CourseController::class, 'unenroll'])
    ->middleware(['auth', 'active', 'role:Learner'])
    ->name('courses.unenroll');

Auth::routes(['register' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'active'])->controller(ModuleController::class)
    ->prefix('ManageModules')
    ->name('modules-')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/list', 'list')->name('list');
        Route::get('/create/{subjectId}', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('{modules}/edit', 'edit')->name('edit');
        Route::put('{modules}', 'update')->name('update');
        Route::get('{modules}', 'view')->name('view');
        Route::delete('{modules}', 'destroy')->name('destroy');
    });

// Material Routes
Route::middleware(['auth', 'active'])->controller(MaterialController::class)
    ->prefix('materials')
    ->name('materials-')
    ->group(function () {
        Route::get('/create/{id}', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });


// Commented out - ClassController doesn't exist, using Admin\ClassController via educator routes instead
// Route::middleware(['auth', 'active', 'role:Educator'])->controller(ClassController::class)
//     ->prefix('ManageClasses')
//     ->name('classes-')
//     ->group(function () {
//         Route::get('/', 'index')->name('index');
//         Route::get('create', 'create')->name('create');
//         Route::post('/store', 'store')->name('store');
//         Route::get('{classes}/edit', 'edit')->name('edit');
//         Route::put('{classes}', 'update')->name('update');
//         Route::get('{classes}', 'view')->name('view');
//         Route::delete('{classes}', 'destroy')->name('destroy');
//     });





Route::get('/report', function () {
    return view('pages.ManageReport.index');
})->name('report');

Route::get('/subject', function () {
    return view('pages.ManageSubject.index');
})->name('subject');

// Assessment Routes
Route::middleware(['auth', 'active'])->controller(AssessmentController::class)
    ->prefix('assessments')
    ->name('assessments.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        
        // Question management (Teacher only)
        Route::post('/{id}/questions', 'storeQuestion')->name('questions.store');
        Route::delete('/{id}/questions/{questionId}', 'deleteQuestion')->name('questions.delete');
        
        // Material management (Teacher only)
        Route::post('/{id}/materials', 'uploadMaterial')->name('materials.upload');
        Route::delete('/{id}/materials/{materialId}', 'deleteMaterial')->name('materials.delete');
        
        // Student actions
        Route::post('/{id}/start-quiz', 'startQuiz')->name('start-quiz');
        Route::post('/{id}/submit-quiz', 'submitQuiz')->name('submit-quiz');
        Route::post('/{id}/submit-homework', 'submitHomework')->name('submit-homework');
        Route::delete('/{id}/remove-submission', 'removeSubmission')->name('remove-submission');
        
        // Teacher actions - view and grade submissions
        Route::get('/{id}/submissions', 'viewSubmissions')->name('submissions');
        Route::put('/{id}/submissions/{submissionId}/mark', 'updateSubmissionMark')->name('submissions.update-mark');
    });

// Legacy route for backward compatibility
Route::get('/assessment', function () {
    return redirect()->route('assessments.index');
})->name('assessment');

// Material file download route (for Windows/Laragon compatibility)
Route::get('/storage/materials/{filename}', function ($filename) {
    $path = storage_path('app/public/materials/' . $filename);
    
    if (!file_exists($path)) {
        abort(404, 'File not found');
    }
    
    return response()->file($path);
})->where('filename', '.*')->name('materials.show');
