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

            DB::beginTransaction();

            // Insertar la nueva subactividad
            DB::table('subactivities')->insert([
                'activity_id' => $request->activity_id,
                'name' => $request->name,
                'comment' => $request->comment,
                'status' => 'no completada',
            ]);

            // Actualizar el porcentaje de la actividad
            $this->updateActivityPercentage($request->activity_id);

            DB::commit();

            return response()->json(['message' => 'Subactividad creada correctamente', 'status' => 200], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la subactividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function completeSubactivity(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:completada,no completada',
            ], [
                'status.required' => 'El estado de la subactividad es requerido.',
                'status.in' => 'El estado debe ser "completada" o "no completada".',
            ]);

            DB::beginTransaction();

            // Obtener la subactividad para saber a qué actividad pertenece
            $subactivity = DB::table('subactivities')->where('id', $id)->first();
            $activity_id = $subactivity->activity_id;

            // Actualizar el estado de la subactividad
            DB::table('subactivities')->where('id', $id)->update([
                'status' => $request->status,
            ]);

            // Actualizar el porcentaje de la actividad
            $this->updateActivityPercentage($activity_id);

            DB::commit();

            return response()->json([
                'message' => 'Subactividad actualizada correctamente',
                'status' => 200,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la subactividad',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function updateActivityPercentage($activity_id)
    {
        // Obtener todas las subactividades de la actividad
        $subactivities = DB::table('subactivities')
            ->where('activity_id', $activity_id)
            ->get();

        if ($subactivities->isEmpty()) {
            $newPercentage = 0;
        } else {
            // Contar subactividades completadas
            $completed = $subactivities->where('status', 'completada')->count();
            $total = $subactivities->count();

            // Calcular porcentaje (redondeado a 2 decimales)
            $newPercentage = round(($completed / $total) * 100, 2);
        }

        // Actualizar el porcentaje de la actividad
        DB::table('activities')
            ->where('id', $activity_id)
            ->update([
                'completion_percentage' => $newPercentage,
                'status' => $this->determineActivityStatus($newPercentage)
            ]);

        // Obtener la categoría para actualizar el proyecto
        $activity = DB::table('activities')->where('id', $activity_id)->first();
        $category = DB::table('categories')->where('id', $activity->category_id)->first();

        if ($category) {
            $this->updateProjectPercentage($category->project_id);
        }
    }

    private function determineActivityStatus($percentage)
    {
        if ($percentage == 0) {
            return 'no empezado';
        } elseif ($percentage == 100) {
            return 'finalizado';
        } else {
            return 'en proceso';
        }
    }

    private function updateProjectPercentage($project_id)
    {
        // Obtener todas las actividades del proyecto
        $activities = DB::table('activities')
            ->join('categories', 'activities.category_id', '=', 'categories.id')
            ->where('categories.project_id', $project_id)
            ->select('activities.completion_percentage')
            ->get();

        if ($activities->isEmpty()) {
            $averagePercentage = 0;
        } else {
            // Calcular el promedio de los porcentajes
            $total = $activities->sum('completion_percentage');
            $averagePercentage = round($total / $activities->count(), 2);
        }

        // Actualizar el proyecto
        DB::table('projects')
            ->where('id', $project_id)
            ->update(['completion_percentage' => $averagePercentage]);
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
