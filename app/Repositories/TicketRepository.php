<?php
namespace App\Repositories;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

    public function areSeatsBooked($schedule_id, array $seat_numbers, $date): bool
    {
        // Kiểm tra ghế đã đặt cho cùng tuyến và ngày
        return BookedSeat::where('schedule_id', $schedule_id)
            ->whereIn('seat_number', $seat_numbers)
            ->where('date', $date)
            ->exists();
    }

    public function bookSeats($schedule_id, array $seat_numbers, $date): void
    {
        if (empty($seat_numbers)) {
            return;
        }

        // Prepare the data for bulk insert into BookedSeat table
        $seatsToBook = array_map(function ($seat_number) use ($schedule_id, $date) {
            return [
                'schedule_id' => $schedule_id,
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

    public function getSeatAvailability($route_id ,$schedule_id, $date)
    {
        $route = Route::with('bus.busModel')->findOrFail($route_id);
        // Get all booked seats for the given route and date
        $bookedSeats = BookedSeat::where('schedule_id', $schedule_id)
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

    public function getBookedSeatsByRoute($schedule_id, $date): Collection
    {
        return BookedSeat::where('schedule_id', $schedule_id)
            ->where('date', $date)
            ->get();
    }

    public function calculateTotalPrice($ticket_price, $quantity): float
    {
        return $ticket_price * $quantity;
    }


    // Revunue 

  public function getRevenueByTimeRange($range, $garage_id)
{
    $query = Ticket::query()
        ->selectRaw('SUM(price_paid) as total_revenue')
        ->whereHas('schedule.route', function ($q) use ($garage_id) {
            $q->where('garage_id', $garage_id);
        });

    switch ($range) {
        case 'year':
            $query->whereYear('date', Carbon::now()->year);
            break;
        case 'last_year':
            $query->whereYear('date', Carbon::now()->subYear()->year);
            break;
        case 'month':
            $query->whereYear('date', Carbon::now()->year)
                  ->whereMonth('date', Carbon::now()->month);
            break;
        case 'last_month':
            $query->whereYear('date', Carbon::now()->subMonth()->year)
                  ->whereMonth('date', Carbon::now()->subMonth()->month);
            break;
        case 'week':
            $query->whereBetween('date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
            break;
        case 'last_week':
            $query->whereBetween('date', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek(),
            ]);
            break;
        default:
            return 0;
    }

    return $query->value('total_revenue') ?? 0;
}

public function getDailyRevenueByDateRange($startDate, $endDate, $garage_id)
{
    $data = Ticket::query()
        ->selectRaw('DATE(date) as revenue_date, SUM(price_paid) as total_revenue')
        ->whereBetween('date', [$startDate, $endDate])
        ->whereHas('schedule.route', function ($q) use ($garage_id) {
            $q->where('garage_id', $garage_id);
        })
        ->groupBy('revenue_date')
        ->orderBy('revenue_date')
        ->get()
        ->map(function ($item) {
            return [
                'date' => Carbon::parse($item->revenue_date)->format('d/m'),
                'revenue' => (float) $item->total_revenue,
            ];
        });

    return $data;
}

}
