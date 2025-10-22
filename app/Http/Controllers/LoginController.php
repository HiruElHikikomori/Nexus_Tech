<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function ShowLoginForm(){
        return view('auth.login');
    }

    public function login(Request $request){
        $credentials = $request-> validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            //Obtiene el usuario autenticado
            $user = Auth::user();

            //Validación del rol de la cuenta
            if ($user->role->name === "Administrador"){
                return redirect()-> intended('/controlPanel');
            }else{
                return redirect()-> intended('/products');
            }
            
        }

        return back()->withErrors([
            'username' => 'Credenciales incorrectas.',
        ]);
    }

    public function logout(Request $request){
        Auth::logout();
        $request-> session()-> invalidate();
        $request-> session()-> regenerateToken();
        return redirect('/login');
    }
}
