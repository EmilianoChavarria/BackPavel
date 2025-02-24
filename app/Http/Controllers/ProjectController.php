<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    //
    public function getAll()
    {
        $projects = DB::table('projects')->get();
        return response()->json(['projects' => $projects, 'status' => 200], 200);

    }

    public function findOne($id)
    {
        $projects = DB::table('projects')->where('id', $id)->get();
        return response()->json(['projectInfo' => $projects, 'status' => 200], 200);

    }

    public function saveProject(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ], [
                'name.required' => 'El nombre del proyecto es requerido.',
                'description.required' => 'La descripción del proyecto es requerida.',
                'start_date.required' => 'La fecha de inicio es requerida.',
                'start_date.date' => 'La fecha de inicio no es válida.',
                'end_date.required' => 'La fecha de finalización es requerida.',
                'end_date.date' => 'La fecha de finalización no es válida.',
                'end_date.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
            ]);

            DB::table('projects')->insert([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'completion_percentage' => 0.00,
            ]);

            return response()->json(['message' => 'Proyecto creado correctamente', 'status' => 200], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProject(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
            ], [
                'name.required' => 'El nombre del proyecto es requerido.',
                'description.required' => 'La descripción del proyecto es requerida.',
                'start_date.required' => 'La fecha de inicio es requerida.',
                'start_date.date' => 'La fecha de inicio no es válida.',
                'end_date.required' => 'La fecha de finalización es requerida.',
                'end_date.date' => 'La fecha de finalización no es válida.',
                'end_date.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
            ]);

            // Actualizar el proyecto en la base de datos
            $updated = DB::table('projects')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'description' => $request->description,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);

            // Verificar si se actualizó correctamente
            if ($updated) {
                return response()->json(['message' => 'Proyecto actualizado correctamente', 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'No se encontró el proyecto a actualizar', 'status' => 404], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteProject($id)
{
    try {
        // Eliminar el proyecto de la base de datos
        $deleted = DB::table('projects')
            ->where('id', $id)
            ->delete();

        // Verificar si se eliminó correctamente
        if ($deleted) {
            return response()->json(['message' => 'Proyecto eliminado correctamente', 'status' => 200], 200);
        } else {
            return response()->json(['message' => 'No se encontró el proyecto a eliminar', 'status' => 404], 404);
        }
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al eliminar el proyecto',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function getProjectDetails($id)
    {
        // Obtener el proyecto con sus relaciones
        $project = DB::table('projects')
            ->where('projects.id', $id)
            ->select('projects.*')
            ->first();

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        // Obtener categorías relacionadas
        $categories = DB::table('categories')
            ->where('categories.project_id', $id)
            ->select('categories.*')
            ->get()
            ->map(function ($category) {
                // Obtener actividades relacionadas con la categoría
                $activities = DB::table('activities')
                    ->where('activities.category_id', $category->id)
                    ->select('activities.*', 'users.name as responsible_name')
                    ->leftJoin('users', 'activities.responsible_id', '=', 'users.id')
                    ->get();

                $category->activities = $activities;
                return $category;
            });

        // Agregar categorías al proyecto
        $project->categories = $categories;

        // Retornar la respuesta en formato JSON
        return response()->json($project);
    }

}
