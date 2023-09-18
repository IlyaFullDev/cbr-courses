<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurseController;
use App\Http\Controllers\QueueController;
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
    return view('home');
});

Route::get('/queue/', [QueueController::class, 'QueueStart']);

Route::post('/', [CurseController::class, 'GetCourseStart'])->name('curs-form');
