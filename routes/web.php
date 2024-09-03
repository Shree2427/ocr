<?php

//use App\Http\Controllers\OcrController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\OCRController;

Route::get('/', function () {
    return view('ocr_form');
});

Route::post('/process-image', [OCRController::class, 'processImage']);
