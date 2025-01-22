<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function getAll()
    {
        $users = DB::table('users')->get();
        return response()->json(['users' => $users, 'status' => 200], 200);
    }
    

    public function saveUser(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'department_id' => 'required',
                'position_id' => 'required',
                'role_id' => 'required',
                
            ], [
                'name.required' => 'El nombre del usuario es requerido.',
                'email.required' => 'El email del usuario es requerido.',
                'department_id.required' => 'El id del departamento es requerido.',
                'position_id.required' => 'El id del cargo es requerido.',
                'role_id.required' => 'El id del rol es requerido.',
            ]);

            DB::table('users')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'status' => 'activo',
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'role_id' => $request->role_id
            ]);

            return response()->json(['message' => 'Usuario creado correctamente', 'status' => 200], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error en la validación de datos',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
