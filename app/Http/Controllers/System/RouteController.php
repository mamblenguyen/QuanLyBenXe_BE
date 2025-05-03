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
use App\Http\Requests\System\Routes\ScheduleRequest;
use App\Models\RouteSchedule;

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

    public function getAllAction()
    {
        try {
            $token = JWTAuth::parseToken();

            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];


            $Routes = Route::where('garage_id', $Garage_id)->where('route_status_id', 1)->get();

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

            // Kiểm tra nếu tuyến với cặp departure - arrival đã tồn tại
            $existingRoute = Route::where('departure_station_id', $validatedData['departure_station_id'])
                ->where('arrival_station_id', $validatedData['arrival_station_id'])
                ->exists();

            if ($existingRoute) {
                return ResponseService::customResponse(null, 'Tuyến với trạm đi và trạm đến này đã tồn tại.', 409);
            }

            Route::create([
                'departure_station_id' => $validatedData['departure_station_id'],
                'arrival_station_id' => $validatedData['arrival_station_id'],
                'garage_id' => $Garage_id,
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


    public function createSchedule(ScheduleRequest $request , $id)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']) || !isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $validatedData = $request->validated();

            RouteSchedule::create([
                'route_id' => $id,
                'departure_time' => $validatedData['departure_time'],
                'arrival_time' => $validatedData['arrival_time'],
                'ticket_price' => $validatedData['ticket_price'],
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


    public function ShowStatus($id)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            $route = Route::with(['schedules' => function ($query) {
                $query->where('schedule_status_id', 1);
            }])
                ->whereHas('schedules', function ($query) {
                    $query->where('schedule_status_id', 1);
                })
                ->where('id', $id)
                ->first();



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


    public function showScheduleDetail($scheduleId)
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();

            if (!isset($payload['garage']['Garage_id'])) {
                return ResponseService::customResponse(null, 'Garage ID does not exist in the token.', 404);
            }

            $Garage_id = $payload['garage']['Garage_id'];

            // Tìm schedule
            $schedule = RouteSchedule::with('status')->find($scheduleId);
            if (!$schedule) {
                return ResponseService::customResponse(null, 'Schedule not found.', 404);
            }
            // Tìm route có chứa schedule
            $route = $schedule->route;

            if (!$route) {
                return ResponseService::customResponse(null, 'Route not found.', 404);
            }

            if ($route->garage_id !== $Garage_id) {
                return ResponseService::customResponse(null, 'You do not have permission to access this route.', 403);
            }

            $data = [
                'id' => $route->id,
                'departure_station_id' => $route->departureStation,
                'arrival_station_id' => $route->arrivalStation,
                'garage_id' => $route->garage,
                'schedule' => [
                    'id' => $schedule->id,
                    'departure_time' => $schedule->departure_time,
                    'arrival_time' => $schedule->arrival_time,
                    'ticket_price' => $schedule->ticket_price,
                    'status_id' => $schedule->status->id ?? null,
                    'status_name' => $schedule->status->name ?? null,
                ],
                'bus_id' => $route->bus,
                'status' => $route->status,
                'driver_id' => $route->driver,
                'created_at' => $route->created_at->toISOString(),
                'updated_at' => $route->updated_at->toISOString(),
            ];

            return ResponseService::customResponse($data, 'Data retrieved successfully.', 200);
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
            $existingRoute = Route::where('departure_station_id', $validatedData['departure_station_id'])
            ->where('arrival_station_id', $validatedData['arrival_station_id'])
            ->where('id', '!=', $id) // Loại trừ chính tuyến hiện tại
            ->exists();

        if ($existingRoute) {
            return ResponseService::customResponse(null, 'Tuyến với trạm đi và trạm đến này đã tồn tại.', 409);
        }
            $route->update([
                'departure_station_id' => $validatedData['departure_station_id'],
                'arrival_station_id' => $validatedData['arrival_station_id'],
                // 'departure_time' => $validatedData['departure_time'],
                // 'arrival_time' => $validatedData['arrival_time'],
                // 'ticket_price' => $validatedData['ticket_price'],
                'bus_id' => $validatedData['bus_id'],
                'driver_id' => $validatedData['driver_id'],
                'updated_at' => Carbon::now(),
            ]);

            return ResponseService::customResponse(null, 'Route updated successfully.', 200);
        } catch (\Exception $e) {
            return ResponseService::customResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
        }
    }

    public function updateSchedule(Request $request, $scheduleId)
    {
        try {
            // Validate input
            $validatedData = $request->validate([
                'departure_time' => 'required',
                'arrival_time' => 'required',
                'ticket_price' => 'required|numeric|min:0',
            ]);

            // Tìm schedule theo ID
            $schedule = RouteSchedule::find($scheduleId);

            if (!$schedule) {
                return ResponseService::customResponse(null, 'Schedule not found.', 404);
            }

            // Cập nhật thông tin
            $schedule->update([
                'departure_time' => $validatedData['departure_time'],
                'arrival_time' => $validatedData['arrival_time'],
                'ticket_price' => $validatedData['ticket_price'],
            ]);

            return ResponseService::customResponse($schedule, 'Schedule updated successfully.', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return ResponseService::customResponse(null, 'An error occurred: ' . $e->getMessage(), 500);
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

            $route = Route::where('id', $id)->where('garage_id', $Garage_id)->first();

            if (!$route) {
                return ResponseService::customResponse(null, 'Route not found or does not belong to this garage.', 404);
            }

            if ($route->route_status_id == 2) {
                return ResponseService::customResponse(null, 'Route is already inactive.', 400);
            }

            // Cập nhật route status
            $route->update([
                'route_status_id' => 2,
                'updated_at' => Carbon::now(),
            ]);

            // Cập nhật tất cả schedules về trạng thái 2
            RouteSchedule::where('route_id', $route->id)->update([
                'schedule_status_id' => 3,
                'updated_at' => Carbon::now(),
            ]);

            return ResponseService::customResponse(null, 'Route and all schedules marked as inactive.', 200);
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

            $route = Route::where('id', $id)
                ->where('garage_id', $Garage_id)
                ->first();

            if (!$route) {
                return ResponseService::customResponse(null, 'Route not found or does not belong to this garage.', 404);
            }

            if ($route->route_status_id == 1) {
                return ResponseService::customResponse(null, 'Route is already active.', 400);
            }

            $isDriverBusy = Route::where('driver_id', $route->driver_id)
                ->where('id', '!=', $route->id)
                ->where('route_status_id', 1)
                ->exists();

            if ($isDriverBusy) {
                return ResponseService::customResponse(null, 'Driver is currently assigned to another active route.', 400);
            }

            $route->update([
                'route_status_id' => 1,
                'updated_at' => Carbon::now(),
            ]);

            return ResponseService::customResponse(null, 'Route restored successfully.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function softDeleteSchedule($id)
    {
        try {
            $schedule = RouteSchedule::find($id);


            if (!$schedule) {
                return ResponseService::customResponse(null, 'Schedule not found.', 404);
            }

            if ($schedule->schedule_status_id == 2) {
                return ResponseService::customResponse(null, 'Schedule is already inactive.', 400);
            }

            $schedule->update([
                'schedule_status_id' => 3,
                'updated_at' => Carbon::now(),
            ]);

            return ResponseService::customResponse(null, 'Schedule marked as inactive.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function restoreSchedule($id)
    {
        try {
            $schedule = RouteSchedule::find($id);

            $route = Route::where('id', $schedule->route_id)
                ->first();

            if ($route->route_status_id == 2) {
                return ResponseService::customResponse(null, 'Route đang bị vô hiệu hóa.', 403);
            }


            if (!$schedule) {
                return ResponseService::customResponse(null, 'Schedule not found.', 404);
            }

            if ($schedule->schedule_status_id == 1) {
                return ResponseService::customResponse(null, 'Schedule is already active.', 400);
            }

            $schedule->update([
                'schedule_status_id' => 1,
                'updated_at' => Carbon::now(),
            ]);

            return ResponseService::customResponse(null, 'Schedule restored successfully.', 200);
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
