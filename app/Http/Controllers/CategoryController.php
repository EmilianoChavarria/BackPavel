<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{

    public function getAll(){
        $categories = DB::table('categories')->get();
        return response()->json(['categories' => $categories, 'status' => 200], 200);
    }

    public function findOne($id)
    {
        $projects = DB::table('categories')->where('id', $id)->get();
        return response()->json(['categoryInfo' => $projects, 'status' => 200], 200);

    }

    public function getCategoriesByProject($id){
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