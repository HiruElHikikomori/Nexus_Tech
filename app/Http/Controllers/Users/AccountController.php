<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    //Mostrar perfil
    public function show(){
        $user = Auth::user();

        return view('users.userProfile', compact('user'));
    }

    //Editar perfil
    public function edit($userId){
        $user = User::findOrFail($userId); // Busca el usuario por ID, si no existe lanza un 404
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $userId){
        $user = User::findOrFail($userId); // Encuentra el usuario a actualizar

        // Reglas de validación
        $rules = [
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            // Asegura que el username y email sean únicos, excepto para el usuario actual
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Para la imagen de perfil
            'password' => 'nullable|string|min:6|confirmed', // 'confirmed' requiere un campo 'password_confirmation'
        ];

        // Valida los datos de la solicitud
        $validatedData = $request->validate($rules);

        // Actualizar datos del usuario
        $user->name = $validatedData['name'];
        $user->last_name = $validatedData['last_name'];
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->phone_number = $validatedData['phone_number'] ?? null; // Usa null si es opcional y no se envía
        $user->address = $validatedData['address'] ?? null;

        // Si se proporcionó una nueva contraseña, la hashea y la actualiza
        if (!empty($validatedData['password'])) {
            $user->password = $validatedData['password']; // Tu mutador `setPasswordAttribute` se encargará del hash
        }

        // Manejo de la imagen de perfil
        if ($request->hasFile('profile_img')) {
            // Eliminar la imagen anterior si existe y no es la por defecto
            if ($user->profile_img_name && $user->profile_img_name !== 'default.png') {
                $oldImagePath = public_path('img/users/' . $user->profile_img_name);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Almacenar la nueva imagen
            $image = $request->file('profile_img');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('img/users'), $imageName);
            $user->profile_img_name = $imageName;
        }

        $user->save(); // Guarda los cambios en la base de datos

        return redirect()->route('users.edit', $user->user_id) // O a donde quieras redirigir después de la edición
                         ->with('success', 'Perfil actualizado exitosamente.');
    
    }
}
