<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    use HttpResponses;

    public function register(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|max:255'
        ]);


        if ($validations->fails()) {
            return $this->error($validations->errors(), 'Validation failed', 422);
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

        return $this->success($data, 'Registration Successfully', 201);
    }

    public function login(Request $request)
    {
        $validations = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|max:255'
        ]);


        if ($validations->fails()) {
            return $this->error($validations->errors(), 'Validation failed', 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->failed('Invalid credentials', 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        $data = [
            'user' => $user,
            'token' => $token
        ];

        return $this->success($data, 'Login Successfully', 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->success(null, 'Logged Out', 200);
    }
}
