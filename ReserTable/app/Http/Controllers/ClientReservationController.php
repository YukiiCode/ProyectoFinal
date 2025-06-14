<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;

class ClientReservationController extends Controller
{
    /**
     * Mostrar el formulario de reserva
     */
    public function create()
    {
        // Obtener todas las mesas para mostrar en el mapa
        $tables = Table::all()->map(function ($table) {
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'position_x' => $table->position_x ?? 50,
                'position_y' => $table->position_y ?? 50,
            ];
        });

        return Inertia::render('Reserva', [
            'tables' => $tables,
            'availableTimes' => $this->getAvailableTimes(),
        ]);
    }

    /**
     * Crear una nueva reserva desde el cliente
     */
    public function store(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'reservation_date' => 'required|date|after:now',
            'reservation_time' => 'required|date_format:H:i',
            'party_size' => 'required|integer|min:1|max:20',
            'discount_coupon_id' => 'nullable|exists:discount_coupons,id',
            'special_requests' => 'nullable|string|max:500'
        ]);

        $client = Auth::guard('client')->user();
        $dateTime = Carbon::parse($request->reservation_date . ' ' . $request->reservation_time);
        $table = Table::findOrFail($request->table_id);

        // Verificar que la mesa esté disponible
        if ($table->status !== 'available') {
            return back()->withErrors([
                'table_id' => 'La mesa seleccionada no está disponible.'
            ]);
        }

        // Verificar que no hay conflictos de reserva en esa fecha/hora
        $conflictingReservation = Reservation::where('table_id', $table->id)
            ->where('reservation_date', $dateTime)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($conflictingReservation) {
            return back()->withErrors([
                'reservation_time' => 'La mesa ya tiene una reserva confirmada para esa fecha y hora.'
            ]);
        }

        // Verificar capacidad de la mesa
        if ($request->party_size > $table->capacity) {
            return back()->withErrors([
                'party_size' => "La mesa seleccionada tiene capacidad para {$table->capacity} personas. Usted solicita para {$request->party_size} personas."
            ]);
        }

        // Validar cupón de descuento si se proporciona
        $discountCoupon = null;
        if ($request->discount_coupon_id) {
            $discountCoupon = DiscountCoupon::find($request->discount_coupon_id);
            
            if (!$discountCoupon || !$this->isValidCoupon($discountCoupon, $client->id)) {
                return back()->withErrors([
                    'discount_coupon_id' => 'El cupón de descuento no es válido o ha expirado.'
                ]);
            }
        }

        // Crear la reserva
        $reservation = Reservation::create([
            'client_id' => $client->id,
            'table_id' => $table->id,
            'reservation_date' => $dateTime,
            'party_size' => $request->party_size,
            'status' => 'pending',
            'discount_coupon_id' => $request->discount_coupon_id,
            'special_requests' => $request->special_requests,
        ]);

        // Actualizar estado de la mesa
        $table->update(['status' => 'reserved']);

        // Incrementar uso del cupón si aplica
        if ($discountCoupon) {
            $discountCoupon->increment('used_count');
        }

        return redirect()->route('client.reservations')
            ->with('success', 'Reserva realizada exitosamente. Recibirá una confirmación por email.');
    }

    /**
     * Obtener las reservas del cliente autenticado
     */
    public function index()
    {
        $client = Auth::guard('client')->user();
        
        $reservations = $client->reservations()
            ->with(['table', 'discountCoupon'])
            ->orderBy('reservation_date', 'desc')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'table_number' => $reservation->table->table_number ?? 'Mesa eliminada',
                    'table_capacity' => $reservation->table->capacity ?? 0,
                    'reservation_date' => $reservation->reservation_date,
                    'party_size' => $reservation->party_size,
                    'status' => $reservation->status,
                    'special_requests' => $reservation->special_requests,
                    'discount_coupon' => $reservation->discountCoupon ? [
                        'code' => $reservation->discountCoupon->code,
                        'discount_type' => $reservation->discountCoupon->discount_type,
                        'value' => $reservation->discountCoupon->value
                    ] : null,
                    'created_at' => $reservation->created_at,
                    'can_cancel' => $reservation->status === 'pending' && 
                                   Carbon::parse($reservation->reservation_date)->isFuture()
                ];
            });

        return Inertia::render('Client/MyReservations', [
            'reservations' => $reservations
        ]);
    }

    /**
     * Cancelar una reserva del cliente
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        $client = Auth::guard('client')->user();

        // Verificar que la reserva pertenece al cliente
        if ($reservation->client_id !== $client->id) {
            return back()->withErrors([
                'error' => 'No tiene permisos para cancelar esta reserva.'
            ]);
        }

        // Verificar que la reserva se puede cancelar
        if ($reservation->status !== 'pending') {
            return back()->withErrors([
                'error' => 'Solo se pueden cancelar reservas pendientes.'
            ]);
        }

        // Verificar que la reserva es futura
        if (Carbon::parse($reservation->reservation_date)->isPast()) {
            return back()->withErrors([
                'error' => 'No se pueden cancelar reservas pasadas.'
            ]);
        }

        // Actualizar estado de la reserva
        $reservation->update(['status' => 'cancelled']);

        // Liberar la mesa si no hay otras reservas para el mismo día
        $table = $reservation->table;
        $hasOtherReservations = Reservation::where('table_id', $table->id)
            ->where('id', '!=', $reservation->id)
            ->whereDate('reservation_date', Carbon::parse($reservation->reservation_date)->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if (!$hasOtherReservations) {
            $table->update(['status' => 'available']);
        }

        // Devolver uso del cupón si aplica
        if ($reservation->discount_coupon_id) {
            $reservation->discountCoupon()->decrement('used_count');
        }

        return back()->with('success', 'Reserva cancelada exitosamente.');
    }

    /**
     * Obtener mesas disponibles para una fecha/hora específica
     * Esta función se puede usar vía Inertia para actualizar dinámicamente las mesas disponibles
     */
    public function getAvailableTables(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'party_size' => 'required|integer|min:1|max:20'
        ]);

        $dateTime = Carbon::parse($request->date . ' ' . $request->time);

        // Obtener mesas con capacidad suficiente y disponibles en esa fecha/hora
        $availableTables = Table::where('capacity', '>=', $request->party_size)
            ->where('status', 'available')
            ->whereNotExists(function ($query) use ($dateTime) {
                $query->select('id')
                    ->from('reservations')
                    ->whereRaw('reservations.table_id = tables.id')
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($dateTime) {
                        // Reserva que se solapa con la hora solicitada (2 horas de duración)
                        $q->whereBetween('reservation_date', [
                            $dateTime->copy()->subHours(2),
                            $dateTime->copy()->addHours(2)
                        ]);
                    });
            })
            ->orderBy('capacity')
            ->get()
            ->map(function ($table) {
                return [
                    'id' => $table->id,
                    'table_number' => $table->table_number,
                    'capacity' => $table->capacity,
                    'position_x' => $table->position_x ?? 50,
                    'position_y' => $table->position_y ?? 50,
                    'status' => 'available'
                ];
            });

        return response()->json([
            'tables' => $availableTables,
            'requested_datetime' => $dateTime->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Validar si un cupón es válido para un cliente
     */
    private function isValidCoupon($coupon, $clientId)
    {
        $now = now();
        
        // Verificar fechas de validez
        if ($now < $coupon->valid_from || $now > $coupon->valid_to) {
            return false;
        }
        
        // Verificar usos máximos
        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return false;
        }
        
        // Si es personalizado, verificar que sea para este cliente
        if ($coupon->type === 'personalized' && $coupon->client_id !== $clientId) {
            return false;
        }
        
        return true;
    }

    /**
     * Obtener horarios disponibles
     */
    private function getAvailableTimes()
    {
        $times = [];
        $start = Carbon::createFromTime(12, 0); // 12:00 PM
        $end = Carbon::createFromTime(22, 0);   // 10:00 PM

        while ($start <= $end) {
            $times[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        return $times;
    }
}
