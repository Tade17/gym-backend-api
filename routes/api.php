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
use App\Http\Controllers\DietPlanMealController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TrainerReportController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\StudentProgressController;
use App\Http\Controllers\TrainerDashboardController;


// 1. Rutas Públicas
//Para el registro y login
Route::prefix('/auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
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




// RUTAS PÚBLICAS 

// Rutas públicas de ejercicios para insertar en distintas rutinas
Route::get('/exercises', [ExerciseController::class, 'index']);
Route::get('/exercises/{id}', [ExerciseController::class, 'show']);

//Rutas públicas para meals->comidas para insertar en distintas dietas
Route::get('/meals', [MealController::class, 'index']);
Route::get('/meals/{id}', [MealController::class, 'show']);
Route::post('/meals', [MealController::class, 'store']);
Route::put('/meals/{id}', [MealController::class, 'update']);
Route::delete('/meals/{id}', [MealController::class, 'destroy']);




// Rutas Protegidas (Necesitas Token para Crear/Editar/Borrar)
Route::middleware('auth:sanctum')->group(function () {

    // SUSCRIPCIONES
    Route::post('/subscribe', [SubscriptionController::class, 'store']);
    Route::get('/my-subscription', [SubscriptionController::class, 'mySubscription']);

    // PLANES
    Route::get('/plans', [PlanController::class, 'index']);
    Route::post('/plans', [PlanController::class, 'store']);
    Route::get('/plans/{id}', [PlanController::class, 'show']);
    Route::put('/plans/{id}', [PlanController::class, 'update']);
    Route::delete('/plans/{id}', [PlanController::class, 'destroy']);

    // EJERCICIOS
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::put('/exercises/{id}', [ExerciseController::class, 'update']);
    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy']);

    // RUTINAS
    Route::get('/routines', [RoutineController::class, 'index']);
    Route::post('/routines', [RoutineController::class, 'store']);
    Route::put('/routines/{id}', [RoutineController::class, 'update']);
    Route::delete('/routines/{id}', [RoutineController::class, 'destroy']);


    // --- DIETAS ---
    Route::get('/diet-plans', [DietPlanController::class, 'index']);
    Route::get('/diet-plans/{id}', [DietPlanController::class, 'show']);
    Route::post('/diet-plans', [DietPlanController::class, 'store']);
    Route::put('/diet-plans/{id}', [DietPlanController::class, 'update']);
    Route::delete('/diet-plans/{id}', [DietPlanController::class, 'destroy']);

    // ROUTINE-EXERCISE (Pivot)
    // Agregar ejercicio a una rutina específica
    Route::post('/routines/{routineId}/exercises', [RoutineExerciseController::class, 'store']);
    // Quitar un ejercicio de una rutina específica
    Route::delete('/routines/{routineId}/exercises/{exerciseId}', [RoutineExerciseController::class, 'destroy']);


    // Diet-plan-meal (Pivot)
    // Agregar comida a una dieta especifica
    Route::post('/diet-plans/{dietPlanId}/meals', [DietPlanMealController::class, 'store']);
    //Quitar comida de una comida en especifica
    Route::delete('/diet-plans/{dietPlanId}/meals/{mealId}', [DietPlanMealController::class, 'destroy']);




    // --- AGENDA DE ENTRENAMIENTO ---
    Route::post('/schedule', [AssignedRoutineController::class, 'store']); // Agendar
    Route::get('/schedule', [AssignedRoutineController::class, 'index']);  // Ver agenda
    Route::put('/schedule/{id}/complete', [AssignedRoutineController::class, 'complete']); // Marcar listo

    // Gestión del Entrenador
    Route::get('/trainer/my-students', [AssignedRoutineController::class, 'myStudents']);
    Route::get('/trainer/my-plans', [AssignedRoutineController::class, 'myPlans']);
    Route::get('/trainer/my-routines', [AssignedRoutineController::class, 'myRoutines']);

    // --- ASIGNACIONES MASIVAS ---
    Route::post('/assignments/mass-routine', [AssignmentController::class, 'massAssignRoutine']);
    Route::post('/assignments/mass-diet', [AssignmentController::class, 'massAssignDiet']);

    // --- ASIGNACIONES INDIVIDUALES ---
    Route::post('/assignments/individual-routine', [AssignmentController::class, 'assignRoutineToUser']);
    Route::post('/assignments/individual-diet', [AssignmentController::class, 'assignDietToUser']);

    // --- RUTAS DEL ALUMNO ---
    Route::get('/my-daily-routine', [ClientController::class, 'todayRoutine']);
    Route::post('/log-workout', [ClientController::class, 'logWorkout']);

    // --- REPORTES PARA EL ENTRENADOR ---
    Route::get('/trainer/recent-activity', [TrainerReportController::class, 'recentActivity']);
    Route::get('/trainer/student-progress/{studentId}', [TrainerReportController::class, 'studentProgress']);
});




// Rutas para el Alumno (Móvil - Kotlin)
Route::middleware('auth:sanctum')->prefix('student')->group(function () {
    Route::post('/log-exercise', [StudentProgressController::class, 'logExercise']);
    Route::post('/complete-routine/{id}', [StudentProgressController::class, 'completeRoutine']);
});

// Rutas para el Entrenador (Web - React)
Route::middleware('auth:sanctum')->prefix('trainer')->group(function () {
    Route::get('/dashboard/compliance', [TrainerDashboardController::class, 'dailyCompliance']);
    Route::get('/dashboard/alerts', [TrainerDashboardController::class, 'inactivityAlerts']);
});
