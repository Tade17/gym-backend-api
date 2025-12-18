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
use App\Http\Controllers\DietPlanController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\AssignedDietController;

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
    Route::post('/subscribe', [SubscriptionController::class, 'store']);
    Route::get('/my-subscription', [SubscriptionController::class, 'mySubscription']);

    // --- AGENDA DE ENTRENAMIENTO ---
    Route::post('/schedule', [AssignedRoutineController::class, 'store']); // Agendar
    Route::get('/schedule', [AssignedRoutineController::class, 'index']);  // Ver agenda
    Route::put('/schedule/{id}/complete', [AssignedRoutineController::class, 'complete']); // Marcar listo


    // --- DIETAS ---
    Route::get('/diets', [DietPlanController::class, 'index']);      // Ver mis dietas
    Route::post('/diets', [DietPlanController::class, 'store']);     // Crear dieta
    Route::delete('/diets/{id}', [DietPlanController::class, 'destroy']); // Borrar

    // --- COMIDAS (Dentro de dietas) ---
    Route::get('/diets/{id}/meals', [MealController::class, 'index']); // Ver comidas de la dieta X
    Route::post('/diets/{id}/meals', [MealController::class, 'store']); // Agregar comida a la dieta X

    // -- ASIGNACIÓN DE DIETAS ---
    Route::post('/assigned-diets', [AssignedDietController::class, 'store']); // Asignar
    Route::get('/users/{id}/diet', [AssignedDietController::class, 'showUserDiet']); // Ver dieta del alumno
});
