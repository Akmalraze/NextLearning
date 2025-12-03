<?php

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

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/report', function () {
    return view('pages.ManageReport.index');  
})->name('report');

Route::get('/subject', function () {
    return view('pages.ManageSubject.index');  
})->name('subject');

Route::get('/module', function () {
    return view('pages.ManageModule.index');  
})->name('module');

Route::get('/class', function () {
    return view('pages.ManageClass.index');  
})->name('class');

Route::get('/assessment', function () {
    return view('pages.ManageAssessment.index');  
})->name('assessment');