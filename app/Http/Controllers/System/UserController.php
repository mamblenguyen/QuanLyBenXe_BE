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
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Alireaza\FormData\FormData; // Đảm bảo bạn đã import đúng

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
    public function getAvailableDrivers(Request $request)
    {
        // Lấy danh sách tất cả driver_id đã được gán trong routes
        $assignedDriverIds = DB::table('routes')
            ->where('route_status_id', 1)
            ->pluck('driver_id')
            ->toArray();
        $garage_id = $request->input('garage_id');
        if (!$garage_id) {
            return ResponseService::customResponse(null, 'Garage ID is required', 400);
        }
        $availableDrivers = User::where('garage_id', $garage_id)
            ->whereNotIn('id', $assignedDriverIds)
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['Driver', 'Traffic']);
            })
            ->where('status_id', 1)
            ->get();

        return ResponseService::customResponse(
            UserListResource::collection($availableDrivers),
            'Available drivers retrieved successfully!',
            200
        );
    }

    public function getAvailableDriversEdit($currentRouteId, Request $request)
    {
        $garage_id = $request->input('garage_id');

        if (!$garage_id) {
            return ResponseService::customResponse(null, 'Garage ID is required', 400);
        }

        // Lấy danh sách driver_id đang được gán cho các tuyến đang hoạt động
        $assignedDriverIds = DB::table('routes')
            ->where('route_status_id', 1)
            ->pluck('driver_id')
            ->toArray();

        // Nếu có current_route_id, thì lấy driver_id của tuyến đó
        $currentDriverId = null;
        if ($currentRouteId) {
            $currentDriverId = DB::table('routes')
                ->where('id', $currentRouteId)
                ->value('driver_id');
        }

        // Nếu currentDriverId hợp lệ, loại bỏ khỏi danh sách loại trừ
        if ($currentDriverId) {
            $assignedDriverIds = array_filter($assignedDriverIds, function ($id) use ($currentDriverId) {
                return $id != $currentDriverId;
            });
        }

        $availableDrivers = User::where('garage_id', $garage_id)
            ->where(function ($query) use ($assignedDriverIds, $currentDriverId) {
                $query->whereNotIn('id', $assignedDriverIds);
                if ($currentDriverId) {
                    $query->orWhere('id', $currentDriverId); // giữ lại driver hiện tại
                }
            })
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['Driver', 'Traffic']);
            })
            ->where('status_id', 1)
            ->get();

        return ResponseService::customResponse(
            UserListResource::collection($availableDrivers),
            'Available drivers retrieved successfully!',
            200
        );
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

        DB::beginTransaction(); // Bắt đầu transaction để đảm bảo tính nhất quán

        try {
            $requestData = $request->validated();
            $requestData['id'] = (string) Str::uuid();
            $requestData['password'] = Hash::make($requestData['password']);
            $requestData['garage_id'] = auth()->user()->garage_id;

            // Tạo user mới và load Garage
            $user = $this->userRepository->create($requestData)->load('Garage');

            // Xử lý upload avatar nếu có
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileName = time() . '_' . $file->getClientOriginalName(); // Đặt tên file tránh trùng lặp

                // Lưu file lên S3
                $path = Storage::disk('s3')->putFileAs('avatars', $file, $fileName);

                // Lấy URL của file trên S3
                if ($path) {
                    $avatarUrl = Storage::disk('s3')->url($path);
                    $user->update(['avatar' => $avatarUrl]);
                }

                // Comment lại dòng sử dụng Supabase để có thể sử dụng lại nếu cần
                /*
                $path = Storage::disk('supabase')->put($fileName, file_get_contents($file));
                if ($path) {
                    $avatarUrl = config('filesystems.disks.supabase.url') . "/storage/v1/object/public/qlbx1/" . $fileName;
                    $user->update(['avatar' => $avatarUrl]);
                }
                */
            }

            // Lưu danh sách roles vào bảng role_users (dùng sync để tối ưu)
            if ($request->has('roles')) {
                $user->roles()->sync($request->roles);
            }

            DB::commit(); // Lưu thay đổi vào database

            return ResponseService::customResponse(new UserListResource($user), 'Create user successfully !!', 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác nếu có lỗi
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function update(UpdateUserRequest $request, $id)
    {
        $requestData = $request->validated();

        DB::beginTransaction();

        try {
            $requestData['garage_id'] = auth()->user()->garage_id;
            $user = $this->userRepository->find($id);

            if (!$user) {
                return ResponseService::customResponse(null, 'User not found!', 404);
            }

            // Kiểm tra quyền truy cập
            if ($user->garage_id !== ($requestData['garage_id'] ?? null)) {
                return ResponseService::customResponse(null, 'Access denied to this user!', 403);
            }

            // Nếu có thay đổi mật khẩu
            if (!empty($requestData['password'])) {
                $requestData['password'] = Hash::make($requestData['password']);
            }

            // Cập nhật thông tin người dùng
            $user = $this->userRepository->update($id, $requestData);

            // Xử lý avatar nếu có tải lên
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileName = time() . '_' . $file->getClientOriginalName(); // Đặt tên file tránh trùng lặp

                // Lưu file avatar lên S3
                $path = Storage::disk('s3')->putFileAs('avatars', $file, $fileName);

                // Lấy URL của file avatar từ S3
                if ($path) {
                    $avatarUrl = Storage::disk('s3')->url($path);
                    $user->update(['avatar' => $avatarUrl]);
                }
            }

            // Cập nhật quyền (roles)
            if ($request->has('roles')) {
                // Xóa các roles cũ và chèn các role mới
                DB::table('role_users')->where('user_id', $id)->delete();
                $roleUserData = collect($requestData['roles'])->map(fn($roleId) => [
                    'user_id' => $id,
                    'role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray();

                DB::table('role_users')->insert($roleUserData);
            }

            DB::commit();
            return ResponseService::customResponse(new UserListResource($user), 'User updated successfully!', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('User update failed:', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Cập nhật người dùng thất bại.'], 500);
        }
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
            return ResponseService::customResponse(null, 'Delete user successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'User not found !!', 404);
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
