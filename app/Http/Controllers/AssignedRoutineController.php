<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Routine;
use App\Models\User;
use App\Models\Plan;

class AssignedRoutineController extends Controller
{
    // 1. Ver mis alumnos (Solo los que tiene asignados un entrenador)
    public function myStudents()
    {
        $trainerId = Auth::id();
        $students = User::where('assigned_trainer_id', $trainerId)
            ->where('role', 'client')
            ->get();

        return response()->json($students);
    }

    //listar planes creados por el entrenador logueado
    public function myPlans()
    {
        $trainerId = Auth::id();
        $plans = Plan::where('trainer_id', $trainerId)->get();

        return response()->json($plans);
    }
    //Listar rutinas creadas por el entrenador logueado
    public function myRoutines()
    {
        $trainerId = Auth::id();
        $routines = Routine::where('trainer_id', $trainerId)
            ->with('exercises')
            ->get();

        return response()->json($routines);
    }

}
