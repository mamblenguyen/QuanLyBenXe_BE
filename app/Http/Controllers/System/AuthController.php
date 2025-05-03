<?php
namespace App\Http\Controllers\system;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Tymon\JWTAuth\Facades\JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;
    use Tymon\JWTAuth\Exceptions\TokenExpiredException;
    use Tymon\JWTAuth\Exceptions\TokenInvalidException;
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

                $user = JWTAuth::user();
                $roles = $user->roles->pluck('name')->toArray();
                $garage = [
                    'Garage_id' => $user->garage->id,
                    'Garage_name' => $user->garage->name,
                ];

                $customClaims = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $roles,
                    'garage' => $garage,
                ];

                // Tạo token mới với custom claims
                $token = JWTAuth::claims($customClaims)->attempt($credentials);

                return response()->json([
                    'status' => 'success',
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60, // thời gian hết hạn (s)
                ], 200);
            } catch (JWTException $e) {
                return response()->json(['status' => 'error', 'message' => 'Could not create token'], 500);
            }
        }

        // ✅ Refresh token
        public function refresh()
        {
            try {
                $newToken = JWTAuth::parseToken()->refresh();
                return response()->json([
                    'status' => 'success',
                    'token' => $newToken,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ]);
            } catch (TokenExpiredException $e) {
                return response()->json(['status' => 'error', 'message' => 'Token has expired and can no longer be refreshed'], 401);
            } catch (TokenInvalidException $e) {
                return response()->json(['status' => 'error', 'message' => 'Invalid token'], 401);
            } catch (JWTException $e) {
                return response()->json(['status' => 'error', 'message' => 'Token not provided'], 401);
            }
        }

        // ✅ Logout
        public function logout()
        {
            try {
                JWTAuth::invalidate(JWTAuth::getToken());
                return response()->json(['message' => 'Successfully logged out']);
            } catch (JWTException $e) {
                return response()->json(['message' => 'Failed to logout, token invalid'], 500);
            }
        }
    }
