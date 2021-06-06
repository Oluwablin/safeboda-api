<?php

namespace App\Http\Controllers\v1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    //RESISTER A USER
    public function register(UserRegistrationRequest $request)
    {
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $password = $request->password;

        $user = User::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => Hash::make($request->password)
        ]);

        if (!$user) {
            $response = [
                'status' => 'error',
                'message' => 'Error creating user account.'
            ];

            return response()->json($response, 400);
        }

        $response = [
            'status' => 'success',
            'message' => 'New User Account Successfully Created'
        ];

        return response()->json($response, 201);

    }

    //LOGIN A USER
    public function login(UserLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();

            if (!$user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Incorrect email or password',
                ], 401);
            }

            $token = JWTAuth::attempt($credentials);

            $response = [
                'status' => 'success',
                'message' => 'You are logged in successfully',
                'user' => $user,
                'token' => $token,
                'tokenType' => 'bearer',
                'expiresIn' => auth('api')->factory()->getTTL() * 60
            ];

        return response()->json($response, 200);
    }
}
