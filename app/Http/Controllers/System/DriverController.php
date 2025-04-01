<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\Driver\StoreDriverRequest;
use App\Http\Requests\System\Driver\UpdateDriverRequest;
use App\Repositories\Implementations\DriverRepositoryInterface;
use App\Http\Resources\System\Driver\DriverListResource;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    protected $driverRepository;

    public function __construct(DriverRepositoryInterface $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }

    public function index(Request $request)
    {
        $garage_id = $request->input('garage_id');
        if (!$garage_id) {
            return ResponseService::customResponse(null, 'Garage ID is required', 400);
        }
        $drivers = $this->driverRepository->getAll($garage_id);

        if ($drivers->isNotEmpty()) {
            return ResponseService::customResponse(DriverListResource::collection($drivers), 'Get all drivers successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Drivers not found', 404);
    }

    public function show($id, Request $request)
    {
        $garage_id = $request->input('garage_id');
        if (!$garage_id) {
            return ResponseService::customResponse(null, 'Garage ID is required', 400);
        }
        $driver = $this->driverRepository->find($id);
        if (!$driver || $driver->garage_id !== $garage_id) {
            return ResponseService::customResponse(null, 'Driver not found or access denied !!', 403);
        }

        return ResponseService::customResponse(new DriverListResource($driver), 'Show driver successfully!!', 200);
    }

    public function store(StoreDriverRequest $request)
    {
        $garage_id = auth()->user()->garage_id;
        $requestData = $request->all();
        $requestData['garage_id'] = $garage_id;
        $driver = $this->driverRepository->create($requestData);

        return ResponseService::customResponse(new DriverListResource($driver), 'Create driver successfully !!', 201);
    }

    public function update(UpdateDriverRequest $request, $id)
    {
        $userGarageId = auth()->user()->garage_id;
        $driver = $this->driverRepository->find($id);
        if (!$driver) {
            return ResponseService::customResponse(null, 'Driver not found !!', 404);
        }
        if ($driver->garage_id !== $userGarageId) {
            return ResponseService::customResponse(null, 'Access denied! You can only update drivers from your own garage.', 403);
        }
        $validatedData = $request->validated();
        $driver = $this->driverRepository->update($id, $validatedData);
        return ResponseService::customResponse(new DriverListResource($driver), 'Update driver successfully !!', 200);
    }

    public function destroy($id, Request $request)
    {
        $garage_id = $request->input('garage_id');
        $driver = $this->driverRepository->find($id);
        if (!$driver || $driver->garage_id !== $garage_id) {
            return ResponseService::customResponse(null, 'Driver not found or access denied !!', 403);
        }
        $deleted = $this->driverRepository->delete($id);
        if ($deleted) {
            return ResponseService::customResponse(null, 'Delete driver successfully !!', 200);
        }
        return ResponseService::customResponse(null, 'Driver not found !!', 404);
    }
}
