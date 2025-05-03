<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|string|email|max:255|unique:users',
            'password'=>'required|string|min:8|confirmed',
        ]);
        $user=user::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
        ]);
        $token=$user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'user'=>$user,
            'token'=>$token,
        ],201);
    }  
    public function login(Request $request){
        $credentials=$request->validate([
            'email'=>'required|string|email|max:255',
            'password'=>'required|string|min:8',
        ]);
        $user=User::where('email',$credentials['email'])->first();
        if(!$user||!Hash::check($credentials['password'],$user->password)){
            Log::warning('Login attempt failed',[
                'email'=>Str::mask($credentials['email'],'*',3,2),
                'ip'=>$request->ip(),
                'user_agent'=>$request->userAgent()
            ]);
            return response()->json([
                'message'=>'Invalid credentials',
            ],401);
        }
         $user->tokens()->delete();


        return new UserResource($user);
    } 
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        Log::info('User Logged out',[
            'ip'=>$request->ip(),
            'user_agent'=>$request->userAgent(),
            'user_id'=>$request->user()->id,
        ]);
        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
