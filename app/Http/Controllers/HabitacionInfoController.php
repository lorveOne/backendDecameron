<?php

namespace App\Http\Controllers;
use App\Models\habitacion_info_tipo;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HabitacionInfoController extends Controller
{
    public function getHabitacion()
    {
        try {
            $habitaciones = habitacion_info_tipo::all();

            if ($habitaciones->isEmpty()) {
                return response()->json([
                    'message' => 'error',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $habitaciones
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching habitaciones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }   

    public function getHabitacionId($id)
    {
        try {
            $habitacion = habitacion_info_tipo::find($id);

            if (!$habitacion) {
                return response()->json([
                    'message' => 'error',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $habitacion
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the habitacion.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function insertHabitacion(Request $request)
    {
        try {
            // Validación
            $validated = $request->validate([
                'idHotel' => 'required|integer',
                'numHabi' => 'required|integer',
                'tipoHabi' => 'required|string|max:255',
                'acomoda' => 'required|string|max:255'
            ]);
 
    
            // Verificar cantidad de habitaciones no supere la permitida por el hotel
            $hotel = Hotel::find($request->idHotel);
            if (!$hotel) {
                return response()->json([
                    'message' => 'El hotel asociado no existe.',
                    'status' => 404
                ], 404);
            }
            $numHabi = $request->input('numHabi');
            $totalHabitacionesAsignadas = habitacion_info_tipo::where('idHotel', $request->idHotel)->sum('numHabi');
            $totalHabitacionesDisponibles = $hotel->numHab - $totalHabitacionesAsignadas;
            if ( $numHabi > $totalHabitacionesDisponibles) {
                return response()->json([
                    'message' => 'El total de habitaciones excede la capacidad del hotel.',
                    'status' => 400
                ], 400);
            }
            if ( $totalHabitacionesDisponibles == 0) {
                return response()->json([
                    'message' => 'Ya no hay habitaciones disponibles para asignar.',
                    'status' => 400
                ], 400);
            }

            $habitacion = habitacion_info_tipo::create($validated);

            return response()->json([
                'message' => 'success',
                'status' => 201,
                'data' => $habitacion
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the habitacion.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateHabitacion(Request $request, $id)
    {
        try {
            // Validación sin permitir modificar el idHotel
            $validated = $request->validate([
                'numHabi' => 'required|integer',
                'tipoHabi' => 'required|string|max:255',
                'acomoda' => 'required|string|max:255'
            ]);
    
            // Buscar la habitación
            $habitacion = habitacion_info_tipo::find($id);
    
            if (!$habitacion) {
                return response()->json([
                    'message' => 'error',
                    'status' => 404,
                    'data' => []
                ], 404);
            }
    
            $idHotel = $habitacion->idHotel;
    
            // Verificar cantidad de habitaciones no supere la permitida por el hotel
            $hotel = Hotel::find($idHotel);
    
            if (!$hotel) {
                return response()->json([
                    'message' => 'El hotel asociado no existe.',
                    'status' => 404
                ], 404);
            }
            $numHabi = $request->input('numHabi');
            $totalHabitacionesAsignadas = habitacion_info_tipo::where('idHotel', $request->idHotel)->sum('numHabi');
            $totalHabitacionesDisponibles = $hotel->numHab - $totalHabitacionesAsignadas;
            if ( $numHabi > $totalHabitacionesDisponibles) {
                return response()->json([
                    'message' => 'El total de habitaciones excede la capacidad del hotel.',
                    'status' => 400
                ], 400);
            }
            if ( $totalHabitacionesDisponibles == 0) {
                return response()->json([
                    'message' => 'Ya no hay habitaciones disponibles para asignar.',
                    'status' => 400
                ], 400);
            }
          
    
            // Actualizar datos de la habitación
            $habitacion->update($validated);
    
            return response()->json([
                'message' => 'Habitación actualizada correctamente.',
                'status' => 200,
                'data' => $habitacion
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the habitacion.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
