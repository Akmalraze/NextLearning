<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;

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





// Route::get('/report', function () {
//     return view('pages.ManageReport.adminreport');
// })->name('report');
<<<<<<< HEAD
Route::middleware(['auth', 'role:Admin'])->get('/report', [ReportController::class, 'adminReport'])->name('report');
=======
Route::middleware(['auth', 'role:Admin'])
    ->get('/admin/report', [ReportController::class, 'adminReport'])
    ->name('admin.report');

<<<<<<< HEAD
Route::middleware(['auth', 'role:Teacher'])
    ->get('/teacher/report', [ReportController::class, 'teacherReport'])
    ->name('teacher.report');
=======
Route::middleware(['auth', 'role:Admin'])
    ->get('/admin/report', [ReportController::class, 'adminReport'])
    ->name('admin.report');

Route::get('/admin/report/export', [ReportController::class, 'adminReportExport'])
    ->name('admin.report.export');

Route::middleware(['auth', 'role:Teacher'])
    ->get('/teacher/report', [ReportController::class, 'teacherReport'])
    ->name('teacher.report');

Route::get('/teacher/report/export', [ReportController::class, 'teacherReportExport'])
    ->name('teacher.report.export')
    ->middleware('auth', 'role:Teacher');

>>>>>>> parent of 694f6f0 (update)

<<<<<<< HEAD
    Route::get('/teacher/report/export', [ReportController::class, 'teacherReportExport'])
    ->name('teacher.report.export')
    ->middleware('auth', 'role:Teacher');
>>>>>>> parent of 71ddf72 (update)

=======
>>>>>>> parent of 42a1092 (teacher report update)
Route::get('/subject', function () {
    return view('pages.ManageSubject.index');
})->name('subject');

Route::get('/assessment', function () {
    return view('pages.ManageAssessment.index');
})->name('assessment');
