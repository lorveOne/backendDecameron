<?php

use App\Http\Controllers\HotelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([

  'middleware' => 'api',
  'prefix' => ''

], function ($router) {
});

//-------------------APIS PARA HOTEL
Route::controller(HotelController::class)->prefix('hoteles')->group(function () {
  Route::get('/', 'get');               // Obtener todos los hoteles
  Route::get('/{id}', 'getById');         // Obtener un hotel por ID
  Route::post('/', 'create');           // Crear un nuevo hotel
  Route::put('/{id}', 'update');           // Actualizar hotel por ID
  Route::delete('/{id}', 'delete');     // Eliminar hotel por ID
});





