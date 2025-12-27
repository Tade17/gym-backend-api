<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Registrar usuario (Usado por el Entrenador para crear Alumnos)
    public function register(Request $request)
    {
        // PASO 1: Validamos los datos que llegan de React
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'nullable|string|max:20', // Validamos el formato
            'gender' => 'required|in:male,female,other',
            'role' => 'required|in:admin,trainer,client',
            'weight' => 'required|numeric',
            'height' => 'required|numeric',
            'birth_date' => 'required|date',
            'goals' => 'nullable|string',
            'profile_photo' => 'nullable|string'
            // OJO: assigned_trainer_id NO se valida aquí porque no viene del formulario, lo calculamos nosotros
        ]);

        // PASO 2: Calculamos quién es el entrenador
        // Si quien hace el registro es un Entrenador logueado, él será el 'assigned_trainer'
        $trainerId = null;
        if (Auth::check() && Auth::user()->role === 'trainer') {
            $trainerId = Auth::id();
        }

        // PASO 3: Creamos al usuario en la BD
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number, // <--- CORREGIDO: Usamos el dato del request
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'weight' => $request->weight,
            'height' => $request->height,
            'birth_date' => $request->birth_date,
            'goals' => $request->goals,
            'profile_photo' => $request->profile_photo ?? 'default.png',
            'assigned_trainer_id' => $trainerId // <--- CORREGIDO: Aquí asignamos la variable calculada
        ]);

        // PASO 4: Creamos el token (opcional si solo estás registrando a otro, pero útil)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
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