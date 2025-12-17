<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\RoutineExerciseController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AssignedRoutineController;

// 1. Rutas Públicas
//Para el registro y login
Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


// Rutas protegidas de autenticacion
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




//Rutas para planes de entrenamiento
// Rutas públicas 
Route::get('/plans', [PlanController::class, 'index']);
Route::get('/plans/{id}', [PlanController::class, 'showById']);

// Rutas públicas de Ejercicios
Route::get('/exercises', [ExerciseController::class, 'index']);
Route::get('/exercises/{id}', [ExerciseController::class, 'show']);

//Rutas publicas dfe rutinas
Route::get('/routines', [RoutineController::class, 'index']);
Route::get('/routines/{id}', [RoutineController::class, 'show']);



// Rutas Protegidas (Necesitas Token para Crear/Editar/Borrar)
Route::middleware('auth:sanctum')->group(function () {
    // PLANES
    Route::post('/plans', [PlanController::class, 'store']);
    Route::put('/plans/{id}', [PlanController::class, 'update']);
    Route::delete('/plans/{id}', [PlanController::class, 'destroy']);

    // EJERCICIOS
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::put('/exercises/{id}', [ExerciseController::class, 'update']);
    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy']);

    // RUTINAS
    Route::post('/routines', [RoutineController::class, 'store']);
    Route::put('/routines/{id}', [RoutineController::class, 'update']);
    Route::delete('/routines/{id}', [RoutineController::class, 'destroy']);

    // RUTINA-EJERCICIOS (Pivot)
    // Agregar ejercicio a una rutina específica
    Route::post('/routines/{id}/exercises', [RoutineExerciseController::class, 'store']);
    
    // Quitar un ejercicio de una rutina (por el ID de la asignación)
    Route::delete('/routine-exercises/{id}', [RoutineExerciseController::class, 'destroy']);


    // SUSCRIPCIONES
    Route::post('/subscriptions', [SubscriptionController::class, 'store']); // Crear
    Route::get('/users/{id}/subscriptions', [SubscriptionController::class, 'getUserSubscriptions']); // Ver historial
    Route::put('/subscriptions/{id}/cancel', [SubscriptionController::class, 'cancel']); // Cancelar


    // --- AGENDA DE ENTRENAMIENTO ---
    Route::post('/schedule', [AssignedRoutineController::class, 'store']); // Agendar
    Route::get('/schedule', [AssignedRoutineController::class, 'index']);  // Ver agenda
    Route::put('/schedule/{id}/complete', [AssignedRoutineController::class, 'complete']); // Marcar listo

});
