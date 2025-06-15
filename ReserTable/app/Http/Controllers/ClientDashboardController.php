<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Reservation;
use App\Models\Client;
use Carbon\Carbon;

class ClientDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    /**
     * Helper method to safely create a Carbon instance from reservation date and time
     */
    private function getReservationDateTime($reservation)
    {
        try {
            // Ensure reservation_date is a date and reservation_time is a time string
            $date = $reservation->reservation_date instanceof \Carbon\Carbon 
                ? $reservation->reservation_date->format('Y-m-d')
                : Carbon::parse($reservation->reservation_date)->format('Y-m-d');
            
            $time = is_string($reservation->reservation_time) 
                ? $reservation->reservation_time 
                : $reservation->reservation_time;
            
            // Create full datetime string
            $dateTimeString = $date . ' ' . $time;
            
            return Carbon::parse($dateTimeString);
        } catch (\Exception $e) {
            // Fallback: use reservation_date only
            return Carbon::parse($reservation->reservation_date);
        }
    }

    public function index()
    {
        $client = Auth::guard('client')->user();        // Obtener estadísticas del cliente
        $stats = [
            'total_reservations' => $client->reservations()->count(),
            'pending_reservations' => $client->reservations()->where('status', 'pending')->count(),
            'confirmed_reservations' => $client->reservations()->where('status', 'confirmed')->count(),
            'cancelled_reservations' => $client->reservations()->where('status', 'cancelled')->count(),
            'completed_reservations' => $client->reservations()->where('status', 'completed')->count(),
        ];        // Obtener reservas recientes
        $recent_reservations = $client->reservations()
            ->with(['table'])
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'table' => $reservation->table,
                    'reservation_date' => $reservation->reservation_date,
                    'reservation_time' => $reservation->reservation_time,
                    'party_size' => $reservation->party_size,
                    'status' => $reservation->status,                    'special_requests' => $reservation->special_requests,
                    'can_cancel' => $reservation->status === 'pending' && 
                                  $this->getReservationDateTime($reservation)->isFuture(),
                ];
            });

        // Obtener próximas reservas
        $upcoming_reservations = $client->reservations()
            ->with(['table'])
            ->where('reservation_date', '>=', Carbon::today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('reservation_date', 'asc')
            ->orderBy('reservation_time', 'asc')
            ->limit(3)
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'table' => $reservation->table,
                    'reservation_date' => $reservation->reservation_date,
                    'reservation_time' => $reservation->reservation_time,
                    'party_size' => $reservation->party_size,
                    'status' => $reservation->status,                    'special_requests' => $reservation->special_requests,
                    'can_cancel' => $reservation->status === 'pending' && 
                                  $this->getReservationDateTime($reservation)->isFuture(),
                ];
            });        return Inertia::render('Client/Dashboard', [
            'client' => $client,
            'stats' => $stats,
            'recent_reservations' => $recent_reservations,
            'upcoming_reservations' => $upcoming_reservations,
        ]);
    }

    public function account()
    {
        $client = Auth::guard('client')->user();

        // Obtener todas las reservas del cliente con información de cancelación
        $reservations = $client->reservations()
            ->with(['table'])
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'table' => $reservation->table,
                    'reservation_date' => $reservation->reservation_date,
                    'reservation_time' => $reservation->reservation_time,
                    'party_size' => $reservation->party_size,
                    'status' => $reservation->status,                    'special_requests' => $reservation->special_requests,
                    'can_cancel' => $reservation->status === 'pending' && 
                                  $this->getReservationDateTime($reservation)->isFuture(),
                ];
            });

        return Inertia::render('Client/Account', [
            'client' => $client,
            'reservations' => $reservations,
        ]);
    }

    public function profile()
    {
        $client = Auth::guard('client')->user();
        
        return Inertia::render('Client/Profile', [
            'client' => $client,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $client = Auth::guard('client')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $client->update($request->only(['name', 'email', 'phone']));

        return redirect()->back()->with('success', __('Perfil actualizado correctamente.'));
    }    public function updateEmailPreferences(Request $request)
    {
        $client = Auth::guard('client')->user();
        
        $request->validate([
            'promotional_emails' => 'boolean',
            'reservation_reminders' => 'boolean',
        ]);

        $client->update([
            'promotional_emails' => $request->promotional_emails ?? false,
            'reservation_reminders' => $request->reservation_reminders ?? false,
        ]);

        return redirect()->back()->with('success', 'Preferencias de email actualizadas correctamente.');
    }    public function requestAccountDeletion(Request $request)
    {
        $client = Auth::guard('client')->user();
        
        $request->validate([
            'deletion_reason' => 'required|string|max:500',
        ]);
        
        $client->update([
            'account_deletion_requested_at' => Carbon::now(),
            'deletion_reason' => $request->deletion_reason,
        ]);

        return redirect()->back()->with('success', 'Solicitud de eliminación enviada. Nuestro equipo se pondrá en contacto contigo.');
    }

    public function cancelAccountDeletion(Request $request)
    {
        $client = Auth::guard('client')->user();
        
        $client->update([
            'account_deletion_requested_at' => null,
            'deletion_reason' => null,
        ]);

        return redirect()->back()->with('success', 'Solicitud de eliminación cancelada correctamente.');
    }
}
