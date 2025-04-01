<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use App\Http\Requests\System\Auth\LoginSystemRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Resources\System\Auth\LoginSystemResource;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{

    public function login(LoginSystemRequest $request)
    {
        
        $credentials = $request->only('name', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
            }

            // Get the authenticated user
            $user = JWTAuth::user();

            // Get user roles as an array
            $roles = $user->roles->pluck('name')->toArray();
            $garage = [ 'Garage_id' => $user->garage->id,
                'Garage_name' => $user->garage->name,
                ];
            // Add all user information to the token
            $customClaims = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $roles, // Include roles in the claims
                'garage' => $garage,
            ];

            // Create a new token with custom claims
            $token = JWTAuth::claims($customClaims)->attempt($credentials);

            return response()->json([
                'status' => 'success',
                'token' => $token,
            ], 200);
        } catch (JWTException $e) {
            return response()->json(['status' => 'error', 'message' => 'Could not create token: ' . $e->getMessage()], 500);
        }
    }
    // User logout
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }
}
