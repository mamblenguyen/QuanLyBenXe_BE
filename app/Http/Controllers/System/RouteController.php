<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Route;
use App\Http\Resources\System\Routes\RouteResource;
use App\Http\Requests\System\Routes\RouteRequest;
use Illuminate\Support\Carbon;
use App\Http\Requests\System\Routes\RouteUpdateRequest;


class RouteController extends Controller
{
    public function getAll()
    {
        try {
            $token = JWTAuth::parseToken();

            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];


            $Routes = Route::where('garage_id', $Garage_id)->get();

            if ($Routes->isEmpty()) {
                return ResponseService::customResponse(null, 'No bus stations found for this garage.', 404);
            }

            return ResponseService::customResponse(RouteResource::collection($Routes), 'Data retrieved successfully.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    public function create(RouteRequest $request)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $validatedData = $request->validated();

            Route::create([
                'departure_station_id' => $validatedData['departure_station_id'],
                'arrival_station_id' => $validatedData['arrival_station_id'],
                'garage_id' => $Garage_id,
                'departure_time' => $validatedData['departure_time'],
                'arrival_time' => $validatedData['arrival_time'],
                'ticket_price' => $validatedData['ticket_price'],
                'bus_id' => $validatedData['bus_id'],
                'driver_id' => $validatedData['driver_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return ResponseService::customResponse(null, 'Route created successfully.', 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $route = Route::find($id);

            if (!$route) {
                return ResponseService::customResponse(null, 'Route not found.', 404);
            }

            if ($route->garage_id !== $Garage_id) {
                return ResponseService::customResponse(null, 'You do not have permission to access this route.', 403);
            }

            return ResponseService::customResponse(new RouteResource($route), 'Data retrieved successfully.', 200);
        } catch (\Exception $e) {
            return ResponseService::customResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
        }
    }

    public function update(RouteUpdateRequest $request, $id)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $route = Route::where('id', $id)->where('garage_id', $Garage_id)->first();

            if (!$route) {
                return ResponseService::customResponse(null, 'Route not found or does not belong to this garage.', 404);
            }

            $validatedData = $request->validated();

            $route->update([
                'departure_station_id' => $validatedData['departure_station_id'],
                'arrival_station_id' => $validatedData['arrival_station_id'],
                'departure_time' => $validatedData['departure_time'],
                'arrival_time' => $validatedData['arrival_time'],
                'ticket_price' => $validatedData['ticket_price'],
                'bus_id' => $validatedData['bus_id'],
                'driver_id' => $validatedData['driver_id'],
                'updated_at' => Carbon::now(),
            ]);

            return ResponseService::customResponse(null, 'Route updated successfully.', 200);
        } catch (\Exception $e) {
            return ResponseService::customResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            // Tìm route theo ID và garage_id
            $route = Route::where('id', $id)->where('garage_id', $Garage_id)->first();

            if (!$route) {
                return ResponseService::customResponse(null, 'Route not found or does not belong to this garage.', 404);
            }

            // Xóa route
            $route->delete();

            return ResponseService::customResponse(null, 'Route deleted successfully.', 200);
        } catch (\Exception $e) {
            return ResponseService::customResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
        }
    }
}
