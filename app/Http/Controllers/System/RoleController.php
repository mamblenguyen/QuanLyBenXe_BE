<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\Role\StoreRoleRequest;
use App\Http\Requests\System\Role\UpdateRoleRequest;
use App\Http\Resources\System\Role\RoleListResource;
use App\Models\Role;
use App\Repositories\Implementations\RoleRepositoryInterface;
use Illuminate\Http\Request;
use App\Services\ResponseService;

class RoleController extends Controller
{
    protected $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function index()
    {
        $roles = Role::all();
        if ($roles) {
            return ResponseService::customResponse(RoleListResource::collection($roles), 'Get all roles successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Roles not found', 404);
    }

    public function show($id)
    {
        $role = $this->roleRepository->find($id);
        if ($role) {
            return ResponseService::customResponse(new RoleListResource($role), 'Show role successfully!!', 200);
        }
        return ResponseService::customResponse(null, 'Role not found', 404);
    }

    public function store(StoreRoleRequest $request)
    {
        $role = $this->roleRepository->create($request->all());
        return ResponseService::customResponse(new RoleListResource($role), 'Create role successfully !!', 201);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        $role = $this->roleRepository->update($id, $request->all());
        if ($role) {
            return ResponseService::customResponse(new RoleListResource($role), 'Update role successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Role not found', 404);
    }

    public function destroy($id)
    {
        $deleted = $this->roleRepository->delete($id);
        if ($deleted) {
            return ResponseService::customResponse(null, 'Role deleted successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Role not found', 404);
    }

    public function getAll()
    {
        $roles = Role::all();
        if ($roles) {
            return ResponseService::customResponse(RoleListResource::collection($roles), 'Get all roles successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Roles not found', 404);
    }
}
