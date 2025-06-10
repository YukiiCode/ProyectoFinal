<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ReservationController extends Controller
{
    // Crear una nueva reserva (POST /api/reservations)
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => ['required', 'exists:tables,id'],
            'reservation_date' => ['required', 'date', 'after:now'],
            'party_size' => ['required', 'integer', 'min:1'],
        ]);

        $table = Table::findOrFail($request->table_id);

        // Verifica que la mesa esté disponible en ese momento
        $alreadyReserved = Reservation::where('table_id', $table->id)
            ->where('reservation_date', $request->reservation_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($table->status !== 'available' || $alreadyReserved) {
            return response()->json(['message' => 'La mesa no está disponible para esa fecha y hora.'], 422);
        }

        // Crea la reserva
        $reservation = Reservation::create([
            'client_id' => Auth::id() ?? $request->user()->id ?? 1, // Ajusta según tu lógica de cliente
            'table_id' => $table->id,
            'reservation_date' => $request->reservation_date,
            'party_size' => $request->party_size,
            'status' => 'pending',
            'discount_coupon_id' => $request->discount_coupon_id ?? null,
        ]);

        // Actualiza el estado de la mesa a 'reserved'
        $table->status = 'reserved';
        $table->save();

        return response()->json([
            'message' => 'Reserva realizada correctamente.',
            'reservation' => $reservation,
        ]);
    }

    /**
     * Store a new reservation from admin panel
     */
    public function adminStore(Request $request): JsonResponse
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'table_id' => 'required|exists:tables,id',
            'reservation_date' => 'required|date',
            'party_size' => 'required|integer|min:1|max:20',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        // Check if client exists, if not create one
        $client = Client::firstOrCreate(
            ['email' => $request->client_email],
            ['name' => $request->client_name]
        );

        $table = Table::findOrFail($request->table_id);

        // Check table availability for the date/time
        $conflictingReservation = Reservation::where('table_id', $table->id)
            ->where('reservation_date', $request->reservation_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($conflictingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'La mesa ya tiene una reserva confirmada para esa fecha y hora'
            ], 400);
        }

        $reservation = Reservation::create([
            'client_id' => $client->id,
            'table_id' => $request->table_id,
            'reservation_date' => $request->reservation_date,
            'party_size' => $request->party_size,
            'status' => $request->status,
        ]);

        $reservation->load(['client', 'table']);        return response()->json([
            'success' => true,
            'message' => __('reservations.created_successfully'),
            'reservation' => [
                'id' => $reservation->id,
                'client_name' => $reservation->client->name,
                'client_email' => $reservation->client->email,
                'table_number' => $reservation->table->table_number,
                'reservation_date' => $reservation->reservation_date,
                'party_size' => $reservation->party_size,
                'status' => $reservation->status,
                'created_at' => $reservation->created_at,
                'updated_at' => $reservation->updated_at,
            ]
        ]);
    }

    /**
     * Update the specified reservation
     */
    public function update(Request $request, Reservation $reservation): JsonResponse
    {
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'table_id' => 'required|exists:tables,id',
            'reservation_date' => 'required|date',
            'party_size' => 'required|integer|min:1|max:20',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        // Update or create client
        $client = Client::updateOrCreate(
            ['email' => $request->client_email],
            ['name' => $request->client_name]
        );

        // Check table availability if changing table or date
        if ($request->table_id != $reservation->table_id || $request->reservation_date != $reservation->reservation_date) {
            $conflictingReservation = Reservation::where('table_id', $request->table_id)
                ->where('reservation_date', $request->reservation_date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('id', '!=', $reservation->id)
                ->exists();

            if ($conflictingReservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'La mesa ya tiene una reserva confirmada para esa fecha y hora'
                ], 400);
            }
        }

        $reservation->update([
            'client_id' => $client->id,
            'table_id' => $request->table_id,
            'reservation_date' => $request->reservation_date,
            'party_size' => $request->party_size,
            'status' => $request->status,
        ]);

        $reservation->load(['client', 'table']);        return response()->json([
            'success' => true,
            'message' => __('reservations.updated_successfully'),
            'reservation' => [
                'id' => $reservation->id,
                'client_name' => $reservation->client->name,
                'client_email' => $reservation->client->email,
                'table_number' => $reservation->table->table_number,
                'reservation_date' => $reservation->reservation_date,
                'party_size' => $reservation->party_size,
                'status' => $reservation->status,
                'created_at' => $reservation->created_at,
                'updated_at' => $reservation->updated_at,
            ]
        ]);
    }

    /**
     * Remove the specified reservation
     */
    public function destroy(Reservation $reservation): JsonResponse
    {
        $reservation->delete();        return response()->json([
            'success' => true,
            'message' => __('reservations.deleted_successfully')
        ]);
    }

    /**
     * Update reservation status
     */
    public function updateStatus(Request $request, Reservation $reservation): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $reservation->update(['status' => $request->status]);
        $reservation->load(['client', 'table']);        return response()->json([
            'success' => true,
            'message' => __('reservations.status_updated_successfully'),
            'reservation' => [
                'id' => $reservation->id,
                'client_name' => $reservation->client->name,
                'client_email' => $reservation->client->email,
                'table_number' => $reservation->table->table_number,
                'reservation_date' => $reservation->reservation_date,
                'party_size' => $reservation->party_size,
                'status' => $reservation->status,
                'created_at' => $reservation->created_at,
                'updated_at' => $reservation->updated_at,
            ]
        ]);
    }

    /**
     * Get available tables for a specific date and time
     */
    public function getAvailableTables(Request $request): JsonResponse
    {
        $request->validate([
            'reservation_date' => 'required|date',
        ]);

        $reservedTableIds = Reservation::where('reservation_date', $request->reservation_date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('table_id');

        $availableTables = Table::whereNotIn('id', $reservedTableIds)
            ->where('status', 'available')
            ->orderBy('table_number')
            ->get()
            ->map(function ($table) {
                return [
                    'id' => $table->id,
                    'table_number' => $table->table_number,
                    'capacity' => $table->capacity,
                ];
            });

        return response()->json([
            'success' => true,
            'tables' => $availableTables
        ]);
    }
}
