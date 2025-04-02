<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [FormController::class, 'form'])->name('show-form');
Route::post('/submit-form', [FormController::class, 'submitForm'])->name('submit-form');
Route::get('/fetch-data', [FormController::class, 'fetchData'])->name('fetch-data');
