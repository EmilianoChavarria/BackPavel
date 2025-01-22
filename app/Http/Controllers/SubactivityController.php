<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubactivityController extends Controller
{

    public function getAll()
    {
        $subactivities = DB::table('subactivities')->get();
        return response()->json(['subactivities' => $subactivities, 'status' => 200], 200);
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
