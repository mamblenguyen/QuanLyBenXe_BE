<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\Ticket\StoreTicketRequest;
use App\Http\Resources\System\Routes\RouteResource;
use App\Http\Resources\System\Ticket\TicketDetailResource;
use App\Http\Resources\System\Ticket\TicketResource;
use App\Models\Route;
use App\Models\RouteSchedule;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TicketController extends Controller
{
    protected $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function list(Request $request)
    {
        try {
            $garage_id = $request->garage_id;

            $tickets = Ticket::whereHas('schedule.route', function ($query) use ($garage_id) {
                $query->where('garage_id', $garage_id);
            })->get();

            if ($tickets->isEmpty()) {
                return ResponseService::customResponse(null, 'No ticket found for this garage.', 404);
            }

            return ResponseService::customResponse(TicketResource::collection($tickets), 'Data retrieved successfully.', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


public function detail($id, Request $request)
{
    try {
        $garage_id = $request->garage_id;

        $ticket = Ticket::where('id', $id)
            ->whereHas('schedule.route', function ($query) use ($garage_id) {
                $query->where('garage_id', $garage_id);
            })
            ->first();

        if (!$ticket) {
            return ResponseService::customResponse(null, 'Ticket not found for this garage.', 404);
        }

        return ResponseService::customResponse(TicketDetailResource::make($ticket), 'Data retrieved successfully.', 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ], 500);
    }
}



    public function store(StoreTicketRequest $request): JsonResponse
    {
        $schedule_id = $request->input('schedule_id');
        $seat_numbers = $request->input('seat_number', []);
        $date = $request->input('date');
        $ticket_price = RouteSchedule::findOrFail($schedule_id)->ticket_price;
        // Kiểm tra nếu ghế đã được đặt cho cùng tuyến và ngày
        if ($this->ticketRepository->areSeatsBooked($schedule_id, $seat_numbers, $date)) {
            return response()->json(['message' => 'Some of the selected seats are already booked'], 400);
        }

        // Tính toán giá vé
        $total_price = $this->ticketRepository->calculateTotalPrice($ticket_price, count($seat_numbers));

        // Tạo dữ liệu cho vé
        $ticketData = array_merge($request->validated(), [
            'price_paid' => $total_price,
            'date' => $date,
        ]);

        // Đặt ghế
        $this->ticketRepository->bookSeats($schedule_id, $seat_numbers, $date);

        // Tạo vé
        $ticket = $this->ticketRepository->createTicket($ticketData, $seat_numbers);

        return response()->json(['ticket' => $ticket, 'message' => 'Tickets booked successfully'], 201);
    }

    public function getBookedSeats(Request $request, $schedule_id): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        $bookedSeats = $this->ticketRepository->getBookedSeatsByRoute($schedule_id, $date);

        return response()->json(['booked_seats' => $bookedSeats], 200);
    }


    public function checkSeats(Request $request, $schedule_id): JsonResponse
    {
        $date = $request->input('date');
        $route = Route::with('bus')->findOrFail($schedule_id);
        $seatCapacity = $route->bus->busModel->capacity;

        $bookedSeats = $this->ticketRepository->getBookedSeatsByRoute($schedule_id, $date)->pluck('seat_number')->toArray();
        $allSeats = range(1, $seatCapacity);
        $availableSeats = array_diff($allSeats, $bookedSeats);

        return response()->json([
            'booked_seats' => $bookedSeats,
            'available_seats' => array_values($availableSeats),
        ]);
    }

    public function checkSeatAvailability($schedule_id, $date): JsonResponse
    {
        // $date = now()->toDateString();
        $schedule = RouteSchedule::find($schedule_id);
        $route_id = $schedule->route->id;
        $availability = $this->ticketRepository->getSeatAvailability($route_id, $schedule_id, $date);
        return response()->json([
            'schedule_id' => $schedule_id,
            'booked_seats' => $availability['booked'],
            'available_seats' => $availability['available'],
            'total_seats' => $availability['total'],
        ]);
    }

    //Revenue
   public function revenueByTimeRange(Request $request)
{
    $range = $request->input('range');
    $garage_id = $request->garage_id;

    if (!in_array($range, ['year', 'last_year', 'month', 'last_month', 'week', 'last_week'])) {
        return response()->json([
            'message' => 'Invalid range type. Allowed values: year, last_year, month, last_month, week, last_week.'
        ], 422);
    }

    try {
        $revenue = $this->ticketRepository->getRevenueByTimeRange($range, $garage_id);
        return ResponseService::customResponse([
                 'range' => $range,
            'revenue' => $revenue
            ], 'Data retrieved successfully.', 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error calculating revenue: ' . $e->getMessage()
        ], 500);
    }
}
public function revenueByDateRange(Request $request)
{
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');
    $garage_id = $request->query('garage_id');

    if (!$startDate || !$endDate) {
        return response()->json(['message' => 'Missing start_date or end_date'], 422);
    }

    try {
        $revenues = $this->ticketRepository->getDailyRevenueByDateRange($startDate, $endDate, $garage_id);
        return ResponseService::customResponse($revenues, 'Revenue by date retrieved successfully.', 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}

}
