<?php

use App\Http\Controllers\HabitacionInfoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([

  'middleware' => 'api',
  'prefix' => ''

], function ($router) {
});

//-------------------APIS PARA Habitacion
Route::controller(HabitacionInfoController::class)->prefix('habitaciones')->group(function () {
  Route::get('/', 'getHabitacion');               // Obtener todos los hoteles
  Route::get('/{id}', 'getHabitacionId');         // Obtener un hotel por ID
  Route::post('/', 'insertHabitacion');           // Crear un nuevo hotel
  Route::put('/{id}', 'updateHabitacion');           // Actualizar hotel por ID
  Route::delete('/{id}');     // Eliminar hotel por ID
});





