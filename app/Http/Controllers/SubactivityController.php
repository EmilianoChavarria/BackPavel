<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubactivityController extends Controller
{

    public function getAll()
    {
        $subactivities = DB::table('subactivities')->get();
        return response()->json(['subactivities' => $subactivities, 'status' => 200], 200);
    }

    public function getByActivity($id)
    {
        try {
            $data = DB::table('subactivities')->where('activity_id', $id)->get();
            return response()->json(['subactivities' => $data, 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveSubactivity(Request $request)
    {
        try {
            $request->validate([
                'activity_id' => 'required',
                'name' => 'required',

            ], [
                'activity_id.required' => 'El id de la actividad es requerido.',
                'name.required' => 'El nombre de la subactividad es requerido.',
            ]);

            DB::table('subactivities')->insert([
                'activity_id' => $request->activity_id,
                'name' => $request->name,
                'comment' => $request->comment,
                'status' => 'no completada',
            ]);

            return response()->json(['message' => 'Actividad creada correctamente', 'status' => 200], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function completeSubactivity(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'status' => 'required|in:completada,no completada',
            ], [
                'status.required' => 'El estado de la subactividad es requerido.',
                'status.in' => 'El estado debe ser "completada" o "no completada".',
            ]);

            // Actualizar el estado de la subactividad
            DB::table('subactivities')->where('id', $id)->update([
                'status' => $request->status,
            ]);

            return response()->json([
                'message' => 'Subactividad actualizada correctamente',
                'status' => 200,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la subactividad',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateSubactivity(Request $request, $id)
    {
        try {

            // var_dump($request);
            // Log de request
            Log::error('Error actualizando subactividad: ' . $request);

            // Validación
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'comment' => 'sometimes|nullable|string',
                'status' => 'sometimes|required|in:completada,no completada',
            ], [
                'name.required' => 'El nombre es requerido cuando se proporciona.',
                'status.in' => 'El estado debe ser "completada" o "no completada".',
            ]);

            // Verificar si hay datos para actualizar
            if (empty($validated)) {
                return response()->json([
                    'message' => 'No se proporcionaron datos válidos para actualizar',
                    'status' => 400,
                ], 400);
            }

            // Verificar si la subactividad existe
            $exists = DB::table('subactivities')->where('id', $id)->exists();
            if (!$exists) {
                return response()->json([
                    'message' => 'Subactividad no encontrada',
                    'status' => 404,
                ], 404);
            }

            // Actualización
            $affected = DB::table('subactivities')
                ->where('id', $id)
                ->update($validated);

            return response()->json([
                'message' => 'Subactividad actualizada correctamente',
                'affected_rows' => $affected,
                'status' => 200,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
                'status' => 422,
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error actualizando subactividad: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error interno del servidor',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Contacte al administrador',
                'status' => 500,
            ], 500);
        }
    }

    public function deleteSubactivity($id)
    {
        try {
            // Eliminar la subactividad
            DB::table('subactivities')->where('id', $id)->delete();

            return response()->json([
                'message' => 'Subactividad eliminada correctamente',
                'status' => 200,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la subactividad',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
