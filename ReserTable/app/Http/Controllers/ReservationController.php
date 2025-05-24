<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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
}
