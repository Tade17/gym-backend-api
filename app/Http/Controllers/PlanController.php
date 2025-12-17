<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    // GET: Obtener todos los planes
    // 1. LISTAR todos (GET /api/plans)
    public function index()
    {
        $planes = Plan::all();
        return response()->json($planes, 200);
    }

    // POST: Crear un nuevo plan
    // 2. CREAR uno nuevo (POST /api/plans)
    public function store(Request $request)
    {
        // Validamos que envíen los datos necesarios
        $request->validate([
            'name' => 'required|string|unique:plans,name',
            'price' => 'required|numeric',
            'duration_days' => 'required|integer',
            'description' => 'required|string',
            'is_active' => 'sometimes|boolean',
            'type_plan'=>'required|string|in:basic,pro,personalized',
            'trainer_id' => 'required|exists:users,id'
        ]);

        // Creamos el plan en la BD
        $plan = Plan::create($request->all());

        return response()->json([
            'message' => 'Plan creado con éxito',
            'data' => $plan
        ], 201);
    }

    // GET: Obtener un plan por ID
    // 3. OBTENER uno por ID (GET /api/plans/{id})
    public function showById($id){
        
        $plan=Plan::find($id);
        if(!$plan){
            return response()->json([
                'message' =>'Plan no encontrado'
            ],404);
        }
        return response() -> json([
            'message'=>'Plan encontrado con exito',
            'data'=>$plan
        ],200);
    }

    //UPDATE:Actualizar un plan (PUT /api/plans/{id})
    public function update(Request $request,$id){
        $plan=Plan::find($id);
        if(!$plan){
            return response()-> json([
                'message' =>'Plan no encontrado'
            ],404);
        }
        //validamos los datos 
        $request->validate([
            'name'=>'sometimes|string|unique:plans,name,'.$id,//ignora su propio nombre al validar duplicado
            'price' =>'sometimes|numeric',
            'duration_days'=>'sometimes|integer',
            'description'=>'sometimes|string',
            'is_active'=>'sometimes|boolean',
            'type_plan'=>'sometimes|string|in:basic,pro,personalized',
            'trainer_id'=>'sometimes|exists:users,id'
        ]);

        //actualizamos el plan 
        $plan -> update($request ->all());

        return response()-> json([
            'message'=>'Plan actualizado con exito',
            'data'=> $plan
        ],200);
    }

    // DELETE: Eliminar un plan (DELETE /api/plans/{id})
    public function destroy($id){
        $plan=Plan::find($id);
        if(!$plan){
            return response() ->json([
                'message'=>'Plan no encontrado'
            ],404);
        }
        $plan -> delete();

        return response()-> json([
            'message'=>'Plan eliminado con exito'
        ],200);
    }

}
