<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function getAll()
    {
        try {
            $users = DB::table('user_details')->get();

            return response()->json([
                'users' => $users,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los detalles de los usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRoles()
    {
        try {
            $users = DB::table('roles')->get();

            return response()->json([
                'roles' => $users,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los detalles de los roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDepartments()
    {
        try {
            $users = DB::table('departments')->get();

            return response()->json([
                'departments' => $users,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los detalles de los departamentos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getPositions()
    {
        try {
            $users = DB::table('positions')->get();

            return response()->json([
                'positions' => $users,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los detalles de las posiciones',
                'error' => $e->getMessage()
            ], 500);
        }
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
