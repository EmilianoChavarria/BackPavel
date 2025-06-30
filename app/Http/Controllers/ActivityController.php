<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    //

    public function getAll()
    {
        $actividades = DB::table('activities')->get();
        return response()->json(['actividades' => $actividades, 'status' => 200], 200);
    }

    public function findOneActivity($id)
    {
        $activity = DB::table('activities')
            ->leftJoin('users', 'activities.responsible_id', '=', 'users.id')
            ->where('activities.id', $id)
            ->select('activities.*', 'users.name as responsible_name')
            ->first();

        return response()->json([
            'activity' => $activity,
            'status' => 200
        ], 200);
    }

    public function saveActivity(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required',
                'name' => 'required',
                'description' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'responsible_id' => 'required',
                'dependencies' => 'required',
                'deliverables' => 'required',
            ], [
                // ... (tus mensajes de validación actuales)
            ]);

            DB::beginTransaction();

            // Insertar la nueva actividad
            DB::table('activities')->insert([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'no empezado',
                'responsible_id' => $request->responsible_id,
                'dependencies' => $request->dependencies,
                'deliverables' => $request->deliverables,
                'completion_percentage' => 0.00,
            ]);

            // Obtener el project_id a través de la categoría
            $category = DB::table('categories')->where('id', $request->category_id)->first();
            $project_id = $category->project_id;

            // Calcular el nuevo porcentaje del proyecto
            $projectPercentage = $this->calculateProjectPercentage($project_id);

            // Actualizar el porcentaje del proyecto
            DB::table('projects')
                ->where('id', $project_id)
                ->update(['completion_percentage' => $projectPercentage]);

            DB::commit();

            return response()->json(['message' => 'Actividad creada correctamente', 'status' => 200], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateActivity(Request $request, $id)
    {
        try {
            $request->validate([
                'category_id' => 'required',
                'name' => 'required',
                'description' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'responsible_id' => 'required',
                'dependencies' => 'required',
                'deliverables' => 'required',
            ], [
                'category_id.required' => 'La categoría es requerida.',
                'name.required' => 'El nombre es requerido.',
                'description.required' => 'La descripción es requerida.',
                'start_date.required' => 'La fecha de inicio es requerida.',
                'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
                'end_date.required' => 'La fecha de fin es requerida.',
                'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
                'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
                'responsible_id.required' => 'El responsable es requerido.',
                'dependencies.required' => 'Las dependencias son requeridas.',
                'deliverables.required' => 'Los entregables son requeridos.',
            ]);

            DB::beginTransaction();

            // Actualizar la actividad
            DB::table('activities')
                ->where('id', $id)
                ->update([
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'description' => $request->description,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'responsible_id' => $request->responsible_id,
                    'dependencies' => $request->dependencies,
                    'deliverables' => $request->deliverables
                ]);

            DB::commit();

            return response()->json([
                'message' => 'Actividad actualizada correctamente',
                'status' => 200
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al actualizar la actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function calculateProjectPercentage($project_id)
    {
        // Obtener todas las actividades del proyecto a través de sus categorías
        $activities = DB::table('activities')
            ->join('categories', 'activities.category_id', '=', 'categories.id')
            ->where('categories.project_id', $project_id)
            ->select('activities.completion_percentage')
            ->get();

        if ($activities->isEmpty()) {
            return 0;
        }

        // Calcular el promedio de los porcentajes de todas las actividades
        $totalPercentage = $activities->sum('completion_percentage');
        $averagePercentage = $totalPercentage / $activities->count();

        return round($averagePercentage, 2);
    }

    public function saveMessage(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required',
                'activity_id' => 'required',
            ], [

                'content.required' => 'El mensaje es requerido.',
                'activity_id.required' => 'El activity_id es requerido.',

            ]);

            DB::table('messages_activities')->insert([
                'content' => $request->content,
                'activity_id' => $request->activity_id
            ]);

            return response()->json(['message' => 'Mensaje enviado correctamente', 'status' => 200], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al enviar el mensaje',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteActivity($id)
    {
        try {
            DB::table('activities')->where('id', $id)->delete();
            return response()->json(['message' => 'Actividad eliminada correctamente', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
