<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plan;            // <--- IMPORTANTE
use App\Models\Subscription;    // <--- IMPORTANTE
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <--- PARA LA TRANSACCIÓN
use Carbon\Carbon;              // <--- PARA LAS FECHAS
use Illuminate\Support\Facades\Storage; // <--- AGREGA ESTO ARRIBA

class AuthController extends Controller
{
    public function register(Request $request)
{
    // 1. VALIDACIÓN
    $request->validate([
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'email' => 'required|string|email|unique:users,email',
        'password' => 'required|string|min:6',
        'phone_number' => 'nullable|string|max:20',
        'gender' => 'required|in:M,F', 
        'role' => 'required|in:admin,trainer,client',
        
        // Nullables para el entrenador
        'weight' => 'nullable|numeric', 
        'height' => 'nullable|numeric',
        'birth_date' => 'required|date',
        'goals' => 'nullable|string',
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        
        // Nullable porque el entrenador no envía plan
        'plan_id' => 'nullable|exists:plans,id' 
    ]);

    // 2. INICIO DE LA TRANSACCIÓN (Ahora envuelve TODO)
    return DB::transaction(function () use ($request) {

        // A. Detectar si quien registra es un entrenador (Logueado previamente)
        $trainerId = null;
        if (Auth::guard('sanctum')->check()) {
            $currentUser = Auth::guard('sanctum')->user();
            if ($currentUser->role === 'trainer') {
                $trainerId = $currentUser->id;
            }
        }

        // B. Procesar la Foto de Perfil
        $photoPath = 'default.png'; 
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        // C. Crear el Usuario (Entrenador o Alumno)
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'weight' => $request->weight,
            'height' => $request->height,
            'birth_date' => $request->birth_date,
            'goals' => $request->goals,
            'profile_photo' => $photoPath, 
            'assigned_trainer_id' => $trainerId 
        ]);

        // --------------------------------------------------------------------------
        // D. LOGICA CONDICIONAL DE SUSCRIPCIÓN (Aquí estaba el error)
        // --------------------------------------------------------------------------
        // Solo si enviaron un plan_id (Es decir, es un Alumno), creamos la suscripción.
        if ($request->plan_id) {
            $plan = Plan::findOrFail($request->plan_id); 
            $durationInDays = $plan->duration_days; 

            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays($durationInDays),
                'status' => 'active'
            ]);
        }
        // Si no hay plan_id (Entrenador), el código salta esta parte y sigue feliz.

        // E. Generar Token y Respuesta
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado con éxito', // Mensaje genérico
            'user' => $user,
            'token' => $token
        ], 201);
    });
}
    // 2. Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->with('subscriptions.plan') // Traemos el plan si existe
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'message' => 'Login exitoso',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    // 3. Logout 
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ], 200);
    }
}