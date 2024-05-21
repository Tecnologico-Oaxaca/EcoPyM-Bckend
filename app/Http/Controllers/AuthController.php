<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    //Register Api, Login API, Profile API, Lagout API
    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if(!empty($user)){
            if(Hash::check($request -> password, $user->password )){
                $token = $user -> createToken('miToken') -> plainTextToken;
                $data = [
                    'message' => 'Token Generado',
                    'token' => $token,
                    'user' => $user,
                    'status' => 200
                ];
                return response() -> json($data,200);
            }else{
                $data = [
                    'message' => 'Contraseña incorrecta',
                    'status' => 400
                ];
                return response() -> json($data,400);
            }
        }else{
            $data = [
                'message' => 'Tu correo es incorrecto',
                'status' => 400
            ];
            return response() -> json($data,400);
        }
    }

    public function profile(){
        $userData = auth() ->user();
        $data = [
            'message' => 'Perfil del usuario',
            'perfil' => $userData,
            'status' => 200,
            'id' => auth() -> user() -> id
        ];
        return response() -> json($data,200);
    }

    public function logout(){
        $user = auth() -> user();
        $user -> tokens() -> delete();

        $data = [
            'message' => 'Sesion cerrada',
            'status' => 200
        ];
        return response() -> json($data,200);
    }
}
