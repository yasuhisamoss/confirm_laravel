<?php

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

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RaceCardController;
use App\Http\Controllers\RaceHistoryController;
use App\Http\Controllers\HorseController;
use App\Http\Controllers\ChokyoController;
use App\Http\Controllers\MoistureCushionController;
use App\Http\Controllers\RaceScheduleController;
use App\Http\Controllers\RaceSearchController;
use App\Http\Controllers\StallionController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/mcc_regist', [HomeController::class, 'regist'])->name('mcc_regist');

Route::get('/moisture_cushion', [MoistureCushionController::class, 'index'])->name('moisture_cushion');
Route::post('/cushion_regist', [MoistureCushionController::class, 'regist'])->name('cushion_regist');

Route::get('/race_card/{race_date}/{place}/{num}', [RaceCardController::class, 'index'])->name('race_card');

Route::get('/race_history/{horse_id}/{race_date?}', [RaceHistoryController::class, 'index'])->name('race_history');

Route::get('/horse/{horse_id}/', [HorseController::class, 'index'])->name('horse');
Route::get('/horse_search', [HorseController::class, 'search'])->name('horse_search');

Route::get('/chokyo', [ChokyoController::class, 'index'])->name('chokyo');
Route::get('/chokyo/horse/{horse_id}/{race_id}', [ChokyoController::class, 'horse']);
Route::get('/chokyo/race/{race_date}/{place}/{race_num}', [ChokyoController::class, 'race']);

Route::get('/race_schedule/{start_date?}/{end_date?}', [RaceScheduleController::class, 'index'])->name('race_schedule');
Route::post('/race_schedule_regist', [RaceScheduleController::class, 'regist'])->name('race_schedule_regist');

Route::get('/race_search', [RaceSearchController::class, 'index'])->name('race_search');
Route::post('/search_exec', [RaceSearchController::class, 'exec'])->name('search_exec');

Route::get('/stallion', [StallionController::class, 'index'])->name('stallion');
Route::get('/corse_point_regist/{stallion_id}', [StallionController::class, 'corse_point_regist'])->name('corse_point_regist');
Route::post('/regist_stallion_bias', [StallionController::class, 'regist_stallion_bias'])->name('regist_stallion_bias');
Route::post('/regist_stallion_corse', [StallionController::class, 'regist_stallion_corse'])->name('regist_stallion_corse');
Route::post('/update_stallion_memo', [StallionController::class, 'update_stallion_memo'])->name('update_stallion_memo');
