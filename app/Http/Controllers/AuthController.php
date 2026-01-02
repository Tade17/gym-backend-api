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
            'weight' => 'required|numeric',
            'height' => 'required|numeric',
            'birth_date' => 'required|date',
            'goals' => 'nullable|string',
            // CAMBIO: Validamos que sea una imagen real
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Validamos que el plan exista en la tabla 'plans'
            'plan_id' => 'required|exists:plans,id' 
        ]);

                // 2. INICIO DE LA TRANSACCIÓN
        return DB::transaction(function () use ($request) {

            // --------------------------------------------------------------------------
            // A. Detectar si quien registra es un entrenador
            // --------------------------------------------------------------------------
            $trainerId = null;

            // MODIFICACIÓN: Mantenemos tu lógica de 'guard(sanctum)'.
            // POR QUÉ: Como la ruta '/register' es pública (está fuera del middleware auth en api.php),
            // Laravel no revisa el token automáticamente. Usando 'guard->check()' forzamos
            // al sistema a mirar si hay un token Bearer en la cabecera para identificar al entrenador.
            if (Auth::guard('sanctum')->check()) {
                $currentUser = Auth::guard('sanctum')->user();
                
                if ($currentUser->role === 'trainer') {
                    $trainerId = $currentUser->id;
                }
            }

            // --------------------------------------------------------------------------
            // B. Procesar la Foto de Perfil (NUEVO BLOQUE)
            // --------------------------------------------------------------------------
            $photoPath = 'default.png'; // Valor por defecto si no suben nada

            // MODIFICACIÓN: Verificamos si existe un archivo en la petición.
            // POR QUÉ: Ahora tu Frontend envía un 'FormData' con un archivo real. 
            // Si no hacemos esto, el sistema intentaría guardar el objeto del archivo como texto y fallaría.
            if ($request->hasFile('profile_photo')) {
                // Guardamos el archivo en la carpeta 'public/profile_photos' dentro del storage.
                // La función store() devuelve automáticamente la ruta hash (ej: "profile_photos/XyZ123.jpg")
                $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
            }

            // --------------------------------------------------------------------------
            // C. Crear el Usuario (Alumno)
            // --------------------------------------------------------------------------
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
                
                // MODIFICACIÓN: Usamos la variable $photoPath en lugar del texto fijo.
                // POR QUÉ: Para que en la base de datos quede guardada la ruta de la imagen
                // que acabamos de subir, o 'default.png' si no subió ninguna.
                'profile_photo' => $photoPath, 
                
                'assigned_trainer_id' => $trainerId 
            ]);

            // --------------------------------------------------------------------------
            // D. Buscar Plan y Crear Suscripción (Esto se mantiene igual)
            // --------------------------------------------------------------------------
            $plan = Plan::findOrFail($request->plan_id); 
            $durationInDays = $plan->duration_days; 

            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays($durationInDays),
                'status' => 'active'
            ]);

            // --------------------------------------------------------------------------
            // E. Generar Token y Respuesta
            // --------------------------------------------------------------------------
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Alumno registrado y suscripción creada con éxito',
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