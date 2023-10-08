<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function login(LoginRequest $request) {
        $data = $request->validated();

        if(Auth::attempt($data)) {
            return response()->json(['user' => Auth::user()]);
        }
        return response()->json(['error' => 'Чет не так']);

    }
    public function register(RegisterRequest $request) {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        if(Auth::attempt($request->only('email','password'))) {
            return response()->json([
                'user' => $user,
            ]);
        }
        return response()->json(['error' => 'Чет случилось']);
    }
    public function logout(Request $request) {
        Auth::logout();
        Session::flush();
        return response()->json(['success' => 'Вы вышли']);
    }
}
