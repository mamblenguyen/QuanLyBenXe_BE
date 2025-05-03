<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\BusStation;
use App\Http\Resources\System\BusStation\BusStationResource;
use App\Services\ResponseService;
use Illuminate\Support\Carbon;
use App\Http\Requests\System\Bustations\BustationRequest;
use App\Http\Requests\System\Bustations\BustationUpdateRequest;



class BustationController extends Controller
{

    public function getAll(Request $request)
    {
        try {
            $token = JWTAuth::parseToken();

            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $busStations = BusStation::where('garage_id', $Garage_id)->get();

            if ($busStations->isEmpty()) {
                return ResponseService::customResponse(null, 'No bus stations found for this garage.', 404);
            }

            return ResponseService::customResponse(BusStationResource::collection($busStations), 'Data retrieved successfully.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAllStatus(Request $request)
    {
        try {
            $token = JWTAuth::parseToken();

            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $busStations = BusStation::where('garage_id', $Garage_id)->where('status_id', 1)->get();

            if ($busStations->isEmpty()) {
                return ResponseService::customResponse(null, 'No bus stations found for this garage.', 404);
            }

            return ResponseService::customResponse(BusStationResource::collection($busStations), 'Data retrieved successfully.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create(BustationRequest $request)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $validatedData = $request->validated();

            BusStation::create([
                'name' => $validatedData['name'],
                'address' => $validatedData['address'],
                'province_id' => $validatedData['province_id'],
                'status_id' => $validatedData['status_id'],
                'garage_id' => $Garage_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return ResponseService::customResponse(null, 'Bus station created successfully.', 201);
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

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $busStation = BusStation::find($id);

            if (!$busStation) {
                return ResponseService::customResponse(null, 'Bus station not found.', 404);
            }

            if ($busStation->garage_id !== $Garage_id) {
                return ResponseService::customResponse(null, 'You do not have permission to access this bus station.', 403);
            }

            return ResponseService::customResponse(new BusStationResource($busStation), 'Data retrieved successfully.', 200);
        } catch (\Exception $e) {
            return ResponseService::customResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
        }
    }


    public function update(BustationUpdateRequest $request, $id)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $busStation = BusStation::where('id', $id)->where('garage_id', $Garage_id)->first();

            if (!$busStation) {
                return ResponseService::customResponse(null, 'Bus station not found or does not belong to this garage.', 404);
            }

            $validatedData = $request->validated();

            $busStation->update([
                'name' => $validatedData['name'],
                'address' => $validatedData['address'],
                'province_id' => $validatedData['province_id'],
                'updated_at' => Carbon::now(),
            ]);

            return ResponseService::customResponse(null, 'Bus station updated successfully.', 200);
        } catch (\Exception $e) {
            // Xử lý lỗi nếu xảy ra
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function softDelete($id)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $busStation = BusStation::where('id', $id)->where('garage_id', $Garage_id)->first();

            if (!$busStation) {
                return ResponseService::customResponse(null, 'Bus station not found or does not belong to this garage.', 404);
            }

            if ($busStation->status_id == 2) {
                return ResponseService::customResponse(null, 'Bus station is already inactive.', 400);
            }

            $busStation->update([
                'status_id' => 2,
                'updated_at' => Carbon::now(),
            ]);

            return ResponseService::customResponse(null, 'Bus station deleted (soft) successfully.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    public function restore($id)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $busStation = BusStation::where('id', $id)
                ->where('garage_id', $Garage_id)
                ->first();

            if (!$busStation) {
                return ResponseService::customResponse(null, 'Bus station not found or does not belong to this garage.', 404);
            }

            if ($busStation->status_id == 1) {
                return ResponseService::customResponse(null, 'Bus station is already active.', 400);
            }

            $busStation->update([
                'status_id' => 1,
                'updated_at' => Carbon::now(),
            ]);

            return ResponseService::customResponse(null, 'Bus station restored successfully.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
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

            $busStation = BusStation::find($id);

            if (!$busStation) {
                return ResponseService::customResponse(null, 'Bus station not found.', 404);
            }

            if ($busStation->garage_id !== $Garage_id) {
                return ResponseService::customResponse(null, 'You do not have permission to delete this bus station.', 403);
            }

            $busStation->delete();

            return ResponseService::customResponse(null, 'Bus station deleted successfully.', 200);
        } catch (\Exception $e) {
            return ResponseService::customResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
        }
    }
}
