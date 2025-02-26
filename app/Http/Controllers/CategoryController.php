<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{

    public function getAll()
    {
        $categories = DB::table('categories')->get();
        return response()->json(['categories' => $categories, 'status' => 200], 200);
    }

    public function findOne($id)
    {
        $projects = DB::table('categories')->where('id', $id)->get();
        return response()->json(['categoryInfo' => $projects, 'status' => 200], 200);

    }

    public function updateCategory(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'name' => 'required',
                'description' => 'required'
            ], [
                'name.required' => 'El nombre de la categoria es requerido.',
                'description.required' => 'La descripción de la categoria es requerida.',
            ]);

            // Actualizar el proyecto en la base de datos
            $updated = DB::table('categories')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'description' => $request->description,
                ]);

            // Verificar si se actualizó correctamente
            if ($updated) {
                return response()->json(['message' => 'Categoria actualizado correctamente', 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'No se encontró la categoría a actualizar', 'status' => 404], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteCategory($id)
    {
        try {
            // Eliminar el proyecto de la base de datos
            $deleted = DB::table('categories')
                ->where('id', $id)
                ->delete();

            // Verificar si se eliminó correctamente
            if ($deleted) {
                return response()->json(['message' => 'Categoría eliminada correctamente', 'status' => 200], 200);
            } else {
                return response()->json(['message' => 'No se encontró la categoría a eliminar', 'status' => 404], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getCategoriesByProject($id)
    {
        $categories = DB::table('categories')->where('project_id', $id)->get();
        return response()->json(['categories' => $categories, 'status' => 200], 200);
    }

    public function saveCategory(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'project_id' => 'required',

            ], [
                'name.required' => 'El nombre de la categoría es requerido.',
                'description.required' => 'La descripción de la categoría es requerida.',
                'project_id.required' => 'La id del proyecto es requerido.',
            ]);

            DB::table('categories')->insert([
                'name' => $request->name,
                'description' => $request->description,
                'project_id' => $request->project_id
            ]);

            return response()->json(['message' => 'Categoría creada correctamente', 'status' => 200], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la categoría',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}