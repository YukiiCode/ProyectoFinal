<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\Client;
use App\Mail\ReservationConfirmation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Inertia\Inertia;

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
    }    /**
     * Store a new reservation from admin panel
     */
    public function adminStore(Request $request)
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
            return back()->withErrors([
                'reservation_date' => 'La mesa ya tiene una reserva confirmada para esa fecha y hora'
            ]);
        }

        $reservation = Reservation::create([
            'client_id' => $client->id,
            'table_id' => $request->table_id,
            'reservation_date' => $request->reservation_date,
            'party_size' => $request->party_size,
            'status' => $request->status,
        ]);

        // Send confirmation email
        try {
            Mail::to($client->email)->send(new ReservationConfirmation($reservation, $client));
        } catch (\Exception $e) {
            Log::warning('Failed to send reservation confirmation email', [
                'error' => $e->getMessage(),
                'reservation_id' => $reservation->id
            ]);
        }        return redirect()->route('admin.reservations')->with('success', 'Reserva creada correctamente');
    }

    /**
     * Update the specified reservation
     */
    public function update(Request $request, Reservation $reservation)
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
                ->exists();            if ($conflictingReservation) {
                return back()->withErrors([
                    'reservation_date' => 'La mesa ya tiene una reserva confirmada para esa fecha y hora'
                ]);
            }
        }

        $reservation->update([
            'client_id' => $client->id,
            'table_id' => $request->table_id,
            'reservation_date' => $request->reservation_date,
            'party_size' => $request->party_size,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.reservations')->with('success', 'Reserva actualizada correctamente');
    }

    /**
     * Remove the specified reservation
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();        return redirect()->route('admin.reservations')->with('success', 'Reserva eliminada correctamente');
    }

    /**
     * Update reservation status
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $reservation->update(['status' => $request->status]);        return redirect()->route('admin.reservations')->with('success', 'Estado de reserva actualizado correctamente');
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
            });        return response()->json([
            'success' => true,
            'tables' => $availableTables
        ]);
    }    /**
     * Store a public reservation (for authenticated users)
     * This method will be intercepted by middleware if user is not authenticated
     */    public function publicStore(Request $request)
    {        // Validación básica
        $validationRules = [
            'table_id' => 'required|exists:tables,id',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required|date_format:H:i',
            'party_size' => 'required|integer|min:1',
            'duration_hours' => 'integer|min:1|max:4',
            'special_requests' => 'nullable|string|max:500',
            'discount_coupon_id' => 'nullable|exists:discount_coupons,id',
        ];

        // Si el usuario no está autenticado, requerir datos del cliente
        if (!Auth::check() && !Auth::guard('client')->check()) {
            $validationRules['client_name'] = 'required|string|max:255';
            $validationRules['client_email'] = 'required|email|max:255';
        }

        $request->validate($validationRules);

        // Combinar fecha y hora y validar que sea al menos 1 hora en el futuro
        $reservationDateTime = Carbon::parse($request->reservation_date . ' ' . $request->reservation_time);
        
        if ($reservationDateTime->lte(now()->addHour())) {
            if ($request->header('X-Inertia')) {
                return back()->withErrors([
                    'message' => 'La reserva debe ser al menos 1 hora en el futuro'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'La reserva debe ser al menos 1 hora en el futuro'
            ], 400);
        }

        $table = Table::findOrFail($request->table_id);

        // Check table availability for the date/time
        $conflictingReservation = Reservation::where('table_id', $table->id)
            ->where('reservation_date', $reservationDateTime->format('Y-m-d'))
            ->where('reservation_time', $reservationDateTime->format('H:i:s'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($conflictingReservation) {
            if ($request->header('X-Inertia')) {
                return back()->withErrors([
                    'message' => 'La mesa ya tiene una reserva confirmada para esa fecha y hora'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'La mesa ya tiene una reserva confirmada para esa fecha y hora'
            ], 400);
        }// Determinar el cliente
        $clientId = null;
        if (Auth::guard('client')->check()) {
            // Cliente autenticado
            $clientId = Auth::guard('client')->user()->id;
        } elseif (Auth::check()) {
            // Usuario autenticado (admin/empleado) - crear un cliente temporal o usar su info
            $user = Auth::user();
            // Si es admin/empleado haciendo una reserva, crear un cliente con sus datos
            $client = Client::firstOrCreate(
                ['email' => $user->email],
                [
                    'name' => $user->name,
                    'phone' => null // Los Users no tienen phone por defecto
                ]
            );
            $clientId = $client->id;
        } else {
            // Usuario no autenticado, crear o encontrar cliente
            $client = Client::firstOrCreate(
                ['email' => $request->client_email],
                ['name' => $request->client_name]
            );
            $clientId = $client->id;
        }        $reservation = Reservation::create([
            'client_id' => $clientId,
            'table_id' => $request->table_id,
            'reservation_date' => $reservationDateTime->format('Y-m-d'),
            'reservation_time' => $reservationDateTime->format('H:i:s'),
            'party_size' => $request->party_size,
            'duration_hours' => $request->duration_hours ?? 1,
            'status' => 'pending',
            'discount_coupon_id' => $request->discount_coupon_id,
            'special_requests' => $request->special_requests ?? null,
        ]);

        $reservation->load(['client', 'table', 'discountCoupon']);

        // Enviar correo de confirmación
        try {
            Mail::to($reservation->client->email)->send(new ReservationConfirmation($reservation));
        } catch (\Exception $e) {
            Log::error('Error enviando correo de confirmación de reserva: ' . $e->getMessage());
        }

        // Para peticiones Inertia, redirigir con mensaje de éxito
        if ($request->header('X-Inertia')) {
            return back()->with('flash', [
                'type' => 'success',
                'title' => 'Reserva confirmada',
                'message' => 'Tu reserva ha sido creada exitosamente. ¡Te esperamos!'
            ]);
        }

        // Para peticiones AJAX/API
        return response()->json([
            'success' => true,
            'message' => 'Reserva creada exitosamente',
            'reservation' => [
                'id' => $reservation->id,
                'client_name' => $reservation->client->name,
                'client_email' => $reservation->client->email,
                'table_number' => $reservation->table->table_number,
                'reservation_date' => $reservation->reservation_date,
                'party_size' => $reservation->party_size,
                'status' => $reservation->status,
                'created_at' => $reservation->created_at,
            ]
        ]);
    }

    /**
     * Obtener las horas disponibles para una mesa en una fecha específica
     */    public function getAvailableHours(Request $request)
    {
        $request->validate([
            'table_id' => ['required', 'exists:tables,id'],
            'date' => ['required', 'date']
        ]);

        $table = Table::findOrFail($request->table_id);
        $date = Carbon::parse($request->date);
        
        // Horas de operación del restaurante (9:00 AM a 11:00 PM)
        $operatingHours = [];
        for ($hour = 9; $hour <= 23; $hour++) {
            $operatingHours[] = sprintf('%02d:00', $hour);
        }        // Obtener reservas existentes para esa mesa y fecha
        $existingReservations = Reservation::where('table_id', $table->id)
            ->whereDate('reservation_date', $date->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        $availableHours = [];
        $now = Carbon::now();        // Log básico para debugging si es necesario
        Log::info('Available hours calculated', [
            'table_id' => $table->id,
            'date' => $date->format('Y-m-d'),
            'reservations_found' => $existingReservations->count(),
            'available_hours_count' => collect($availableHours)->where('available', true)->count()
        ]);
          foreach ($operatingHours as $hour) {
            $slotDateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $hour);
            
            // Verificar si es una hora pasada (solo para hoy) - necesita al menos 1 hora de anticipación
            $isPastTime = false;
            if ($date->isToday()) {
                $oneHourFromNow = $now->copy()->addHour();
                $isPastTime = $slotDateTime->lte($oneHourFromNow);
            }
              // Verificar si ya existe una reserva en esta hora
            // Convertir el formato de hora para comparación (HH:MM vs HH:MM:SS)
            $isReserved = false;
            $hourWithSeconds = $hour . ':00'; // Convertir "09:00" a "09:00:00"
            
            foreach ($existingReservations as $reservation) {
                if ($reservation->reservation_time === $hourWithSeconds) {
                    $isReserved = true;
                    break;
                }
            }            $available = !$isReserved && !$isPastTime && $table->status === 'available';
            
            $availableHours[] = [
                'time' => $hour,
                'available' => $available,
                'display' => $this->formatTimeDisplay($hour),
                'reason' => $isPastTime ? 'past_time' : ($isReserved ? 'reserved' : null)
            ];
        }        return response()->json([
            'hours' => $availableHours,
            'table_info' => [
                'id' => $table->id,
                'number' => $table->table_number,
                'capacity' => $table->capacity
            ]
        ]);
    }

    /**
     * Formatear la hora para mostrar en formato 24h
     */
    private function formatTimeDisplay($hour)
    {
        return $hour; // Devolver directamente en formato 24h (ej: "09:00", "14:00", "23:00")
    }
}
