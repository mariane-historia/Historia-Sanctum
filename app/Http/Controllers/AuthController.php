<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // Add this import for Validator
use App\Models\User;

class AuthController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Validation Error',
                    'errors' => $validateUser->errors(), // Fixed typo: 'errrors' to 'errors'
                ], 401);
            }

            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email, // Fixed typo: 'emal' to 'email'
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => 'Ok',
                'message' => 'User created successfully',
                'token' => $newUser->createToken('API-TOKEN')->plainTextToken, // Fixed typo: 'creatToken' to 'createToken'
            ], 200);
        } catch (\Throwable $error) {
            return response()->json([
                'status' => 'Error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = Validator::make($request->only(['name', 'password']), [
                'name' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($credentials->fails()) {
                return response()->json([
                    'status' => 'Error',
                    'message' => 'Validation Error',
                    'errors' => $credentials->errors(), // Fixed typo: 'jason' to 'json'
                ], 401);
            }

            if (Auth::attempt($request->only(['name', 'password']))) { // Fixed: passing credentials correctly
                $request->session()->regenerate();
                $token = $request->user()->createToken('API-TOKEN')->plainTextToken; // Fixed typo: 'creatToken' to 'createToken'
                return response()->json([
                    'status' => 'ok',
                    'message' => 'Login Successful',
                    'token' => $token,
                ], 200);
            }

            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid credentials',
            ], 401);
        } catch (\Throwable $error) {
            return response()->json([
                'status' => 'Error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }
    public function userInfo()
    {
        $userData = auth()->user();
        return response()->json([
            'status' => true,
            'message' => 'User Login Profile',
            'data' => $userData,
            'id' => auth()->user()->id
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout Successful',
            'data' => []
        ], 200);
    }
}