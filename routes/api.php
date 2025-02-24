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
    Route::get('findOneProject/{id}', [ProjectController::class, 'findOne']);

    // endpoints de categorias
    Route::get('getAllCategories', [CategoryController::class, 'getAll']);
    Route::get('getCategoriesByProject/{id}', [CategoryController::class, 'getCategoriesByProject']);
    Route::post('saveCategory', [CategoryController::class, 'saveCategory']);

    // endpoints de usuarios
    Route::get('getAllUsers', [UserController::class, 'getAll']);
    Route::post('saveUser', [UserController::class, 'saveUser']);

    // endpoints de actividades
    Route::get('getAllActivities', [ActivityController::class, 'getAll']);
    Route::post('saveActivity', [ActivityController::class, 'saveActivity']);
    Route::put('completeActivity/{id}', [ActivityController::class, 'completeActivity']);
    Route::delete('deleteActivity/{id}', [ActivityController::class, 'deleteActivity']);

    // endpoints de subactividades
    Route::get('getSubactivitiesByActivity/{id}', [SubactivityController::class, 'getByActivity']);
    Route::get('getAllSubactivities', [SubactivityController::class, 'getAll']);
    Route::post('saveSubactivity', [SubActivityController::class, 'saveSubActivity']);
    Route::put('completeSubactivity/{id}', [SubactivityController::class, 'completeSubactivity']);
    Route::delete('deleteSubactivity/{id}', [SubactivityController::class, 'deleteSubactivity']);

});
