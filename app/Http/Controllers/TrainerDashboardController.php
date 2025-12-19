<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AssignedRoutine;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TrainerDashboardController extends Controller
{
    /**
     * RF-13: Quiénes cumplieron hoy y quiénes faltaron
     */
    public function dailyCompliance()
    {
        $today = Carbon::today()->format('Y-m-d');
        $trainerId = Auth::id();

        // Alumnos que tenían rutina hoy y su estado
        $compliance = AssignedRoutine::with('user:id,first_name,last_name')
            ->where('trainer_id', $trainerId)
            ->where('assigned_date', $today)
            ->get();

        return response()->json([
            'date' => $today,
            'total_assigned' => $compliance->count(),
            'completed' => $compliance->where('status', 1)->count(),
            'pending' => $compliance->where('status', 0)->count(),
            'details' => $compliance
        ]);
    }

    /**
     * RF-13 y RF-21: Alerta de alumnos que faltaron 3 días seguidos
     */
    public function inactivityAlerts()
    {
        $trainerId = Auth::id();
        $threeDaysAgo = Carbon::today()->subDays(3)->format('Y-m-d');

        // Buscamos alumnos del trainer que tengan rutinas sin completar en los últimos 3 días
        $inactiveStudents = User::where('assigned_trainer_id', $trainerId)
            ->whereHas('assignedRoutines', function ($query) use ($threeDaysAgo) {
                $query->where('assigned_date', '>=', $threeDaysAgo)
                    ->where('status', 0); // Pendientes
            })
            ->withCount(['assignedRoutines' => function ($query) use ($threeDaysAgo) {
                $query->where('assigned_date', '>=', $threeDaysAgo)
                    ->where('status', 0);
            }])
            ->get()
            ->filter(function ($user) {
                return $user->assigned_routines_count >= 3; // Faltaron 3 sesiones
            });

        return response()->json([
            'alert' => 'Usuarios que requieren notificación PUSH (RF-21)',
            'students' => $inactiveStudents->values()
        ]);
    }
}
