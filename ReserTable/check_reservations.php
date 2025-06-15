<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Reservation;
use Carbon\Carbon;

echo "=== VERIFICACIÓN DE RESERVAS ===\n";

$reservations = Reservation::whereIn('status', ['pending', 'confirmed'])
    ->orderBy('reservation_date')
    ->orderBy('reservation_time')
    ->get();

echo "Total de reservas activas: " . $reservations->count() . "\n\n";

foreach ($reservations as $reservation) {
    echo "ID: {$reservation->id}\n";
    echo "Mesa: {$reservation->table_id}\n";
    echo "Fecha: {$reservation->reservation_date}\n";
    echo "Hora: {$reservation->reservation_time}\n";
    echo "Estado: {$reservation->status}\n";
    echo "Cliente: {$reservation->client_id}\n";
    echo "--------------------\n";
}

// Verificar también las reservas para hoy
$today = Carbon::today();
echo "\n=== RESERVAS PARA HOY ({$today->format('Y-m-d')}) ===\n";

$todayReservations = Reservation::whereDate('reservation_date', $today)
    ->whereIn('status', ['pending', 'confirmed'])
    ->get();

echo "Reservas para hoy: " . $todayReservations->count() . "\n";

foreach ($todayReservations as $reservation) {
    echo "Mesa {$reservation->table_id} a las {$reservation->reservation_time} (Estado: {$reservation->status})\n";
}
