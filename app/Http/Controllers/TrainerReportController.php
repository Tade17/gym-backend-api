<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainerReportController extends Controller
{
    // 1. Ver actividad reciente de TODOS mis alumnos
    public function recentActivity()
    {
        // Buscamos los últimos logs de los alumnos asignados a este trainer
        $logs = WorkoutLog::whereHas('assignedRoutine', function($query) {
            $query->whereHas('user', function($q) {
                $q->where('assigned_trainer_id', Auth::id());
            });
        })
        ->with(['assignedRoutine.user', 'assignedRoutine.routine'])
        ->orderBy('workout_date', 'desc')
        ->take(20) // Últimos 20 movimientos
        ->get();

        return response()->json($logs);
    }

    // 2. Progreso detallado de un alumno específico
    public function studentProgress($studentId)
    {
        // Seguridad: Verificar que el alumno sea mío
        $student = User::where('id', $studentId)
                       ->where('assigned_trainer_id', Auth::id())
                       ->first();

        if (!$student) {
            return response()->json(['message' => 'Alumno no encontrado o no está a tu cargo'], 404);
        }

        // Obtener todos sus logs ordenados por fecha
        $progress = WorkoutLog::whereHas('assignedRoutine', function($query) use ($studentId) {
            $query->where('user_id', $studentId);
        })
        ->orderBy('workout_date', 'asc')
        ->get();

        return response()->json([
            'student' => $student->first_name . ' ' . $student->last_name,
            'logs' => $progress
        ]);
    }
}