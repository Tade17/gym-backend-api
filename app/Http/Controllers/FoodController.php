<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{

    public function index(Request $request)
    {
        $query = Food::query();

        // 1. FALTA: Lógica del buscador
        if ($request->has('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('category', 'LIKE', '%' . $request->search . '%');
        }

        // Ordenamos por categoría y nombre
        return response()->json($query->orderBy('category')->orderBy('name')->get(), 200);
    }

    public function show($id)
    {
        $food = Food::find($id);
        if (!$food) {
            return response()->json(['message' => 'Comida no encontrada'], 404);
        }

        return response()->json($food, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:food,name|max:150',
            'category' => 'required|string|in:Proteína,Cereal/Grano,Fruta,Verdura,Lácteo,Grasa,Azúcar,Bebida,Otro',
            'calories_per_100g' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        // Preparamos los datos básicos
        $data = [
            'name' => $request->name,
            'category' => $request->category, // <--- 2. FALTA: Agregar categoría
            'calories_per_100g' => $request->calories_per_100g,
        ];

        // 3. FALTA: Lógica para guardar la imagen física
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('foods', 'public');
            $data['image_url'] = asset('storage/' . $path);
        }

        $food = Food::create($data);

        return response()->json([
            'message' => 'Comida creada con éxito',
            'data' => $food
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $food = Food::find($id);

        if (!$food) {
            return response()->json(['message' => 'Comida no encontrada'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:150|unique:food,name,' . $id,
            'category' => 'required|string|in:Proteína,Cereal/Grano,Fruta,Verdura,Lácteo,Grasa,Azúcar,Bebida,Otro',
            'calories_per_100g' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'category' => $request->category, // <--- AGREGADO
            'calories_per_100g' => $request->calories_per_100g,
        ];

        // 4. FALTA: Lógica para reemplazar imagen al editar
        if ($request->hasFile('image')) {
            // Borrar foto vieja si existe
            if ($food->image_url) {
                $relativePath = str_replace(asset('storage/'), '', $food->image_url);
                Storage::disk('public')->delete($relativePath);
            }
            
            // Subir nueva
            $path = $request->file('image')->store('foods', 'public');
            $data['image_url'] = asset('storage/' . $path);
        }

        $food->update($data);

        return response()->json([
            'message' => 'Comida actualizada con éxito',
            'data' => $food
        ], 200);
    }

    public function destroy($id)
    {
        $food = Food::find($id);

        if (!$food) {
            return response()->json(['message' => 'Comida no encontrada'], 404);
        }

        // 5. FALTA: Borrar la imagen del disco antes de borrar el registro
        if ($food->image_url) {
            $relativePath = str_replace(asset('storage/'), '', $food->image_url);
            Storage::disk('public')->delete($relativePath);
        }

        $food->delete();

        return response()->json([
            'message' => 'Comida eliminada con éxito'
        ], 200);
    }
}