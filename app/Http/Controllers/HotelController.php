<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;

class HotelController extends Controller
{
    /**
     * Consultar hoteles
     * @param null
     * @author Lorenzo Sanchez
     * @return JSON
     */
    public function get()
    {
        try {
            $hotels = Hotel::orderBy('id', 'asc')->get();

            if ($hotels->isEmpty()) {
                return response()->json([
                    'message' => 'error',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $hotels
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching hotels.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Insertar hoteles
     * @param  @nit, @nombre, ciudad, direccion, numHab   
     * @author Lorenzo Sanchez
     * @return JSON
     */
    public function create(Request $request)
    {
        try {
            // Validación
            $validated = $request->validate([
                'nit' => 'required|integer',  // Cambiado de string a integer
                'nombre' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'numHab' => 'required|integer'
            ]);

            // Validar existencia por NIT o nombre
            $existingNit = Hotel::where('nit', $validated['nit'])->exists();
            if ($existingNit) {
                return response()->json([
                    'error' => 'error',
                    'message' => 'Ya existe un hotel con ese NIT.',
                ], 409);
            }

            $existingNombre = Hotel::where('nombre', $validated['nombre'])->exists();
            if ($existingNombre) {
                return response()->json([
                    'error' => 'error',
                    'message' => 'Ya existe un hotel con ese nombre.',
                ], 409);
            }

            // Crear el hotel con los datos validados
            $hotel = Hotel::create($validated);

            return response()->json([
                'success' => 'success',
                'status' => 201,
                'data' => $hotel
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while inserting the hotel.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update hoteles
     * @param id, @nit, @nombre, ciudad, direccion, numHab   
     * @author Lorenzo Sanchez
     * @return JSON
     */

    public function update(Request $request, $id)
    {
        try {
            // Validación básica
            $validated = $request->validate([
                'nit' => 'required|integer',
                'nombre' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'numHab' => 'required|integer'
            ]);

            // Buscar el hotel por ID
            $hotel = Hotel::find($id);
            if (!$hotel) {
                return response()->json([
                    'message' => 'error',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            // Verificar si el NIT ya existe en otro hotel
            $existingNit = Hotel::where('nit', $validated['nit'])->where('id', '!=', $id)->exists();
            if ($existingNit) {
                return response()->json([
                    'message' => 'Ya existe otro hotel con ese NIT.',
                    'status' => 409
                ], 409);
            }

            // Verificar si el nombre ya existe en otro hotel
            $existingNombre = Hotel::where('nombre', $validated['nombre'])->where('id', '!=', $id)->exists();
            if ($existingNombre) {
                return response()->json([
                    'message' => 'Ya existe otro hotel con ese nombre.',
                    'status' => 409
                ], 409);
            }

            // Actualizar el hotel con los datos validados
            $hotel->update($validated);

            return response()->json([
                'message' => 'Hotel updated successfully.',
                'success' => 'success',
                'data' => $hotel
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the hotel.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * delete hotel
     * @param id   
     * @author Lorenzo Sanchez
     * @return JSON
     */

    public function delete($id)
    {
        try {
            // Buscar el hotel por ID
            $hotel = Hotel::find($id);
            if (!$hotel) {
                return response()->json([
                    'message' => 'error',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            // Eliminar el hotel
            $hotel->delete();

            return response()->json([
                'message' => 'Hotel deleted successfully.',
                'success' => 'success',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the hotel.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getById($id)
    {
        try {
            // Buscar el hotel por ID
            $hotel = Hotel::find($id);
            if (!$hotel) {
                return response()->json([
                    'message' => 'error',
                    'status' => 404,
                    'data' => []
                ], 404);
            }

            return response()->json([
                'message' => 'success',
                'status' => 200,
                'data' => $hotel
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the hotel.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
