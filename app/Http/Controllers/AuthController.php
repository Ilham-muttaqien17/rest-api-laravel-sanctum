<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|max:255'
        ]);


        if ($validations->fails()) {
            $response['status'] = false;
            $response['message'] = "Register failed!";
            $response['errors'] = $validations->errors();

            return response()->json($response, 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('token')->plainTextToken;

        $data = [
            'user' => $user,
            'token' => $token
        ];

        $response['status'] = true;
        $response['message'] = 'Registration Successfully';
        $response['data'] = $data;

        return response()->json($response, 201);
    }

    public function login(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255'
        ]);


        if ($validations->fails()) {
            $response['status'] = false;
            $response['message'] = "Login failed!";
            $response['errors'] = $validations->errors();

            return response()->json($response, 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $response['status'] = false;
            $response['message'] = "Invalid credentials!";

            return response()->json($response, 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        $data = [
            'user' => $user,
            'token' => $token
        ];

        $response['status'] = true;
        $response['message'] = 'Login Successfully';
        $response['data'] = $data;

        return response()->json($response, 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        $response['status'] = true;
        $response['message'] = 'Logged Out';

        return response()->json($response, 200);
    }
}
