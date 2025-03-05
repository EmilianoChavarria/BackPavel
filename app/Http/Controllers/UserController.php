<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function findOne($id)
    {
        $projects = DB::table('users')->where('id', $id)->get();
        return response()->json(['user' => $projects, 'status' => 200], 200);
    }

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

    public function updateUser(Request $request, $id)
    {
        try {
            // Verificar si el usuario existe
            $user = DB::table('users')->where('id', $id)->first();
            if (!$user) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }

            // Filtrar solo los datos que se enviaron en la solicitud
            $data = $request->only(['name', 'email', 'status', 'department_id', 'position_id', 'role_id']);

            // Si no hay datos para actualizar, devolver un mensaje
            if (empty($data)) {
                return response()->json(['message' => 'No se enviaron datos para actualizar'], 400);
            }

            // Actualizar el usuario
            DB::table('users')->where('id', $id)->update($data);

            return response()->json(['message' => 'Usuario actualizado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            // Verificar si el usuario existe
            $user = DB::table('users')->where('id', $id)->first();
            if (!$user) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }

            // Eliminar el usuario
            DB::table('users')->where('id', $id)->delete();

            return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }



}
