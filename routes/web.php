<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/test', 'TestController@index');
Route::get('/', 'HomeController@index');
Route::get('/master', function () {
    return Redirect::to('/');
});
Route::get('/penjadwalan', function () {
    return Redirect::to('/');
});
Route::get('/master/program-studi', 'ProgramStudiController@index');
Route::get('/master/program-studi/tambah', 'ProgramStudiController@create');
Route::post('/master/program-studi/tambah', 'ProgramStudiController@store');
Route::get('/master/program-studi/ubah/{id}', 'ProgramStudiController@edit');
Route::post('/master/program-studi/ubah/{id}', 'ProgramStudiController@update');
Route::get('/master/program-studi/hapus/{id}', 'ProgramStudiController@destroy');

Route::get('/master/mata-kuliah', 'MataKuliahController@index');
Route::get('/master/mata-kuliah/tambah', 'MataKuliahController@create');
Route::post('/master/mata-kuliah/tambah', 'MataKuliahController@store');
Route::get('/master/mata-kuliah/ubah/{id}', 'MataKuliahController@edit');
Route::post('/master/mata-kuliah/ubah/{id}', 'MataKuliahController@update');
Route::get('/master/mata-kuliah/hapus/{id}', 'MataKuliahController@destroy');
Route::get('/master/mata-kuliah/detail/{id}', 'MataKuliahController@show');

Route::get('/master/ruang', 'RuangController@index');
Route::get('/master/ruang/tambah', 'RuangController@create');
Route::post('/master/ruang/tambah', 'RuangController@store');
Route::get('/master/ruang/ubah/{id}', 'RuangController@edit');
Route::post('/master/ruang/ubah/{id}', 'RuangController@update');
Route::get('/master/ruang/hapus/{id}', 'RuangController@destroy');

Route::get('/master/dosen', 'DosenController@index');
Route::get('/master/dosen/detail/{id}', 'DosenController@show');
Route::get('/master/dosen/tambah', 'DosenController@create');
Route::post('/master/dosen/tambah', 'DosenController@store');
Route::get('/master/dosen/ubah/{id}', 'DosenController@edit');
Route::post('/master/dosen/ubah/{id}', 'DosenController@update');
Route::get('/master/dosen/hapus/{id}', 'DosenController@destroy');

Route::get('/master/dosen/detail/{id}/mata-kuliah', 'DosenMataKuliahController@createDosenMataKuliah');
Route::post('/master/dosen/detail/{id}/mata-kuliah', 'DosenMataKuliahController@storeDosenMataKuliah');
Route::get('/master/dosen/detail/{id}/mata-kuliah/hapus/{iddosenmatkul}', 'DosenMataKuliahController@destroyDosenMataKuliah');

Route::get('/master/mata-kuliah/detail/{id}/dosen', 'DosenMataKuliahController@createMataKuliahDosen');
Route::post('/master/mata-kuliah/detail/{id}/dosen', 'DosenMataKuliahController@storeMataKuliahDosen');
Route::get('/master/mata-kuliah/detail/{id}/dosen/hapus/{iddosenmatkul}', 'DosenMataKuliahController@destroy');

Route::get('/master/sesi', 'SesiController@index');
Route::get('/master/sesi/detail/{id}', 'SesiController@show');
Route::get('/master/sesi/tambah', 'SesiController@create');
Route::post('/master/sesi/tambah', 'SesiController@store');
Route::get('/master/sesi/ubah/{id}', 'SesiController@edit');
Route::post('/master/sesi/ubah/{id}', 'SesiController@update');
Route::get('/master/sesi/hapus/{id}', 'SesiController@destroy');

Route::get('/master/hari', 'HariController@index');
Route::get('/master/hari/detail/{id}', 'HariController@show');
Route::get('/master/hari/tambah', 'HariController@create');
Route::post('/master/hari/tambah', 'HariController@store');
Route::get('/master/hari/ubah/{id}', 'HariController@edit');
Route::post('/master/hari/ubah/{id}', 'HariController@update');
Route::get('/master/hari/hapus/{id}', 'HariController@destroy');

Route::get('/master/peminat', 'PeminatController@index');
Route::get('/master/peminat/tambah', 'PeminatController@create');
Route::post('/master/peminat/tambah', 'PeminatController@store');
Route::get('/master/peminat/ubah/{id}', 'PeminatController@edit');
Route::post('/master/peminat/ubah/{id}', 'PeminatController@update');
Route::get('/master/peminat/hapus/{id}', 'PeminatController@destroy');

Route::get('/master/peminat/detail/{id}', 'PeminatDetailController@index');
Route::get('/master/peminat/detail/{idpeminat}/tambah', 'PeminatDetailController@create');
Route::post('/master/peminat/detail/{idpeminat}/tambah', 'PeminatDetailController@store');
Route::get('/master/peminat/detail/{idpeminat}/ubah/{id}', 'PeminatDetailController@edit');
Route::post('/master/peminat/detail/{idpeminat}/ubah/{id}', 'PeminatDetailController@update');
Route::get('/master/peminat/detail/{idpeminat}/hapus/{id}', 'PeminatDetailController@destroy');

Route::post('/master/peminat/detail/{idpeminat}/tambah/batch/preview', 'PeminatDetailBatchController@validationInput');
Route::get('/master/peminat/detail/{idpeminat}/tambah/batch/preview', 'PeminatDetailBatchController@create');
Route::post('/master/peminat/detail/{idpeminat}/tambah/batch', 'PeminatDetailBatchController@store');

Route::get('/penjadwalan/kelompok-dosen', 'KelompokDosenController@index');
Route::get('/penjadwalan/kelompok-dosen/tambah', 'KelompokDosenController@create');
Route::post('/penjadwalan/kelompok-dosen/tambah', 'KelompokDosenController@store');
