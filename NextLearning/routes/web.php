<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\ModuleController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'active'])->prefix('ManageModules')->name('modules-')->group(function () {
    Route::get('/', [ModuleController::class, 'index'])->name('index');
    Route::get('/list', [ModuleController::class, 'list'])->name('list');
    Route::get('/create/{subjectId}', [ModuleController::class, 'create'])->name('create');
    Route::post('/store', [ModuleController::class, 'store'])->name('store');
    Route::get('{module}/edit', [ModuleController::class, 'edit'])->name('edit');
    Route::put('{module}', [ModuleController::class, 'update'])->name('update');
    Route::delete('{module}', [ModuleController::class, 'destroy'])->name('destroy');
    Route::get('{module}', [ModuleController::class, 'show'])->name('view');
});

Route::middleware(['auth', 'active'])->prefix('ManageMaterials')->name('materials-')->group(function () {
    // Create material form
    Route::get('create/{moduleId}', [MaterialController::class, 'create'])->name('create');
    
    // Store new material
    Route::post('store', [MaterialController::class, 'store'])->name('store');
    
    // Edit material form
    Route::get('edit/{materialId}', [MaterialController::class, 'edit'])->name('edit');
    
    // Update material
    Route::put('update/{materialId}', [MaterialController::class, 'update'])->name('update');
    
    // Delete material
    Route::delete('destroy/{materialId}', [MaterialController::class, 'destroy'])->name('destroy');
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





Route::get('/report', function () {
    return view('pages.ManageReport.index');
})->name('report');

Route::get('/subject', function () {
    return view('pages.ManageSubject.index');
})->name('subject');

Route::get('/assessment', function () {
    return view('pages.ManageAssessment.index');
})->name('assessment');
