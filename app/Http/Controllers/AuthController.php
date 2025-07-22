<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors(), 'Validation Error', 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ResponseFormatter::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 'User registered successfully');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return ResponseFormatter::error(null, 'Invalid credentials', 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return ResponseFormatter::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 'Login success');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ResponseFormatter::error(null, 'User not found', 404);
        }

        // Generate reset token (contoh sederhana)
        $resetToken = Str::random(60);
        $user->update(['reset_token' => $resetToken]);

        // Di sini Anda bisa mengirim email dengan reset token
        // Mail::to($user->email)->send(new PasswordResetMail($resetToken));

        return ResponseFormatter::success(null, 'Reset password link has been sent');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success(null, 'Logout success');
    }
}
