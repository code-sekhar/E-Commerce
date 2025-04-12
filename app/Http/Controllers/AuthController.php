<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        try{
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'phone' => 'required',
            ],[
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Email is invalid',
                'password.required' => 'Password is required',
                'phone.required' => 'Phone is required'
            ]);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'role' => $request->role ?? 'user'
            ]);
    
            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //Login
    public function login(Request $request) {
        try{
            $request->validate([
                'login' => 'required|string',
                'password' => 'required',
            ],[
                'login.required' => 'Login is required',
                'password.required' => 'Password is required'
            ]);
            $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
            $user = User::where($login_type, $request->login)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Password is incorrect'
                ], 401);
            }
            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json([
                'message' => 'User logged in successfully',
                'user' => $user,
                'token' => $token
            ], 200);
        }catch(Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
