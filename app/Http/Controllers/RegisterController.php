<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    public function ShowRegisterForm(){
        return view('auth.register'); // Ruta donde se encuentra el archivo de la vista
    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:users',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed|min:6',
            'phone_number' => 'required|unique:users',
            'address' => 'required',
            'profile_img_name' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2040',
        ]);

        $filename = 'default.png'; //Archivo por defecto si no ponen su perfil

        //Subir foto de perfil
        if($request->hasFile('profile_img_name')){
            $file = $request->file('profile_img_name');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img/users'), $filename);
        }else{
            $filename = 'default.png'; //Archivo por defecto si no ponen su perfil
        }
        
        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'username' => $request-> username,
            'email' => $request-> email,
            'password' => Hash::make($request-> password),
            'phone_number' => $request-> phone_number,
            'address' => $request-> address,
            'profile_img_name' => $filename,
            'rol_id' => 2,
        ]);

        //Crear un carrito vacío para el usuario
        $cart = new \App\Models\Cart();
        $cart-> user_id = $user->user_id;
        $cart-> save();


        return redirect('/login')->with('success', 'Usuario creado con éxito');
    }
}
