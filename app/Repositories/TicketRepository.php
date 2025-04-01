<?php
namespace App\Repositories;

use App\Models\BookedSeat;
use App\Models\Route;
use App\Models\Ticket;
use App\Models\TicketSeat;
use Illuminate\Database\Eloquent\Collection;

class TicketRepository
{
    protected $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function areSeatsBooked($route_id, array $seat_numbers, $date): bool
    {
        // Kiểm tra ghế đã đặt cho cùng tuyến và ngày
        return BookedSeat::where('route_id', $route_id)
            ->whereIn('seat_number', $seat_numbers)
            ->where('date', $date)
            ->exists();
    }

    public function bookSeats($route_id, array $seat_numbers, $date): void
    {
        if (empty($seat_numbers)) {
            return;
        }

        // Prepare the data for bulk insert into BookedSeat table
        $seatsToBook = array_map(function ($seat_number) use ($route_id, $date) {
            return [
                'route_id' => $route_id,
                'seat_number' => $seat_number,
                'date' => $date,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $seat_numbers);

        BookedSeat::insert($seatsToBook);
    }

    public function createTicket(array $data, array $seat_numbers): Ticket
    {
        // Remove seat_number from the ticket data
        unset($data['seat_number']); // Remove the seat_number key from data

        // Create the ticket
        $ticket = $this->ticket->create($data);

        // Prepare the seat data to insert
        $seatsToInsert = array_map(function ($seat_number) use ($ticket) {
            return [
                'ticket_id' => $ticket->id,
                'seat_number' => $seat_number,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $seat_numbers);

        // Insert the seat numbers into the TicketSeat table
        TicketSeat::insert($seatsToInsert);

        return $ticket;
    }

    public function getSeatAvailability($route_id, $date)
    {
        $route = Route::with('bus.busModel')->findOrFail($route_id);

        // Get all booked seats for the given route and date
        $bookedSeats = BookedSeat::where('route_id', $route_id)
            ->where('date', $date)
            ->pluck('seat_number')
            ->toArray();

        // Get total seat capacity
        $totalSeats = $route->bus->busModel->capacity;

        // Create an array of all seat numbers
        $allSeats = range(1, $totalSeats);

        // Calculate available seats
        $availableSeats = array_diff($allSeats, $bookedSeats);

        return [
            'booked' => $bookedSeats,
            'available' => array_values($availableSeats), // Re-index the array
            'total' => $totalSeats,
        ];
    }

    public function getBookedSeatsByRoute($route_id, $date): Collection
    {
        return BookedSeat::where('route_id', $route_id)
            ->where('date', $date)
            ->get();
    }

    public function calculateTotalPrice($ticket_price, $quantity): float
    {
        return $ticket_price * $quantity;
    }
}
