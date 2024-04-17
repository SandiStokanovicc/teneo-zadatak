<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;

header('Access-Control-Allow-Methods:  POST, GET, OPTIONS, PUT, DELETE');

Route::get('/getMonthData', [CalendarController::class, 'getMonthData']);

Route::get('/getAbsenceTypes', [CalendarController::class, 'getAbsenceTypes']);

Route::get('/getAbsences', [CalendarController::class, 'getAbsences']);

Route::post('/insertAbsence', [CalendarController::class, 'insertAbsence']);

Route::get('/toArchive', [CalendarController::class, 'toArchive']);

Route::get('/getArchive', [CalendarController::class, 'getArchive']);

