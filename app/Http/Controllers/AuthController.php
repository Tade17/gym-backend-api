<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //1.Registrar para el usuario
    public function register(Request $request)
    {

        //validamos los datos recibidos
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,trainer,client',
            'weight' => 'required|numeric',
            'height' => 'required|numeric',
            'birth_date' => 'required|date',
            'goals' => 'nullable|string',
            'profile_photo' => 'nullable|string'
        ]);
        //creamos al usuario 
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password), //encriptamos la contraseña
            'role' => $request->role,
            'weight' => $request->weight,
            'height' => $request->height,
            'birth_date' => $request->birth_date,
            'goals' => $request->goals,
            'profile_photo' => $request->profile_photo
        ]);

        //creamos el token de autenticacion
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'=>'User registered successfully',
            'user'=>$user,
            'token'=>$token
        ],201);
    }

    //2.Login para el usuario
    public function login(Request $request){
        //validamos los datos recubudis
        $request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);

        //buscamos al usuario por su email
        $user=User::where('email',$request->email)->first();

        //verificamos si el usuario existe y la contraseña es correcta
        if(!$user ||!Hash::check($request->password,$user->password)){
            return response()->json([
                'message'=>'Invalid credentials'
            ],401);
        }

        //si pasa la verificacion , creamos un token de autenticacion
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message'=>'Login successful',
            'user'=>$user,
            'token'=>$token
        ],200);
    }
    //3.Logout 
    public function logout(Request $request)
    {
        //borrar el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }


}
