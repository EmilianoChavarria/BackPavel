<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    //

    public function getAll(){
        $actividades = DB::table('activities')->get();
        return response()->json(['actividades' => $actividades, 'status' => 200], 200);
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
                'category_id.required' => 'El id de la categoría es requerido.',
                'name.required' => 'El nombre de la actividad es requerido.',
                'description.required' => 'La descripción de la actividad es requerida.',
                'start_date.required' => 'La fecha de inicio es requerida.',
                'start_date.date' => 'La fecha de inicio no es válida.',
                'end_date.required' => 'La fecha de finalización es requerida.',
                'end_date.date' => 'La fecha de finalización no es válida.',
                'end_date.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
                'responsible_id.required' => 'El id del responsable es requerido.',
                'dependencies.required' => 'Las dependencias de la actividad son requeridas.',
                'deliverables.required' => 'Los entregables de la actividad son requeridos.',
            ]);

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
