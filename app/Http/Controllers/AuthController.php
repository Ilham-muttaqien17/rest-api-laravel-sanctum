<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    use HttpResponses;

    public function register(StoreUserRequest $request)
    {
        $request->validate($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('API Token of ' . $user->name)->plainTextToken;

        $data = [
            'user' => $user,
            'token' => $token
        ];

        return $this->success($data, 'Registration Successfully', 201);
    }

    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->failed('Invalid credentials', 401);
        }

        $token = $user->createToken('API Token of ' . $user->name)->plainTextToken;

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
