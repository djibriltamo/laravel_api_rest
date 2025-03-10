<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = new User();

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password, [
                'rounds' => 12
            ]);
            $user->save();

            return response()->json([
                'status' => 200,
                'status_message' => 'User Added',
                'user' => $user
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only(['email', 'password']))) {
            $user = Auth::user();

            dd($user);

            $token = $user->createToken('SECRET_KEY')->plainTextToken;

            return response()->json([
                'status_code' => 200,
                'status_message' => 'User Connected',
                'user' => $user,
                'token' => $token
            ]);
        } else {
            return response()->json([
                'status_code' => 403,
                'status_message' => 'User not found OR invalid informations',
            ]);
        }
    }
}
