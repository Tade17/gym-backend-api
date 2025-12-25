<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AssignedRoutine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TrainerDashboardController extends Controller
{
    public function getDailyStats()
    {
        $trainerId = Auth::id();
        $today = Carbon::today();

        // Alumnos que completaron su rutina hoy
        $completedToday = AssignedRoutine::where('trainer_id', $trainerId)
            ->where('assigned_date', $today)
            ->where('status', 1)
            ->with('user')
            ->get();

        // Alumnos que NO han entrenado hoy
        $pendingToday = AssignedRoutine::where('trainer_id', $trainerId)
            ->where('assigned_date', $today)
            ->where('status', 0)
            ->with('user')
            ->get();

        return response()->json([
            'completadas' => $completedToday,
            'pendientes' => $pendingToday,
            'total_completadas' => $completedToday->count()
        ]);
    }

    public function getAbsentees()
    {
        $trainerId = Auth::id();
        // Lógica simple: Alumnos que tienen rutinas pendientes de hace más de 3 días
        $threeDaysAgo = Carbon::now()->subDays(3);

        $absentees = AssignedRoutine::where('trainer_id', $trainerId)
            ->where('status', 0)
            ->where('assigned_date', '<=', $threeDaysAgo)
            ->with('user')
            ->get()
            ->unique('user_id');

        return response()->json($absentees);
    }
}
