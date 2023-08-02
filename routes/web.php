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

Route::get('/', function () {
    return redirect('home');
});/*
Route::get('/correo', function () {
    return view('pages.extras.registro_siniestros');
});*/

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/listado/nombres', [App\Http\Controllers\HomeController::class, 'lst_nombres'])->name('home.listado');
Route::get('/personaje/{id}/ver', [App\Http\Controllers\HomeController::class, 'personaje_show'])->name('personaje.show');

Route::get('form/prueba',function(){
    return view('prueba_from');
});