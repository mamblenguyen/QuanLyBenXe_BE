<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\Ticket\StoreTicketRequest;
use App\Models\Route;
use App\Repositories\TicketRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected $ticketRepository;

    public function __construct(TicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $route_id = $request->input('route_id');
        $seat_numbers = $request->input('seat_number', []);
        $date = $request->input('date');
        $ticket_price = Route::findOrFail($route_id)->ticket_price;

        // Kiểm tra nếu ghế đã được đặt cho cùng tuyến và ngày
        if ($this->ticketRepository->areSeatsBooked($route_id, $seat_numbers, $date)) {
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
        $this->ticketRepository->bookSeats($route_id, $seat_numbers, $date);

        // Tạo vé
        $ticket = $this->ticketRepository->createTicket($ticketData, $seat_numbers);

        return response()->json(['ticket' => $ticket, 'message' => 'Tickets booked successfully'], 201);
    }

    public function getBookedSeats(Request $request, $route_id): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());
    
        $bookedSeats = $this->ticketRepository->getBookedSeatsByRoute($route_id, $date);
    
        return response()->json(['booked_seats' => $bookedSeats], 200);
    }
    

    public function checkSeats(Request $request, $route_id): JsonResponse
    {
        $date = $request->input('date');
        $route = Route::with('bus')->findOrFail($route_id);
        $seatCapacity = $route->bus->busModel->capacity;

        $bookedSeats = $this->ticketRepository->getBookedSeatsByRoute($route_id, $date)->pluck('seat_number')->toArray();
        $allSeats = range(1, $seatCapacity);
        $availableSeats = array_diff($allSeats, $bookedSeats);

        return response()->json([
            'booked_seats' => $bookedSeats,
            'available_seats' => array_values($availableSeats),
        ]);
    }

    public function checkSeatAvailability(Request $request, $route_id): JsonResponse
    {
        // Lấy ngày hiện tại
        $date = now()->toDateString();

        // Lấy thông tin về tình trạng ghế
        $availability = $this->ticketRepository->getSeatAvailability($route_id, $date);

        return response()->json([
            'route_id' => $route_id,
            'booked_seats' => $availability['booked'],
            'available_seats' => $availability['available'],
            'total_seats' => $availability['total'],
        ]);
    }
}
