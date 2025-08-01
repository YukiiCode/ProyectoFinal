<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Client;
use App\Models\Table;
use App\Models\Reservation;
use App\Models\Product;
use App\Models\DiscountCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminController extends Controller
{    /**
     * Display the admin dashboard
     */    public function dashboard()
    {        
        // Obtener configuraciones del usuario autenticado
        $user = Auth::user();
        $settings = optional($user)->settings;
          // Estadísticas para el dashboard
        $stats = [
            'total_reservations' => Reservation::count(),
            'today_reservations' => Reservation::whereDate('reservation_date', Carbon::today())->count(),
            'total_tables' => Table::count(),
            'available_tables' => Table::where('status', 'available')->count(),
            'total_users' => User::count(),
            'total_products' => Product::count(),
            // Estadísticas de cupones de descuento
            'total_coupons' => DiscountCoupon::count(),
            'active_coupons' => DiscountCoupon::active()->count(),
            'used_coupons' => DiscountCoupon::used()->count(),            'total_savings' => DiscountCoupon::whereNotNull('used_count')
                ->where('used_count', '>', 0)
                ->get()
                ->sum(function ($coupon) {
                    return $coupon->value * $coupon->used_count;
                }),
        ];
        
        // Reservas recientes
        $recent_reservations = Reservation::with(['client', 'table'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'client_name' => $reservation->client->name ?? 'Cliente eliminado',
                    'client_email' => $reservation->client->email ?? 'N/A',
                    'table_number' => $reservation->table->table_number ?? 'Mesa eliminada',
                    'reservation_date' => $reservation->reservation_date,
                    'party_size' => $reservation->party_size,
                    'status' => $reservation->status,
                    'created_at' => $reservation->created_at,
                ];
            });

        // Reservas por día de la semana actual
        $weeklyReservations = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->startOfWeek()->addDays($i);
            $weeklyReservations[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('l'),
                'count' => Reservation::whereDate('reservation_date', $date)->count()
            ];
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recent_reservations' => $recent_reservations,
            'weekly_reservations' => $weeklyReservations,
        ]);
    }

    /**
     * Display tables management
     */
    public function tables()
    {
        $tables = Table::orderBy('table_number')->get()->map(function ($table) {
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'position_x' => $table->position_x,
                'position_y' => $table->position_y,
                'created_at' => $table->created_at,
                'updated_at' => $table->updated_at,
            ];
        });

        return Inertia::render('Admin/Tables', [
            'tables' => $tables,
        ]);
    }    /**
     * Display reservations management
     */
    public function reservations()
    {
        $reservations = Reservation::with(['client', 'table'])
            ->orderBy('reservation_date', 'desc')
            ->get()
            ->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'client_name' => $reservation->client->name ?? 'Cliente eliminado',
                    'client_email' => $reservation->client->email ?? 'N/A',
                    'table_number' => $reservation->table->table_number ?? 'Mesa eliminada',
                    'reservation_date' => $reservation->reservation_date,
                    'party_size' => $reservation->party_size,
                    'status' => $reservation->status,
                    'created_at' => $reservation->created_at,
                    'updated_at' => $reservation->updated_at,
                ];
            });

        return Inertia::render('Admin/Reservations', [
            'reservations' => $reservations,
        ]);
    }    /**
     * Display menu management
     */
    public function menu()
    {
        // Redirigir al nuevo controlador AdminMenuController
        return app(\App\Http\Controllers\AdminMenuController::class)->index();
    }

    /**
     * Display users management
     */    public function users()
    {
        $users = User::orderBy('name')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role ?? 'employee', // Aseguramos que siempre tenga un rol
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });

        return Inertia::render('Admin/Users', [
            'users' => $users,
        ]);
    }
}
