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
