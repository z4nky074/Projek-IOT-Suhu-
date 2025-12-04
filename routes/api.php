<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IoTController;

// IoT Data - Suhu & Kelembapan
Route::get('/suhu', [IoTController::class, 'getSuhu']);
Route::post('/suhu', [IoTController::class, 'simpanSuhu']);
Route::delete('/suhu/{id}', [IoTController::class, 'deleteSuhu']);
Route::get('/kelembapan', [IoTController::class, 'getKelembapan']);
Route::post('/kelembapan', [IoTController::class, 'simpanKelembapan']);
Route::delete('/kelembapan/{id}', [IoTController::class, 'deleteKelembapan']);

// Master Data - Ruangan
Route::get('/ruangan', [IoTController::class, 'getRuangan']);
Route::get('/ruangan/{id}', [IoTController::class, 'getRuanganById']); // ← Tambahkan ini
Route::post('/ruangan', [IoTController::class, 'simpanRuangan']);
Route::put('/ruangan/{id}', [IoTController::class, 'updateRuangan']);
Route::delete('/ruangan/{id}', [IoTController::class, 'deleteRuangan']);

// Master Data - Alat
Route::get('/alat', [IoTController::class, 'getAlat']);
Route::get('/alat/{id}', [IoTController::class, 'getAlatById']);
Route::post('/alat', [IoTController::class, 'simpanAlat']);
Route::put('/alat/{id}', [IoTController::class, 'updateAlat']);
Route::delete('/alat/{id}', [IoTController::class, 'deleteAlat']);

// Master Data - Sensor
Route::get('/sensor', [IoTController::class, 'getSensor']);
Route::get('/sensor/{id}', [IoTController::class, 'getSensorById']);
Route::post('/sensor', [IoTController::class, 'simpanSensor']);
Route::put('/sensor/{id}', [IoTController::class, 'updateSensor']);
Route::delete('/sensor/{id}', [IoTController::class, 'deleteSensor']);

// Dashboard & Analytics
Route::get('/dashboard', [IoTController::class, 'getDashboard']);
Route::get('/statistik', [IoTController::class, 'getStatistik']);
Route::get('/statistik/{id_sensor}', [IoTController::class, 'getStatistikBySensor']);

// Filter By Date Range
Route::get('/suhu/range', [IoTController::class, 'getSuhuByRange']);
Route::get('/kelembapan/range', [IoTController::class, 'getKelembapanByRange']);

// Export Data
Route::get('/export/suhu', [IoTController::class, 'exportSuhu']);
Route::get('/export/kelembapan', [IoTController::class, 'exportKelembapan']);

// Utility & Debug
Route::get('/cek-sensor', [IoTController::class, 'cekSensor']);
Route::get('/cek-alat', [IoTController::class, 'cekAlat']);
Route::get('/cek-ruangan', [IoTController::class, 'cekRuangan']);
Route::get('/seed-test-data', [IoTController::class, 'seedTestData']);
Route::get('/tes', [IoTController::class, 'tes']);