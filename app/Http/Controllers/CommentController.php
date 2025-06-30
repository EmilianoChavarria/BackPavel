<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{

    public function getByActivity($activity_id)
    {
        try {
            $comments = DB::table('messages_activities')
                ->where('activity_id', $activity_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'comments' => $comments,
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve comments',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
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

    public function saveMessage(Request $request)
    {
        try {
            $request->validate([
                'content' => 'required',
                'activity_id' => 'required',

            ], [
                'content.required' => 'El comentario es requerido.',
                'activity_id.required' => 'La id de la actividad es requerida.',
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
                'message' => 'Error al crear el mensaje',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}