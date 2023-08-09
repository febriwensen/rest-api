<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email / password salah.'],
            ]);
        }

        return $user->createToken('Kopi Hitam Jos')->plainTextToken;
    }

    public function logout(Request $request)
    {
        // Revoke all tokens...
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Logout berhasil'], 200);
    }

    public function me(Request $request)
    {
        return response()->json(Auth::user());
    }
}
