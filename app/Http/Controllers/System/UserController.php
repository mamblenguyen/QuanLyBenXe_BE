<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\User\StoreUserRequest;
use App\Http\Requests\System\User\UpdateUserRequest;
use App\Http\Resources\System\User\UserListResource;
use App\Http\Responses\ApiResponse;
use App\Repositories\Implementations\UserRepositoryInterface;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {
        // Lấy garage_id từ request (được merge từ middleware JwtMiddleware)
        $garage_id = $request->input('garage_id');

        // Kiểm tra xem garage_id có tồn tại không
        if (!$garage_id) {
            return ResponseService::customResponse(null, 'Garage ID is required', 400);
        }
//
//        // Truyền garage_id vào phương thức getAll()
        $users = $this->userRepository->getAll($garage_id);

        if ($users) {
            $users->load('roles')->load('Garage');
            return ResponseService::customResponse(UserListResource::collection($users), 'Get all users successfully !!', 200);
        }

        return ResponseService::customResponse(null, 'User not found !!', 404);
    }

    public function show($id)
    {
        $user = $this->userRepository->find($id);

        // Check if the user exists and if the garage_id matches
        $garage_id = request()->input('garage_id');
        if (!$user || $user->garage_id !== $garage_id) {
            return ResponseService::customResponse(null, 'User not found or access denied !!', 403);
        }

        $user->load('roles')->load('Garage');
        return ResponseService::customResponse(new UserListResource($user), 'Show user successfully!!', 200);
    }



    public function store(StoreUserRequest $request)
    {
        $requestData = $request->validated(); // Get validated data
        $requestData['id'] = (string) Str::uuid(); // Add UUID
        $requestData['password'] = Hash::make($requestData['password']); // Hash the password
        $requestData['garage_id'] = auth()->user()->garage_id; // Set garage_id based on the authenticated user
        $user = $this->userRepository->create($requestData)->load('Garage');
        if ($request->has('roles')) {
            $roleUserData = [];
            foreach ($request->roles as $roleId) {
                $roleUserData[] = [
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            DB::table('role_users')->insert($roleUserData);
        }

        return ResponseService::customResponse(new UserListResource($user), 'Create user successfully !!', 201);
    }



    public function update(UpdateUserRequest $request, $id)
    {
        Log::info('Updating user:', [
            'id' => $id,
            'request' => $request->all(),
        ]);

        $user = $this->userRepository->find($id);

        // Check if the user exists and if the garage_id matches
        $garage_id = request()->input('garage_id');
        if (!$user || $user->garage_id !== $garage_id) {
            return ResponseService::customResponse(null, 'User not found or access denied !!', 403);
        }

        $requestData = $request->validated(); // Get validated data

        // Hash the password if it's provided
        if (isset($requestData['password'])) {
            $requestData['password'] = Hash::make($requestData['password']); // Hash the password
        }

        // Update the user with validated data
        $user = $this->userRepository->update($id, $requestData);

        // Handle roles if provided
        if ($request->has('roles')) {
            // Remove existing roles
            DB::table('role_users')->where('user_id', $id)->delete();
            $roleUserData = [];
            foreach ($request->roles as $roleId) {
                $roleUserData[] = [
                    'user_id' => $id,
                    'role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // Insert the new roles
            DB::table('role_users')->insert($roleUserData);
        }

        return ResponseService::customResponse(new UserListResource($user), 'Cập nhật người dùng thành công !!', 200);
    }


    public function destroy($id)
    {
        $user = $this->userRepository->find($id);

        // Check if the user exists and if the garage_id matches
        $garage_id = request()->input('garage_id');
        if (!$user || $user->garage_id !== $garage_id) {
            return ResponseService::customResponse(null, 'User not found or access denied !!', 403);
        }

        $deleted = $this->userRepository->delete($id);
        if ($deleted) {
            return ResponseService::customResponse(null,'Delete user successfully !!', 200);
        }
        return ResponseService::customResponse(null,'User not found !!', 404);
    }


    public function getAll(Request $request)
    {
        $garage_id = $request->input('garage_id');
        if (!$garage_id) {
            return ResponseService::customResponse(null, 'Garage ID is required', 400);
        }

        // Truyền garage_id vào phương thức getAll()
        $users = $this->userRepository->getAll($garage_id);

        if ($users) {
            $users->load('roles')->load('Garage');
            return ResponseService::customResponse(UserListResource::collection($users), 'Get all users successfully !!', 200);
        }

        return ResponseService::customResponse(null, 'User not found !!', 404);
    }
}
