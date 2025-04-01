<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\Garage\StoreGarageRequest;
use App\Http\Requests\System\Garage\UpdateGarageRequest;
use App\Models\Garage;
use App\Repositories\Implementations\GarageRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Resources\System\Garage\GarageListResource; // Make sure you have this resource created
use App\Services\ResponseService;

class GarageController extends Controller
{
    protected $garageRepository;

    public function __construct(GarageRepositoryInterface $garageRepository)
    {
        $this->garageRepository = $garageRepository;
    }

    public function index()
    {
        $roles = Garage::all();
        if ($roles) {
            return ResponseService::customResponse(GarageListResource::collection($roles), 'Get all garages successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Garage not found', 404);
    }

    public function show($id)
    {
        $role = $this->garageRepository->find($id);
        if ($role) {
            return ResponseService::customResponse(new GarageListResource($role), 'Show garage successfully!!', 200);
        }
        return ResponseService::customResponse(null, 'Garage not found', 404);
    }

    public function store(StoreGarageRequest $request)
    {
        $role = $this->garageRepository->create($request->all());
        return ResponseService::customResponse(new GarageListResource($role), 'Create garage successfully !!', 201);
    }

    public function update(UpdateGarageRequest $request, $id)
    {
        $role = $this->garageRepository->update($id, $request->all());
        if ($role) {
            return ResponseService::customResponse(new GarageListResource($role), 'Update garage successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Garage not found', 404);
    }

    public function destroy($id)
    {
        $deleted = $this->garageRepository->delete($id);
        if ($deleted) {
            return ResponseService::customResponse(null, 'garage deleted successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Garage not found', 404);
    }

    public function getAll()
    {
        $roles = Garage::all();
        if ($roles) {
            return ResponseService::customResponse(GarageListResource::collection($roles), 'Get all garages successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Garage not found', 404);
    }
}
