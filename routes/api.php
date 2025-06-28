<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SubactivityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware;

Route::middleware([CorsMiddleware::class])->group(function () {

    // endpoints de proyectos
    Route::get('getAll', [ProjectController::class, 'getAll']);
    Route::post('saveProject', [ProjectController::class, 'saveProject']);
    Route::put('updateProject/{id}', [ProjectController::class, 'updateProject']);
    Route::delete('deleteProject/{id}', [ProjectController::class, 'deleteProject']);
    Route::get('projects/{id}/details', [ProjectController::class, 'getProjectDetails']);
    Route::get('findOneProject/{id}', action: [ProjectController::class, 'findOne']);

    // endpoints de categorias
    Route::get('getAllCategories', [CategoryController::class, 'getAll']);
    Route::get('getCategoriesByProject/{id}', [CategoryController::class, 'getCategoriesByProject']);
    Route::post('saveCategory', [CategoryController::class, 'saveCategory']);
    Route::get('findOneCategory/{id}', action: [CategoryController::class, 'findOne']);
    Route::put('updateCategory/{id}', [CategoryController::class, 'updateCategory']);
    Route::delete('deleteCategory/{id}', [CategoryController::class, 'deleteCategory']);


    // endpoints de usuarios
    Route::get('getAllUsers', [UserController::class, 'getAll']);
    Route::get('getRoles', [UserController::class, 'getRoles']);
    Route::get('getDepartments', [UserController::class, 'getDepartments']);
    Route::get('getPositions', [UserController::class, 'getPositions']);
    Route::get('findOneUser/{id}', action: [UserController::class, 'findOne']);
    Route::post('saveUser', [UserController::class, 'saveUser']);
    Route::post('saveDepartment', [UserController::class, 'saveDepartment']);
    Route::post('savePosition', [UserController::class, 'savePosition']);
    Route::put('updateUser/{id}', [UserController::class, 'updateUser']);
    Route::delete('deleteUser/{id}', [UserController::class, 'deleteUser']);


    // endpoints de actividades
    Route::get('getAllActivities', [ActivityController::class, 'getAll']);
    Route::get('findOneActivity/{id}', [ActivityController::class, 'findOneActivity']);
    Route::post('saveActivity', [ActivityController::class, 'saveActivity']);
    Route::post('sendMessage', [ActivityController::class, 'saveMessage']);
    Route::put('completeActivity/{id}', [ActivityController::class, 'completeActivity']);
    Route::delete('deleteActivity/{id}', [ActivityController::class, 'deleteActivity']);

    // endpoints de subactividades
    Route::get('getSubactivitiesByActivity/{id}', [SubactivityController::class, 'getByActivity']);
    Route::get('getAllSubactivities', [SubactivityController::class, 'getAll']);
    Route::post('saveSubactivity', [SubActivityController::class, 'saveSubActivity']);
    Route::put('completeSubactivity/{id}', [SubactivityController::class, 'completeSubactivity']);
    Route::put('updateSubactivity/{id}', [SubactivityController::class, 'updateSubactivity']);
    Route::delete('deleteSubactivity/{id}', [SubactivityController::class, 'deleteSubactivity']);

});
